<?php

namespace App\Services\RickAndMorty\DTOs;

class CharacterDTO
{
    public function __construct(
        public readonly int $externalId,
        public readonly string $name,
        public readonly string $status,
        public readonly string $species,
        public readonly string $type,
        public readonly string $gender,
        public readonly string $image,
        public readonly ?int $originExternalId,
        public readonly ?int $locationExternalId,
        public readonly array $episodeExternalIds,
    ) {}

    public static function fromApiArray(array $data): self
    {
        $originId = isset($data['origin']['url']) && !empty($data['origin']['url'])
            ? (int) basename($data['origin']['url'])
            : null;

        $locationId = isset($data['location']['url']) && !empty($data['location']['url'])
            ? (int) basename($data['location']['url'])
            : null;

        $episodeIds = array_map(fn($url) => (int) basename($url), $data['episode'] ?? []);

        return new self(
            externalId: $data['id'],
            name: $data['name'],
            status: $data['status'] ?? 'unknown',
            species: $data['species'] ?? 'unknown',
            type: $data['type'] ?? '',
            gender: $data['gender'] ?? 'unknown',
            image: $data['image'] ?? '',
            originExternalId: $originId,
            locationExternalId: $locationId,
            episodeExternalIds: $episodeIds
        );
    }
}