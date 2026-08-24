<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_characters(): void
    {
        Character::factory()->create([
            'external_id' => 1,
            'name' => 'Rick Sanchez',
            'status' => 'Alive',
            'species' => 'Human',
            'gender' => 'Male',
        ]);

        $response = $this->getJson('/api/characters');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'external_id', 'name', 'status', 'species', 'gender', 'image']
                     ]
                 ])
                 ->assertJsonFragment(['name' => 'Rick Sanchez']);
    }

    public function test_authenticated_user_can_add_and_list_favorites(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'external_id' => 1,
            'name' => 'Morty Smith',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Añadir a favoritos
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson("/api/favorites/{$character->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Personaje añadido a favoritos']);

        // Listar favoritos
        $favResponse = $this->withHeader('Authorization', "Bearer $token")
                            ->getJson('/api/favorites');

        $favResponse->assertStatus(200)
                    ->assertJsonFragment(['name' => 'Morty Smith']);
    }
}