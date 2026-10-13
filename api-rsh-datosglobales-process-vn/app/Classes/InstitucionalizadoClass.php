<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InstitucionalizadoClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('INSTITUCIONALIZADO_CACHE_KEY_PRE','INSTITUCIONALIZADO_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.institucionalizado', true);

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
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/persona-institucionalizado/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["data"]["institucionalizado"] = false;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $personaInstitucionalizado = isset($responseData["persona-institucionalizado"]["institucionalizado"]) ?
                $responseData["persona-institucionalizado"]["institucionalizado"] : false;
            $dataResponse["data"]["institucionalizado"] = $personaInstitucionalizado;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
