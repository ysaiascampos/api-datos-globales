<?php

namespace App\Http\Controllers\Api;

use App\Classes\ConfirmarDatosRSHClass;
use App\Constantes\Constante;
use App\Http\ResponseData\ConfirmarDatosRSHData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;


class ConfirmarDatosRSHController extends Controller
{
    #[OA\Get(
        path: '/api/v1/getconfirmardatosrsh/{runCiudadano}',
        summary: 'Confirmar si datos de contacto están actualizados',
        description: 'Indica si el teléfono y email de una persona fueron confirmados o actualizados dentro de los últimos 12 meses. Caso de certificación: estado = true significa que el dato está vigente.',
        security: [['ApiToken' => []]],
        tags: ['Personas'],
        parameters: [
            new OA\PathParameter(
                name: 'runCiudadano',
                required: true,
                description: 'RUN del ciudadano, sin puntos ni dígito verificador',
                schema: new OA\Schema(type: 'integer'),
                example: 12345678
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Consulta exitosa',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 1000),
                        new OA\Property(property: 'message', type: 'string', example: ''),
                        new OA\Property(property: 'time', type: 'string', example: '2026-09-15 14:00:00.000000'),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'telefono', type: 'object', properties: [
                                new OA\Property(property: 'estado', type: 'boolean', example: true, description: 'true si el teléfono fue actualizado/confirmado en los últimos 12 meses'),
                                new OA\Property(property: 'fecha-actualizacion', type: 'string', example: '15/09/2026', description: 'Fecha de la última actualización o confirmación'),
                            ]),
                            new OA\Property(property: 'email', type: 'object', properties: [
                                new OA\Property(property: 'estado', type: 'boolean', example: true, description: 'true si el email fue actualizado/confirmado en los últimos 12 meses'),
                                new OA\Property(property: 'fecha-actualizacion', type: 'string', example: '15/09/2026', description: 'Fecha de la última actualización o confirmación'),
                            ]),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token inválido o no enviado'),
            new OA\Response(
                response: 404,
                description: 'No se encontraron datos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 1402),
                        new OA\Property(property: 'message', type: 'string', example: 'Problemas al obtener datos de Confirmar RSH'),
                        new OA\Property(property: 'time', type: 'string', example: '2026-09-15 14:00:00.000000'),
                        new OA\Property(property: 'data', type: 'string', example: null),
                    ]
                )
            ),
        ]
    )]
    public function __invoke($runCiudadano = null)
    {
        $respuesta = new MDSFApiResponse();
        try {

            $classConfirmarDatosRSH = new ConfirmarDatosRSHClass;
            $respuestaConfirmarDatosRSH = $classConfirmarDatosRSH($runCiudadano);
            if($respuestaConfirmarDatosRSH){
                $confirmarDatosRSHData = new ConfirmarDatosRSHData($respuestaConfirmarDatosRSH);
                $respuesta->data = $confirmarDatosRSHData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->message = 'Problemas al obtener datos de Confirmar RSH';
                $respuesta->data = null;
            }
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de Confirmar RSH', $e);
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1402;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
