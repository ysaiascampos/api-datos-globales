<?php

namespace App\Classes;


use Illuminate\Support\Facades\Http;
use App\Models\MDSFApiResponse;
use App\Constantes\Constante;
use Illuminate\Support\Facades\Log;

class LogBuscaCartolaEmitidaClass
{

    public function create(array $cartolaData)
    {
        try {

            $response = Http::APISYSTEM('EXPORT_ACCESOS')
                ->post('/log-busqueda-cartola-emitida', $cartolaData);

            // 1. HTTP status real
            $httpStatus = $response->status();

            // 2. Body casteado
            $apiresponse = MDSFApiResponse::cast($response);
            $dataResponse["data"] = $this->toArray($apiresponse->data);
            $dataResponse["code"] = $apiresponse->code;
            if($apiresponse->code == 1409){
                $dataResponse["http_code"] = Constante::STATUS_409_ERROR_CONFLICT;
            } else {
                $dataResponse["http_code"] = $apiresponse->http_code;
            }

            $dataResponse["message"] = $apiresponse->message;

            return $dataResponse;

        } catch (\Exception $e) {
            Log::error('Error consumiendo API');
            Log::error($e);

            return response()->json([
                'code' => 5000,
                'message' => 'Error interno',
                'data' => null
            ], 500);
        }
    }

    private function toArray($data)
    {
        return is_array($data) ? $data : json_decode(json_encode($data), true);
    }

}
