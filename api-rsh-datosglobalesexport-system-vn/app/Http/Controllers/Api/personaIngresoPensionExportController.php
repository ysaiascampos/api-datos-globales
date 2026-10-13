<?php

namespace App\Http\Controllers\Api;


use App\Http\ResponseData\PersonaExportData;
use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;



class personaIngresoPensionExportController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $personasExport = PersonasExport::findByRunCiudadanoIngresosPension($runCiudadano);
            if($personasExport) {
                $personaExportData = new PersonaExportData($personasExport);
                $respuesta->data = $personaExportData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de persona ingreso pension', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
