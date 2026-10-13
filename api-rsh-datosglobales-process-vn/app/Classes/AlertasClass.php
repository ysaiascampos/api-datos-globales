<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class AlertasClass
{
    public function __invoke($runCiudadano = null)
    {
        $respuesta = new MDSFApiResponse();

        $cacheKeyPre = env('ALERTAS_CACHE_KEY_PRE', 'ALERTAS_');
        $cacheKey = $cacheKeyPre . $runCiudadano;
        $ttl = (int) config('cache.ttl.short', 60);
        $cacheActivar = config('cache.features.alertas', true);

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
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/alertas/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);

        $apiresponse = json_decode(json_encode($apiresponse->data), true);
        $alertas = isset($apiresponse["alertas"])?$apiresponse["alertas"]:null;
        $dataResponse = ['mensaje-individual' => [],
            'mensaje-grupal' => []];

        if (isset($alertas['mensajes']) && count($alertas['mensajes']) > 0) {
            $runes = array();
            $elemento = $alertas['mensajes'];
            foreach ($elemento as $key => $item) {
                if ((int)$item["menor"] == 1 &&
                    (int)$item["run-menor"] > 0 &&
                    !in_array((int)$item["run-menor"], $runes)) {

                    $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/grupo-familiar-beneficiario-persona/' . $item["run"]);
                    $apiresponse = MDSFApiResponse::cast($apiresponse);
                    $gfasignacionfamiliarResponse = json_decode(json_encode($apiresponse->data), true);
                    $gfasignacionfamiliar = $gfasignacionfamiliarResponse["grupo-familiar-beneficiario-persona"];

                    $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/grupo-familiar-beneficiario-persona/' . $item["run-menor"]);
                    $apiresponse = MDSFApiResponse::cast($apiresponse);
                    $gfasignacionmenorResponse = json_decode(json_encode($apiresponse->data), true);
                    $gfasignacionmenor = $gfasignacionmenorResponse["grupo-familiar-beneficiario-persona"];

                    if (isset($gfasignacionfamiliar) && isset($gfasignacionmenor) && $gfasignacionfamiliar["grupfami-nuevofolio"] == $gfasignacionmenor["grupfami-nuevofolio"]) {
                        $runes[] = (int)$item["run-menor"];
                    } else {
                        unset($elemento[$key]);
                    }
                }
            }

            $personas = array();
            foreach ($runes as $item) {
                $persona = Utiles::getPersona($item);
                if (!is_int($persona)) {
                    $personas[$item]['SV_RUN_MENOR'] = $persona["per-run"];
                    $personas[$item]['SV_RUN_COMPLETO_MENOR'] = Utiles::ponePuntosAlRun($persona["per-run"],
                        $persona["per-dv"]);
                    $nombre = $persona["per-nombres"] . " " . $persona["per-apellidopaterno"] . " "
                        . $persona["per-apellidomaterno"];
                    $personas[$item]['SV_NOMBRE_MENOR'] = $nombre;
                }
            }
            $GrupoFamiliarExportClass = new GrupoFamiliarExportClass;
            $responseGrupoFamiliarExport = $GrupoFamiliarExportClass($runCiudadano);
            $grupoFamiliarExport = $responseGrupoFamiliarExport["grupo-familiar-export"];

            $calle = Utiles::obtenerCalleExport($grupoFamiliarExport["grupfami-c-calle"], $grupoFamiliarExport["grupfami-n-calle"],
                $grupoFamiliarExport["grupfami-comunaine"], $grupoFamiliarExport["grupfami-localidad"]);
            $direccion = "Calle no encontrada" != $calle ? $calle : "";
            $direccion .= isset($grupoFamiliarExport["grupfami-numdomicilio"]) ? " " .
                $grupoFamiliarExport["grupfami-numdomicilio"] : "";

            $ComunaIneClass = new ComunaIneClass;
            $responseComunaIne = $ComunaIneClass($grupoFamiliarExport["grupfami-comunaine"]);
            $comunaIne = $responseComunaIne["comuna-ine"];

            $direccion .= isset($comunaIne["descripcion"]) ? ", " . $comunaIne["descripcion"] : "";

            $region = null;
            if ($comunaIne) {
                $RegionPorComunaIneClass = new RegionPorComunaIneClass;
                $responseRegionPorComunaIne = $RegionPorComunaIneClass($grupoFamiliarExport["grupfami-comunaine"]);
                $region = $responseRegionPorComunaIne["region"];
            }
            $direccion .= isset($region["descripcion"]) ? ", " . $region["descripcion"] : "";

            $tipoSolicitudClass = new TipoSolicitudClass;
            $responseTipos = $tipoSolicitudClass();
            $tipos = isset($responseTipos['data']['tipo-solicitud']) ?
                $responseTipos['data']['tipo-solicitud']: [];
            foreach ($elemento as $key => $item) {
                if ((int)$item["menor"] == 1 && isset($personas[$item["run-menor"]])) {
                    $runMenor = $personas[$item["run-menor"]]['SV_RUN_COMPLETO_MENOR'];
                    $item["mensaje"] = str_replace("SV_RUN_MENOR", $runMenor, $item["mensaje"]);
                    $item["mensaje-correo"] = str_replace("SV_RUN_MENOR", $runMenor, $item["mensaje-correo"]);
                    $nombre = $personas[$item["run-menor"]]['SV_NOMBRE_MENOR'];
                    $item["mensaje"] = str_replace("SV_NOMBRE_MENOR", $nombre, $item["mensaje"]);
                    $item["mensaje-correo"] = str_replace("SV_NOMBRE_MENOR", $nombre, $item["mensaje-correo"]);
                    $item["run-menor"] = $runMenor;
                }
                $item["mensaje"] = str_replace("DIRECCION_RSH_CARTOLA", $direccion, $item["mensaje"]);
                $item["mensaje-correo"] = str_replace("DIRECCION_RSH_CARTOLA", $direccion, $item["mensaje-correo"]);
                if (!empty($item["periodo-rsh"])) {
                    $formato = self::formato_periodorsh($item["periodo-rsh"]);
                    if ($formato === 0) {
                        $formato = '';
                    }
                    $item["mensaje"] = str_replace("SV_PERIODO_RSH", $formato, $item["mensaje"]);
                }
                $id_tipo_solicitud_1 = $item['tipo-sol-relevante-1'] ?? null;
                $item['desc_link_1'] = $id_tipo_solicitud_1 && isset($tipos[$id_tipo_solicitud_1]['desc_link']) ?
                    $tipos[$id_tipo_solicitud_1]['desc_link']: null;

                $id_tipo_solicitud_2 = $item['tipo-sol-relevante-2'] ?? null;
                $item['desc_link_2'] = $id_tipo_solicitud_2 && isset($tipos[$id_tipo_solicitud_2]['desc_link']) ?
                    $tipos[$id_tipo_solicitud_2]['desc_link']: null;

                $id_tipo_solicitud_3 = $item['tipo-sol-relevante-3'] ?? null;
                $item['desc_link_3'] = $id_tipo_solicitud_3 && isset($tipos[$id_tipo_solicitud_3]['desc_link']) ?
                    $tipos[$id_tipo_solicitud_3]['desc_link']: null;

                $elemento[$key] = $item;
            }

            foreach ($elemento as $key => $item) {
                if ($item['run'] != 0
                    && $item['destinatario-mens'] == 2
                    && (int)$item['menor'] < 1) {
                    $dataResponse['mensaje-individual'][] = $item;
                } else {
                    $dataResponse['mensaje-grupal'][] = $item;
                }
            }
        }
        return $dataResponse;
    }

    public static function formato_periodorsh($periodo_rsh){
        if(!empty($periodo_rsh)){
            $anoperiodo=substr($periodo_rsh,0,4);
            $mes=substr($periodo_rsh,4,5);
            switch($mes){
                case 01:
                    $mesperiodo=Constante::MES01;
                    break;
                case 02:
                    $mesperiodo=Constante::MES02;
                    break;
                case 03:
                    $mesperiodo=Constante::MES03;
                    break;
                case 04:
                    $mesperiodo=Constante::MES04;
                    break;
                case 05:
                    $mesperiodo=Constante::MES05;
                    break;
                case 06:
                    $mesperiodo=Constante::MES06;
                    break;
                case 07:
                    $mesperiodo=Constante::MES07;
                    break;
                case 8:
                    $mesperiodo=Constante::MES08;
                    break;
                case 9:
                    $mesperiodo=Constante::MES09;
                    break;
                case 10:
                    $mesperiodo=Constante::MES10;
                    break;
                case 11:
                    $mesperiodo=Constante::MES11;
                    break;
                case 12:
                    $mesperiodo=Constante::MES12;
                    break;
                default:
                    break;
            }
            $periodo=$mesperiodo.'  de  '.$anoperiodo;
            return $periodo;
        }
        else
            return 0;
    }
}
