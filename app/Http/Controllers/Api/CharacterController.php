<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CharacterController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Character::with(['origin', 'location', 'episodes']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('species')) {
            $query->where('species', 'like', '%' . $request->query('species') . '%');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->query('gender'));
        }

        return CharacterResource::collection($query->paginate(15));
    }

    #[OA\Get(
        path: "/api/characters/{id}",
        summary: "Detalle de un personaje",
        tags: ["Personajes"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Información detallada del personaje"),
            new OA\Response(response: 404, description: "Personaje no encontrado")
        ]
    )]
    public function show(Character $character): CharacterResource
    {
        $character->load(['origin', 'location', 'episodes']);
        return new CharacterResource($character);
    }
}