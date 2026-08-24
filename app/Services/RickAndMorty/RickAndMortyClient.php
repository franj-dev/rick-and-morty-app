<?php

namespace App\Services\RickAndMorty;

use Illuminate\Support\Facades\Http;

class RickAndMortyClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.rickandmorty.base_url', 'https://rickandmortyapi.com/api');
    }

    public function fetchPage(string $endpoint, int $page = 1): array
    {
        $response = Http::timeout(10)
            ->retry(3, 100)
            ->get("{$this->baseUrl}/{$endpoint}", ['page' => $page]);

        if ($response->failed()) {
            throw new \Exception("Error al consultar la API externa: {$response->status()}");
        }

        return $response->json();
    }
}