<?php

namespace App\Http\Controllers\Api;

use App\classes\GrupoFamiliarVigenteAprobadoByRunClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\GrupoFamiliarVigenteAprobadoByRunData;


class GrupoFamiliarVigenteAprobadoByRunController extends Controller
{

    public function __invoke($runCiudadano)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classGrupoFamiliarVigenteAprobadoByRun = new GrupoFamiliarVigenteAprobadoByRunClass();
            $respuestaGrupoFamiliarVigenteAprobadoByRun = $classGrupoFamiliarVigenteAprobadoByRun($runCiudadano);
            if($respuestaGrupoFamiliarVigenteAprobadoByRun){
                $GrupoFamiliarVigenteAprobadoByRunData = new GrupoFamiliarVigenteAprobadoByRunData($respuestaGrupoFamiliarVigenteAprobadoByRun);
                $respuesta->data = $GrupoFamiliarVigenteAprobadoByRunData->data;
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
