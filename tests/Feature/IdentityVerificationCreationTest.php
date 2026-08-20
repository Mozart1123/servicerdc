<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentityVerificationCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_identity_verification_show_creates_record_without_document_fields(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'user_type' => User::TYPE_CLIENT,
        ]);

        $this->actingAs($user)
            ->get(route('user.identity-verification.show'))
            ->assertOk();

        $this->assertDatabaseHas('identity_verifications', [
            'user_id' => $user->id,
            'verification_status' => 'not_submitted',
        ]);
    }
}
