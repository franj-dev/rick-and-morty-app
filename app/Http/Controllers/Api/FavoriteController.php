<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->with(['origin', 'location', 'episodes'])->get();
        return CharacterResource::collection($favorites);
    }

    public function store(Request $request, $characterId)
    {
        $character = Character::findOrFail($characterId);
        $request->user()->favorites()->syncWithoutDetaching([$character->id]);

        return response()->json(['message' => 'Personaje añadido a favoritos']);
    }

    public function destroy(Request $request, $characterId)
    {
        $character = Character::findOrFail($characterId);
        $request->user()->favorites()->detach($character->id);

        return response()->json(['message' => 'Personaje eliminado de favoritos']);
    }
}