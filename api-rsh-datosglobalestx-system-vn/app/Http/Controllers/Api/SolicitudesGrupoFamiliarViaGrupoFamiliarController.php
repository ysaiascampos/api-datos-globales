<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudesGrupoFamiliarViaGrupoFamiliarClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudesGrupoFamiliarData;


class SolicitudesGrupoFamiliarViaGrupoFamiliarController extends Controller
{

    public function __invoke($runCiudadano = null)
    {


        try {
            $respuesta = new MDSFApiResponse();
            $classSolicitudesGrupoFamiliar = new SolicitudesGrupoFamiliarViaGrupoFamiliarClass;
            $respuestaSolicitudesGrupoFamiliar = $classSolicitudesGrupoFamiliar($runCiudadano);
            $solicitudesGrupoFamiliarData = new SolicitudesGrupoFamiliarData($respuestaSolicitudesGrupoFamiliar);
            $respuesta->data = $solicitudesGrupoFamiliarData->data;
            if($respuestaSolicitudesGrupoFamiliar) {
                $respuesta->data = $respuestaSolicitudesGrupoFamiliar;
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
