<?php

namespace Tests\Feature;

use App\Models\Commune;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Étape 2 : corrections des points 6, 10, 11 et sécurisation du cycle de vie
 * des demandes (accepter / refuser / annuler).
 */
class RevisionStep2HardeningTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $type): User
    {
        return User::factory()->create([
            'user_type' => $type,
            'role'      => User::ROLE_USER,
            'status'    => User::STATUS_ACTIVE,
        ]);
    }

    private function makeRequest(User $client, ?User $artisan, string $status): ServiceRequest
    {
        return ServiceRequest::create([
            'user_id'                => $client->id,
            'artisan_id'             => $artisan?->id,
            'requested_service_name' => 'Peinture salon confidentielle',
            'description'            => 'Description privée du client',
            'status'                 => $status,
        ]);
    }

    public function test_opportunities_never_lists_other_clients_open_requests(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);
        $this->makeRequest($client, null, 'pending');

        $this->actingAs($artisan)->get(route('user.artisan.opportunities.index'))
            ->assertOk()
            ->assertDontSee('Peinture salon confidentielle')
            ->assertDontSee($client->name);
    }

    public function test_artisan_cannot_accept_or_reject_a_request_that_is_no_longer_pending(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);

        foreach (['cancelled', 'completed', 'rejected'] as $status) {
            $request = $this->makeRequest($client, $artisan, $status);

            $this->actingAs($artisan)->post(route('user.service-requests.accept', $request))->assertRedirect();
            $this->actingAs($artisan)->post(route('user.service-requests.reject', $request))->assertRedirect();

            $this->assertSame($status, $request->fresh()->status, "Le statut $status ne doit pas changer");
        }
    }

    public function test_other_artisan_cannot_accept_a_request(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);
        $intruder = $this->makeUser(User::TYPE_ARTISAN);
        $request = $this->makeRequest($client, $artisan, 'pending');

        $this->actingAs($intruder)->post(route('user.service-requests.accept', $request))->assertForbidden();
        $this->actingAs($intruder)->post(route('user.service-requests.cancel', $request))->assertForbidden();
        $this->assertSame('pending', $request->fresh()->status);
    }

    public function test_client_cannot_cancel_a_completed_or_in_progress_request(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);

        foreach (['completed', 'in_progress', 'awaiting_validation'] as $status) {
            $request = $this->makeRequest($client, $artisan, $status);
            $this->actingAs($client)->post(route('user.service-requests.cancel', $request))->assertRedirect();
            $this->assertSame($status, $request->fresh()->status);
        }
    }

    public function test_client_cancellation_is_tracked_and_notifies_the_artisan(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);
        $request = $this->makeRequest($client, $artisan, 'pending');

        $this->actingAs($client)->post(route('user.service-requests.cancel', $request))->assertRedirect();

        $fresh = $request->fresh();
        $this->assertSame('cancelled', $fresh->status);
        $this->assertSame($client->id, $fresh->cancelled_by);
        $this->assertNotNull($fresh->cancelled_at);
        $this->assertTrue(Notification::where('user_id', $artisan->id)->where('type', 'mission_cancelled')->exists());
        $this->assertFalse(Notification::where('user_id', $client->id)->where('type', 'mission_cancelled')->exists());
    }

    public function test_artisan_can_still_abandon_a_mission_in_progress(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);
        $request = $this->makeRequest($client, $artisan, 'in_progress');

        $this->actingAs($artisan)->post(route('user.service-requests.cancel', $request))->assertRedirect();

        $this->assertSame('cancelled', $request->fresh()->status);
        $this->assertSame($artisan->id, $request->fresh()->cancelled_by);
    }

    public function test_client_can_save_commune_and_quartier_but_not_an_unknown_commune(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $lemba = Commune::where('slug', 'lemba')->firstOrFail();

        $this->actingAs($client)->put(route('user.profile.update'), [
            'name' => $client->name, 'commune_id' => $lemba->id, 'quartier' => 'Righini',
        ])->assertSessionHasNoErrors();

        $this->assertSame($lemba->id, $client->fresh()->commune_id);
        $this->assertSame('Righini', $client->fresh()->quartier);

        $this->actingAs($client)->put(route('user.profile.update'), [
            'name' => $client->name, 'commune_id' => 99999,
        ])->assertSessionHasErrors('commune_id');
        $this->assertSame($lemba->id, $client->fresh()->commune_id);
    }

    public function test_client_cannot_attach_intervention_communes(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);

        $this->actingAs($client)->put(route('user.profile.update'), [
            'name' => $client->name, 'commune_ids_submitted' => 1,
            'commune_ids' => [Commune::value('id')],
        ]);

        $this->assertSame(0, $client->interventionCommunes()->count());
    }

    public function test_artisan_profile_syncs_intervention_communes(): void
    {
        $artisan = $this->makeUser(User::TYPE_ARTISAN);
        $ids = Commune::whereIn('slug', ['gombe', 'limete'])->pluck('id')->all();

        $this->actingAs($artisan)->put(route('user.profile.update'), [
            'name' => $artisan->name, 'commune_ids_submitted' => 1, 'commune_ids' => $ids,
        ])->assertSessionHasNoErrors();

        $this->assertEqualsCanonicalizing($ids, $artisan->interventionCommunes()->pluck('communes.id')->all());
        $this->assertSame('Gombe, Limete', $artisan->fresh()->intervention_zone);

        // Tout décocher vide bien la liste.
        $this->actingAs($artisan)->put(route('user.profile.update'), [
            'name' => $artisan->name, 'commune_ids_submitted' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertSame(0, $artisan->interventionCommunes()->count());
        $this->assertNull($artisan->fresh()->intervention_zone);
    }

    public function test_unread_messages_count_only_counts_messages_from_others(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);
        $artisan = $this->makeUser(User::TYPE_ARTISAN);
        $stranger = $this->makeUser(User::TYPE_CLIENT);
        $conversation = Conversation::findOrCreateBetween($client->id, $artisan->id);
        $other = Conversation::findOrCreateBetween($stranger->id, $artisan->id);

        foreach ([[$artisan, false], [$artisan, false], [$artisan, true], [$client, false]] as [$sender, $read]) {
            Message::create(['conversation_id' => $conversation->id, 'sender_id' => $sender->id, 'content' => 'x', 'message' => 'x', 'is_read' => $read]);
        }
        Message::create(['conversation_id' => $other->id, 'sender_id' => $artisan->id, 'content' => 'x', 'message' => 'x', 'is_read' => false]);

        $this->assertSame(2, $client->fresh()->unread_messages_count);
    }

    public function test_profile_page_shows_commune_fields_and_picker_only_for_artisans(): void
    {
        $this->actingAs($this->makeUser(User::TYPE_CLIENT))->get(route('user.profile'))
            ->assertOk()
            ->assertSee('name="commune_id"', false)
            ->assertSee('name="quartier"', false)
            ->assertDontSee('name="commune_ids[]"', false);

        $this->actingAs($this->makeUser(User::TYPE_ARTISAN))->get(route('user.profile'))
            ->assertOk()
            ->assertSee('name="commune_ids[]"', false)
            ->assertDontSee('name="intervention_zone"', false);
    }

    public function test_back_to_menu_button_is_removed(): void
    {
        $client = $this->makeUser(User::TYPE_CLIENT);

        $this->actingAs($client)->get(route('user.service-requests.index'))
            ->assertOk()
            ->assertDontSee('Retour au menu');
    }
}
