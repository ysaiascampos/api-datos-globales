<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RegionPorComunaIneClass
{
    public function __invoke($codigoComunaIne = null)
    {
        $cacheKeyPre = env('REGION_COMUNA_INE_CACHE_KEY_PRE','REGION_COMUNA_INE_');
        $cacheKey = $cacheKeyPre.$codigoComunaIne;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.region_comuna_ine', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () use ($codigoComunaIne) {
                return $this->obtenerDatos($codigoComunaIne);
            },
            $cacheActivar
        );

    }

    private function obtenerDatos($codigoComunaIne)
    {
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/region-por-comuna/' . $codigoComunaIne);
        $apiresponse = MDSFApiResponse::cast($apiresponse);

        $responseData = json_decode(json_encode($apiresponse->data), true);
        $dataResponse["region"] = isset($responseData["region"]) ? $responseData["region"] : null;
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
