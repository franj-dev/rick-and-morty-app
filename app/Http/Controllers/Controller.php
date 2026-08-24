<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Rick and Morty Sync API",
    description: "API REST de sincronización de datos de Rick & Morty y gestión de favoritos de usuarios."
)]
#[OA\Server(
    url: "http://localhost",
    description: "Servidor API local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
abstract class Controller
{
    //
}