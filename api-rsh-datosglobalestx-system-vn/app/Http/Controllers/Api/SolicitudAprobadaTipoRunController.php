<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudAprobadaTipoRunClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudAprobadaTipoRunData;


class SolicitudAprobadaTipoRunController extends Controller
{

    public function __invoke($tipoSolicitud, $runCiudadano)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudAprobadaTipoRun = new SolicitudAprobadaTipoRunClass;
            $respuestaSolicitudAprobadaTipoRun = $classSolicitudAprobadaTipoRun($tipoSolicitud, $runCiudadano);
            if($respuestaSolicitudAprobadaTipoRun) {
                $solicitudData = new SolicitudAprobadaTipoRunData($respuestaSolicitudAprobadaTipoRun);
                $respuesta->data = $solicitudData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de solicitud aprobadas', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
