<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserVerificationGracefulFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_identity_verification_checks_do_not_crash_when_table_is_missing(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test.user@example.com',
            'password' => 'password',
            'role' => User::ROLE_USER,
            'user_type' => User::TYPE_CLIENT,
        ]);

        Schema::dropIfExists('identity_verifications');

        $this->assertFalse($user->isVerified());
    }
}
