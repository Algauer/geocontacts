<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_own_contact(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.name', $contact->name);
    }

    public function test_user_cannot_see_other_users_contact(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $contact = Contact::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(404);
    }
}
