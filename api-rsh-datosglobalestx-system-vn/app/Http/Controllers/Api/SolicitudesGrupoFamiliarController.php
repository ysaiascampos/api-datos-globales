<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudesGrupoFamiliarClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudesGrupoFamiliarFullData;


class SolicitudesGrupoFamiliarController extends Controller
{

    public function __invoke(Request $request)
    {
        try {
            $runCiudadano = $request->post("run-ciudadano");
            $runesIntegrantes = $request->post("runes-integrantes");

            $respuesta = new MDSFApiResponse();
            $classSolicitudesGrupoFamiliar = new SolicitudesGrupoFamiliarClass;
            $respuestaSolicitudesGrupoFamiliar = $classSolicitudesGrupoFamiliar($runCiudadano,$runesIntegrantes);
            if($respuestaSolicitudesGrupoFamiliar) {
                $solicitudesGrupoFamiliarData = new SolicitudesGrupoFamiliarFullData($respuestaSolicitudesGrupoFamiliar);
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
