<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PoseeRSHClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('POSEE_RSH_CACHE_KEY_PRE','POSEE_RSH_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.posee_rsh', true);

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
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/posee-rsh/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["posee-rsh"] = false;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["posee-rsh"] = $responseData["posee-rsh"];
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
