<?php

namespace App\Http\Controllers\Api;

use App\classes\TipoSolicitudClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\TipoSolicitudData;


class TipoSolicitudController extends Controller
{

    public function __invoke()
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classTipoSolicitud = new TipoSolicitudClass;
            $respuestaTipoSolicitud = $classTipoSolicitud();
            if($respuestaTipoSolicitud){
                $tipoSolicitudData = new TipoSolicitudData($respuestaTipoSolicitud);
                $respuesta->data = $tipoSolicitudData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->code = 1404;
                $respuesta->message = "Problemas al obtener datos de los tipos de solicitud";
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de los tipos de solicitud', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
