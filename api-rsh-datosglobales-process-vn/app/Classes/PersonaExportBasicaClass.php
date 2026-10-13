<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Constantes\ConstantePersonaRunificador;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PersonaExportBasicaClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('PERSONA_EXPORT_CACHE_KEY_PRE','PERSONA_EXPORT_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.persona_export', true);

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

        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/persona-export/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse['data']['persona-export'] = null;
        if ($apiresponse->data) {
            $persona = json_decode(json_encode($apiresponse->data), true);
            $dataResponse['data']['persona-export'] = $persona;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }

}
