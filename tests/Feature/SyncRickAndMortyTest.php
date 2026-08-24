<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncRickAndMortyTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_command_fetches_and_stores_characters_correctly(): void
    {
        Http::fake([
            'https://rickandmortyapi.com/api/character*' => Http::response([
                'info' => ['pages' => 1, 'next' => null],
                'results' => [
                    [
                        'id' => 1,
                        'name' => 'Rick Sanchez',
                        'status' => 'Alive',
                        'species' => 'Human',
                        'type' => '',
                        'gender' => 'Male',
                        'image' => 'https://rickandmortyapi.com/api/character/avatar/1.jpeg',
                        'origin' => ['name' => 'Earth (C-137)', 'url' => 'https://rickandmortyapi.com/api/location/1'],
                        'location' => ['name' => 'Citadel of Ricks', 'url' => 'https://rickandmortyapi.com/api/location/2'],
                        'episode' => ['https://rickandmortyapi.com/api/episode/1']
                    ]
                ]
            ], 200),
        ]);

        $this->artisan('rickandmorty:sync --pages=1')
             ->assertExitCode(0);

        $this->assertDatabaseHas('characters', [
            'external_id' => 1,
            'name' => 'Rick Sanchez',
            'status' => 'Alive',
            'species' => 'Human',
        ]);

        $this->assertDatabaseHas('locations', [
            'external_id' => 1,
            'name' => 'Earth (C-137)',
        ]);

        $this->assertDatabaseHas('episodes', [
            'external_id' => 1,
        ]);
    }
}