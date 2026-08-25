# Rick and Morty Sync & Management API

API REST desarrollada en Laravel 11 para la sincronización idempotente de datos desde la API externa de Rick & Morty, persistencia relacional local, gestión de usuarios y administración de personajes favoritos.

## 🛠️ Requisitos del Entorno

- Docker Engine y Docker Compose (o Laravel Sail)
- Git

## 🚀 Instalación y Despliegue con Laravel Sail

Clonar el repositorio:
```bash
git clone https://github.com/franj-dev/rick-and-morty-app.git
cd rmapi <nombre-de-tu-carpeta>
```

Configurar el entorno:
```bash
cp .env.example .env
```

Iniciar los contenedores Docker:
```bash
./vendor/bin/sail up -d

O directamente con Docker Compose:
docker compose up -d
```

Instalar dependencias, generar clave y ejecutar migraciones:
```bash
docker compose exec laravelapp composer install
docker compose exec laravelapp php artisan key:generate
docker compose exec laravelapp php artisan migrate
```

Generar la documentación OpenAPI (Swagger):
```bash
docker compose exec laravelapp php artisan l5-swagger:generate
```

##  🔄 Comando de Sincronización

Para ejecutar la descarga e integración idempotente de personajes, ubicaciones y episodios desde la API remota hacia la base de datos local:

```bash
docker compose exec laravelapp php artisan rickandmorty:sync
```

Nota: Admite el parámetro opcional --pages=X para limitar el número de páginas sincronizadas durante pruebas o ejecuciones controladas.

## 📑 Documentación de la API (Swagger UI)

Una vez levantado el servidor, la documentación interactiva OpenAPI está disponible en:

👉 **[http://localhost/api/documentation](http://localhost/api/documentation)**

## 🧪 Pruebas Automatizadas

La suite de pruebas aísla las peticiones HTTP remotas mediante `Http::fake()` para garantizar ejecuciones deterministas sin dependencia del servicio en vivo:

```bash
docker compose exec laravelapp php artisan test
```

## 📐 Decisiones de Diseño y Arquitectura

### Cliente HTTP Desacoplado y DTOs:

- Consumo de la API externa encapsulado en un servicio cliente dedicado `RickAndMortyClient`, aislando la lógica de negocio y los controladores del proveedor externo.

- Mapeo mediante Data Transfer Objects (`DTOs`) para validar y estructurar la respuesta externa antes de persistirla.

### Idempotencia y Tolerancia a Fallos:

- Uso de `external_id` como clave de mapeo única para personajes, ubicaciones y episodios.

- Sincronización mediante `updateOrCreate` y actualización de relaciones N:M con `sync()`, evitando duplicidades ante ejecuciones repetidas.

### Modelo de Datos y Relaciones:

- `Character` implementa dos relaciones independientes hacia `Location` (`origin_id` y `location_id`).

- Relación N:M entre `Character` y `Episode` gestionada mediante tabla pivote.

### Autenticación y Economía de Dependencias:

- Gestión de usuarios y favoritos mediante Laravel Sanctum (tokens Bearer), priorizando herramientas nativas del framework frente a paquetes de terceros no esenciales o propias.