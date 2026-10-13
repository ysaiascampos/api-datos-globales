<?php


namespace App\Classes;


use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class ValidaRememberTokenClass
{
    public function __invoke($rememberToken = null)
    {
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->post('/validar-remember-token', [
            'token' => $rememberToken
        ]);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        if($apiresponse->http_code == 200 && $apiresponse->data) {
            $dataResponse["data"] = $apiresponse->data;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
