<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'joao@example.com',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'joao@example.com']);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'joao@example.com',
            'token' => $token,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertTrue(Hash::check('new-password123', $user->password));
    }

    public function test_reset_fails_with_invalid_token(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'joao@example.com',
            'token' => 'invalid-token',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertStatus(422);
    }
}
