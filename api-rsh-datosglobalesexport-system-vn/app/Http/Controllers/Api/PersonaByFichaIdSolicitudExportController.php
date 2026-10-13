<?php

namespace App\Http\Controllers\Api;

use App\Http\ResponseData\PersonaCortaData;
use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;



class PersonaByFichaIdSolicitudExportController extends Controller
{

    public function __invoke($idSolicitud = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $respuestaPersonasExport = PersonasExport::findByFichaIdSolicitud($idSolicitud);
            if($respuestaPersonasExport) {
                $personaCortaExportData = new PersonaCortaData($respuestaPersonasExport);
                $respuesta->data["persona-ficha"] = $personaCortaExportData->data;
            } else {
                $respuesta->data["persona-ficha"] = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de persona ficha', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
