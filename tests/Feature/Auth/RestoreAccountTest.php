<?php

namespace Tests\Feature\Auth;

use App\Models\Contact;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_restore_account_within_seven_days(): void
    {
        $user = User::factory()->create([
            'email' => 'restore@example.com',
            'password' => 'password123',
        ]);

        $user->delete();

        $response = $this->postJson('/api/auth/restore-account', [
            'email' => 'restore@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'restore@example.com')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_cannot_restore_account_after_seven_days(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-02-01 10:00:00'));

        $user = User::factory()->create([
            'email' => 'expired@example.com',
            'password' => 'password123',
        ]);
        $user->delete();

        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-02-09 10:00:01'));

        $response = $this->postJson('/api/auth/restore-account', [
            'email' => 'expired@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(410)
            ->assertJsonPath('message', 'Prazo de restauracao expirado.');

        CarbonImmutable::setTestNow();
    }

    public function test_restores_contacts_together_with_account(): void
    {
        $user = User::factory()->create([
            'email' => 'with-contacts@example.com',
            'password' => 'password123',
        ]);

        $contact = Contact::factory()->create([
            'user_id' => $user->id,
        ]);

        $contact->delete();
        $user->delete();

        $response = $this->postJson('/api/auth/restore-account', [
            'email' => 'with-contacts@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'deleted_at' => null,
        ]);
    }
}
