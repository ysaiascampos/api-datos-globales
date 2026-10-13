<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudAprobadaTipoPerIdClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudAprobadaTipoPerIdData;


class SolicitudAprobadaTipoPerIdController extends Controller
{

    public function __invoke($tipoSolicitud, $perId)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudAprobadaTipoPerId = new SolicitudAprobadaTipoPerIdClass;
            $respuestaSolicitud = $classSolicitudAprobadaTipoPerId($tipoSolicitud, $perId);
            if($respuestaSolicitud) {
                $solicitudData = new SolicitudAprobadaTipoPerIdData($respuestaSolicitud);
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
