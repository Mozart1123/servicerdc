<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientRevision11PointsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Point 1: Choose profile screen & pre-selection
     */
    public function test_point_1_choose_profile_screen(): void
    {
        $response = $this->get(route('register.choose'));
        $response->assertStatus(200);
        $response->assertSee('Trouvez un artisan de confiance près de chez vous.');
        $response->assertSee('Recevez de nouveaux clients dans votre quartier.');

        // /register without type redirects to /register/choose
        $regRedirect = $this->get('/register');
        $regRedirect->assertRedirect(route('register.choose'));

        // /register?type=client preselects client
        $regClient = $this->get('/register?type=client');
        $regClient->assertStatus(200);
        $regClient->assertSee('value="client" class="peer sr-only" required', false);
    }

    /**
     * Point 4: User reviews page does not throw 500 error and displays empty message
     */
    public function test_point_4_user_reviews_page_loads_without_500(): void
    {
        $client = User::factory()->create([
            'user_type' => User::TYPE_CLIENT,
            'role'      => User::ROLE_USER,
            'status'    => User::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($client)->get(route('user.reviews.index'));
        $response->assertStatus(200);
        $response->assertSee('Tu n\'as pas encore laissé d\'avis.', false);
    }

    /**
     * Point 5 & 9: User rating accessors and response time badge
     */
    public function test_point_5_and_9_rating_and_response_time_accessors(): void
    {
        $artisan = User::factory()->create([
            'user_type' => User::TYPE_ARTISAN,
            'role'      => User::ROLE_USER,
            'status'    => User::STATUS_ACTIVE,
        ]);

        // Default: new artisan with 0 reviews
        $ratingStats = $artisan->rating_stats;
        $this->assertTrue($ratingStats['is_new']);
        $this->assertEquals('Nouveau', $ratingStats['badge']);

        // Default profession fallback
        $this->assertEquals('Artisan', $artisan->main_profession);

        // Response time badge (null when no history)
        $this->assertNull($artisan->response_time_badge);
    }

    /**
     * Point 10: Artisan availability toggle & update
     */
    public function test_point_10_artisan_availability_toggle_and_update(): void
    {
        $artisan = User::factory()->create([
            'user_type'    => User::TYPE_ARTISAN,
            'role'         => User::ROLE_USER,
            'is_available' => true,
        ]);

        // Toggle availability via AJAX
        $toggleRes = $this->actingAs($artisan)->postJson(route('user.artisan.availability.toggle'));
        $toggleRes->assertStatus(200);
        $toggleRes->assertJson(['status' => 'ok', 'is_available' => false]);
        $this->assertFalse($artisan->fresh()->is_available);

        // Update schedule
        $updateRes = $this->actingAs($artisan)->post(route('user.artisan.availability.update'), [
            'is_available'        => 1,
            'working_hours_start' => '07:30',
            'working_hours_end'   => '19:00',
            'availability_days'   => ['lun', 'mar', 'mer', 'jeu', 'ven'],
            'intervention_zone'   => 'Gombe, Kintambo',
        ]);
        $updateRes->assertRedirect();

        $fresh = $artisan->fresh();
        $this->assertTrue($fresh->is_available);
        $this->assertEquals('07:30', $fresh->working_hours_start);
        $this->assertEquals('19:00', $fresh->working_hours_end);
        $this->assertEquals('Gombe, Kintambo', $fresh->intervention_zone);
    }

    /**
     * Point 11: Artisan opportunities page returns 200
     */
    public function test_point_11_artisan_opportunities_page(): void
    {
        $artisan = User::factory()->create([
            'user_type' => User::TYPE_ARTISAN,
            'role'      => User::ROLE_USER,
        ]);

        $response = $this->actingAs($artisan)->get(route('user.artisan.opportunities.index'));
        $response->assertStatus(200);
        $response->assertSee('Opportunités');
        $response->assertSee('Aucune opportunité pour le moment');
    }
}
