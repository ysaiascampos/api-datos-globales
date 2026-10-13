<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Constantes\ConstantePersonaRunificador;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SituacionBasicaClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('SITUACION_BASICA_CACHE_KEY_PRE','SITUACION_BASICA_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.situacion_basica', true);

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
            ];
            $runificar = env('CONSUMIR_WS_RUNIFICADOR', Constante::ENV_DEFECTO_CONSUMIR_WS_RUNIFICADOR);
            if ($runificar) {
                $runificarRest = env('RUNIFICADOR_REST_CONSUMIR_WS', Constante::ENV_DEFECTO_CONSUMIR_REST_RUNIFICADOR);
                if ($runificarRest) {
                    $apiRunificador = new ApiRunificador;
                    $personaWSRunificador = $apiRunificador($runCiudadano, Utiles::dv($runCiudadano));
                } else {
                    $WSRunificador = new WSRunificador;
                    $personaWSRunificador = $WSRunificador($runCiudadano);
                }

                if (is_numeric($personaWSRunificador)) {
                    Log::info("El runificador devuelve error pero persona Persona existe en BD y se permite continuar", ['wsPersona' => $personaWSRunificador]);
                    return $dataResponse = [
                        'code' => 1301,
                        'message' => ConstantePersonaRunificador::$RESPUESTA_RUNIFICADOR[$personaWSRunificador],
                        'situacion' => true,
                        'mensaje' => ConstantePersonaRunificador::$RESPUESTA_RUNIFICADOR[$personaWSRunificador],
                    ];
                }
                if ($personaWSRunificador && $personaWSRunificador->FechaDefuncion != 0) {
                    $dataResponse = [
                        'code' => 1002,
                        'message' => ConstantePersonaRunificador::MENSAJE_ERROR_RUNIFICADOR_FALLECIDO,
                        'situacion' => false,
                        'mensaje' => ConstantePersonaRunificador::MENSAJE_ERROR_RUNIFICADOR_FALLECIDO,
                    ];
                }
            }
        return $dataResponse;
    }

}
