<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\NeedDispatch;
use App\Models\NeedProposal;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\Matching\ArtisanMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Étape 6 : réponse de l'artisan (proposition de prix, visite puis devis, refus).
 */
class NeedProposalTest extends TestCase
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

    private function makeArtisan(): User
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
        $artisan->interventionCommunes()->sync([$this->lemba->id]);

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

    private function priceOffer(array $overrides = []): array
    {
        return array_merge(['type' => 'price', 'price' => 150, 'intervention_date' => now()->addDays(3)->toDateString(), 'message' => 'Peinture fournie, 2 couches.'], $overrides);
    }

    public function test_artisan_sends_a_price_proposal(): void
    {
        $artisan = $this->makeArtisan();
        $need = $this->publish();

        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer())
            ->assertRedirect(route('user.artisan.opportunities.show', $need))
            ->assertSessionHasNoErrors();

        $proposal = NeedProposal::firstOrFail();
        $this->assertSame('price', $proposal->type);
        $this->assertSame('pending', $proposal->status);
        $this->assertEquals(150, $proposal->price);
        $this->assertSame('proposed', $need->dispatches()->value('status'));
        $this->assertNotNull($need->dispatches()->value('responded_at'));
        $this->assertTrue(Notification::where('user_id', $this->client->id)->where('type', 'need_proposal')->exists());

        $this->actingAs($artisan)->get(route('user.artisan.opportunities.show', $need))
            ->assertOk()->assertSee('Votre proposition')->assertSee('$150')->assertDontSee('Envoyer ma proposition');

        $this->actingAs($this->client)->getJson(route('user.needs.status', $need))->assertJson(['proposals' => 1]);
    }

    public function test_artisan_cannot_answer_twice_even_with_a_double_click(): void
    {
        $artisan = $this->makeArtisan();
        $need = $this->publish();

        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer());
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer(['price' => 90]))
            ->assertSessionHasErrors('response');
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.decline', $need))->assertSessionHasErrors('response');

        $this->assertSame(1, NeedProposal::count());
        $this->assertEquals(150, NeedProposal::first()->price);
        $this->assertSame('proposed', $need->dispatches()->value('status'));
    }

    public function test_artisan_who_did_not_receive_the_need_cannot_answer(): void
    {
        $this->makeArtisan();
        $need = $this->publish();
        $intruder = $this->makeArtisan();

        $this->actingAs($intruder)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer())->assertNotFound();
        $this->actingAs($intruder)->post(route('user.artisan.opportunities.decline', $need))->assertNotFound();
        $this->actingAs($this->client)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer())->assertRedirect();
        $this->assertSame(0, NeedProposal::count());
    }

    public function test_cannot_answer_after_the_deadline_or_after_cancellation(): void
    {
        [$late, $other] = [$this->makeArtisan(), $this->makeArtisan()];
        $need = $this->publish();

        $this->travel(121)->minutes(); // délai écoulé, même avant le passage de la tâche planifiée
        $this->actingAs($late)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer())
            ->assertSessionHasErrors('response');
        $this->actingAs($late)->get(route('user.artisan.opportunities.show', $need))->assertSee('Le délai de réponse est dépassé');
        $this->travelBack();

        $need2 = $this->publish();
        $this->actingAs($this->client)->post(route('user.service-requests.cancel', $need2));
        $this->actingAs($other)->post(route('user.artisan.opportunities.propose', $need2), $this->priceOffer())
            ->assertSessionHasErrors('response');

        $this->assertSame(0, NeedProposal::count());
    }

    public function test_price_proposal_validation(): void
    {
        $artisan = $this->makeArtisan();
        $need = $this->publish();

        foreach ([
            'price'             => ['price' => 0],
            'intervention_date' => ['intervention_date' => now()->subDay()->toDateString()],
            'message'           => ['message' => str_repeat('a', 501)],
            'type'              => ['type' => 'gratuit'],
        ] as $field => $override) {
            $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer($override))
                ->assertSessionHasErrors($field);
        }
        $this->assertSame(0, NeedProposal::count());
        $this->assertSame('sent', $need->dispatches()->value('status'));
    }

    public function test_visit_time_is_entered_in_kinshasa_time(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-14 08:00:00', 'UTC'));
        $artisan = $this->makeArtisan();
        $need = $this->publish();

        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), [
            'type' => 'visit', 'visit_at' => '2026-10-15T10:30', 'study_fee' => 0,
        ])->assertSessionHasNoErrors();

        $proposal = NeedProposal::firstOrFail();
        $this->assertSame('visit', $proposal->type);
        $this->assertSame('2026-10-15 09:30:00', $proposal->visit_at->format('Y-m-d H:i:s')); // 10:30 Kinshasa = 09:30 UTC
        $this->assertNull($proposal->price);

        // Visite dans le passé (heure de Kinshasa) refusée.
        $need2 = $this->publish();
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need2), [
            'type' => 'visit', 'visit_at' => '2026-10-14T08:30', 'study_fee' => 0, // 07:30 UTC < maintenant
        ])->assertSessionHasErrors('visit_at');
        Carbon::setTestNow();
    }

    public function test_decline_with_optional_reason(): void
    {
        $artisan = $this->makeArtisan();
        $need = $this->publish();

        $this->actingAs($artisan)->post(route('user.artisan.opportunities.decline', $need), ['reason' => 'Trop loin de ma zone'])
            ->assertRedirect(route('user.artisan.opportunities.index'));

        $dispatch = NeedDispatch::firstOrFail();
        $this->assertSame('declined', $dispatch->status);
        $this->assertSame('Trop loin de ma zone', $dispatch->decline_reason);
        $this->assertNotNull($dispatch->responded_at);
        $this->assertSame(0, NeedProposal::count());
    }

    public function test_quote_only_after_the_visit_is_accepted_and_only_once(): void
    {
        $artisan = $this->makeArtisan();
        $need = $this->publish();
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), [
            'type' => 'visit', 'visit_at' => now()->addDays(2)->format('Y-m-d\TH:i'), 'study_fee' => 10,
        ])->assertSessionHasNoErrors();
        $quote = [
            'quote_labor' => 200, 'quote_materials' => 80, 'quote_duration' => '3 jours',
            'steps' => [['label' => 'Acompte', 'percent' => 40], ['label' => 'Fin des travaux', 'percent' => 60]],
        ];

        // Visite pas encore acceptée par le client.
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.quote', $need), $quote)->assertForbidden();

        NeedProposal::first()->update(['status' => NeedProposal::STATUS_VISIT_ACCEPTED]); // acceptation client (étape 7)

        $this->actingAs($artisan)->post(route('user.artisan.opportunities.quote', $need), array_merge($quote, [
            'steps' => [['label' => 'Acompte', 'percent' => 40], ['label' => 'Fin', 'percent' => 40]],
        ]))->assertSessionHasErrors('steps');

        $this->actingAs($artisan)->post(route('user.artisan.opportunities.quote', $need), $quote)->assertSessionHasNoErrors();
        $proposal = NeedProposal::first();
        $this->assertNotNull($proposal->quote_sent_at);
        $this->assertEquals(280, $proposal->price);
        $this->assertSame(280.0, $proposal->total);
        $this->assertSame([['label' => 'Acompte', 'percent' => 40], ['label' => 'Fin des travaux', 'percent' => 60]], $proposal->quote_payment_steps);
        $this->assertTrue(Notification::where('user_id', $this->client->id)->where('type', 'need_quote')->exists());

        // Pas de second devis.
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.quote', $need), array_merge($quote, ['quote_labor' => 1]))->assertForbidden();
        $this->assertEquals(280, NeedProposal::first()->price);
    }

    public function test_five_proposals_stop_the_waves(): void
    {
        $artisans = array_map(fn () => $this->makeArtisan(), range(1, 7));
        $need = $this->publish();
        $contacted = $need->dispatches()->pluck('artisan_id');

        foreach ($contacted as $id) {
            $this->actingAs(User::find($id))->post(route('user.artisan.opportunities.propose', $need), $this->priceOffer())->assertSessionHasNoErrors();
        }

        $this->assertNull($need->fresh()->wave_deadline_at);
        $this->travel(3)->hours();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame(5, $need->dispatches()->count());
        $this->assertCount(7, $artisans);
    }

    public function test_accepted_proposals_become_the_usual_price_for_quote_only_artisans(): void
    {
        $artisan = $this->makeArtisan(); // service "sur devis" : pas de prix affiché
        $old = $this->publish();
        NeedProposal::create([
            'service_request_id' => $old->id, 'artisan_id' => $artisan->id, 'type' => 'price',
            'status' => NeedProposal::STATUS_ACCEPTED, 'price' => 300,
        ]);

        $need = ServiceRequest::create([
            'user_id' => $this->client->id, 'mode' => 'open', 'status' => 'pending', 'category_id' => $this->category->id,
            'commune_id' => $this->lemba->id, 'requested_service_name' => 'x', 'description' => 'x',
            'budget_type' => 'amount', 'budget_min' => 200, 'budget_max' => 200,
        ]);
        $candidate = app(ArtisanMatcher::class)->rank($need)->first(fn ($c) => $c->artisan->id === $artisan->id);

        $this->assertEquals(300, $candidate->usualPrice);
        $this->assertSame(0.0, $candidate->breakdown['budget']); // 300 > 200 × 1,2
    }
}
