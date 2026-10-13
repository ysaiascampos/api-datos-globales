<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudesGrupoFamiliarViaDatoNuevoClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudesGrupoFamiliarViaDatoNuevoData;


class SolicitudesGrupoFamiliarViaDatoNuevoController extends Controller
{

    public function __invoke($perId = null)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudesGrupoFamiliar = new SolicitudesGrupoFamiliarViaDatoNuevoClass;
            $respuestaSolicitudesGrupoFamiliar = $classSolicitudesGrupoFamiliar($perId);
            if($respuestaSolicitudesGrupoFamiliar) {
                $solicitudesGrupoFamiliarData = new SolicitudesGrupoFamiliarViaDatoNuevoData($respuestaSolicitudesGrupoFamiliar);
                $respuesta->data = $solicitudesGrupoFamiliarData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de solicitudes grupo familiar', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
