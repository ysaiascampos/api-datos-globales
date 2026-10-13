<?php

namespace App\Http\Controllers\Api;


use App\Classes\ApiRunificador;
use App\Classes\PersonaBasicaClass;
use App\Constantes\ConstantePersonaRunificador;
use App\Http\ResponseData\PersonaData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class RunificadorController extends Controller
{

    public function __invoke($runCiudadano = null,$dv=null)
    {
        $respuesta = new MDSFApiResponse();
        $ApiRunificadorClass = new ApiRunificador();
        $personaRunificador = $ApiRunificadorClass($runCiudadano,$dv);
        if($personaRunificador && isset($personaRunificador->RUN)) {
            if($personaRunificador->FechaDefuncion != 0 &&
                $personaRunificador->FechaDefuncion != null) {
                $respuesta->code = 1002;
                $respuesta->message = ConstantePersonaRunificador::MENSAJE_ERROR_RUNIFICADOR_FALLECIDO;
                $respuesta->data = null;
            }else {
                $respuesta->data = $personaRunificador;
            }
        } else {
            if (is_numeric($personaRunificador)) {
                $respuesta->code = 1301;
                $respuesta->message = ConstantePersonaRunificador::$RESPUESTA_RUNIFICADOR[$personaRunificador];
                $respuesta->data = null;
            } else {
                $respuesta->code = 1201;
                $respuesta->message = "No se encuentra en registrado en bd";
                $respuesta->data = null;
            }
        }
        return $respuesta->json();
    }
}
