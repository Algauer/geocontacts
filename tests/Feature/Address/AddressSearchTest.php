<?php

namespace Tests\Feature\Address;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_search_address(): void
    {
        $response = $this->getJson('/api/address/search?uf=SP&city=Sao%20Paulo&street=Praca%20da%20Se');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_search_address_through_viacep_proxy(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'https://viacep.com.br/*' => Http::response([
                [
                    'cep' => '01001-000',
                    'logradouro' => 'Praca da Se',
                    'bairro' => 'Se',
                    'localidade' => 'Sao Paulo',
                    'uf' => 'SP',
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/address/search?uf=SP&city=Sao%20Paulo&street=Praca%20da%20Se');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.cep', '01001-000')
            ->assertJsonPath('data.0.localidade', 'Sao Paulo')
            ->assertJsonPath('data.0.uf', 'SP');
    }

    public function test_returns_bad_gateway_when_viacep_is_unavailable(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'https://viacep.com.br/*' => Http::response([], 500),
        ]);

        $response = $this->getJson('/api/address/search?uf=SP&city=Sao%20Paulo&street=Praca%20da%20Se');

        $response->assertStatus(502)
            ->assertJsonPath('message', 'ViaCEP indisponivel no momento.');
    }
}
