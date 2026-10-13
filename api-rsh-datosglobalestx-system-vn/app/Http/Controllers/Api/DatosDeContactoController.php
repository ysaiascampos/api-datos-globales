<?php

namespace App\Http\Controllers\Api;

use App\classes\DatosContacto;
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
        try {
            $respuesta = new MDSFApiResponse();
            $classDatosContacto = new DatosContacto;
            $respuestaDatosContacto = $classDatosContacto($runCiudadano);
            if($respuestaDatosContacto) {
                $datosContactoData = new DatosContactoData($respuestaDatosContacto);
                $respuesta->data = $datosContactoData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de contacto', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
