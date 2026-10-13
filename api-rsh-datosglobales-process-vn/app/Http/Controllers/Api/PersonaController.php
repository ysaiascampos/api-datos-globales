<?php

namespace App\Http\Controllers\Api;


use App\Classes\PersonaBasicaClass;
use App\Constantes\Constante;
use App\Http\ResponseData\PersonaData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;



class PersonaController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        $respuesta = new MDSFApiResponse();
        $personaBasicaClass = new PersonaBasicaClass;
        $personaBasica = $personaBasicaClass($runCiudadano);
        if(isset($personaBasica['data']['persona-asignacion']) && is_array($personaBasica['data']['persona-asignacion'])) {
            $personaData = new PersonaData($personaBasica);
            $respuesta->data = $personaData->data;
        } else {
            if(isset($personaBasica['code']) && $personaBasica['code']!=1000){
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->code = $personaBasica['code'];
                $respuesta->message = $personaBasica['message'];
                $respuesta->data = $personaBasica['data'];
            }else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->code = 1201;
                $respuesta->message = "Problemas al obtener datos de la persona";
                $respuesta->data = null;
            }
        }
        return $respuesta->json();
    }
}
