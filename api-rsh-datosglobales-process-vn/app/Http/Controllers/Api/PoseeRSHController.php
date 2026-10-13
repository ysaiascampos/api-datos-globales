<?php

namespace App\Http\Controllers\Api;

use App\Classes\PoseeRSHClass;
use App\Constantes\Constante;
use App\Http\Controllers\Controller;
use App\Http\ResponseData\PoseeRSHData;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Models\MDSFApiResponse;

use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenApi\Attributes as OA;

class PoseeRSHController extends Controller
{
    #[OA\Get(
        path: '/api/v1/getposeersh/{runCiudadano}',
        summary: 'Verificar si una persona posee RSH',
        description: 'Indica si el ciudadano tiene Registro Social de Hogares vigente. Caso de certificación: un RUN con RSH retorna posee-rsh = true; uno sin RSH retorna false.',
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
                content: new OA\JsonContent(ref: '#/components/schemas/MDSFApiResponse')
            ),
            new OA\Response(response: 401, description: 'Token inválido o no enviado'),
            new OA\Response(response: 404, description: 'Error al consultar el servicio'),
        ]
    )]
    public function __invoke($runCiudadano)
    {
        $respuesta = new MDSFApiResponse();

        try {

            $classPoseeRSH = new PoseeRSHClass;
            $respuestaPoseeRSHData = $classPoseeRSH($runCiudadano);

            $datosPoseeRSHData = new PoseeRSHData($respuestaPoseeRSHData);
            $respuesta->data = $datosPoseeRSHData->data;
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener posee rsh', $e);
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1402;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
