<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commune;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Étape 3 : "Décrire mon besoin" (publication d'une demande par un client).
 */
class PublishNeedTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private ServiceType $serviceType;
    private Commune $commune;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->category = Category::create(['name' => 'Peinture', 'slug' => 'peinture']);
        $this->serviceType = ServiceType::create(['category_id' => $this->category->id, 'title' => 'Peinture intérieure']);
        $this->commune = Commune::where('slug', 'lemba')->firstOrFail();
    }

    private function user(string $type, array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'user_type' => $type, 'role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE,
        ], $attrs));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category_id'     => $this->category->id,
            'service_type_id' => $this->serviceType->id,
            'precision'       => 'Repeindre mon salon',
            'description'     => 'Salon de 20 m2, murs et plafond, peinture déjà achetée.',
            'commune_id'      => $this->commune->id,
            'quartier'        => 'Righini',
            'timing'          => 'this_week',
            'budget_type'     => 'range',
            'budget_min'      => 50,
            'budget_max'      => 120,
        ], $overrides);
    }

    public function test_client_sees_the_form_prefilled_from_profile(): void
    {
        $client = $this->user(User::TYPE_CLIENT, ['commune_id' => $this->commune->id, 'quartier' => 'Righini']);

        $this->actingAs($client)->get(route('user.needs.create', ['category' => $this->category->id]))
            ->assertOk()
            ->assertSee('Publier ma demande')
            ->assertSee('\u0022communeId\u0022:' . $this->commune->id, false)
            ->assertSee('\u0022categoryId\u0022:' . $this->category->id, false)
            ->assertSee('Righini');
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get(route('user.needs.create'))->assertRedirect(route('login'));
        $this->post(route('user.needs.store'), $this->payload())->assertRedirect(route('login'));
        $this->assertSame(0, ServiceRequest::count());
    }

    public function test_artisans_cannot_publish_a_need(): void
    {
        $artisan = $this->user(User::TYPE_ARTISAN);

        $this->actingAs($artisan)->get(route('user.needs.create'))->assertRedirect(route('user.dashboard'));
        $this->actingAs($artisan)->post(route('user.needs.store'), $this->payload())->assertRedirect(route('user.dashboard'));
        $this->assertSame(0, ServiceRequest::count());
    }

    public function test_client_publishes_a_need_with_photos(): void
    {
        $client = $this->user(User::TYPE_CLIENT);

        $response = $this->actingAs($client)->post(route('user.needs.store'), $this->payload([
            'photos' => [UploadedFile::fake()->image('mur.jpg', 800, 600), UploadedFile::fake()->image('plafond.png')],
        ]));

        $need = ServiceRequest::firstOrFail();
        $response->assertRedirect(route('user.service-requests.show', $need));

        $this->assertSame(ServiceRequest::MODE_OPEN, $need->mode);
        $this->assertSame('pending', $need->status);
        $this->assertNull($need->artisan_id);
        $this->assertNull($need->service_id);
        $this->assertSame($client->id, $need->user_id);
        $this->assertSame('Repeindre mon salon', $need->requested_service_name);
        $this->assertSame($this->commune->id, $need->commune_id);
        $this->assertSame('high', $need->urgency);
        $this->assertEquals(50, $need->budget_min);
        $this->assertEquals(120, $need->budget_max);
        $this->assertTrue($need->expires_at->between(now()->addDays(6), now()->addDays(8)));
        $this->assertCount(2, $need->photos);
        foreach ($need->photos as $path) {
            Storage::disk('public')->assertExists($path);
            $this->assertStringStartsWith('service-requests/' . $need->id . '/', $path);
            $this->assertStringNotContainsString('mur', $path); // nom aléatoire, pas celui du client
        }

        // Le profil sans commune est pré-rempli pour la prochaine fois.
        $this->assertSame($this->commune->id, $client->fresh()->commune_id);

        $this->actingAs($client)->get(route('user.service-requests.show', $need))
            ->assertOk()
            ->assertSee("Recherche d'artisans en cours", false)
            ->assertSee('Righini, Lemba')
            ->assertSee('$50 - $120');
    }

    public function test_service_type_must_belong_to_the_chosen_category(): void
    {
        $client = $this->user(User::TYPE_CLIENT);
        $otherCategory = Category::create(['name' => 'Plomberie', 'slug' => 'plomberie']);
        $foreignType = ServiceType::create(['category_id' => $otherCategory->id, 'title' => 'Fuite']);

        $this->actingAs($client)->post(route('user.needs.store'), $this->payload(['service_type_id' => $foreignType->id]))
            ->assertSessionHasErrors('service_type_id');
        $this->assertSame(0, ServiceRequest::count());
    }

    public function test_invalid_inputs_are_rejected(): void
    {
        $client = $this->user(User::TYPE_CLIENT);
        $cases = [
            'description'    => ['description' => 'trop court'],
            'commune_id'     => ['commune_id' => 99999],
            'timing'         => ['timing' => 'hier'],
            'preferred_date' => ['timing' => 'date', 'preferred_date' => now()->subDay()->toDateString()],
            'budget_max'     => ['budget_min' => 200, 'budget_max' => 100],
            'budget_amount'  => ['budget_type' => 'amount', 'budget_amount' => null],
            'photos'         => ['photos' => array_fill(0, 6, UploadedFile::fake()->image('p.jpg'))],
            'photos.0'       => ['photos' => [UploadedFile::fake()->create('virus.php', 10, 'application/x-php')]],
        ];

        foreach ($cases as $field => $override) {
            $this->actingAs($client)->post(route('user.needs.store'), $this->payload($override))
                ->assertSessionHasErrors($field);
        }
        $this->assertSame(0, ServiceRequest::count());
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_budget_none_and_urgent_timing(): void
    {
        $client = $this->user(User::TYPE_CLIENT);

        $this->actingAs($client)->post(route('user.needs.store'), $this->payload([
            'budget_type' => 'none', 'budget_min' => 999, 'budget_max' => 9999, 'timing' => 'urgent',
        ]))->assertSessionHasNoErrors();

        $need = ServiceRequest::firstOrFail();
        $this->assertNull($need->budget_min);
        $this->assertNull($need->budget_max);
        $this->assertSame('urgent', $need->urgency);
        $this->assertSame("Pas d'idée du prix", $need->budget_range);
    }

    public function test_client_cannot_have_more_than_five_open_needs(): void
    {
        $client = $this->user(User::TYPE_CLIENT);
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($client)->post(route('user.needs.store'), $this->payload())->assertSessionHasNoErrors();
        }

        $this->actingAs($client)->post(route('user.needs.store'), $this->payload())->assertSessionHas('error');
        $this->assertSame(5, ServiceRequest::count());
    }

    public function test_another_user_cannot_view_a_published_need(): void
    {
        $client = $this->user(User::TYPE_CLIENT);
        $this->actingAs($client)->post(route('user.needs.store'), $this->payload());
        $need = ServiceRequest::firstOrFail();

        $this->actingAs($this->user(User::TYPE_ARTISAN))->get(route('user.service-requests.show', $need))->assertForbidden();
        $this->actingAs($this->user(User::TYPE_CLIENT))->get(route('user.service-requests.show', $need))->assertForbidden();
    }

    public function test_client_can_cancel_a_published_need(): void
    {
        $client = $this->user(User::TYPE_CLIENT);
        $this->actingAs($client)->post(route('user.needs.store'), $this->payload());
        $need = ServiceRequest::firstOrFail();

        $this->actingAs($client)->post(route('user.service-requests.cancel', $need))->assertRedirect();
        $this->assertSame('cancelled', $need->fresh()->status);
    }

    public function test_entry_points_link_to_the_form(): void
    {
        $client = $this->user(User::TYPE_CLIENT);
        $url = route('user.needs.create');

        $this->actingAs($client)->get(route('user.dashboard'))->assertOk()->assertSee($url, false);
        $this->actingAs($client)->get(route('public.categories.service-types', $this->category))
            ->assertOk()->assertSee(route('user.needs.create', ['category' => $this->category->id]), false);

        // L'artisan ne voit pas l'appel à publier un besoin sur une catégorie vide.
        $this->actingAs($this->user(User::TYPE_ARTISAN))->get(route('public.categories.service-types', $this->category))
            ->assertOk()->assertDontSee($url, false);
    }

    public function test_register_page_has_a_password_toggle_on_both_fields(): void
    {
        $html = $this->get('/register?type=client')->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, 'class="toggle-password'));
        $this->assertSame(1, substr_count($html, "closest('.toggle-password')"));
    }
}
