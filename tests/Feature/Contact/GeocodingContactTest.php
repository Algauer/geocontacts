<?php

namespace Tests\Feature\Contact;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GeocodingContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_contact_fills_coordinates_using_geocoding_service(): void
    {
        Sanctum::actingAs(User::factory()->create());

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

        $response = $this->postJson('/api/contacts', [
            'name' => 'Joao Silva',
            'cpf' => '52998224725',
            'phone' => '11999998888',
            'cep' => '01001000',
            'street' => 'Praca da Se',
            'number' => '100',
            'district' => 'Se',
            'city' => 'Sao Paulo',
            'state' => 'SP',
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('data.latitude'));
        $this->assertNotNull($response->json('data.longitude'));

        $this->assertDatabaseHas('contacts', [
            'cpf' => '52998224725',
            'latitude' => -23.5505199,
            'longitude' => -46.6333094,
        ]);
    }
}
