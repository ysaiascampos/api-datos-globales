<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GrupoFamiliarExportClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('GRUPO_FAMILIAR_EXPORT_CACHE_KEY_PRE','GRUPO_FAMILIAR_EXPORT_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.grupo_familiar_export', true);

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
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/grupo-familiar-export/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["grupo-familiar-export"] = null;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["grupo-familiar-export"] = isset($responseData["grupo-familiar-export"]) ? $responseData["grupo-familiar-export"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
