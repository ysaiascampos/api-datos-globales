<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MensajeTramoClass
{
    public function __invoke()
    {
        $cacheKeyPre = env('MENSAJE_TRAMO_CACHE_KEY_PRE','MENSAJE_TRAMO_');
        $cacheKey = $cacheKeyPre;
        $ttl = (int) config('cache.ttl.long', 1800);
        $cacheActivar = config('cache.features.mensaje_tramo', true);

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

        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/mensaje-tramo/');
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["data"]["mensaje-tramo"] = null;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["data"]["mensaje-tramo"] = isset($responseData["mensaje-tramo"]) ? $responseData["mensaje-tramo"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
