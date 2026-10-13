<?php

namespace App\Http\Controllers\Api;

use App\classes\PersonaInstitucionalizadoClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\PersonaInstitucionalizadoData;


class PersonaInstitucionalizadoController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classPersonaInstitucionalizado = new PersonaInstitucionalizadoClass();
            $respuestaDatosContacto = $classPersonaInstitucionalizado($runCiudadano);
            if($respuestaDatosContacto) {
                $datosContactoData = new PersonaInstitucionalizadoData($respuestaDatosContacto);
                $respuesta->data = $datosContactoData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de persona institucionalizada', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
