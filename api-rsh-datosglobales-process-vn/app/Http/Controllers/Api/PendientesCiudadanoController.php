<?php

namespace App\Http\Controllers\Api;

use App\Classes\PendientesCiudadanoClass;
use App\Constantes\Constante;
use App\Http\ResponseData\PendientesCiudadanoData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class PendientesCiudadanoController extends Controller
{

    public function __invoke($runCiudadano = null)
    {

        $respuesta = new MDSFApiResponse();
        try{
            $classPendientesCiudadano = new PendientesCiudadanoClass;
            $respuestaPendientesCiudadano = $classPendientesCiudadano($runCiudadano);
            if($respuestaPendientesCiudadano) {
                $datosPendientesCiudadano = new PendientesCiudadanoData($respuestaPendientesCiudadano);
                $respuesta->data = $datosPendientesCiudadano->data;
                if(isset($respuestaPendientesCiudadano['code'])
                    && $respuestaPendientesCiudadano['code'] != 1000) {
                    $respuesta->code = $respuestaPendientesCiudadano['code'];
                    $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                    $respuesta->data = null;
                    if (isset($respuestaPendientesCiudadano['message'])) {
                        $respuesta->message = $respuestaPendientesCiudadano['message'];
                    }
                }
            } else {
                $respuesta = MDSFApiResponse::exceptionResponse('Problemas al consultar datos de la persona');
                $respuesta->code = 1301;
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->data = null;
            }

        }
        catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al consultar datos de la persona', $e);
            $respuesta->code = 1301;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
