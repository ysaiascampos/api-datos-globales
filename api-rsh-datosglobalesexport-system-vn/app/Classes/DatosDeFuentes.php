<?php

namespace App\Classes;

use Exception;
use App\Constantes\Constante;
use App\Models\FuenteExport;

class DatosDeFuentes
{
    public function __invoke()
    {
        $filePath =storage_path("../app/Classes/DatosDeFuentes.php");
        if(!file_exists($filePath)){
            $cacheKey = 'servicio-fuentes-ingresos';
        }else{
            $version = filemtime($filePath);
            $cacheKey = 'servicio-fuentes-ingresos-'.$version;
        }
        $listaFuentes = [];
        try {
            /*if (Cache::has($cacheKey)) {
                $listaFuentes = Cache::get($cacheKey);
                if(!(isset($listaFuentes['tipos-fuentes'])
                    && count($listaFuentes['tipos-fuentes'])>0
                    && isset($listaFuentes['solicitudes']))){
                    Cache::forget($cacheKey);
                    $listaFuentes = [];
                }
            }*/
            //if (!Cache::has($cacheKey)) {
                $fuentes = FuenteExport::getFuentes();
                if($fuentes->count()){
                    $listaFuentes = ['tipos-fuentes' => null,
                        'solicitudes' =>[
                            60=>['fuentes'=>[]],
                            61=>['fuentes'=>[]],
                            62=>['fuentes'=>[]],
                            63=>['fuentes'=>[]],
                            64=>['fuentes'=>[]],
                            70=>['fuentes'=>[]],
                            71=>['fuentes'=>[]],
                            72=>['fuentes'=>[]],
                            77=>['fuentes'=>[]],
                            78=>['fuentes'=>[]]]
                    ];
                    foreach ($fuentes as $fuente) {
                        $idFuente = (string) $fuente->fue_id;
                        $tipoFuente = ['id-fuente' => $idFuente,
                            'descripcion' => (string)$fuente->fue_descripcion,
                            'descripcion-larga' => $fuente->fue_descripcion_larga,
                            'descripcion-municipal-larga' => $fuente->fue_descripcion_larga,
                            'sub-fuentes' => null
                        ];

                        if($fuente->tipo_solicitud_62 == "1"
                            && $fuente->tipo_solicitud_60 == "1"
                            && $fuente->tipo_solicitud_61 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_62_ACTUALIZACION_INGRESO_TRABAJADOR_DEPENDIENTE_E_INDEPENDIENTE]['fuentes'][] = $idFuente;
                            $tipoFuente['sub-fuentes']['dependiente'] = [
                                'id-fuente' => (string)$fuente->fue_dependiente,
                                'nro-meses' => $fuente->n_meses_dependiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador dependiente'
                            ];
                            $tipoFuente['sub-fuentes']['independiente'] = [
                                'id-fuente' => (string)$fuente->fue_independiente,
                                'nro-meses' => $fuente->n_meses_independiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador independiente'
                            ];
                        }else if($fuente->tipo_solicitud_60 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_60_ACTUALIZACION_INGRESO_TRABAJADOR_DEPENDIENTE]['fuentes'][] = $idFuente;
                            $tipoFuente['sub-fuentes']['dependiente'] = [
                                'id-fuente' => (string)$fuente->fue_dependiente,
                                'nro-meses' => $fuente->n_meses_dependiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador dependiente'
                            ];
                            $tipoFuente['sub-fuentes']['independiente'] = [
                                'id-fuente' => (string)$fuente->fue_independiente,
                                'nro-meses' => $fuente->n_meses_independiente,
                                'obligatorio' => '0',
                                'descripcion-municipal' => 'Ingresos como trabajador independiente'
                            ];

                        }else if($fuente->tipo_solicitud_61 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_61_ACTUALIZACION_INGRESO_TRABAJADOR_INDEPENDIENTE]['fuentes'][] = $idFuente;
                            $tipoFuente['sub-fuentes']['independiente'] = [
                                'id-fuente' => (string)$fuente->fue_independiente,
                                'nro-meses' => $fuente->n_meses_independiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador independiente'
                            ];
                            $tipoFuente['sub-fuentes']['dependiente'] = [
                                'id-fuente' => (string)$fuente->fue_dependiente,
                                'nro-meses' => $fuente->n_meses_dependiente,
                                'obligatorio' => '0',
                                'descripcion-municipal' => 'Ingresos como trabajador dependiente'
                            ];

                        }
                        if($fuente->tipo_solicitud_63 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_63_ACTUALIZACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION]['fuentes'][] = $idFuente;
                            if((int)$fuente->fue_pens_1 > 0){
                                $tipoFuente['sub-fuentes']['pension'] = [
                                    'id-fuente' => (string)$fuente->fue_pens_1,
                                    'nro-meses' => $fuente->n_meses_independiente,
                                    'obligatorio' => '1',
                                    'descripcion-municipal' => 'Ingresos por pensión y/o jubilación'
                                ];
                            }
                            if((int)$fuente->fue_pens_2 > 0) {
                                $tipoFuente['sub-fuentes']['sii'] = [
                                    'id-fuente' => (string)$fuente->fue_pens_2,
                                    'nro-meses' => $fuente->n_meses_dependiente,
                                    'obligatorio' => '1',
                                    'descripcion-municipal' => 'Ingresos por Servicio de Impuestos Internos'
                                ];
                            }
                        }
                        if($fuente->tipo_solicitud_64 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_64_ACTUALIZACION_DE_INGRESOS_DE_CAPITAL]['fuentes'][] = $idFuente;
                        }
                        if($fuente->tipo_solicitud_78 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_78_RECTIFICACION_INGRESO_DEPENDIENTE_E_INDEPENDIENTE]['fuentes'][] = $idFuente;
                            $tipoFuente['sub-fuentes']['dependiente'] = [
                                'id-fuente' => (string)$fuente->fue_dependiente,
                                'nro-meses' => $fuente->n_meses_dependiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador dependiente'
                            ];
                            $tipoFuente['sub-fuentes']['independiente'] = [
                                'id-fuente' => (string)$fuente->fue_independiente,
                                'nro-meses' => $fuente->n_meses_independiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador independiente'
                            ];
                        }
                        if ($fuente->tipo_solicitud_70 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_70_RECTIFICACION_INGRESO_TRABAJADOR_DEPENDIENTE]['fuentes'][] = $idFuente;
                            $tipoFuente['sub-fuentes']['dependiente'] = [
                                'id-fuente' => (string)$fuente->fue_dependiente,
                                'nro-meses' => $fuente->n_meses_dependiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador dependiente'
                            ];
                            $tipoFuente['sub-fuentes']['independiente'] = [
                                'id-fuente' => (string)$fuente->fue_independiente,
                                'nro-meses' => $fuente->n_meses_independiente,
                                'obligatorio' => '0',
                                'descripcion-municipal' => 'Ingresos como trabajador independiente'
                            ];

                        }
                        if ($fuente->tipo_solicitud_71 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_71_RECTIFICACION_INGRESO_TRABAJADOR_INDEPENDIENTE]['fuentes'][] = $idFuente;
                            $tipoFuente['sub-fuentes']['independiente'] = [
                                'id-fuente' => (string)$fuente->fue_independiente,
                                'nro-meses' => $fuente->n_meses_independiente,
                                'obligatorio' => '1',
                                'descripcion-municipal' => 'Ingresos como trabajador independiente'
                            ];
                            $tipoFuente['sub-fuentes']['dependiente'] = [
                                'id-fuente' => (string)$fuente->fue_dependiente,
                                'nro-meses' => $fuente->n_meses_dependiente,
                                'obligatorio' => '0',
                                'descripcion-municipal' => 'Ingresos como trabajador dependiente'
                            ];

                        }
                        if ($fuente->tipo_solicitud_77 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_77_RECTIFICACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION]['fuentes'][] = $idFuente;
                            if((int)$fuente->fue_pens_1 > 0){
                                $tipoFuente['sub-fuentes']['pension'] = [
                                    'id-fuente' => (string)$fuente->fue_pens_1,
                                    'nro-meses' => $fuente->n_meses_independiente,
                                    'obligatorio' => '1',
                                    'descripcion-municipal' => 'Ingresos por pensión y/o jubilación'
                                ];
                            }
                            if((int)$fuente->fue_pens_2 > 0) {
                                $tipoFuente['sub-fuentes']['sii'] = [
                                    'id-fuente' => (string)$fuente->fue_pens_2,
                                    'nro-meses' => $fuente->n_meses_dependiente,
                                    'obligatorio' => '1',
                                    'descripcion-municipal' => 'Ingresos por Servicio de Impuestos Internos'
                                ];
                            }
                        }
                        if ($fuente->tipo_solicitud_72 == "1") {
                            $listaFuentes['solicitudes'][Constante::TIPO_SOLICITUD_72_RECTIFICACION_DE_INGRESOS_DE_CAPITAL]['fuentes'][] = $idFuente;
                        }
                        $listaFuentes['tipos-fuentes'][$idFuente] = $tipoFuente;
                    }
                    //Cache::put($cacheKey, $listaFuentes, now()->addMinutes(Constante::CACHE_API_MINUTOS));
                }
            //}
        } catch (Exception $ex) {
            throw $ex;
        }
        return $listaFuentes;
    }

}
