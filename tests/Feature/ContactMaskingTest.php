<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\NeedProposal;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Support\ContactMasker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Numéros masqués entre client et artisan tant qu'aucune proposition n'est acceptée.
 */
class ContactMaskingTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $artisan;
    private Category $category;
    private Commune $lemba;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['matching.dispatch_enabled' => true]);
        $this->category = Category::create(['name' => 'Peinture', 'slug' => 'peinture']);
        $this->lemba = Commune::where('slug', 'lemba')->firstOrFail();
        $this->client = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);
        $this->artisan = User::factory()->create([
            'user_type' => User::TYPE_ARTISAN, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE,
            'is_available' => true, 'availability_days' => ['lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim'],
            'working_hours_start' => '00:00', 'working_hours_end' => '23:59',
        ]);
        DB::table('services')->insert([
            'artisan_id' => $this->artisan->id, 'category_id' => $this->category->id, 'title' => 'Peinture',
            'provider_name' => $this->artisan->name, 'profession' => 'Peintre', 'city' => 'Kinshasa',
            'phone_number' => '0800000000', 'status' => 'active', 'pricing_type' => 'quote',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->artisan->interventionCommunes()->sync([$this->lemba->id]);
    }

    private function send(User $from, User $to, string $text): Message
    {
        $conversation = Conversation::findOrCreateBetween($from->id, $to->id);
        $this->actingAs($from)->post(route('user.messages.send', $conversation), ['content' => $text])->assertRedirect();

        return Message::latest('id')->firstOrFail();
    }

    public function test_phone_numbers_are_detected_without_touching_dates_and_prices(): void
    {
        foreach (['+243 81 234 5678', '0812345678', '081-234-5678', '099.123.45.67', '00243 97 000 1111', '(081) 234 56 78', 'wa.me/243812345678', '0 8 1 2 3 4 5 6 7 8'] as $phone) {
            $masked = ContactMasker::mask("Appelle-moi au {$phone} merci");
            $this->assertStringContainsString(ContactMasker::PLACEHOLDER, $masked, $phone);
            $this->assertFalse(ContactMasker::containsPhone($masked), $phone);
            $this->assertTrue(ContactMasker::containsPhone($phone), $phone);
        }
        foreach (['RDV le 13/10/2026 à 10h30', 'Prix 150 000 FC', '1 500 000', 'Salon de 20 m2', 'Facture 2026-10-13', 'Code 12345678'] as $safe) {
            $this->assertSame($safe, ContactMasker::mask($safe));
        }
    }

    public function test_chat_masks_numbers_until_a_proposal_is_accepted(): void
    {
        $toArtisan = $this->send($this->client, $this->artisan, 'Appelez-moi au 0812345678 svp');
        $this->assertSame('Appelez-moi au [numéro masqué] svp', $toArtisan->content);
        $this->assertStringNotContainsString('0812345678', (string) $toArtisan->message);

        $toClient = $this->send($this->artisan, $this->client, 'Mon WhatsApp : +243 99 123 4567');
        $this->assertSame('Mon WhatsApp : [numéro masqué]', $toClient->content);

        // Le client retient l'artisan : les coordonnées peuvent circuler.
        ServiceRequest::create(['user_id' => $this->client->id, 'artisan_id' => $this->artisan->id, 'status' => 'accepted', 'requested_service_name' => 'x', 'description' => 'x']);
        $after = $this->send($this->client, $this->artisan, 'Voici mon numéro : 0812345678');
        $this->assertSame('Voici mon numéro : 0812345678', $after->content);

        // Ancien message : reste masqué (le numéro n'a jamais été stocké).
        $this->assertSame('Appelez-moi au [numéro masqué] svp', $toArtisan->fresh()->content);
    }

    public function test_masking_comes_back_if_the_mission_is_cancelled(): void
    {
        ServiceRequest::create(['user_id' => $this->client->id, 'artisan_id' => $this->artisan->id, 'status' => 'cancelled', 'requested_service_name' => 'x', 'description' => 'x']);

        $this->assertSame('[numéro masqué]', $this->send($this->client, $this->artisan, '0812345678')->content);
    }

    public function test_other_conversations_are_not_affected(): void
    {
        $otherClient = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);
        $recruiter = User::factory()->create(['user_type' => User::TYPE_RECRUITER, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);

        $this->assertSame('0812345678', $this->send($this->client, $otherClient, '0812345678')->content);
        $this->assertSame('0812345678', $this->send($recruiter, $this->artisan, '0812345678')->content);
    }

    public function test_published_need_and_proposal_messages_hide_numbers(): void
    {
        $this->actingAs($this->client)->post(route('user.needs.store'), [
            'category_id' => $this->category->id, 'precision' => 'Peinture, appeler 0812345678',
            'description' => 'Repeindre le salon. Joignable au +243 81 234 5678 le soir.',
            'commune_id' => $this->lemba->id, 'quartier' => 'Righini 0991234567', 'timing' => 'this_week', 'budget_type' => 'none',
        ])->assertSessionHasNoErrors();
        $need = ServiceRequest::latest('id')->firstOrFail();

        $this->assertSame('Repeindre le salon. Joignable au [numéro masqué] le soir.', $need->description);
        $this->assertSame('Peinture, appeler [numéro masqué]', $need->requested_service_name);
        $this->assertSame('Righini [numéro masqué]', $need->quartier);
        $this->actingAs($this->artisan)->get(route('user.artisan.opportunities.show', $need))->assertOk()->assertDontSee('5678');

        $this->actingAs($this->artisan)->post(route('user.artisan.opportunities.propose', $need), [
            'type' => 'price', 'price' => 120, 'intervention_date' => now()->addDays(2)->toDateString(),
            'message' => 'Dispo demain, WhatsApp 099 765 4321',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Dispo demain, WhatsApp [numéro masqué]', NeedProposal::firstOrFail()->message);
    }

    public function test_direct_catalogue_request_hides_numbers_from_the_artisan(): void
    {
        $serviceId = DB::table('services')->where('artisan_id', $this->artisan->id)->value('id');

        $this->actingAs($this->client)->post(route('user.service-requests.store'), [
            'service_id' => $serviceId, 'requested_service_name' => 'Peinture',
            'description' => 'Salon à repeindre, appelez le 0812345678',
        ]);

        $request = ServiceRequest::latest('id')->firstOrFail();
        $this->assertSame('Salon à repeindre, appelez le [numéro masqué]', $request->description);
        $this->assertStringNotContainsString('0812345678', Message::latest('id')->value('content'));
    }

    public function test_chat_shows_the_masking_notice(): void
    {
        $conversation = Conversation::findOrCreateBetween($this->client->id, $this->artisan->id);

        $this->actingAs($this->client)->get(route('user.messages.index', ['id' => $conversation->id]))
            ->assertOk()->assertSee('les numéros de téléphone sont masqués', false);
    }
}
