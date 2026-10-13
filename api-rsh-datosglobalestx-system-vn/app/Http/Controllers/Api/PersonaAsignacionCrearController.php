<?php

namespace App\Http\Controllers\Api;

use App\classes\PersonaAsignacionCrearClass;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\PersonaAsignacionData;


class PersonaAsignacionCrearController extends Controller
{

    public function __invoke(Request $request)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classPersonaAsignacionCrear = new PersonaAsignacionCrearClass;
            $respuestaPersonaAsignacionCrear = $classPersonaAsignacionCrear($request);
            if($respuestaPersonaAsignacionCrear) {
                $personaAsignacionCrearData = new PersonaAsignacionData($respuestaPersonaAsignacionCrear);
                $respuesta->data = $personaAsignacionCrearData->data;
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
