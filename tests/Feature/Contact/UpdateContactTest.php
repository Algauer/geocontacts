<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_own_contact(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $contact = Contact::factory()->create(['user_id' => $user->id]);

        Http::fake([
            'https://maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'results' => [
                    [
                        'geometry' => [
                            'location' => [
                                'lat' => -23.5505199,
                                'lng' => -46.6333094,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name' => 'Nome Atualizado',
            'cpf' => $contact->cpf,
            'phone' => '11999990000',
            'cep' => '01001000',
            'street' => 'Rua Nova',
            'number' => '200',
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Nome Atualizado');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Nome Atualizado',
        ]);
    }

    public function test_user_cannot_update_other_users_contact(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $contact = Contact::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name' => 'Hack',
            'cpf' => $contact->cpf,
            'phone' => '11999990000',
            'cep' => '01001000',
            'street' => 'Rua',
            'number' => '1',
            'district' => 'Centro',
            'city' => 'SP',
            'state' => 'SP',
        ]);

        $response->assertStatus(404);
    }
}
