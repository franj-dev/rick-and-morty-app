<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'name' => $this->name,
            'status' => $this->status,
            'species' => $this->species,
            'type' => $this->type,
            'gender' => $this->gender,
            'image' => $this->image,
            'origin' => $this->whenLoaded('origin', fn () => [
                'id' => $this->origin->id,
                'name' => $this->origin->name,
                'dimension' => $this->origin->dimension,
            ]),
            'location' => $this->whenLoaded('location', fn () => [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'dimension' => $this->location->dimension,
            ]),
            'episodes' => $this->whenLoaded('episodes', fn () => 
                $this->episodes->map(fn ($ep) => [
                    'id' => $ep->id,
                    'name' => $ep->name,
                    'episode_code' => $ep->episode_code,
                ])
            ),
        ];
    }
}