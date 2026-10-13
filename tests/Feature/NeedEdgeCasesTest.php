<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\NeedProposal;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Étape 8 : cas limites (cahier des charges §2.6).
 */
class NeedEdgeCasesTest extends TestCase
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
            'commune_id' => $this->lemba->id, 'quartier' => 'Righini', 'timing' => 'this_week',
            'budget_type' => 'amount', 'budget_amount' => 50,
        ])->assertSessionHasNoErrors();

        return ServiceRequest::latest('id')->firstOrFail();
    }

    private function offer(User $artisan, ServiceRequest $need): NeedProposal
    {
        $this->actingAs($artisan)->post(route('user.artisan.opportunities.propose', $need), [
            'type' => 'price', 'price' => 120, 'intervention_date' => now()->addDays(3)->toDateString(),
        ])->assertSessionHasNoErrors();

        return NeedProposal::where('artisan_id', $artisan->id)->latest('id')->firstOrFail();
    }

    private function editPayload(array $overrides = []): array
    {
        return array_merge([
            'precision' => 'Peinture salon et couloir', 'description' => 'Salon de 20 m2 et couloir de 6 m2, deux couches.',
            'quartier' => 'Matonge', 'timing' => 'date', 'preferred_date' => now()->addDays(10)->toDateString(),
            'budget_type' => 'range', 'budget_min' => 80, 'budget_max' => 150,
        ], $overrides);
    }

    public function test_need_expires_after_seven_days_and_pending_proposals_are_closed(): void
    {
        [$a, $b] = [$this->makeArtisan(), $this->makeArtisan()];
        $need = $this->publish();
        $proposal = $this->offer($a, $need);

        $this->travel(6)->days();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame('pending', $need->fresh()->status);

        $this->travel(1)->days();
        $this->travel(2)->hours();
        $this->artisan('proconnect:advance-need-waves')->assertExitCode(0);

        $need->refresh();
        $this->assertSame('expired', $need->status);
        $this->assertSame('Expirée', $need->status_label);
        $this->assertSame('rejected', $proposal->fresh()->status);
        // B n'avait pas répondu dans ses 2 h : son envoi était déjà expiré, plus aucun n'attend de réponse.
        $this->assertSame('expired', $need->dispatches()->where('artisan_id', $b->id)->value('status'));
        $this->assertSame(0, $need->dispatches()->where('status', 'sent')->count());
        $this->assertTrue(Notification::where('user_id', $this->client->id)->where('type', 'need_expired')->exists());
        $this->assertTrue(Notification::where('user_id', $a->id)->where('type', 'need_expired')->exists());

        // Plus de choix ni de réponse possible.
        $this->actingAs($this->client)->post(route('user.needs.proposals.accept', $proposal))->assertSessionHasErrors('proposal');
        $this->actingAs($b)->get(route('user.artisan.opportunities.show', $need))->assertRedirect(route('user.artisan.opportunities.index'));
        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))
            ->assertOk()->assertSee('sans suite')->assertSee('Relancer ma demande');
    }

    public function test_client_is_told_once_when_no_proposal_after_24_hours(): void
    {
        $this->makeArtisan();
        $need = $this->publish();

        $this->travel(23)->hours();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame(0, Notification::where('type', 'need_stale')->count());

        $this->travel(2)->hours();
        $this->artisan('proconnect:advance-need-waves');
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame(1, Notification::where('user_id', $this->client->id)->where('type', 'need_stale')->count());

        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))
            ->assertOk()->assertSee('Pas encore de proposition')->assertSee('Modifier budget ou date')->assertSee('Relancer');
    }

    public function test_no_stale_notice_when_a_proposal_exists(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $this->offer($a, $need);

        $this->travel(30)->hours();
        $this->artisan('proconnect:advance-need-waves');

        $this->assertSame(0, Notification::where('type', 'need_stale')->count());
        $this->actingAs($this->client)->get(route('user.service-requests.show', $need))->assertDontSee('Pas encore de proposition');
    }

    public function test_client_edits_budget_and_date_while_no_proposal(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();

        $this->actingAs($this->client)->get(route('user.needs.edit', $need))->assertOk()->assertSee('Modifier ma demande');
        $this->actingAs($this->client)->put(route('user.needs.update', $need), $this->editPayload())
            ->assertRedirect(route('user.service-requests.show', $need))->assertSessionHasNoErrors();

        $need->refresh();
        $this->assertSame('Peinture salon et couloir', $need->requested_service_name);
        $this->assertSame('Matonge', $need->quartier);
        $this->assertSame('date', $need->timing);
        $this->assertEquals(80, $need->budget_min);
        $this->assertEquals(150, $need->budget_max);
        $this->assertSame($this->category->id, $need->category_id); // non modifiable
        $this->assertSame($this->lemba->id, $need->commune_id);
        // L'artisan qui doit encore répondre est prévenu.
        $this->assertTrue(Notification::where('user_id', $a->id)->where('type', 'need_updated')->exists());
    }

    public function test_edit_is_blocked_once_a_proposal_is_received(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $this->offer($a, $need);

        $this->actingAs($this->client)->get(route('user.needs.edit', $need))->assertRedirect(route('user.service-requests.show', $need));
        $this->actingAs($this->client)->put(route('user.needs.update', $need), $this->editPayload())->assertSessionHasErrors('need');
        $this->assertEquals(50, $need->fresh()->budget_max);
    }

    public function test_only_the_owner_can_edit_or_relaunch(): void
    {
        $this->makeArtisan();
        $need = $this->publish();
        $stranger = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);

        $this->actingAs($stranger)->get(route('user.needs.edit', $need))->assertForbidden();
        $this->actingAs($stranger)->put(route('user.needs.update', $need), $this->editPayload())->assertForbidden();
        $this->actingAs($stranger)->post(route('user.needs.relaunch', $need))->assertForbidden();
        $this->assertEquals(50, $need->fresh()->budget_max);
    }

    public function test_relaunch_is_only_possible_when_the_need_is_stale(): void
    {
        $this->makeArtisan();
        $need = $this->publish();

        $this->actingAs($this->client)->post(route('user.needs.relaunch', $need))->assertSessionHas('error');
        $this->assertNull($need->fresh()->relaunched_at);
    }

    public function test_relaunching_a_stale_need_contacts_new_artisans_and_extends_it(): void
    {
        $first = array_map(fn () => $this->makeArtisan(), range(1, 5));
        $need = $this->publish();
        $this->travel(25)->hours();
        $newcomers = array_map(fn () => $this->makeArtisan(), range(1, 2));

        $this->actingAs($this->client)->post(route('user.needs.relaunch', $need))->assertSessionHas('success');

        $need->refresh();
        $this->assertNotNull($need->relaunched_at);
        $this->assertTrue($need->expires_at->between(now()->addDays(7)->subMinute(), now()->addDays(7)->addMinute()));
        $this->assertEqualsCanonicalizing(
            array_map(fn ($u) => $u->id, $newcomers),
            $need->dispatches()->where('wave', 2)->pluck('artisan_id')->all()
        );
        $this->assertCount(5, $first);
    }

    public function test_an_expired_need_can_be_relaunched(): void
    {
        $this->makeArtisan();
        $need = $this->publish();
        $this->travel(8)->days();
        $this->artisan('proconnect:advance-need-waves');
        $this->assertSame('expired', $need->fresh()->status);

        $this->actingAs($this->client)->post(route('user.needs.relaunch', $need))->assertSessionHas('success');

        $need->refresh();
        $this->assertSame('pending', $need->status);
        $this->assertTrue($need->expires_at->isFuture());
    }

    public function test_editing_a_stale_need_also_relaunches_it(): void
    {
        $this->makeArtisan();
        $need = $this->publish();
        $this->travel(25)->hours();
        $newcomer = $this->makeArtisan();

        $this->actingAs($this->client)->put(route('user.needs.update', $need), $this->editPayload())->assertSessionHasNoErrors();

        $this->assertNotNull($need->fresh()->relaunched_at);
        $this->assertTrue($need->dispatches()->where('artisan_id', $newcomer->id)->exists());
    }

    public function test_client_can_still_cancel_while_no_proposal_is_accepted(): void
    {
        $a = $this->makeArtisan();
        $need = $this->publish();
        $this->offer($a, $need);

        $this->actingAs($this->client)->post(route('user.service-requests.cancel', $need))->assertRedirect();
        $this->assertSame('cancelled', $need->fresh()->status);
        // La proposition en attente est close et son artisan prévenu.
        $this->assertSame('rejected', NeedProposal::where('artisan_id', $a->id)->value('status'));
        $this->assertTrue(Notification::where('user_id', $a->id)->where('type', 'need_cancelled')->exists());
    }
}
