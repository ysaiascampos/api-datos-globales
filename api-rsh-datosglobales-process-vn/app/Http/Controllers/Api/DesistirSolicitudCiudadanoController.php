<?php

namespace App\Http\Controllers\Api;

use App\Classes\DesistirSolicitudClass;
use App\Constantes\Constante;
use App\Http\ResponseData\DesistirData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class DesistirSolicitudCiudadanoController extends Controller
{

    public function __invoke(Request $request)
    {
        $requestData = $request->all();
        $respuesta = new MDSFApiResponse();
        try {
            $classDesistirSolicitudCiudadano = new DesistirSolicitudClass();
            $respuestaDesistirSolicitudCiudadano = $classDesistirSolicitudCiudadano($requestData);
            if($respuestaDesistirSolicitudCiudadano['solicitud-asignacion'] == null){
                $respuesta->code = $respuestaDesistirSolicitudCiudadano['code'];
                $respuesta->message = $respuestaDesistirSolicitudCiudadano['message'];
                $respuesta->data = null;
            }else{
                $datosBloqueosSolicitudCiudadano = new DesistirData($respuestaDesistirSolicitudCiudadano);
                $respuesta->data = $datosBloqueosSolicitudCiudadano->data;
            }

        }catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse(Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST], $e);
            $respuesta->code = 1301;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
