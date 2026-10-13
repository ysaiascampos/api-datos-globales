<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GrupoFamiliarClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('GRUPO_FAMILIAR_CACHE_KEY_PRE','GRUPO_FAMILIAR_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.grupo_familiar', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () use ($runCiudadano) {
                return $this->obtenerDatos($runCiudadano);
            },
            $cacheActivar
        );

    }

    private function obtenerDatos($runCiudadano)
    {
        $dataResponse = [
            'code' => 1000,
            'message' => '',
            'situacion' => true,
            'mensaje' => '',
            'data' => [],
        ];

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/grupo-familiar/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["data"]["grupo-familiar"] = null;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["data"]["grupo-familiar"] = isset($responseData["grupo-familiar"]) ? $responseData["grupo-familiar"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
