<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FavoriteController extends Controller
{
    #[OA\Get(
        path: "/api/favorites",
        summary: "Listar favoritos del usuario autenticado",
        security: [["bearerAuth" => []]],
        tags: ["Favoritos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de personajes favoritos"),
            new OA\Response(response: 401, description: "No autenticado")
        ]
    )]
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->with(['origin', 'location', 'episodes'])->get();
        return CharacterResource::collection($favorites);
    }

    #[OA\Post(
        path: "/api/favorites/{characterId}",
        summary: "Marcar personaje como favorito",
        security: [["bearerAuth" => []]],
        tags: ["Favoritos"],
        parameters: [
            new OA\Parameter(name: "characterId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Añadido a favoritos"),
            new OA\Response(response: 401, description: "No autenticado")
        ]
    )]
    public function store(Request $request, $characterId)
    {
        $character = Character::findOrFail($characterId);
        $request->user()->favorites()->syncWithoutDetaching([$character->id]);

        return response()->json(['message' => 'Personaje añadido a favoritos']);
    }

    #[OA\Delete(
        path: "/api/favorites/{characterId}",
        summary: "Eliminar personaje de favoritos",
        security: [["bearerAuth" => []]],
        tags: ["Favoritos"],
        parameters: [
            new OA\Parameter(name: "characterId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Eliminado de favoritos"),
            new OA\Response(response: 401, description: "No autenticado")
        ]
    )]
    public function destroy(Request $request, $characterId)
    {
        $character = Character::findOrFail($characterId);
        $request->user()->favorites()->detach($character->id);

        return response()->json(['message' => 'Personaje eliminado de favoritos']);
    }
}