<?php

namespace App\Http\Controllers\Api;

use App\Classes\DatosDeFuentes;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;



class FuentesController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $datosDeFuentes = new DatosDeFuentes();
            $respuestaDatosDeFuentes = $datosDeFuentes();
            if ($respuestaDatosDeFuentes) {
                $respuesta->data = $respuestaDatosDeFuentes;
            } else {
                $respuesta->code = 1404;
                $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de fuente');
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de fuente', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
