<?php

namespace Tests\Feature\Auth;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_delete_account_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/me', [
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_can_delete_account_with_correct_password_and_soft_delete_related_data(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/me', [
            'password' => 'password123',
        ]);

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
