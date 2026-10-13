<?php

namespace App\Http\Controllers\Api;

use App\classes\DesistirSolicitudActualizarClass;
use App\Constantes\Constante;
use App\Http\ResponseData\DesistirSolicitudData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class DesistirSolicitudActualizarController extends Controller
{

    public function __invoke(Request $request)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classDesistirSolicitudActualizar = new DesistirSolicitudActualizarClass();
            $respuestaDesistirSolicitudActualizar = $classDesistirSolicitudActualizar($request);
            if($respuestaDesistirSolicitudActualizar) {
                $DesistirSolicitudActualizarData = new DesistirSolicitudData($respuestaDesistirSolicitudActualizar);
                $respuesta->data = $DesistirSolicitudActualizarData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->code = $respuestaDesistirSolicitudActualizar['code'];
                $respuesta->message = $respuestaDesistirSolicitudActualizar['message'];
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al desistir la solicitud', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
