<?php

namespace App\Http\Controllers\Api;

use App\Classes\DatosContactoClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\DatosContactoData;


class DatosDeContactoController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        $respuesta = new MDSFApiResponse();
        try {

            $classDatosContacto = new DatosContactoClass;
            $respuestaDatosContacto = $classDatosContacto($runCiudadano);
            if($respuestaDatosContacto){
                $datosContactoData = new DatosContactoData($respuestaDatosContacto);
                $respuesta->data = $datosContactoData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->message = 'Problemas al obtener datos de contacto';
                $respuesta->data = null;
            }
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de contacto', $e);
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1402;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
