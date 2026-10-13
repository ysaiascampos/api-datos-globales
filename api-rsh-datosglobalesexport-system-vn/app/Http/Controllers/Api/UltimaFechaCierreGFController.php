<?php

namespace App\Http\Controllers\Api;

use App\Models\GrupoFamiliarExport;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;



class UltimaFechaCierreGFController extends Controller
{

    public function __invoke()
    {
        $respuesta = new MDSFApiResponse();
        $respuestaFechaCierreRSH = GrupoFamiliarExport::findByFechaCierreGrupoFamiliar();
        $respuesta->data = ["fecha-cierre"=>$respuestaFechaCierreRSH->fecha_cierre];
        return $respuesta->json();
    }
}
