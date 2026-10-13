<?php

namespace App\Http\Controllers\Api;

use App\Classes\BloqueosSolicitudCiudadanoClass;
use App\Constantes\Constante;
use App\Http\ResponseData\BloqueosSolicitudCiudadanoData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;


class BloqueosSolicitudCiudadanoController extends Controller
{
    #[OA\Post(
        path: '/api/v1/getbloqueossolicitudciudadano',
        summary: 'Consultar bloqueos de solicitud de un ciudadano',
        security: [['ApiToken' => []]],
        tags: ['Solicitudes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['run-ciudadano', 'run-usuario', 'origen-registro', 'tipo-solicitud', 'sub-tipo-solicitud'],
                properties: [
                    new OA\Property(property: 'run-ciudadano', type: 'string', example: '12345678'),
                    new OA\Property(property: 'run-usuario', type: 'string', example: '87654321'),
                    new OA\Property(property: 'origen-registro', type: 'string', example: '1'),
                    new OA\Property(property: 'tipo-solicitud', type: 'integer', example: 1),
                    new OA\Property(property: 'sub-tipo-solicitud', type: 'integer', example: 3),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Consulta exitosa', content: new OA\JsonContent(ref: '#/components/schemas/MDSFApiResponse')),
            new OA\Response(response: 401, description: 'Token inválido'),
            new OA\Response(response: 404, description: 'Error de servicio'),
        ]
    )]
    public function __invoke(Request $request)
    {

        $requestData = $request->all();
        $respuesta = new MDSFApiResponse();
        try{
            $classBloqueosSolicitudCiudadano = new BloqueosSolicitudCiudadanoClass();
            $respuestaBloqueosSolicitudCiudadano = $classBloqueosSolicitudCiudadano($requestData);
            if(isset($respuestaBloqueosSolicitudCiudadano['code'])){
                $respuesta->code = $respuestaBloqueosSolicitudCiudadano['code'];
            }
            if(isset($respuestaBloqueosSolicitudCiudadano['message'])){
                $respuesta->message = $respuestaBloqueosSolicitudCiudadano['message'];
            }
            if($respuestaBloqueosSolicitudCiudadano['code'] < 1100) {
                $datosBloqueosSolicitudCiudadano = new BloqueosSolicitudCiudadanoData($respuestaBloqueosSolicitudCiudadano);
                $respuesta->data = $datosBloqueosSolicitudCiudadano->data;
            }

        }
        catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse(Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST], $e);
            $respuesta->code = 1301;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
