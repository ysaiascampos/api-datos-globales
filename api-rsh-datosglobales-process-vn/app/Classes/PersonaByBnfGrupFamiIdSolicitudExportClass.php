<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PersonaByBnfGrupFamiIdSolicitudExportClass
{
    public function __invoke($idSolicitud = null)
    {
        $cacheKeyPre = env('PERSONA_BNF_GF_CACHE_KEY_PRE','PERSONA_BNF_GF_');
        $cacheKey = $cacheKeyPre.$idSolicitud;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.persona_bnf', true);

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
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/persona-bnf-grup-fami-solicitud-export/' . $idSolicitud);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $responseData = json_decode(json_encode($apiresponse->data), true);
        $dataResponse["persona-bnf-grup-fami"] = isset($responseData["persona-bnf-grup-fami"]) ? $responseData["persona-bnf-grup-fami"] : null;
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
