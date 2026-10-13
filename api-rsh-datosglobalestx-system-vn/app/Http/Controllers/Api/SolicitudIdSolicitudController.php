<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudIdSolicitudClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudAprobadaIdSolicitudData;


class SolicitudIdSolicitudController extends Controller
{

    public function __invoke($idSolicitud)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudIdSolicitudClass = new SolicitudIdSolicitudClass;
            $respuestaSolicitudIdSolicitudClass = $classSolicitudIdSolicitudClass($idSolicitud);
            if($respuestaSolicitudIdSolicitudClass) {
                $solicitudData = new SolicitudAprobadaIdSolicitudData($respuestaSolicitudIdSolicitudClass);
                $respuesta->data = $solicitudData->data;
            } else {
                $respuesta = MDSFApiResponse::exceptionResponse('La solicitud no esta registrada en la bd');
                $respuesta->code = 1201;
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de solicitud', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
