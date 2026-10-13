<?php

namespace App\Http\Controllers\Api;

use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class PoseeRSHController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $respuestaPoseeRSH = PersonasExport::findPoseeRSHByRunCiudadano($runCiudadano);
            $respuesta->data = ["posee-rsh"=>$respuestaPoseeRSH];
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de persona posee rsh', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
