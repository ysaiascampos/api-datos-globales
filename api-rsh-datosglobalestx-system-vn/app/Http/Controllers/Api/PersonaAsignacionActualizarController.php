<?php

namespace App\Http\Controllers\Api;

use App\classes\PersonaAsignacionActualizarClass;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\PersonaAsignacionData;


class PersonaAsignacionActualizarController extends Controller
{

    public function __invoke(Request $request)
    {

        try {
            $respuesta = new MDSFApiResponse();
            $classPersonaAsignacionActualizar = new PersonaAsignacionActualizarClass;
            $respuestaPersonaAsignacionActualizar = $classPersonaAsignacionActualizar($request);
            if($respuestaPersonaAsignacionActualizar){
                $personaAsignacionActualizarData = new PersonaAsignacionData($respuestaPersonaAsignacionActualizar);
                $respuesta->data = $personaAsignacionActualizarData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de persona', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
