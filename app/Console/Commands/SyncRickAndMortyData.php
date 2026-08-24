<?php

namespace App\Console\Commands;

use App\Models\Character;
use App\Models\Episode;
use App\Models\Location;
use App\Services\RickAndMorty\DTOs\CharacterDTO;
use App\Services\RickAndMorty\RickAndMortyClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncRickAndMortyData extends Command
{
    protected $signature = 'rickandmorty:sync {--pages=1 : Cantidad de páginas a sincronizar}';
    protected $description = 'Sincroniza datos de la API de Rick and Morty de forma idempotente';

    public function handle(RickAndMortyClient $client): int
    {
        $pages = (int) $this->option('pages');
        $this->info("Iniciando sincronización de {$pages} página(s)...");

        for ($page = 1; $page <= $pages; $page++) {
            $data = $client->fetchPage('character', $page);
            $results = $data['results'] ?? [];

            DB::transaction(function () use ($results) {
                foreach ($results as $rawCharacter) {
                    $dto = CharacterDTO::fromApiArray($rawCharacter);

                    // 1. Origen y Ubicación actual
                    $origin = $dto->originExternalId ? Location::firstOrCreate(
                        ['external_id' => $dto->originExternalId],
                        ['name' => $rawCharacter['origin']['name']]
                    ) : null;

                    $location = $dto->locationExternalId ? Location::firstOrCreate(
                        ['external_id' => $dto->locationExternalId],
                        ['name' => $rawCharacter['location']['name']]
                    ) : null;

                    // 2. Personaje
                    $character = Character::updateOrCreate(
                        ['external_id' => $dto->externalId],
                        [
                            'name' => $dto->name,
                            'status' => $dto->status,
                            'species' => $dto->species,
                            'type' => $dto->type,
                            'gender' => $dto->gender,
                            'image' => $dto->image,
                            'origin_id' => $origin?->id,
                            'location_id' => $location?->id,
                        ]
                    );

                    // 3. Episodios
                    $episodeIds = [];
                    foreach ($dto->episodeExternalIds as $epExtId) {
                        $ep = Episode::firstOrCreate(
                            ['external_id' => $epExtId],
                            ['name' => "Episode {$epExtId}", 'episode_code' => "EP-{$epExtId}"]
                        );
                        $episodeIds[] = $ep->id;
                    }

                    $character->episodes()->sync($episodeIds);
                }
            });

            $this->info("Página {$page} sincronizada.");
        }

        $this->info("¡Sincronización completada exitosamente!");
        return Command::SUCCESS;
    }
}