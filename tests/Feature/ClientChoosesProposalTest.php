<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Conversation;
use App\Models\Mission;
use App\Models\NeedProposal;
use App\Models\Notification;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Étape 7 : le client compare les propositions et en choisit une (§2.5).
 */
class ClientChoosesProposalTest extends TestCase
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
        $this->client = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);
    }

    private function makeArtisan(?Commune $commune = null, string $name = null): User
    {
        $artisan = User::factory()->create(array_filter([
            'user_type' => User::TYPE_ARTISAN, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE,
            'is_available' => true, 'availability_days' => ['lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim'],
            'working_hours_start' => '00:00', 'working_hours_end' => '23:59', 'name' => $name,
        ]));
        DB::table('services')->insert([
            'artisan_id' => $artisan->id, 'category_id' => $this->category->id, 'title' => 'Peinture',
            'provider_name' => $artisan->name, 'profession' => 'Peintre', 'city' => 'Kinshasa',
            'phone_number' => '0800000000', 'status' => 'active', 'pricing_type' => 'quote',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $artisan->interventionCommunes()->sync([($commune ?? $this->lemba)->id]);

        return $artisan;
    }

    private function publish(): ServiceRequest
    {
        $this->actingAs($this->client)->post(route('user.needs.store'), [
            'category_id' => $this->category->id, 'description' => 'Repeindre un salon de 20 m2, murs et plafond.',
            'commune_id' => $this->lemba->id, 'timing' => 'this_week', 'budget_type' => 'none',
        ])->assertSessionHasNoErrors();

        return ServiceRequest::latest('id')->firstOrFail();
    }

    private function offer(User $artisan, ServiceRequest $need, int $price): NeedProposal
    {
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), [
            'type' => 'price', 'price' => $price, 'intervention_date' => now()->addDays(3)->toDateString(), 'message' => "Offre de {$artisan->name}",
        ])->assertSessionHasNoErrors();

        return NeedProposal::where('artisan_id', $artisan->id)->where('service_request_id', $need->id)->firstOrFail();
    }

    private function visit(User $artisan, ServiceRequest $need): NeedProposal
    {
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), [
            'type' => 'visit', 'visit_at' => now()->addDays(2)->format('Y-m-d\TH:i'), 'study_fee' => 10,
        ])->assertSessionHasNoErrors();

        return NeedProposal::where('artisan_id', $artisan->id)->where('service_request_id', $need->id)->firstOrFail();
    }

    public function test_client_sees_comparable_cards_with_a_recommendation(): void
    {
        $good = $this->makeArtisan(name: 'Dan Bope');
        $cheap = $this->makeArtisan(name: 'Chris Mbala');
        // Moyenne plateforme réaliste (3,5) : sinon la note ajustée de tous vaut 5.
        $elsewhere = User::factory()->create(['user_type' => User::TYPE_ARTISAN, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);
        foreach (range(1, 6) as $_) {
            Review::create(['client_id' => $this->client->id, 'artisan_id' => $good->id, 'rating' => 5, 'status' => 'approved']);
            Review::create(['client_id' => $this->client->id, 'artisan_id' => $elsewhere->id, 'rating' => 2, 'status' => 'approved']);
        }
        $need = $this->publish();
        $this->offer($good, $need, 110);
        $this->offer($cheap, $need, 100);

        $html = $this->actingAs($this->client)->get(route('user.service-requests.show', $need))
            ->assertOk()
            ->assertSee('Propositions reçues')
            ->assertSee('Dan Bope')->assertSee('Chris Mbala')
            ->assertSee('$110')->assertSee('$100')
            ->assertSee('Offre de Dan Bope')
            ->assertSee('Recommandé pour toi')
            ->assertSee('Le mieux noté (5,0/5, 6 avis)', false)
            ->assertSee('Aucune proposition ne me convient')
            ->getContent();

        // La recommandation est sur la carte de Dan Bope (meilleure note, prix proche).
        $this->assertLessThan(strpos($html, 'Chris Mbala'), strpos($html, 'Recommandé pour toi'));
    }

    public function test_accepting_locks_the_price_and_switches_to_the_mission_flow(): void
    {
        [$a, $b, $c] = [$this->makeArtisan(), $this->makeArtisan(), $this->makeArtisan()];
        $need = $this->publish();
        $chosen = $this->offer($a, $need, 150);
        $other = $this->offer($b, $need, 130);
        // $c n'a pas encore répondu.

        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $chosen))
            ->assertRedirect(route('user.service-requests.show', $need))
            ->assertSessionHasNoErrors();

        $need->refresh();
        $this->assertSame('accepted', $need->status);
        $this->assertSame($a->id, $need->artisan_id);
        $this->assertSame($chosen->id, $need->accepted_proposal_id);
        $this->assertNull($need->wave_deadline_at);
        $this->assertSame('accepted', $chosen->fresh()->status);
        $this->assertSame('rejected', $other->fresh()->status);
        $this->assertSame('closed', $need->dispatches()->where('artisan_id', $c->id)->value('status'));

        $mission = Mission::where('service_request_id', $need->id)->firstOrFail();
        $this->assertEquals(150, $mission->amount);
        $this->assertSame($a->id, $mission->artisan_id);
        $this->assertTrue(Conversation::findOrCreateBetween($this->client->id, $a->id)->exists);

        $this->assertTrue(Notification::where('user_id', $a->id)->where('type', 'need_proposal_accepted')->exists());
        $this->assertTrue(Notification::where('user_id', $b->id)->where('type', 'need_proposal_rejected')->exists());
        $this->assertFalse(Notification::where('user_id', $c->id)->where('type', 'need_proposal_rejected')->exists());

        // Le prix affiché pour le paiement est le prix verrouillé.
        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))
            ->assertOk()->assertSee('prix verrouillé')->assertDontSee('Aucune proposition ne me convient');

        // La mission apparaît dans les demandes de l'artisan retenu (suivi existant).
        $this->actingAs($a)->get(route('user.artisan.service-requests.index'))->assertOk()->assertSee($need->requested_service_name);
    }

    public function test_full_mission_flow_after_acceptance(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $proposal = $this->offer($a, $need, 150);
        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $proposal));

        $this->actingAs($this->client)->post(route('user.service-requests.pay-cash', $need))->assertSessionHasNoErrors();
        $this->assertSame('in_progress', $need->fresh()->status);

        $this->actingAs($a)->post(route('user.service-requests.complete', $need))->assertSessionHasNoErrors();
        $this->assertSame('awaiting_validation', $need->fresh()->status);

        $this->actingAs($this->client)->post(route('user.service-requests.validate-completion', $need))->assertSessionHasNoErrors();
        $this->assertSame('completed', $need->fresh()->status);

        $this->actingAs($this->client)->post(route('user.service-requests.rate', $need), ['rating' => 5, 'comment' => 'Travail propre'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Review::where('artisan_id', $a->id)->where('service_request_id', $need->id)->where('rating', 5)->exists());
        $this->assertSame('completed', Mission::where('service_request_id', $need->id)->value('status'));
    }

    public function test_a_second_acceptance_is_refused(): void
    {
        [$a, $b] = [$this->makeArtisan(), $this->makeArtisan()];
        $need = $this->publish();
        $first = $this->offer($a, $need, 150);
        $second = $this->offer($b, $need, 130);

        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $first));
        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $second))->assertSessionHasErrors('proposal');

        $this->assertSame($a->id, $need->fresh()->artisan_id);
        $this->assertSame(1, Mission::where('service_request_id', $need->id)->count());
        $this->assertSame('rejected', $second->fresh()->status);
    }

    public function test_only_the_owner_can_choose(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $proposal = $this->offer($a, $need, 150);
        $stranger = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);

        $this->actingAs($stranger)->post(route('user.needs.proposals.accept', $proposal))->assertForbidden();
        $this->actingAs($stranger)->post(route('user.needs.reject-all', $need))->assertForbidden();
        $this->actingAs($a)->post(route('user.needs.proposals.accept', $proposal))->assertForbidden();

        $this->assertSame('pending', $need->fresh()->status);
        $this->assertSame('pending', $proposal->fresh()->status);
    }

    public function test_visit_then_quote_then_acceptance(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $proposal = $this->visit($a, $need);

        // Pas d'acceptation directe d'une visite sans devis.
        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $proposal))->assertSessionHasErrors('proposal');

        $this->actingAs($this->client)->post(route('user.needs.proposals.accept-visit', $proposal))->assertSessionHasNoErrors();
        $this->assertSame('visit_accepted', $proposal->fresh()->status);
        $this->assertTrue(Notification::where('user_id', $a->id)->where('type', 'need_visit_accepted')->exists());

        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))->assertSee('devis attendu');

        $this->actingAs($a)->post(route('user.artisan.opportunities.quote', $need), [
            'quote_labor' => 200, 'quote_materials' => 100, 'quote_duration' => '4 jours',
            'steps' => [['label' => 'Acompte', 'percent' => 30], ['label' => 'Fin', 'percent' => 70]],
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))->assertSee('Accepter le devis')->assertSee('$300');
        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $proposal))->assertSessionHasNoErrors();

        $this->assertSame('accepted', $need->fresh()->status);
        $this->assertEquals(300, Mission::where('service_request_id', $need->id)->value('amount'));
    }

    public function test_rejecting_all_relaunches_the_search_with_new_artisans(): void
    {
        $artisans = array_map(fn () => $this->makeArtisan(), range(1, 7));
        $need = $this->publish();
        $firstWave = $need->dispatches()->pluck('artisan_id')->all();
        foreach (array_slice($firstWave, 0, 3) as $id) {
            $this->offer(User::find($id), $need, 200);
        }

        $this->actingAs($this->client)->post(route('user.needs.reject-all', $need))
            ->assertRedirect(route('user.service-requests.show', $need));

        $need->refresh();
        $this->assertSame('pending', $need->status);
        $this->assertSame(3, $need->proposals()->where('status', 'rejected')->count());
        $this->assertSame(2, $need->current_wave);
        $this->assertSame(2, $need->dispatches()->where('wave', 2)->count()); // les 2 artisans restants
        $this->assertSame(3, Notification::where('type', 'need_proposal_rejected')->count());
        $this->actingAs($this->client)->getJson(route('user.needs.status', $need))->assertJson(['proposals' => 0]);
        $this->assertCount(7, $artisans);

        // Plus rien à refuser.
        $this->actingAs($this->client)->post(route('user.needs.reject-all', $need))->assertSessionHasErrors('proposal');
    }

    public function test_no_choice_on_a_cancelled_need(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $proposal = $this->offer($a, $need, 150);
        $this->actingAs($this->client)->post(route('user.service-requests.cancel', $need));

        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $proposal))->assertSessionHasErrors('proposal');
        $this->assertSame(0, Mission::count());
    }
}
