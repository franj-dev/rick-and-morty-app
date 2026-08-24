<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RickAndMortyApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.rick_and_morty.base_url', 'https://rickandmortyapi.com/api');
    }

    /**
     * Obtener lista de personajes (con soporte para paginación y filtros).
     */
    public function getCharacters(array $filters = []): array
    {
        $response = Http::get("{$this->baseUrl}/character", $filters);

        if ($response->failed()) {
            return [
                'info' => ['count' => 0, 'pages' => 0, 'next' => null, 'prev' => null],
                'results' => []
            ];
        }

        return $response->json();
    }

    /**
     * Obtener un personaje individual por su ID externo.
     */
    public function getCharacterById(int $id): ?array
    {
        $response = Http::get("{$this->baseUrl}/character/{$id}");

        return $response->successful() ? $response->json() : null;
    }

    /**
     * Obtener una ubicación individual por su ID externo.
     */
    public function getLocationById(int $id): ?array
    {
        $response = Http::get("{$this->baseUrl}/location/{$id}");

        return $response->successful() ? $response->json() : null;
    }

    /**
     * Obtener un episodio individual por su ID externo.
     */
    public function getEpisodeById(int $id): ?array
    {
        $response = Http::get("{$this->baseUrl}/episode/{$id}");

        return $response->successful() ? $response->json() : null;
    }
}