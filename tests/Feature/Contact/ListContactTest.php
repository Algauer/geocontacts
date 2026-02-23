<?php

namespace Tests\Feature\Contact;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_list_contacts(): void
    {
        $response = $this->getJson('/api/contacts');

        $response->assertStatus(401);
    }

    public function test_list_contacts_ordered_by_name_asc_by_default(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Contact::factory()->create(['user_id' => $user->id, 'name' => 'Carlos']);
        Contact::factory()->create(['user_id' => $user->id, 'name' => 'Ana']);
        Contact::factory()->create(['user_id' => $user->id, 'name' => 'Bruno']);

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200);

        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals(['Ana', 'Bruno', 'Carlos'], $names);
    }

    public function test_filter_contacts_by_name(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Contact::factory()->create(['user_id' => $user->id, 'name' => 'João Silva']);
        Contact::factory()->create(['user_id' => $user->id, 'name' => 'Maria Santos']);

        $response = $this->getJson('/api/contacts?search=João');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('João Silva', $data[0]['name']);
    }

    public function test_filter_contacts_by_cpf(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Contact::factory()->create(['user_id' => $user->id, 'cpf' => '52998224725']);
        Contact::factory()->create(['user_id' => $user->id, 'cpf' => '11144477735']);

        $response = $this->getJson('/api/contacts?search=529');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('52998224725', $data[0]['cpf']);
    }

    public function test_user_only_sees_own_contacts(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Contact::factory()->create(['user_id' => $user1->id, 'name' => 'Contato do User 1']);
        Contact::factory()->create(['user_id' => $user2->id, 'name' => 'Contato do User 2']);

        Sanctum::actingAs($user1);

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Contato do User 1', $data[0]['name']);
    }

    public function test_list_supports_sorting_by_created_at_desc(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Contact::factory()->create([
            'user_id' => $user->id,
            'name' => 'Contato Antigo',
            'created_at' => now()->subDays(2),
        ]);
        Contact::factory()->create([
            'user_id' => $user->id,
            'name' => 'Contato Novo',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/contacts?sort_by=created_at&sort_dir=desc');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertEquals(['Contato Novo', 'Contato Antigo'], $names);
    }

    public function test_list_supports_dynamic_pagination_with_per_page_param(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Contact::factory()->count(6)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/contacts?per_page=5&page=1');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonCount(5, 'data');
    }
}
