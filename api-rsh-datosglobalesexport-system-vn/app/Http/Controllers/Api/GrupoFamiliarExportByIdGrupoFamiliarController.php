<?php

namespace App\Http\Controllers\Api;

use App\Constantes\Constante;
use App\Http\ResponseData\GrupoFamiliarExportData;
use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Models\GrupoFamiliarExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class GrupoFamiliarExportByIdGrupoFamiliarController extends Controller
{

    public function __invoke($IdGrupoFamiliar = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $grupoFamiliarExport = null;
            if($IdGrupoFamiliar){
                $grupoFamiliarExport = GrupoFamiliarExport::findByIdGrupoFamiliar($IdGrupoFamiliar);
            }
            if ($grupoFamiliarExport) {
                $grupoFamiliarExportData = new GrupoFamiliarExportData($grupoFamiliarExport);
                $respuesta->data = $grupoFamiliarExportData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos del grupo familiar', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
