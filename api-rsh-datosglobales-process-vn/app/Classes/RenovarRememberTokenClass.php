<?php


namespace App\Classes;


use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class RenovarRememberTokenClass
{
    public function __invoke($runUsuario = null)
    {
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->post('/generar-remember-token', ['run' => $runUsuario]);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        if($apiresponse->http_code == 200 && $apiresponse->data) {
            $dataResponse["data"] = $apiresponse->data;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
