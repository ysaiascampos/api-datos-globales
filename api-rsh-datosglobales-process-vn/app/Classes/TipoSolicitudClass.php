<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TipoSolicitudClass
{
    public function __invoke()
    {
        $cacheKeyPre = env('TIPO_SOLICITUD_CACHE_KEY_PRE','TIPO_SOLICITUD_');
        $cacheKey = $cacheKeyPre."TIPO_SOLICITUDED";
        $ttl = (int) config('cache.ttl.extra_long', 7200);
        $cacheActivar = config('cache.features.tipo_solicitud', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () {
                $data = $this->obtenerDatos();
                if(!isset($dataResponse["data"]['tipo-solicitud'][1]["desc_link"]) &&
                    isset($dataResponse["data"]['tipo-solicitud']) &&
                    count($dataResponse["data"]['tipo-solicitud'])==0){
                    return [
                        '_no_cache' => true,
                        'data' => $data
                    ];
                }
                return $data;
            },
            $cacheActivar
        );

    }

    private function obtenerDatos()
    {
        $dataResponse = [
            'code' => 1000,
            'message' => '',
            'situacion' => true,
            'mensaje' => '',
            'data' => [],
        ];

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/tipo-solicitud');
        $apiresponse = MDSFApiResponse::cast($apiresponse);

        $responseData = json_decode(json_encode($apiresponse->data), true);

        $dataResponse["data"]['tipo-solicitud'] = isset($responseData["tipo-solicitud"])?
            $responseData["tipo-solicitud"]:null;
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
