<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SolicitudByIdClass
{
    public function __invoke($idSolicitud = null)
    {
        $cacheKeyPre = env('SOLICITUD_CACHE_KEY_PRE','SOLICITUD_');
        $cacheKey = $cacheKeyPre.$idSolicitud;
        $ttl = (int) config('cache.ttl.short', 60);
        $cacheActivar = config('cache.features.solicitud', true);

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
        $dataResponse = [
            'code' => 1000,
            'message' => '',
            'situacion' => true,
            'mensaje' => '',
            'data' => [],
        ];

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-id-solicitud/' . $idSolicitud);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["data"]["solicitud"] = null;
        if ($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["data"]["solicitud"] = isset($responseData["solicitud"]) ? $responseData["solicitud"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
