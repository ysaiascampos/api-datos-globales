<?php

namespace App\Http\Controllers\Api;

use App\classes\RegionPorComunaIneClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\RegionPorComunaIneData;


class RegionPorComunaIneController extends Controller
{

    public function __invoke($comunaIne)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classRegionPorComunaIne = new RegionPorComunaIneClass;
            $respuestaRegionPorComunaIne = $classRegionPorComunaIne($comunaIne);
            if($respuestaRegionPorComunaIne){
                $RegionPorComunaIneData = new RegionPorComunaIneData($respuestaRegionPorComunaIne);
                $respuesta->data = $RegionPorComunaIneData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->code = 1201;
                $respuesta->message = "La region no se encuentra en registrado en bd";
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de la region', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
