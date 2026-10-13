<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use DateTime;

class ValidaSolicitudTerceroClass
{
    public function __invoke($runCiudadano = null)
    {
        $cacheKeyPre = env('VALIDAD_SOLICITUD_TERCERO_CACHE_KEY_PRE','VALIDAD_SOLICITUD_TERCERO_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) env('VALIDAD_SOLICITUD_TERCERO_CACHE_TIME',15);
        $cacheActivar = config('cache.features.validad_tercero', true);

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
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/valida-solicitud-tercero/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataResponse["code"] = $apiresponse->code;
        $dataResponse["message"] = $apiresponse->message;
        $apiresponseData = json_decode(json_encode($apiresponse->data), true);
        $validaSolicitudTercero = $apiresponseData["solicitudes-validacion-tercero"];
        $responseValidaSolicitudTercero = $validaSolicitudTercero;

        foreach ($responseValidaSolicitudTercero as $key => $solicitud) {
            $mensajeEstado = Constante::MENSAJE_PENDIENTE_FIRMA_DIGITAL;
            $fechaValidacion = (strlen($solicitud["fecha-validacion"]) == 8) ?
                DateTime::createFromFormat('Ymd', $solicitud["fecha-validacion"])->format('Y-m-d') :
                $solicitud["fecha-validacion"];

            switch ($solicitud["id-estado-validacion"]) {
                case  Constante::VALIDACION_TERCERO_ESTADO_0_PENDIENTE:
                    $mensajeEstadoValidacion = Constante::MENSAJE_PENDIENTE_FIRMA_DIGITAL;
                    break;
                case Constante::VALIDACION_TERCERO_ESTADO_1_APROBADO:
                    $mensajeEstadoValidacion = Constante::MENSAJE_APROBADO;
                    break;
                case Constante::VALIDACION_TERCERO_ESTADO_2_RECHAZADO:
                    $mensajeEstadoValidacion = Constante::MENSAJE_RECHAZADA;
                    break;
                case Constante::VALIDACION_TERCERO_ESTADO_3_DESISTIDO:
                    $mensajeEstadoValidacion = Constante::MENSAJE_DESISTIDA;
                    break;
                default:
                    $mensajeEstadoValidacion = Constante::MENSAJE_PENDIENTE_FIRMA_DIGITAL;
                    break;
            }

            switch ($solicitud["id-estado-solicitud"]) {
                case Constante::ESTADO_SOLICITUD_5_APROBADO:
                    $mensajeEstado = Constante::MENSAJE_APROBADO;
                    break;
                case Constante::ESTADO_SOLICITUD_7_RECHAZADA:
                    $mensajeEstado = Constante::MENSAJE_RECHAZADA;
                    break;
                case Constante::ESTADO_SOLICITUD_9_DESISTIDA:
                    $mensajeEstado = Constante::MENSAJE_DESISTIDA;
                    break;
                case Constante::ESTADO_SOLICITUD_17_CONFIRMADO:
                    $mensajeEstado = Constante::MENSAJE_CONFIRMADO;
                    break;
                case Constante::ESTADO_SOLICITUD_19_BORRADOR_PENDIENTE_FIRMA_DIGITAL:
                    $mensajeEstado = Constante::MENSAJE_PENDIENTE_FIRMA_DIGITAL;
                    break;
                case Constante::ESTADO_SOLICITUD_11_EN_DIGITACION:
                    if ($solicitud->ID_SUB_ESTADO != Constante::ESTADO_SOLICITUD_14_INACTIVO_POR_DEFUNCION) {
                        $mensajeEstado = Constante::MENSAJE_BORRADOR;
                    }
                    break;
            }

            $responseValidaSolicitudTercero[$key]["estado-validacion"] = $mensajeEstadoValidacion;
            $responseValidaSolicitudTercero[$key]["fecha-validacion"] = $fechaValidacion;
            $responseValidaSolicitudTercero[$key]["estado-solicitud"] = $mensajeEstado;
        }
        $dataResponse["solicitudes-validacion-tercero"] = $responseValidaSolicitudTercero;
        return $dataResponse;
    }
}
