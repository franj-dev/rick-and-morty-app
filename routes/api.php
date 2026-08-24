<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CharacterController;
use App\Http\Controllers\Api\FavoriteController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas de Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas Públicas de Personajes
Route::get('/characters', [CharacterController::class, 'index']);
Route::get('/characters/{character}', [CharacterController::class, 'show']);

// Rutas Protegidas (Favoritos)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{characterId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{characterId}', [FavoriteController::class, 'destroy']);
});