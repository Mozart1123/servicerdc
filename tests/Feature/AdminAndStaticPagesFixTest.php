<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Erreurs 500 relevées dans les logs de production (2026-10-11 / 2026-10-13). */
class AdminAndStaticPagesFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders(): void
    {
        $this->get(route('about'))->assertOk()->assertSee('Comment ça marche');
    }

    public function test_admin_user_pages_call_the_right_endpoints(): void
    {
        $admin = User::factory()->create(['user_type' => User::TYPE_CLIENT, 'role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);

        // Une adresse non numérique donne 404, plus une erreur 500.
        $this->actingAs($admin)->get('/admin/users/flagged')->assertNotFound();

        $this->actingAs($admin)->get(route('admin.users-mgmt.flagged'))->assertOk()
            ->assertSee(route('admin.users-mgmt.flagged') . '?page=', false)
            ->assertDontSee('/admin/users/flagged?page=', false);
        $this->actingAs($admin)->getJson(route('admin.users-mgmt.flagged'))->assertOk();

        $this->actingAs($admin)->get(route('admin.users-mgmt.docs'))->assertOk()
            ->assertDontSee('/admin/users/documents', false);
    }
}
