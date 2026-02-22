<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateContactTest extends TestCase
{
    use RefreshDatabase;

    private function validContactData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'João Silva',
            'cpf' => '52998224725',
            'phone' => '11999998888',
            'cep' => '01001000',
            'street' => 'Praça da Sé',
            'number' => '100',
            'district' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
            'latitude' => -23.5505199,
            'longitude' => -46.6333094,
        ], $overrides);
    }

    public function test_unauthenticated_user_cannot_create_contact(): void
    {
        $response = $this->postJson('/api/contacts', $this->validContactData());

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_contact(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/contacts', $this->validContactData());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'cpf', 'phone', 'latitude', 'longitude'],
            ]);

        $this->assertDatabaseHas('contacts', ['cpf' => '52998224725']);
    }

    public function test_create_contact_requires_mandatory_fields(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/contacts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 'cpf', 'phone', 'cep', 'street', 'number', 'district', 'city', 'state',
            ]);
    }

    public function test_create_contact_rejects_invalid_cpf(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/contacts', $this->validContactData([
            'cpf' => '12345678901',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_create_contact_rejects_duplicate_cpf_for_same_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Contact::factory()->create([
            'user_id' => $user->id,
            'cpf' => '52998224725',
        ]);

        $response = $this->postJson('/api/contacts', $this->validContactData([
            'cpf' => '52998224725',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_same_cpf_allowed_for_different_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $user1->id,
            'cpf' => '52998224725',
        ]);

        Sanctum::actingAs($user2);

        $response = $this->postJson('/api/contacts', $this->validContactData([
            'cpf' => '52998224725',
        ]));

        $response->assertStatus(201);
    }

    public function test_complement_is_optional(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $data = $this->validContactData();
        unset($data['complement']);

        $response = $this->postJson('/api/contacts', $data);

        $response->assertStatus(201);
    }
}
