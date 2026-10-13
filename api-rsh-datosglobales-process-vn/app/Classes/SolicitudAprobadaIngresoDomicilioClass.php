<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SolicitudAprobadaIngresoDomicilioClass
{
    public function __invoke($idSolicitud = null)
    {
        $cacheKeyPre = env('SOLICITUD_APROBADA_INGRESO_DOMICILIO_CACHE_KEY_PRE','SOLICITUD_APROBADA_INGRESO_DOMICILIO_');
        $cacheKey = $cacheKeyPre.$idSolicitud;
        $ttl = (int) config('cache.ttl.short', 60);
        $cacheActivar = config('cache.features.solicitud_aprobada', true);

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
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-aprobada-ingreso-domicilio/' . $idSolicitud);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["solicitud"] = null;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["data"]["solicitud"] = isset($responseData["solicitud"]) ? $responseData["solicitud"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
