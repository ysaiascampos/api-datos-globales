<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudDatoActualNoFinalizadasClass;
use App\Constantes\Constante;
use App\Http\ResponseData\SolicitudNoFinalizadasData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SolicitudesDatoActualNoFinalizadasController extends Controller
{

    public function __invoke($tipoSolicitud, $datoActual)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudDatoActualNoFinalizadasClass = new SolicitudDatoActualNoFinalizadasClass;
            $respuestaSolicitudDatoActualNoFinalizadas = $classSolicitudDatoActualNoFinalizadasClass($tipoSolicitud, $datoActual);
            if($respuestaSolicitudDatoActualNoFinalizadas) {
                $SolicitudDatoActualNoFinalizadasData = new SolicitudNoFinalizadasData($respuestaSolicitudDatoActualNoFinalizadas);
                $respuesta->data = $SolicitudDatoActualNoFinalizadasData->data;
            } else {
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
