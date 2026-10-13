<?php


namespace App\Classes;


use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class UltimaFechaCierreGrupoFamiliarClass
{
    public function __invoke()
    {
        $cacheKeyPre = env('ULTIMA_FECHA_CIERRE_CACHE_KEY_PRE','ULTIMA_FECHA_CIERRE');
        $cacheKey = $cacheKeyPre;
        $ttl = (int) config('cache.ttl.extra_long', 7200);
        $cacheActivar = config('cache.features.ultima_fecha_cierre', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function (){
                return $this->obtenerDatos();
            },
            $cacheActivar
        );

    }

    private function obtenerDatos()
    {

        $dataResponse = [
            'code' => 1000,
            'message' => '',
            'situacion' => true,
            'mensaje' => '',
            'data' => [],
        ];

        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/ultima-fecha-cierre-gf/');
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["data"]["fecha-cierre"] = null;
        if($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $dataResponse["data"]["fecha-cierre"] = isset($responseData["fecha-cierre"]) ? $responseData["fecha-cierre"] : null;
        }
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        return $dataResponse;
    }
}
