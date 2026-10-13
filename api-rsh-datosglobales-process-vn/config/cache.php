<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    'default' => env('CACHE_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'connection' => env('DB_CACHE_CONNECTION'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, and DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

    'ttl' => [
        'short'  => env('CACHE_TTL_SHORT', 60),    // 1 min
        'medium' => env('CACHE_TTL_MEDIUM', 300),  // 5 min
        'long'   => env('CACHE_TTL_LONG', 1800),   // 30 min
        'extra_long'   => env('CACHE_TTL_EXTRA_LONG', 7200),   // 120 min
    ],
    'features' => [

        'solicitudes'                => env('SOLICITUDES_CACHE_ACTIVAR', true),
        'alertas'                   => env('ALERTAS_CACHE_ACTIVAR', true),
        'persona'                   => env('PERSONA_CACHE_ACTIVAR', true),
        'posee_rsh'                 => env('POSEE_RSH_CACHE_ACTIVAR', true),
        'grupo_familiar_export'     => env('GRUPO_FAMILIAR_EXPORT_CACHE_ACTIVAR', true),
        'datos_contacto'            => env('DATOS_CONTACTO_CACHE_ACTIVAR', true),
        'institucionalizado'        => env('INSTITUCIONALIZADO_CACHE_ACTIVAR', true),
        'persona_export'            => env('PERSONA_EXPORT_CACHE_ACTIVAR', true),
        'grupo_familiar'            => env('GRUPO_FAMILIAR_CACHE_ACTIVAR', true),
        'grupo_familiar_vigente'    => env('GRUPO_FAMILIAR_VIGENTE_RUN_CACHE_ACTIVAR', true),
        'solicitud_aprobada'        => env('SOLICITUD_APROBADA_INGRESO_DOMICILIO_CACHE_ACTIVAR', true),
        'solicitudes_tramitables'   => env('SOLICITUDES_TRAMITABLES_CACHE_ACTIVAR', true),
        'solicitud'                 => env('SOLICITUD_CACHE_ACTIVAR', true),
        'comuna_gf_export'          => env('COMUNA_GF_EXPORT_CACHE_ACTIVAR', true),
        'comuna_ine'                => env('COMUNA_INE_CACHE_ACTIVAR', true),
        'persona_ficha'             => env('PERSONA_FICHA_CACHE_ACTIVAR', true),
        'persona_bnf'               => env('PERSONA_BNF_GF_CACHE_ACTIVAR', true),
        'region_comuna_ine'         => env('REGION_COMUNA_INE_CACHE_ACTIVAR', true),
        'situacion_basica'          => env('SITUACION_BASICA_CACHE_ACTIVAR', true),
        'validad_tercero'           => env('VALIDAD_SOLICITUD_TERCERO_CACHE_ACTIVAR', true),
        'runificador'               => env('RUNIFICADOR_WS_CACHE_ACTIVAR', true),
        'tramo'                     => env('TRAMO_CACHE_ACTIVAR', true),
        'mensaje_tramo'             => env('MENSAJE_TRAMO_CACHE_ACTIVAR', true),
        'tramo_quintal_lista'       => env('TRAMO_QUINTAL_LISTA_CACHE_ACTIVAR', true),
        'tipo_solicitud'            => env('TIPO_SOLICITUD_CACHE_ACTIVAR', true),
        'ultima_fecha_cierre'       => env('ULTIMA_FECHA_CIERRE_CACHE_ACTIVAR', true),
        'runificador_rest'       => env('RUNIFICADOR_REST_CACHE_ACTIVAR', true),

    ],
];
