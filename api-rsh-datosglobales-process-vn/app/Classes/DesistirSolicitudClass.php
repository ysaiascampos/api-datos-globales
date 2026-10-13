<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class DesistirSolicitudClass
{
    public function __invoke($personaData = null)
    {
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->put('/desistir-solicitud',$personaData);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $responseData = json_decode(json_encode($apiresponse->data), true);

        $dataResponse["solicitud-asignacion"] = isset($responseData["solicitud-asignacion"])?$responseData["solicitud-asignacion"]:null;
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
