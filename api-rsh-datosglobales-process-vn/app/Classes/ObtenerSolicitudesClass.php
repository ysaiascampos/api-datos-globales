<?php


namespace App\Classes;

use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ObtenerSolicitudesClass
{
    public function __invoke($runCiudadano = null)
    {
        $dataResponse = new MDSFApiResponse();
        $cacheKeyPre = env('SOLICITUDES_CACHE_KEY_PRE', 'SOLICITUDES_');
        $cacheKey = $cacheKeyPre . $runCiudadano;
        $ttl = (int) config('cache.ttl.short', 60);
        $cacheActivar = config('cache.features.solicitudes', true);

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
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/integrantes-grupo-familiar-export/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $integrantesGrupoFamiliarExportData = [];
        if ($apiresponse->data) {
            $apiresponse = json_decode(json_encode($apiresponse->data), true);
            $integrantesGrupoFamiliarExportData = isset($apiresponse["integrantes-grupo-familiar-export"])
                ? $apiresponse["integrantes-grupo-familiar-export"] : null;
        }
        $apiBody = ["run-ciudadano" => $runCiudadano,
            "runes-integrantes" => []];
        foreach ($integrantesGrupoFamiliarExportData as $integrante) {
            if ($integrante["run"] != $runCiudadano) {
                $apiBody["runes-integrantes"][] = $integrante["run"];
            }
        }

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->post('/solicitudes-grupo-familiar', $apiBody);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $tramitesPendientesData = null;
        if ($apiresponse->data) {
            $apiresponse = json_decode(json_encode($apiresponse->data), true);
            $tramitesPendientesData = isset($apiresponse["solicitud-grupo-familiar"]) ? $apiresponse["solicitud-grupo-familiar"] : null;
        }
        $dataResponse = ["solicitudes" => $tramitesPendientesData];
        return $dataResponse;
    }
}
