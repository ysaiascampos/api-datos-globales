<?php

namespace App\Http\Controllers\Api;

use App\Classes\SolicitudesTramitablesClass;
use App\Constantes\Constante;
use App\Http\ResponseData\PersonaData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SolicitudesTramitablesController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        $respuesta = new MDSFApiResponse();
        $solicitudesTramitablesClass = new SolicitudesTramitablesClass();
        $respuestaSolicitudesTramitablesClass = $solicitudesTramitablesClass($runCiudadano);
        if(isset($respuestaSolicitudesTramitablesClass) ) {
            $respuesta->data = $respuestaSolicitudesTramitablesClass;
        } else {
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1201;
            $respuesta->message = "Problemas al obtener datos de de los tramites";
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
