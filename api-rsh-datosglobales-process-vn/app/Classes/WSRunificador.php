<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SoapClient;

class WSRunificador
{
    /**
     * Si va bien retorna Persona del WerService:
     * EJ: result = {stdClass} [12]
     *    RUN = '13358XXX'
     *    DV = '4'
     *    Nombres = 'XXX XXX'
     *    ApellidoPaterno = 'XXX'
     *    ApellidoMaterno = 'XXX'
     *    FechaNacimiento = '19780919'
     *    FechaDefuncion = '0'
     *    Sexo = 'MASCULINO'
     *    EstadoCivil = 'CASADO'
     *    Estado = 'OK'
     *    Glosa = 'Persona Encontrada en BD'
     *    DiasEnCache = 27
     */
    public function __invoke($runCiudadano)
    {

        $cacheKeyPre = env('RUNIFICADOR_WS_CACHE_KEY_PRE','RUNIFICADOR_WS_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.runificador', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () use ($runCiudadano) {
                $data = $this->obtenerDatos($runCiudadano);
                if ((is_numeric($data))) {
                    return [
                        '_no_cache' => true,
                        'data' => $data
                    ];
                }
                return $data;
            },
            $cacheActivar
        );

    }

    private function obtenerDatos($runCiudadano)
    {
        try {
            $personaWs = null;
                if (env('VERSION_1.2_SOAP', false)) {
                    $configSOAP = Constante::$CONFIG_SOAP_1_2;
                } else {
                    $configSOAP = Constante::$CONFIG_SOAP_DEFAULT;
                }

                if (env('ACTIVAR_TIMEOUT_SOAP', false)) {
                    $timeout = array(
                        'connection_timeout' => 3,
                        'default_socket_timeout' => 10
                    );
                    $configSOAP = array_merge($configSOAP, $timeout);
                }

                if (env('ACTIVAR_CACHE_SOAP', false)) {
                    $cache = array('cache_wsdl' => WSDL_CACHE_DISK);
                } else {
                    $cache = array('cache_wsdl' => WSDL_CACHE_NONE);
                }
                $configSOAP = array_merge($configSOAP, $cache);

                $ws = new SoapClient(env('wsrunificador'), $configSOAP);
                if ($ws) {
                    $ws->__setLocation(env('wsrunificador'));
                    $personaWs = $ws->obtenerPersona([
                        'rut' => $runCiudadano,
                        'dv' => Utiles::dv($runCiudadano)
                    ]);
                    if ($personaWs) {
                        $personaWs = $personaWs->obtenerPersonaResult;
                        if ($personaWs->Estado == 'OK' || $personaWs->Estado == 'OK-CACHE') {
                            return $personaWs;
                        } else {
                            if ($personaWs->Glosa == 'Persona no encontrada en BD') {
                                return Constante::DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL;
                            } elseif (strpos($personaWs->Glosa, 'No responde RC')) {
                                Log::error('actualizaPersonaWS: ' . Constante::MENSAJE_ERROR_SOAP_COMUNICACION_RUNIFICADOR . 'RUN : ' . $runCiudadano);
                                return Constante::DATOS_DE_CONTACTO_6_ERROR_REGISTRO_CIVIL;
                            } else {
                                Log::error('actualizaPersonaWS: ' . Constante::MENSAJE_ERROR_SOAP_COMUNICACION_RUNIFICADOR . 'RUN : ' . $runCiudadano);
                                return Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR;
                            }
                        }
                    } else {
                        return Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR;
                    }
                }

        } catch (Exception $e) {
            Log::error('actualizaPersonaWS (UTILES.php 186): ' . $e->getMessage());
            return Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR;
        }
        return $personaWs;
    }
}
