<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TramoQuintalListaClass
{
    public function __invoke()
    {
        $cacheKeyPre = env('TRAMO_QUINTAL_LISTA_CACHE_KEY_PRE','TRAMO_QUINTAL_LISTA_');
        $cacheKey = $cacheKeyPre;
        $ttl = (int) config('cache.ttl.long', 1800);
        $cacheActivar = config('cache.features.tramo_quintal_lista', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () {
                return $this->obtenerDatos();
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

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/tramo-quintal-lista/');
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["data"]["tramo-quintil"] = null;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["data"]["tramo-quintil"] = isset($responseData["tramo-quintil"]) ? $responseData["tramo-quintil"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
