<?php

namespace App\Http\Controllers\Api;

use App\Http\ResponseData\MensajeTramoData;
use App\Models\FuenteExport;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class MensajeTramoController extends Controller
{

    public function __invoke()
    {
        try{
            $respuesta = new MDSFApiResponse();
            $mensajeTramo = FuenteExport::mensajeTramo();
            if ($mensajeTramo) {
                $mensajeTramoData = new MensajeTramoData($mensajeTramo);
                $respuesta->data = $mensajeTramoData->data;
            } else {
                $respuesta->data = ['mensaje-tramo'=>[]];
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de mensaje tramo', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
