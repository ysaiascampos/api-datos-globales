<?php

namespace App\Http\Controllers\Api;

use App\classes\SolicitudesGrupoFamiliarViaOtrosClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\SolicitudesGrupoFamiliarData;


class SolicitudesGrupoFamiliarViaOtrosController extends Controller
{

    public function __invoke(Request $request)
    {
        try {
            $runCiudadano = $request->post("run-ciudadano");
            $runesIntegrantes = $request->post("runes-integrantes");

            $respuesta = new MDSFApiResponse();
            $classSolicitudesGrupoFamiliar = new SolicitudesGrupoFamiliarViaOtrosClass;
            $respuestaSolicitudesGrupoFamiliar = $classSolicitudesGrupoFamiliar($runCiudadano,$runesIntegrantes);
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
