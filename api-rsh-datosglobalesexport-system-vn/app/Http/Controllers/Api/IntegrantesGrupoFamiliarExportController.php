<?php

namespace App\Http\Controllers\Api;

use App\Classes\OptenerIntegrantesExportClass;
use App\Constantes\Constante;
use App\Http\ResponseData\IntegrantesGrupoFamiliarExportData;
use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Models\GrupoFamiliarExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;



class IntegrantesGrupoFamiliarExportController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $grupoFamiliarExport = PersonasExport::findByRunCiudadanoGrupofamiliarFolioId($runCiudadano);
            $integrantesGrupoFamiliarExport=null;
            if($grupoFamiliarExport){
                $integrantesGrupoFamiliarExportClass = new OptenerIntegrantesExportClass;
                $integrantesGrupoFamiliarExport = $integrantesGrupoFamiliarExportClass($grupoFamiliarExport->grupfami_id);
            }
            if ($integrantesGrupoFamiliarExport) {
                $integrantesGrupoFamiliarExportData = new IntegrantesGrupoFamiliarExportData($integrantesGrupoFamiliarExport);
                $respuesta->data = $integrantesGrupoFamiliarExportData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de integrantes del grupo familiar', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
