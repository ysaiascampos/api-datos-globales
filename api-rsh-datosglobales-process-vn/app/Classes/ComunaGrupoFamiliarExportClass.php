<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ComunaGrupoFamiliarExportClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('COMUNA_GF_EXPORT_CACHE_KEY_PRE','COMUNA_GF_EXPORT_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.comuna_gf_export', true);

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
        $GrupoFamiliarExportClass = new GrupoFamiliarExportClass;
        $responseGrupoFamiliarExport = $GrupoFamiliarExportClass($runCiudadano);
        $grupoFamiliarExport = $responseGrupoFamiliarExport["grupo-familiar-export"];

        $dataResponse["comuna-grupo-familiar-export"] = ["grupfami-comunaine" => 0,
            "descripcion" => ""];

        if (isset($grupoFamiliarExport["grupfami-comunaine"])) {
            $ComunaIneClass = new ComunaIneClass;
            $responseComunaIne = $ComunaIneClass($grupoFamiliarExport["grupfami-comunaine"]);

            $comunaIne = $responseComunaIne["comuna-ine"];

            $descripcion = isset($comunaIne["descripcion"]) ? $comunaIne["descripcion"] : "";
            $dataResponse["comuna-grupo-familiar-export"] = ["grupfami-comunaine" => $grupoFamiliarExport["grupfami-comunaine"],
                "descripcion" => $descripcion];
        }
        $dataResponse["code"] = $responseGrupoFamiliarExport["code"];
        $dataResponse["message"] = $responseGrupoFamiliarExport["message"];
        return $dataResponse;
    }
}
