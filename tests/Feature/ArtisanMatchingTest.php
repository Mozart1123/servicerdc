<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\Setting;
use App\Models\User;
use App\Services\Matching\ArtisanMatcher;
use App\Services\Matching\MatchCandidate;
use App\Services\Matching\MatchingSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Étape 4 : filtres éliminatoires et score de pertinence (cahier des charges §2.1–2.2).
 */
class ArtisanMatchingTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Category $otherCategory;
    private User $client;
    private Commune $lemba;     // commune du client
    private Commune $ngaba;     // voisine de Lemba
    private Commune $gombe;     // ni identique ni voisine

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->category = Category::create(['name' => 'Peinture', 'slug' => 'peinture']);
        $this->otherCategory = Category::create(['name' => 'Plomberie', 'slug' => 'plomberie']);
        $this->lemba = Commune::where('slug', 'lemba')->firstOrFail();
        $this->ngaba = Commune::where('slug', 'ngaba')->firstOrFail();
        $this->gombe = Commune::where('slug', 'gombe')->firstOrFail();
        $this->client = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);
    }

    private function matcher(): ArtisanMatcher
    {
        return app(ArtisanMatcher::class);
    }

    private function makeArtisan(array $attrs = [], ?Commune $commune = null, ?float $price = null, ?Category $category = null): User
    {
        $artisan = User::factory()->create(array_merge([
            'user_type' => User::TYPE_ARTISAN, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE,
            'is_available' => true, 'availability_days' => ['lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim'],
            'working_hours_start' => '00:00', 'working_hours_end' => '23:59',
        ], $attrs));
        DB::table('services')->insert([
            'artisan_id' => $artisan->id, 'category_id' => ($category ?? $this->category)->id,
            'title' => 'Service', 'provider_name' => $artisan->name, 'profession' => 'Peintre',
            'city' => 'Kinshasa', 'phone_number' => '0800000000', 'status' => 'active',
            'price' => $price, 'pricing_type' => $price ? 'fixed' : 'quote',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $artisan->interventionCommunes()->sync([($commune ?? $this->lemba)->id]);

        return $artisan;
    }

    private function need(array $attrs = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'user_id' => $this->client->id, 'mode' => ServiceRequest::MODE_OPEN, 'status' => 'pending',
            'category_id' => $this->category->id, 'commune_id' => $this->lemba->id,
            'requested_service_name' => 'Peinture salon', 'description' => 'Repeindre un salon de 20 m2',
            'timing' => 'this_week', 'budget_type' => 'none',
        ], $attrs));
    }

    private function jobs(User $artisan, string $status, int $count, array $attrs = []): void
    {
        for ($i = 0; $i < $count; $i++) {
            ServiceRequest::create(array_merge([
                'user_id' => $this->client->id, 'artisan_id' => $artisan->id, 'status' => $status,
                'requested_service_name' => 'Mission', 'description' => 'x',
            ], $attrs));
        }
    }

    private function reviews(User $artisan, int $rating, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Review::create(['client_id' => $this->client->id, 'artisan_id' => $artisan->id, 'rating' => $rating, 'status' => 'approved']);
        }
    }

    /** @return MatchCandidate|null */
    private function candidateFor(User $artisan, ServiceRequest $need, bool $widen = false)
    {
        return $this->matcher()->rank($need, widen: $widen)->first(fn (MatchCandidate $c) => $c->artisan->id === $artisan->id);
    }

    public function test_eliminatory_filters(): void
    {
        $eligible = $this->makeArtisan();
        $wrongCategory = $this->makeArtisan(category: $this->otherCategory);
        $unavailable = $this->makeArtisan(['is_available' => false]);
        $onVacation = $this->makeArtisan(['available_until' => now()->addDays(3)->toDateString()]);
        $backFromVacation = $this->makeArtisan(['available_until' => now()->subDay()->toDateString()]);
        $suspended = $this->makeArtisan(['status' => User::STATUS_SUSPENDED]);
        $busy = $this->makeArtisan();
        $this->jobs($busy, 'in_progress', 2);
        $this->jobs($busy, 'accepted', 1);
        $twoJobs = $this->makeArtisan();
        $this->jobs($twoJobs, 'in_progress', 2);
        $this->jobs($twoJobs, 'completed', 5); // terminées : ne comptent pas comme "en cours"
        $farAway = $this->makeArtisan(commune: $this->gombe);
        $noZone = $this->makeArtisan();
        $noZone->interventionCommunes()->sync([]);

        $ids = $this->matcher()->rank($this->need())->map(fn ($c) => $c->artisan->id)->all();

        $this->assertEqualsCanonicalizing([$eligible->id, $backFromVacation->id, $twoJobs->id], $ids);

        // Zone élargie : les artisans hors commune (ou sans zone déclarée) reviennent, à 5 pts de proximité.
        $wide = $this->matcher()->rank($this->need(), widen: true)->map(fn ($c) => $c->artisan->id)->all();
        $this->assertContains($farAway->id, $wide);
        $this->assertContains($noZone->id, $wide);
        $this->assertNotContains($busy->id, $wide);
        $this->assertNotContains($suspended->id, $wide);
    }

    public function test_client_is_never_matched_with_himself_and_exclusions_apply(): void
    {
        $a = $this->makeArtisan();
        $b = $this->makeArtisan();
        $need = $this->need(['user_id' => $a->id]);

        $ids = $this->matcher()->rank($need, excludeIds: [$b->id])->map(fn ($c) => $c->artisan->id)->all();
        $this->assertSame([], $ids);
    }

    public function test_urgent_need_only_reaches_artisans_working_right_now(): void
    {
        // Mercredi 10:00 à Kinshasa = 09:00 UTC.
        Carbon::setTestNow(Carbon::parse('2026-10-14 09:00:00', 'UTC'));

        $working = $this->makeArtisan(['availability_days' => ['mer'], 'working_hours_start' => '08:00', 'working_hours_end' => '18:00']);
        $dayOff = $this->makeArtisan(['availability_days' => ['lun', 'mar'], 'working_hours_start' => '08:00', 'working_hours_end' => '18:00']);
        $notYet = $this->makeArtisan(['availability_days' => ['mer'], 'working_hours_start' => '14:00', 'working_hours_end' => '20:00']);

        $urgent = $this->matcher()->rank($this->need(['timing' => 'urgent']))->map(fn ($c) => $c->artisan->id)->all();
        $this->assertSame([$working->id], $urgent);

        $normal = $this->matcher()->rank($this->need())->map(fn ($c) => $c->artisan->id)->all();
        $this->assertEqualsCanonicalizing([$working->id, $dayOff->id, $notYet->id], $normal);

        Carbon::setTestNow();
    }

    public function test_bayesian_rating_matches_the_specification_example(): void
    {
        // Exemple du cahier des charges : moyenne plateforme 4,0 ; A = 4,8 sur 40 avis ; B = 5,0 sur 1 avis.
        $a = $this->makeArtisan();
        $b = $this->makeArtisan();
        $filler = $this->makeArtisan(category: $this->otherCategory);
        $this->reviews($a, 5, 32);
        $this->reviews($a, 4, 8);   // 40 avis, moyenne 4,8
        $this->reviews($b, 5, 1);
        $this->reviews($filler, 1, 11); // ramène la moyenne plateforme à exactement 4,0
        $this->assertEqualsWithDelta(4.0, Review::approved()->avg('rating'), 0.0001);

        $need = $this->need();
        $ca = $this->candidateFor($a, $need);
        $cb = $this->candidateFor($b, $need);

        $this->assertEqualsWithDelta(4.71, $ca->adjustedRating, 0.01);
        $this->assertEqualsWithDelta(27.8, $ca->breakdown['rating'], 0.05);
        $this->assertEqualsWithDelta(4.17, $cb->adjustedRating, 0.01);
        $this->assertEqualsWithDelta(23.8, $cb->breakdown['rating'], 0.05);
        $this->assertGreaterThan($cb->breakdown['rating'], $ca->breakdown['rating']);
    }

    public function test_proximity_points(): void
    {
        $same = $this->makeArtisan(commune: $this->lemba);
        $neighbor = $this->makeArtisan(commune: $this->ngaba);
        $wide = $this->makeArtisan(commune: $this->gombe);
        $need = $this->need();

        $this->assertSame(20.0, $this->candidateFor($same, $need)->breakdown['proximity']);
        $this->assertSame(12.0, $this->candidateFor($neighbor, $need)->breakdown['proximity']);
        $this->assertNull($this->candidateFor($wide, $need));
        $this->assertSame(5.0, $this->candidateFor($wide, $need, widen: true)->breakdown['proximity']);
    }

    public function test_budget_points(): void
    {
        $cheap = $this->makeArtisan(price: 100);
        $slightlyAbove = $this->makeArtisan(price: 115);
        $tooExpensive = $this->makeArtisan(price: 130);
        $onQuote = $this->makeArtisan();

        $need = $this->need(['budget_type' => 'range', 'budget_min' => 50, 'budget_max' => 100]);
        $this->assertSame(15.0, $this->candidateFor($cheap, $need)->breakdown['budget']);
        $this->assertSame(8.0, $this->candidateFor($slightlyAbove, $need)->breakdown['budget']);
        $this->assertSame(0.0, $this->candidateFor($tooExpensive, $need)->breakdown['budget']);
        $this->assertSame(10.0, $this->candidateFor($onQuote, $need)->breakdown['budget']);

        $noBudget = $this->need();
        $this->assertSame(10.0, $this->candidateFor($tooExpensive, $noBudget)->breakdown['budget']);
    }

    public function test_reliability_and_newcomer_bonus(): void
    {
        $newcomer = $this->makeArtisan();
        $veteran = $this->makeArtisan();
        $this->jobs($veteran, 'completed', 12);
        $this->jobs($veteran, 'cancelled', 3, ['cancelled_by' => $veteran->id]);   // compte contre lui
        $this->jobs($veteran, 'cancelled', 4, ['cancelled_by' => $this->client->id]); // annulé par le client : ignoré
        $middle = $this->makeArtisan();
        $this->jobs($middle, 'completed', 6);

        $need = $this->need();
        $cNew = $this->candidateFor($newcomer, $need);
        $cVet = $this->candidateFor($veteran, $need);
        $cMid = $this->candidateFor($middle, $need);

        $this->assertSame(10.0, $cNew->breakdown['newcomer']);
        $this->assertSame(5.0, $cMid->breakdown['newcomer']);
        $this->assertSame(0.0, $cVet->breakdown['newcomer']);

        $this->assertSame(5.0, $cNew->breakdown['reliability']); // aucune donnée : neutre
        $this->assertSame(8.0, $cVet->breakdown['reliability']); // 12 / (12 + 3) × 10
        $this->assertSame(10.0, $cMid->breakdown['reliability']);
    }

    public function test_responsiveness_rewards_artisans_who_answer_quickly(): void
    {
        $fast = $this->makeArtisan();
        $ignores = $this->makeArtisan();
        // 2 demandes reçues, 2 réponses en 30 min.
        foreach ([1, 2] as $_) {
            $r = ServiceRequest::create(['user_id' => $this->client->id, 'artisan_id' => $fast->id, 'status' => 'rejected', 'requested_service_name' => 'x', 'description' => 'x']);
            $r->forceFill(['responded_at' => $r->created_at->copy()->addMinutes(30)])->save();
        }
        // 3 demandes reçues, aucune réponse.
        $this->jobs($ignores, 'pending', 3);

        $need = $this->need();
        $this->assertSame(15.0, $this->candidateFor($fast, $need)->breakdown['responsiveness']);       // 10 + 5
        $this->assertEqualsWithDelta(2.5, $this->candidateFor($ignores, $need)->breakdown['responsiveness'], 0.01); // 0 + délai inconnu 2,5
    }

    public function test_accepting_or_rejecting_records_the_response_time(): void
    {
        $artisan = $this->makeArtisan();
        $request = ServiceRequest::create(['user_id' => $this->client->id, 'artisan_id' => $artisan->id, 'status' => 'pending', 'requested_service_name' => 'x', 'description' => 'x']);

        $this->actingAs($artisan)->post(route('user.service-requests.reject', $request))->assertRedirect();

        $this->assertNotNull($request->fresh()->responded_at);
        $this->assertSame($artisan->id, $request->fresh()->responded_by);
    }

    public function test_ranking_order_and_shortlist_widening(): void
    {
        $best = $this->makeArtisan(commune: $this->lemba, price: 80);
        $this->reviews($best, 5, 10);
        $second = $this->makeArtisan(commune: $this->ngaba);
        $far = $this->makeArtisan(commune: $this->gombe);

        $need = $this->need(['budget_type' => 'amount', 'budget_min' => 100, 'budget_max' => 100]);
        $ranking = $this->matcher()->rank($need);
        $this->assertSame([$best->id, $second->id], $ranking->map(fn ($c) => $c->artisan->id)->all());
        $this->assertLessThanOrEqual(100, $ranking->first()->score);

        // 2 artisans proches seulement pour une vague de 5 : on élargit et on le signale.
        $result = $this->matcher()->shortlist($need, 5);
        $this->assertTrue($result['widened']);
        $this->assertSame([$best->id, $second->id, $far->id], $result['candidates']->map(fn ($c) => $c->artisan->id)->all());

        $enough = $this->matcher()->shortlist($need, 2);
        $this->assertFalse($enough['widened']);
        $this->assertCount(2, $enough['candidates']);
    }

    public function test_custom_weights_change_the_score(): void
    {
        $artisan = $this->makeArtisan();
        app(MatchingSettings::class)->save(['rating' => 0, 'proximity' => 100, 'budget' => 0, 'responsiveness' => 0, 'reliability' => 0, 'newcomer' => 0]);

        $this->assertSame(100.0, $this->candidateFor($artisan, $this->need())->score);
    }

    public function test_invalid_stored_weights_fall_back_to_defaults(): void
    {
        // La table settings est aussi écrite par le formulaire générique de l'admin.
        Setting::updateOrCreate(['key' => 'matching.weight.rating'], ['value' => '<script>']);

        $this->assertSame(config('matching.weights'), app(MatchingSettings::class)->weights());
    }

    public function test_admin_can_update_weights_but_total_must_be_100(): void
    {
        $admin = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);
        $valid = ['rating' => 25, 'proximity' => 25, 'budget' => 15, 'responsiveness' => 15, 'reliability' => 10, 'newcomer' => 10];

        $this->actingAs($admin)->put(route('admin.matching.update'), ['weights' => array_merge($valid, ['rating' => 40])])
            ->assertSessionHasErrors('weights');
        $this->actingAs($admin)->put(route('admin.matching.update'), ['weights' => array_merge($valid, ['budget' => -5, 'rating' => 45])])
            ->assertSessionHasErrors('weights.budget');
        $this->assertSame(config('matching.weights'), app(MatchingSettings::class)->weights());

        $this->actingAs($admin)->put(route('admin.matching.update'), ['weights' => $valid])->assertSessionHasNoErrors();
        $this->assertSame($valid, app(MatchingSettings::class)->weights());
    }

    public function test_only_admins_reach_the_matching_page(): void
    {
        $need = $this->need();
        $this->makeArtisan();

        $this->actingAs($this->client)->get(route('admin.matching.index'))->assertStatus(403);
        $this->actingAs($this->client)->put(route('admin.matching.update'), ['weights' => config('matching.weights')])->assertStatus(403);

        $admin = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);
        $this->actingAs($admin)->get(route('admin.matching.index', ['need' => $need->id]))
            ->assertOk()
            ->assertSee('Aperçu du classement')
            ->assertSee('Même commune');
    }
}
