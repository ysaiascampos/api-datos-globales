<?php

namespace App\Http\Controllers\Api;

use App\Http\ResponseData\PersonaCortaData;
use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;



class PersonaByBnfGrupFamiIdSolicitudExportController extends Controller
{

    public function __invoke($idSolicitud = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $respuestaPersonasExport = PersonasExport::findByBnfGrupFamiIdSolicitud($idSolicitud);
            if ($respuestaPersonasExport) {
                $personaCortaExportData = new PersonaCortaData($respuestaPersonasExport);
                $respuesta->data["persona-bnf-grup-fami"] = $personaCortaExportData->data;
            } else {
                $respuesta->data["persona-bnf-grup-fami"] = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de persona beneficiario grupo familiar', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
