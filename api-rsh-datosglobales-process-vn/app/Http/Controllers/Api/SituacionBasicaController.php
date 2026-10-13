<?php

namespace App\Http\Controllers\Api;

use App\Classes\SituacionBasicaClass;
use App\Constantes\Constante;
use App\Http\ResponseData\SituacionBasicaData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class SituacionBasicaController extends Controller
{

    public function __invoke($runCiudadano = null)
    {

        $respuesta = new MDSFApiResponse();
        try{
            $classSituacionBasica = new SituacionBasicaClass;
            $respuestaSituacionBasica = $classSituacionBasica($runCiudadano);

            $respuesta->code = $respuestaSituacionBasica['code'];
            $respuesta->message = $respuestaSituacionBasica['message'];
            if($respuestaSituacionBasica['code'] == 1000){
                $datosContactoData = new SituacionBasicaData($respuestaSituacionBasica);
                $respuesta->data = $datosContactoData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al consultar al registro civil', $e);
            $respuesta->code = 1301;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
