<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API Datos Globales',
    version: '1.0.0',
    description: "API de consulta de datos globales de ciudadanos.\n\n**Contrato de respuesta:** todas las respuestas tienen el formato `{ code, message, time, data }`.\n- `code` 1000 → operación exitosa; el resultado está en `data`.\n- `code` 13xx / 14xx → error controlado; `message` describe la causa.\n\n**Criterio de certificación:** un endpoint está OK si retorna `code` 1000 con los datos esperados en `data`."
)]
#[OA\SecurityScheme(
    securityScheme: 'ApiToken',
    type: 'apiKey',
    in: 'header',
    name: 'Api-Token',
    description: 'Token de API entregado por el equipo de integración. Se envía en el header Api-Token.'
)]
class OpenApiSpec
{
}
