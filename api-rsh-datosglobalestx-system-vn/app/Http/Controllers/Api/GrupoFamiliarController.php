<?php

namespace App\Http\Controllers\Api;

use App\classes\GrupoFamiliarClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\GrupoFamiliarData;


class GrupoFamiliarController extends Controller
{

    public function __invoke($runCiudadano)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classGrupoFamiliar = new GrupoFamiliarClass();
            $respuestaGrupoFamiliar = $classGrupoFamiliar($runCiudadano);
            if($respuestaGrupoFamiliar){
                $grupoFamiliarData = new GrupoFamiliarData($respuestaGrupoFamiliar);
                $respuesta->data = $grupoFamiliarData->data;
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
