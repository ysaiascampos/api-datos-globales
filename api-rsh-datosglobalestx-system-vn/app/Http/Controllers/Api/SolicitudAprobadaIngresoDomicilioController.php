<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudAprobadaIngresoDomicilioClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudAprobadaIdSolicitudData;


class SolicitudAprobadaIngresoDomicilioController extends Controller
{

    public function __invoke($idSolicitud)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudAprobadaIdSolicitud = new SolicitudAprobadaIngresoDomicilioClass;
            $respuestaSolicitudAprobadaIdSolicitud = $classSolicitudAprobadaIdSolicitud($idSolicitud);
            if($respuestaSolicitudAprobadaIdSolicitud) {
                $solicitudData = new SolicitudAprobadaIdSolicitudData($respuestaSolicitudAprobadaIdSolicitud);
                $respuesta->data = $solicitudData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de solicitudes aprobadas', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
