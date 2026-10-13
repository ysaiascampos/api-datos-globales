<?php

namespace App\Http\Controllers\Api;

use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Classes\AlertasClass;
use App\Http\ResponseData\AlertasData;
use App\Constantes\Constante;


class AlertasController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $classAlertas = new AlertasClass;
            $respuestaAlertas = $classAlertas($runCiudadano);

            if ($respuestaAlertas) {
                $datosAlertas = new AlertasData($respuestaAlertas);
                $respuesta->data = $datosAlertas->data;
            } else {
                $respuesta->data['alertas'] = [
                    'mensaje-individual' => [],
                    'mensaje-grupal' => [],
                ];
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de alertas', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
