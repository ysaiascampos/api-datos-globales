<?php

namespace App\Http\Controllers\Api;

use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Classes\ValidaSolicitudTerceroClass;
use App\Http\ResponseData\ValidaSolicitudTerceroData;


class ValidaSolicitudTerceroController extends Controller
{
    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $classValidaSolicitudTercero = new ValidaSolicitudTerceroClass;
            $respuestaValidaSolicitudTercero = $classValidaSolicitudTercero($runCiudadano);
            if ($respuestaValidaSolicitudTercero) {
                $datosValidaSolicitudTercero = new ValidaSolicitudTerceroData($respuestaValidaSolicitudTercero);
                $respuesta->data = $datosValidaSolicitudTercero->data;
            } else {
                $respuesta->data['solicitudes-validacion-tercero'] = [];
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de valida solicitud por tercero', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
