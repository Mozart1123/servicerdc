<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\NeedDispatch;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\Matching\ArtisanMatcher;
use App\Services\Matching\NeedDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Étape 5 : envoi par vagues (cahier des charges §2.3).
 */
class NeedDispatchTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Commune $lemba;
    private User $client;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['matching.dispatch_enabled' => true]);
        $this->category = Category::create(['name' => 'Peinture', 'slug' => 'peinture']);
        $this->lemba = Commune::where('slug', 'lemba')->firstOrFail();
        $this->client = User::factory()->create([
            'user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE,
            'name' => 'Grace Mbuyi', 'phone' => '+243810000000',
        ]);
    }

    private function makeArtisan(?Commune $commune = null): User
    {
        $artisan = User::factory()->create([
            'user_type' => User::TYPE_ARTISAN, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE,
            'is_available' => true, 'availability_days' => ['lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim'],
            'working_hours_start' => '00:00', 'working_hours_end' => '23:59',
        ]);
        DB::table('services')->insert([
            'artisan_id' => $artisan->id, 'category_id' => $this->category->id, 'title' => 'Peinture',
            'provider_name' => $artisan->name, 'profession' => 'Peintre', 'city' => 'Kinshasa',
            'phone_number' => '0800000000', 'status' => 'active', 'pricing_type' => 'quote',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $artisan->interventionCommunes()->sync([($commune ?? $this->lemba)->id]);

        return $artisan;
    }

    private function artisans(int $count, ?Commune $commune = null): array
    {
        return array_map(fn () => $this->makeArtisan($commune), range(1, $count));
    }

    private function publish(array $overrides = []): ServiceRequest
    {
        $this->actingAs($this->client)->post(route('user.needs.store'), array_merge([
            'category_id' => $this->category->id, 'description' => 'Repeindre un salon de 20 m2, murs et plafond.',
            'commune_id' => $this->lemba->id, 'quartier' => 'Righini', 'timing' => 'this_week', 'budget_type' => 'none',
        ], $overrides))->assertSessionHasNoErrors();

        return ServiceRequest::latest('id')->firstOrFail();
    }

    private function dispatcher(): NeedDispatcher
    {
        return app(NeedDispatcher::class);
    }

    private function propose(ServiceRequest $need, int $count): void
    {
        $need->dispatches()->where('status', 'sent')->take($count)->get()
            ->each(fn ($d) => $d->update(['status' => NeedDispatch::STATUS_PROPOSED, 'responded_at' => now()]));
    }

    public function test_nothing_is_sent_while_the_feature_is_disabled(): void
    {
        config(['matching.dispatch_enabled' => false]);
        $this->artisans(3);

        $need = $this->publish();
        $this->artisan('proconnect:advance-need-waves')->assertExitCode(0);

        $this->assertSame(0, NeedDispatch::count());
        $this->assertSame(0, $need->fresh()->current_wave);
    }

    public function test_publishing_sends_the_first_wave_to_the_five_best_artisans(): void
    {
        $artisans = $this->artisans(7);

        $need = $this->publish();

        $this->assertSame(1, $need->current_wave);
        $this->assertSame(5, $need->dispatches()->count());
        $this->assertTrue($need->wave_deadline_at->between(now()->addMinutes(119), now()->addMinutes(121)));
        $this->assertSame(5, Notification::where('type', 'need_dispatched')->where('related_id', $need->id)->count());
        $this->assertSame(0, Notification::where('type', 'need_dispatched')->where('user_id', $this->client->id)->count());

        // Les 5 contactés sont bien les 5 premiers du classement.
        $expected = app(ArtisanMatcher::class)->rank($need->fresh())->take(5)->map(fn ($c) => $c->artisan->id)->all();
        $this->assertEqualsCanonicalizing($expected, $need->dispatches()->pluck('artisan_id')->all());
        $this->assertCount(7, $artisans);
    }

    public function test_urgent_need_gets_a_30_minute_deadline(): void
    {
        $this->artisans(2);
        $need = $this->publish(['timing' => 'urgent']);

        $this->assertTrue($need->wave_deadline_at->between(now()->addMinutes(29), now()->addMinutes(31)));
    }

    public function test_advancing_before_the_deadline_changes_nothing_and_is_idempotent(): void
    {
        $this->artisans(8);
        $need = $this->publish();

        $this->dispatcher()->advance($need->id);
        $this->dispatcher()->advance($need->id);
        $this->artisan('proconnect:advance-need-waves');

        $this->assertSame(1, $need->fresh()->current_wave);
        $this->assertSame(5, $need->dispatches()->count());
    }

    public function test_next_wave_goes_to_new_artisans_when_fewer_than_three_proposals(): void
    {
        $this->artisans(8);
        $need = $this->publish();
        $firstWave = $need->dispatches()->pluck('artisan_id')->all();
        $this->propose($need, 2);

        $this->travel(121)->minutes();
        $this->artisan('proconnect:advance-need-waves')->assertExitCode(0);

        $need->refresh();
        $this->assertSame(2, $need->current_wave);
        $secondWave = $need->dispatches()->where('wave', 2)->pluck('artisan_id')->all();
        $this->assertCount(3, $secondWave); // seuls 3 artisans restaient
        $this->assertEmpty(array_intersect($firstWave, $secondWave));
        // Ceux qui n'ont pas répondu passent en "expiré".
        $this->assertSame(3, $need->dispatches()->where('wave', 1)->where('status', 'expired')->count());
        $this->assertSame(2, $need->dispatches()->where('status', 'proposed')->count());
    }

    public function test_sending_stops_with_three_or_more_proposals(): void
    {
        $this->artisans(8);
        $need = $this->publish();
        $this->propose($need, 3);

        $this->travel(121)->minutes();
        $this->artisan('proconnect:advance-need-waves');

        $need->refresh();
        $this->assertSame(1, $need->current_wave);
        $this->assertNull($need->wave_deadline_at);
        $this->assertSame(5, $need->dispatches()->count());

        // Plus rien ensuite, même bien plus tard.
        $this->travel(5)->hours();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame(5, $need->dispatches()->count());
    }

    public function test_no_artisan_available_notifies_the_client_once_and_retries_later(): void
    {
        $need = $this->publish();

        $need->refresh();
        $this->assertNotNull($need->matching_exhausted_at);
        $this->assertSame(1, Notification::where('user_id', $this->client->id)->where('type', 'need_no_artisan')->count());

        // Retente après 1 h, toujours personne : pas de nouvelle notification.
        $this->travel(61)->minutes();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame(1, Notification::where('user_id', $this->client->id)->where('type', 'need_no_artisan')->count());

        // Un artisan devient disponible : la vague part au prochain essai.
        $artisan = $this->makeArtisan();
        $this->travel(61)->minutes();
        $this->artisan('proconnect:advance-need-waves');

        $need->refresh();
        $this->assertSame(1, $need->current_wave);
        $this->assertNull($need->matching_exhausted_at);
        $this->assertSame([$artisan->id], $need->dispatches()->pluck('artisan_id')->all());
    }

    public function test_search_is_widened_and_the_client_is_told(): void
    {
        $near = $this->makeArtisan();
        $far = $this->makeArtisan(Commune::where('slug', 'gombe')->firstOrFail());

        $need = $this->publish();

        $this->assertNotNull($need->fresh()->matching_widened_at);
        $this->assertEqualsCanonicalizing([$near->id, $far->id], $need->dispatches()->pluck('artisan_id')->all());
        $this->assertSame('wide', $need->dispatches()->where('artisan_id', $far->id)->value('proximity'));
        $this->assertSame(1, Notification::where('user_id', $this->client->id)->where('type', 'need_widened')->count());
    }

    public function test_cancelling_closes_pending_dispatches_and_stops_waves(): void
    {
        [$artisan] = $this->artisans(6);
        $need = $this->publish();
        $dispatched = $need->dispatches()->value('artisan_id');

        $this->actingAs($this->client)->post(route('user.service-requests.cancel', $need))->assertRedirect();

        $this->assertSame(5, $need->dispatches()->where('status', 'closed')->count());
        $this->travel(3)->hours();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame(5, $need->dispatches()->count());

        $this->actingAs(User::find($dispatched))->get(route('user.artisan.opportunities.show', $need))
            ->assertRedirect(route('user.artisan.opportunities.index'));
    }

    public function test_only_dispatched_artisans_see_the_need_and_client_details_stay_private(): void
    {
        [$a1] = $this->artisans(1);
        $need = $this->publish();
        $outsider = $this->makeArtisan(); // inscrit après la vague : pas contacté

        $this->actingAs($a1)->get(route('user.artisan.opportunities.index'))
            ->assertOk()->assertSee($need->requested_service_name);
        $this->actingAs($a1)->get(route('user.artisan.opportunities.show', $need))
            ->assertOk()
            ->assertSee('Repeindre un salon')
            ->assertSee('Grace')
            ->assertDontSee('Mbuyi')
            ->assertDontSee('+243810000000')
            ->assertDontSee($this->client->email);

        $this->actingAs($outsider)->get(route('user.artisan.opportunities.index'))
            ->assertOk()->assertDontSee($need->requested_service_name);
        $this->actingAs($outsider)->get(route('user.artisan.opportunities.show', $need))->assertNotFound();
        $this->actingAs($this->client)->get(route('user.artisan.opportunities.show', $need))->assertStatus(302);
    }

    public function test_client_live_status_endpoint(): void
    {
        $this->artisans(4);
        $need = $this->publish();
        $this->propose($need, 1);

        $this->actingAs($this->client)->getJson(route('user.needs.status', $need))
            ->assertOk()
            ->assertJson(['contacted' => 4, 'proposals' => 1, 'searching' => true, 'status' => 'pending']);

        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))
            ->assertOk()->assertSee('Artisans contactés')->assertSee('Propositions reçues');

        $other = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);
        $this->actingAs($other)->getJson(route('user.needs.status', $need))->assertForbidden();
    }

    public function test_ignoring_dispatches_lowers_responsiveness(): void
    {
        [$artisan] = $this->artisans(1);
        $need = $this->publish();
        $this->travel(121)->minutes();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame('expired', $need->dispatches()->value('status'));

        $other = $this->publish(['description' => 'Peindre une chambre de 12 m2, deux couches.']);
        $candidate = app(ArtisanMatcher::class)->rank($other)->first(fn ($c) => $c->artisan->id === $artisan->id);

        // Taux de réponse 0/1 → 0 pt, délai inconnu → 2,5 pts.
        $this->assertEqualsWithDelta(2.5, $candidate->breakdown['responsiveness'], 0.01);
    }
}
