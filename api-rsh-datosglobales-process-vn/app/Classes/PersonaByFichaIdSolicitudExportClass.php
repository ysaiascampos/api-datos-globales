<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PersonaByFichaIdSolicitudExportClass
{
    public function __invoke($idSolicitud = null)
    {
        $cacheKeyPre = env('PERSONA_FICHA_CACHE_KEY_PRE','PERSONA_FICHA_');
        $cacheKey = $cacheKeyPre.$idSolicitud;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.persona_ficha', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () use ($idSolicitud) {
                return $this->obtenerDatos($idSolicitud);
            },
            $cacheActivar
        );

    }

    private function obtenerDatos($idSolicitud)
    {
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/persona-ficha-solicitud-export/' . $idSolicitud);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $responseData = json_decode(json_encode($apiresponse->data), true);
        $dataResponse["persona-ficha"] = isset($responseData["persona-ficha"]) ? $responseData["persona-ficha"] : null;
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
