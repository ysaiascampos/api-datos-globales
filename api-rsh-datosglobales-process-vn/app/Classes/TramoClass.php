<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Symfony\Component\Routing\Loader\isElementValueNull;

class TramoClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('TRAMO_CACHE_KEY_PRE','TRAMO_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.tramo', true);

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
            $grupoFamiliarExport = isset($responseGrupoFamiliarExport["grupo-familiar-export"])?
                                    $responseGrupoFamiliarExport["grupo-familiar-export"]:null;

            $dataTramo = ["numero" => 0,
                "nombre" => Constante::MENSAJE_ERROR_11_NO_POSEE_RSH,
                "mensaje" => ""];

            if (isset($grupoFamiliarExport["calc-quintil-validacion2"])) {
                $numerotramoQuintil = $grupoFamiliarExport["calc-quintil-validacion2"];

                $TramoQuintalListaClass = new TramoQuintalListaClass();
                $responseTramoQuintalLista = $TramoQuintalListaClass();
                $tramoQuintilLista = $responseTramoQuintalLista["data"]["tramo-quintil"];

                switch ($numerotramoQuintil) {
                    case $numerotramoQuintil<= Constante::NUMERO_TRAMO_40:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_40];
                        break;
                    case $numerotramoQuintil> Constante::NUMERO_TRAMO_40
                        && $numerotramoQuintil<= Constante::NUMERO_TRAMO_50:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_50];
                        break;
                    case $numerotramoQuintil> Constante::NUMERO_TRAMO_50
                        && $numerotramoQuintil<= Constante::NUMERO_TRAMO_60:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_60];
                        break;
                    case $numerotramoQuintil> Constante::NUMERO_TRAMO_60
                        && $numerotramoQuintil<= Constante::NUMERO_TRAMO_70:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_70];
                        break;
                    case $numerotramoQuintil> Constante::NUMERO_TRAMO_70
                        && $numerotramoQuintil<= Constante::NUMERO_TRAMO_80:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_80];
                        break;
                    case $numerotramoQuintil> Constante::NUMERO_TRAMO_80
                        && $numerotramoQuintil<= Constante::NUMERO_TRAMO_90:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_90];
                        break;
                    case $numerotramoQuintil> Constante::NUMERO_TRAMO_90
                        && $numerotramoQuintil<= Constante::NUMERO_TRAMO_100:
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_100];
                        break;
                    default;
                        $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_40];
                }
                $dataTramo["numero"] = $dataTramoQuintil["id"];
                $dataTramo["nombre"] = $dataTramoQuintil["nombre"];
                $dataResponse["code"] = $responseTramoQuintalLista["code"];
                $dataResponse["message"] = $responseTramoQuintalLista["message"];

                $MensajeTramoClass = new MensajeTramoClass();
                $responseMensajeTramo = $MensajeTramoClass();
                $mensajeTramoResponse = $responseMensajeTramo["data"];
                if (isset($mensajeTramoResponse["mensaje-tramo"]["descripcion-larga"])) {
                    $dataTramo["mensaje"] = $mensajeTramoResponse["mensaje-tramo"]["descripcion-larga"];
                    $dataResponse["code"] = $responseMensajeTramo["code"];
                    $dataResponse["message"] = $responseMensajeTramo["message"];
                }

            }
            $dataResponse["tramo"] = $dataTramo;
        return $dataResponse;
    }
}
