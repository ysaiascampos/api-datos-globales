<?php

namespace App\Http\Controllers\Api;

use App\classes\PersonaAsignacionClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\PersonaAsignacionData;


class PersonaAsignacionController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classPersonaAsignacion = new PersonaAsignacionClass;
            $respuestaPersonaAsignacion = $classPersonaAsignacion($runCiudadano);
            if($respuestaPersonaAsignacion) {
                $personaAsignacionData = new PersonaAsignacionData($respuestaPersonaAsignacion);
                $respuesta->data = $personaAsignacionData->data;
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
