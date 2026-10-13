<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\BeneficiarioGrupoFamiliar;
use App\Models\GrupoFamiliar;
use App\Models\Personas;
use App\Models\PersonasTokenMail;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\DB;

class DatosDeSolicitud
{
    protected $hidden = ['rn'];
    protected $filled = [];

    public function getModel()
    {
        return new Solicitud();
    }

    public function getSolicitudDatosGlobales2($persona, $runsPorIdGrupoFamiliar)
    {
        /*
         * Se utiliza para Query basadas
         * Solicitudes en estado de borrador
         */
//        $perId = null;
        $datosDeSolicitud = null;

        if ($persona) {
            $perId = $persona->per_id;
            $runCiudadano = $persona->per_run;
        }
        if (!(is_null($perId))) {
            $consultaSegunOrigenRegistro = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->where('run_ciudadano', $runCiudadano)
                ->get();

            $datosDeSolicitudPorDatoNuevo = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->whereIn('id_tipo_solicitud', Constante::$solicitudEspecialesCONST)
                ->whereNotIN('id_estado', [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION])
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->where('dato_nuevo', $perId)
                ->get();

            $datosDeSolicitudPorDatoActual = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->whereIn('id_tipo_solicitud', Constante::$solicitudesActualizacionESO)
                ->whereNotIN('id_estado', [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION])
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->where('dato_actual', $perId)
                ->get();

            $datosDeSolicitudPorRunGrupoFamiliar = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->whereIn('id_tipo_solicitud', [Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                    Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA])
                ->whereNotIN('id_estado', [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION])
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                ->get();

            $a = (count($datosDeSolicitudPorDatoNuevo)) > 0 ? $datosDeSolicitudPorDatoNuevo->toArray() : [];
            $b = (count($datosDeSolicitudPorDatoActual)) > 0 ? $datosDeSolicitudPorDatoActual->toArray() : [];
            $c = (count($datosDeSolicitudPorRunGrupoFamiliar)) > 0 ? $datosDeSolicitudPorRunGrupoFamiliar->toArray() : [];
            $d = (count($consultaSegunOrigenRegistro)) > 0 ? $consultaSegunOrigenRegistro->toArray() : [];

            //-----------------------------------------------------------------//

            // Limpieza de solicitudes Individuales v/s otros por mi

            $solicitudesTEMP = $a;
            if (count($a) && (count($b))) {
                $largoSolicitudA = count($a);
                $largoSolicitudB = count($b);
                if ($largoSolicitudA > 0 && $largoSolicitudB > 0) {
                    for ($i = 0; $i < $largoSolicitudA; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudB; $ii++) {
                            if ($a[$i] == $b[$ii]) {
                                unset($solicitudesTEMP[$i]);
                            }
                        }
                    }
                }
            }
            $conjuntoA = array_merge($solicitudesTEMP, $b);

            $solicitudesTEMP2 = $c;
            if (count($c) && (count($d))) {
                $largoSolicitudC = count($c);
                $largoSolicitudD = count($d);
                if ($largoSolicitudC > 0 && $largoSolicitudD > 0) {
                    for ($i = 0; $i < $largoSolicitudC; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudD; $ii++) {
                            if ($c[$i] == $d[$ii]) {
                                unset($solicitudesTEMP2[$i]);
                            }
                        }
                    }
                }
            }
            $conjuntoB = array_merge($solicitudesTEMP2, $d);

            $solicitudesSemiFinal = $conjuntoA;
            if (count($conjuntoA) && (count($conjuntoB))) {
                $largoSolicitudConjuntoA = count($conjuntoA);
                $largoSolicitudConjuntoB = count($conjuntoB);
                if ($largoSolicitudConjuntoA > 0 && $largoSolicitudConjuntoB > 0) {
                    for ($i = 0; $i < $largoSolicitudConjuntoA; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudConjuntoB; $ii++) {
                            if ($conjuntoA[$i] == $conjuntoB[$ii]) {
                                unset($solicitudesSemiFinal[$ii]);
                            }
                        }
                    }
                }
            }
            $conjuntoFINAL = array_merge($solicitudesSemiFinal, $conjuntoB);
            if (count($conjuntoFINAL) > 0) {
                for ($i = 0; $i < count($conjuntoFINAL); $i++) {
                    $idSolicitudes [] = $conjuntoFINAL[$i]['id_solicitud'];
                }
                $datosDeSolicitud = self::getSolicitudesBasicParaCiudadano($idSolicitudes);
            }
            /* "presuntamente" esto hace lo mismo que el codigo de arriba
            ‌‌$idSolicitudes = array_unique(array_column(array_merge($a, $b, $c, $d), 'id_solicitud'));
            $datosDeSolicitud = self::getSolicitudesDetalladasParaCiudadano($idSolicitudes);
            */
            if ($datosDeSolicitud) {
                $datosDeSolicitud->map(function ($item) {
                    $item->{'ciudadano_asociado'} = null;
                    if (in_array($item['id_tipo_solicitud'], [
                        Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                    ])) {
                        $datoPersona = Personas::find($item['dato_nuevo']);
                        $run = $datoPersona['per_run']."-".$datoPersona['per_dv'];
                        $nombre = ucwords(strtolower(
                            Utiles::cambiaTildeASuLetra($datoPersona['per_nombres']).' '.
                            Utiles::cambiaTildeASuLetra($datoPersona['per_apellidopaterno'])));
                        $item->{'ciudadano_asociado'} = $run." ".$nombre;
                        $item->{'run_titular'} = $datoPersona['per_run'];
                    }
                    if (in_array($item['id_tipo_solicitud'], [
                        Constante::TIPO_SOLICITUD_5_ACTUALIZACION_DE_MODULO_SALUD,
                        Constante::TIPO_SOLICITUD_6_ACTUALIZACION_DE_MODULO_EDUCACION,
                        Constante::TIPO_SOLICITUD_14_ACTUALIZACION_OCUPACION_DE_INGRESOS,
                    ])) {
                        $datoPersona = Personas::find($item['dato_actual']);
                        $run = $datoPersona['per_run']."-".$datoPersona['per_dv'];
                        $nombre = ucwords(strtolower(
                            Utiles::cambiaTildeASuLetra($datoPersona['per_nombres']).' '.
                            Utiles::cambiaTildeASuLetra($datoPersona['per_apellidopaterno'])));
                        $item->{'ciudadano_asociado'} = $run." ".$nombre;
                        $item->{'run_titular'} = $datoPersona['per_run'];
                    }
                });
            }
        }

        if ($datosDeSolicitud == null) {
            $datosDeSolicitud = $consultaSegunOrigenRegistro;
        }

        return $datosDeSolicitud;
    }

    public function getBasicSolicitudDatosGlobales2($persona, $runsPorIdGrupoFamiliar)
    {
        /*
         * Se utiliza para Query basadas
         * Solicitudes en estado de borrador
         */
//        $perId = null;
        $datosDeSolicitud = null;

        if ($persona) {
            $perId = $persona->per_id;
            $runCiudadano = $persona->per_run;
        }
        if (!(is_null($perId))) {
            $consultaSegunOrigenRegistro = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->where('run_ciudadano', $runCiudadano)
                ->get();

            $datosDeSolicitudPorDatoNuevo = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->whereIn('id_tipo_solicitud', Constante::$solicitudEspecialesCONST)
                ->whereNotIN('id_estado', [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION])
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->where('dato_nuevo', $perId)
                ->get();

            $datosDeSolicitudPorDatoActual = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->whereIn('id_tipo_solicitud', Constante::$solicitudesActualizacionESO)
                ->whereNotIN('id_estado', [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION])
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->where('dato_actual', $perId)
                ->get();

            $datosDeSolicitudPorRunGrupoFamiliar = $this->getModel()
                ->select(['id_solicitud', 'id_tipo_solicitud'])
                ->whereIn('id_tipo_solicitud', [Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                    Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA])
                ->whereNotIN('id_estado', [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION])
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                ->get();

            $a = (count($datosDeSolicitudPorDatoNuevo)) > 0 ? $datosDeSolicitudPorDatoNuevo->toArray() : [];
            $b = (count($datosDeSolicitudPorDatoActual)) > 0 ? $datosDeSolicitudPorDatoActual->toArray() : [];
            $c = (count($datosDeSolicitudPorRunGrupoFamiliar)) > 0 ? $datosDeSolicitudPorRunGrupoFamiliar->toArray() : [];
            $d = (count($consultaSegunOrigenRegistro)) > 0 ? $consultaSegunOrigenRegistro->toArray() : [];

            //-----------------------------------------------------------------//

            // Limpieza de solicitudes Individuales v/s otros por mi

            $solicitudesTEMP = $a;
            if (count($a) && (count($b))) {
                $largoSolicitudA = count($a);
                $largoSolicitudB = count($b);
                if ($largoSolicitudA > 0 && $largoSolicitudB > 0) {
                    for ($i = 0; $i < $largoSolicitudA; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudB; $ii++) {
                            if ($a[$i] == $b[$ii]) {
                                unset($solicitudesTEMP[$i]);
                            }
                        }
                    }
                }
            }
            $conjuntoA = array_merge($solicitudesTEMP, $b);

            $solicitudesTEMP2 = $c;
            if (count($c) && (count($d))) {
                $largoSolicitudC = count($c);
                $largoSolicitudD = count($d);
                if ($largoSolicitudC > 0 && $largoSolicitudD > 0) {
                    for ($i = 0; $i < $largoSolicitudC; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudD; $ii++) {
                            if ($c[$i] == $d[$ii]) {
                                unset($solicitudesTEMP2[$i]);
                            }
                        }
                    }
                }
            }
            $conjuntoB = array_merge($solicitudesTEMP2, $d);

            $solicitudesSemiFinal = $conjuntoA;
            if (count($conjuntoA) && (count($conjuntoB))) {
                $largoSolicitudConjuntoA = count($conjuntoA);
                $largoSolicitudConjuntoB = count($conjuntoB);
                if ($largoSolicitudConjuntoA > 0 && $largoSolicitudConjuntoB > 0) {
                    for ($i = 0; $i < $largoSolicitudConjuntoA; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudConjuntoB; $ii++) {
                            if ($conjuntoA[$i] == $conjuntoB[$ii]) {
                                unset($solicitudesSemiFinal[$ii]);
                            }
                        }
                    }
                }
            }
            $conjuntoFINAL = array_merge($solicitudesSemiFinal, $conjuntoB);
            if (count($conjuntoFINAL) > 0) {
                for ($i = 0; $i < count($conjuntoFINAL); $i++) {
                    $idSolicitudes [] = $conjuntoFINAL[$i]['id_solicitud'];
                }
                $datosDeSolicitud = self::getSolicitudesBasic2ParaCiudadano($idSolicitudes);
            }
            /* "presuntamente" esto hace lo mismo que el codigo de arriba
            ‌‌$idSolicitudes = array_unique(array_column(array_merge($a, $b, $c, $d), 'id_solicitud'));
            $datosDeSolicitud = self::getSolicitudesDetalladasParaCiudadano($idSolicitudes);
            */
            if ($datosDeSolicitud) {
                $datosDeSolicitud->map(function ($item) {
                    $item->{'ciudadano_asociado'} = null;
                    if (in_array($item['id_tipo_solicitud'], [
                        Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                    ])) {
                        $datoPersona = Personas::find($item['dato_nuevo']);
                        $run = $datoPersona['per_run']."-".$datoPersona['per_dv'];
                        $nombre = ucwords(strtolower(
                            Utiles::cambiaTildeASuLetra($datoPersona['per_nombres']).' '.
                            Utiles::cambiaTildeASuLetra($datoPersona['per_apellidopaterno'])));
                        $item->{'ciudadano_asociado'} = $run." ".$nombre;
                        $item->{'run_titular'} = $datoPersona['per_run'];
                    }
                    if (in_array($item['id_tipo_solicitud'], [
                        Constante::TIPO_SOLICITUD_5_ACTUALIZACION_DE_MODULO_SALUD,
                        Constante::TIPO_SOLICITUD_6_ACTUALIZACION_DE_MODULO_EDUCACION,
                        Constante::TIPO_SOLICITUD_14_ACTUALIZACION_OCUPACION_DE_INGRESOS,
                    ])) {
                        $datoPersona = Personas::find($item['dato_actual']);
                        $run = $datoPersona['per_run']."-".$datoPersona['per_dv'];
                        $nombre = ucwords(strtolower(
                            Utiles::cambiaTildeASuLetra($datoPersona['per_nombres']).' '.
                            Utiles::cambiaTildeASuLetra($datoPersona['per_apellidopaterno'])));
                        $item->{'ciudadano_asociado'} = $run." ".$nombre;
                        $item->{'run_titular'} = $datoPersona['per_run'];
                    }
                });
            }
        }

        if ($datosDeSolicitud == null) {
            $datosDeSolicitud = $consultaSegunOrigenRegistro;
        }

        return $datosDeSolicitud;
    }

    public function getSolicitudesDetalladasParaCiudadano($idSolicitudes)
    {
        $datosDeSolicitud = null;
        try {
            $datosDeSolicitud = $this->getModel()
                ->select(
                    DB::RAW('ab_solicitud.*'),
                    DB::RAW('ab_estado_solicitud.*'),
                    DB::RAW('ab_sub_estado_solicitud.*'),
                    DB::RAW('comunas_ine.*'),
                    DB::RAW('regiones.*'),
                    DB::RAW('ab_origen_registro.*'),
                    DB::RAW('(SELECT count(1) from ab_solicitud_adjuntos WHERE ab_solicitud_adjuntos.doc_idsolicitud=ab_solicitud.id_solicitud) as "numero_documentos"'))
                ->join('ab_estado_solicitud', 'ab_solicitud.id_estado', '=', 'ab_estado_solicitud.id_estado')
                ->leftJoin('ab_sub_estado_solicitud', 'ab_solicitud.id_sub_estado', '=',
                    'ab_sub_estado_solicitud.id_sub_estado')
                ->join('comunas_ine', 'ab_solicitud.com_id', '=', 'comunas_ine.com_id')
                ->join('regiones', 'comunas_ine.com_region', '=', 'regiones.reg_id')
                ->leftJoin('ab_origen_registro', 'ab_solicitud.origen_registro', '=',
                    'ab_origen_registro.id_origen_registro')
                //    ->where('id_tipo_solicitud', '<>', 28)// calle pendiente
                ->whereIn('id_solicitud', $idSolicitudes)
                //    ->whereIn('run_ciudadano', $runCiudadano)
                //    ->whereIn('vigente', array(Constante::VIGENTE_SOLICITUD_1_VIGENTE, self::VIGENTE_SOLICITUD_3_BORRADOR))
                ->orderBy('id_solicitud', 'desc');
            $datosDeSolicitud = $datosDeSolicitud->get();
            if (!$datosDeSolicitud) {
                throw new ExcepcionConsistencia(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                    Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE);
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        return $datosDeSolicitud;
    }

    public function getSolicitudesBasicParaCiudadano($idSolicitudes)
    {
        $datosDeSolicitud = null;
        try {
            $datosDeSolicitud = $this->getModel()
                ->select([
                    'ab_solicitud.id_solicitud',
                    'ab_solicitud.id_padre_solicitud',
                    'ab_solicitud.id_tipo_solicitud',
                    'ab_solicitud.id_sub_tipo_solicitud',
                    'ab_solicitud.solicitud',
                    'ab_solicitud.run_ciudadano',
                    'ab_solicitud.id_estado',
                    'ab_solicitud.id_sub_estado',
                    'ab_solicitud.dato_nuevo',
                    'ab_solicitud.dato_actual',
                    'ab_solicitud.vigente',
                    'ab_solicitud.canal',
                    'ab_solicitud.fecha_creacion',
                    'ab_solicitud.fecha_modificacion',
                    'ab_solicitud.fecha_asignacion',
                    'ab_solicitud.fecha_creacion_ciudadano',
                    'ab_solicitud.comentario',
                    'ab_solicitud.obs_solicitud',
                    'ab_solicitud.observacion_nivel_central',
                    'ab_solicitud.observaciones',
                    'ab_sub_estado_solicitud.nombre_sub_estado',
                    'comunas_ine.com_id',
                    'comunas_ine.com_descripcion',
                    'regiones.reg_id',
                    'regiones.reg_descripcion',
                    'ab_origen_registro.id_origen_registro',
                    'ab_origen_registro.nombre_origen_registro',
                    'ab_estado_solicitud.nombre_estado',
                ])
                ->join('ab_estado_solicitud', 'ab_solicitud.id_estado', '=', 'ab_estado_solicitud.id_estado')
                ->leftJoin('ab_sub_estado_solicitud', 'ab_solicitud.id_sub_estado', '=',
                    'ab_sub_estado_solicitud.id_sub_estado')
                ->join('comunas_ine', 'ab_solicitud.com_id', '=', 'comunas_ine.com_id')
                ->join('regiones', 'comunas_ine.com_region', '=', 'regiones.reg_id')
                ->leftJoin('ab_origen_registro', 'ab_solicitud.origen_registro', '=',
                    'ab_origen_registro.id_origen_registro')
                //    ->where('id_tipo_solicitud', '<>', 28)// calle pendiente
                ->whereIn('id_solicitud', $idSolicitudes)
                //    ->whereIn('run_ciudadano', $runCiudadano)
                //    ->whereIn('vigente', array(Constante::VIGENTE_SOLICITUD_1_VIGENTE, self::VIGENTE_SOLICITUD_3_BORRADOR))
                ->orderBy('id_solicitud', 'desc');
            $datosDeSolicitud = $datosDeSolicitud->get();
            if (!$datosDeSolicitud) {
                throw new ExcepcionConsistencia(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                    Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE);
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        return $datosDeSolicitud;
    }

    public function getSolicitudesBasic2ParaCiudadano($idSolicitudes)
    {
        $datosDeSolicitud = null;
        try {
            $datosDeSolicitud = $this->getModel()
                ->select([
                    'ab_solicitud.id_solicitud',
                    'ab_solicitud.id_tipo_solicitud',
                    'ab_solicitud.id_sub_tipo_solicitud',
                    'ab_solicitud.solicitud',
                    'ab_solicitud.run_ciudadano',
                    'ab_solicitud.id_estado',
                    'ab_solicitud.dato_nuevo',
                    'ab_solicitud.dato_actual',
                    'ab_solicitud.vigente',
                    'ab_solicitud.fecha_modificacion',
                ])
                ->join('ab_estado_solicitud', 'ab_solicitud.id_estado', '=', 'ab_estado_solicitud.id_estado')
                //->leftJoin('ab_sub_estado_solicitud', 'ab_solicitud.id_sub_estado', '=',
                //    'ab_sub_estado_solicitud.id_sub_estado')
                //->join('comunas_ine', 'ab_solicitud.com_id', '=', 'comunas_ine.com_id')
                //->join('regiones', 'comunas_ine.com_region', '=', 'regiones.reg_id')
                //->leftJoin('ab_origen_registro', 'ab_solicitud.origen_registro', '=',
                //    'ab_origen_registro.id_origen_registro')
                //    ->where('id_tipo_solicitud', '<>', 28)// calle pendiente
                ->whereIn('id_solicitud', $idSolicitudes)
                //    ->whereIn('run_ciudadano', $runCiudadano)
                //    ->whereIn('vigente', array(Constante::VIGENTE_SOLICITUD_1_VIGENTE, self::VIGENTE_SOLICITUD_3_BORRADOR))
                ->orderBy('id_solicitud', 'desc');
            $datosDeSolicitud = $datosDeSolicitud->get();
            if (!$datosDeSolicitud) {
                throw new ExcepcionConsistencia(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                    Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE);
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        return $datosDeSolicitud;
    }

    /**
     * Consulta solicitudes pendientes para cualquier miembro del grupo familiar (esquema asignacion)
     */
    public function getSolicitudesExcluyentesPorRunViaRun(
        $runCiudadano
    ) {
            $datosDeSolicitudPorRunCiudadano = Solicitud::SolicitudPorRunCiudadanoViaRunVigente1($runCiudadano);

            $datosDeSolicitudPorRunCiudadano1 = Solicitud::SolicitudPorRunCiudadanoViaRunVigente3($runCiudadano);

            $a = $datosDeSolicitudPorRunCiudadano->toArray();
            $b = $datosDeSolicitudPorRunCiudadano1->toArray();
            $datosDeSolicitudPorRunCiudadano = array_merge($a, $b);

        if (is_array($datosDeSolicitudPorRunCiudadano)) {
            return $datosDeSolicitudPorRunCiudadano;
        } else {
            return ($datosDeSolicitudPorRunCiudadano->toArray());
        }
    }
    public function getSolicitudesExcluyentesPorRunViaDatoNuevo(
        $perId
    ) {
        $datosDeSolicitudPorRunCiudadano = [];
        if (!(is_null($perId))) {
            $datosDeSolicitudPorRunCiudadano1 = Solicitud::SolicitudPorRunCiudadanoViaDatoNuevoVigente1($perId);

            // solo para solicitudes del tipo 5,6,14 (actualizacion)
            $datosDeSolicitudPorRunCiudadano2 = Solicitud::SolicitudPorRunCiudadanoViaDatoNuevoESOVigente1($perId);

            $datosDeSolicitudPorRunCiudadano3 = Solicitud::SolicitudPorRunCiudadanoViaDatoNuevoIncDesVigente3($perId);

            $a = $datosDeSolicitudPorRunCiudadano1->toArray();
            $b = $datosDeSolicitudPorRunCiudadano2->toArray();
            $c = $datosDeSolicitudPorRunCiudadano3->toArray();
            $datosDeSolicitudPorRunCiudadano = array_merge($a, $b, $c);
        }
        if (is_array($datosDeSolicitudPorRunCiudadano)) {
            return $datosDeSolicitudPorRunCiudadano;
        } else {
            return ($datosDeSolicitudPorRunCiudadano->toArray());
        }
    }

    /**
     * Consulta solicitudes pendientes para cualquier miembro del grupo familiar (esquema asignacion)
     */
    public function getSolicitudesExcluyentesPorRunGrupoFamiliar(
        $ciudadano
    ) {
        $runCiudadano = $ciudadano->PER_RUN;
        $datosDeSolicitudPorRunCiudadano = null;

            /*
             * Se utiliza para obtener las Solicitudes del grupo familiar, funcion de la cual se utiliza para
             * analizar y generar solicitudes-tramitables, al igual que las busquedas por RUN y DATO-NUEVO/ACTUAL
             */
            $datosDeSolicitudPorRunCiudadano1 = [];
            $datosDeSolicitudPorRunCiudadano2 = [];
            $datosDeSolicitudPorRunCiudadano3 = [];
            $datosDeSolicitudPorRunCiudadano4 = [];
            $beneficiariosDelGrupoFamiliar = BeneficiarioGrupoFamiliar::findBeneficiariosByRun($runCiudadano);
            $personaIncorporarDesvincular = $ciudadano;
            $runsPorIdGrupoFamiliar = array_column($beneficiariosDelGrupoFamiliar->toArray(), 'PER_RUN');
            $perIdporGrupoFamiliar = array_column($beneficiariosDelGrupoFamiliar->toArray(), 'PER_ID');
            // Elimina el RUN del usuario del grupo Familiar.
            if (in_array($runCiudadano, $runsPorIdGrupoFamiliar)) {
                $key = array_search($runCiudadano, $runsPorIdGrupoFamiliar);
                unset($runsPorIdGrupoFamiliar[$key]);
            }

            if ($personaIncorporarDesvincular) {
                if (in_array($personaIncorporarDesvincular->PER_ID, $perIdporGrupoFamiliar)) {
                    $key2 = array_search($personaIncorporarDesvincular->PER_ID, $perIdporGrupoFamiliar);
                    unset($perIdporGrupoFamiliar[$key2]);
                }

                if (count($runsPorIdGrupoFamiliar) > 0) {
                    $datosDeSolicitudPorRunCiudadano1 = $this->getModel()
                        ->select([
                            'id_solicitud',
                            'id_tipo_solicitud',
                            'solicitud',
                            'run_ciudadano',
                            'id_estado',
                            'dato_nuevo',
                            'dato_actual',
                        ])
                        ->whereNotIn('id_tipo_solicitud',
                            array_merge(Constante::$solicitudEspecialesCONST, Constante::$solicitudesActualizacionESO))
                        ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                            [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                        ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                        ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                        ->get();
                }
                if (count($perIdporGrupoFamiliar) > 0) {
                    $datosDeSolicitudPorRunCiudadano2 = $this->getModel()
                        ->select([
                            'id_solicitud',
                            'id_tipo_solicitud',
                            'solicitud',
                            'run_ciudadano',
                            'id_estado',
                            'dato_nuevo',
                            'dato_actual',
                        ])
                        ->whereIn('id_tipo_solicitud', Constante::$solicitudEspecialesCONST)
                        ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                            [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                        ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                        ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                        ->get();

                    $datosDeSolicitudPorRunCiudadano3 = $this->getModel()
                        ->select([
                            'id_solicitud',
                            'id_tipo_solicitud',
                            'solicitud',
                            'run_ciudadano',
                            'id_estado',
                            'dato_nuevo',
                            'dato_actual',
                        ])
                        ->whereIn('id_tipo_solicitud', Constante::$solicitudesActualizacionESO)
                        ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                            [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                        ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                        ->whereIn('dato_actual', $perIdporGrupoFamiliar)
                        ->get();

                    $datosDeSolicitudPorRunCiudadano4 = $this->getModel()
                        ->select([
                            'id_solicitud',
                            'id_tipo_solicitud',
                            'solicitud',
                            'run_ciudadano',
                            'id_estado',
                            'dato_nuevo',
                            'dato_actual',
                        ])
                        ->where('id_estado', Constante::ESTADO_SOLICITUD_19_BORRADOR_PENDIENTE_FIRMA_DIGITAL)
                        ->where('vigente', Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR)
                        ->whereIn('id_tipo_solicitud', Constante::$solicitudEspecialesCONST)// parentezco
                        ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                        ->get();
                }
                $a = (count($datosDeSolicitudPorRunCiudadano1)) > 0 ? $datosDeSolicitudPorRunCiudadano1->toArray() : [];
                $b = (count($datosDeSolicitudPorRunCiudadano2)) > 0 ? $datosDeSolicitudPorRunCiudadano2->toArray() : [];
                $c = (count($datosDeSolicitudPorRunCiudadano3)) > 0 ? $datosDeSolicitudPorRunCiudadano3->toArray() : [];
                $d = (count($datosDeSolicitudPorRunCiudadano4)) > 0 ? $datosDeSolicitudPorRunCiudadano4->toArray() : [];
                $datosDeSolicitudPorRunCiudadano = array_merge($a, $b, $c, $d);
            }
        if (is_array($datosDeSolicitudPorRunCiudadano)) {
            return $datosDeSolicitudPorRunCiudadano;
        } else {
            return ($datosDeSolicitudPorRunCiudadano->toArray());
        }
    }

    /**
     * Consulta solicitudes pendientes para cualquier miembro del grupo familiar (esquema asignacion)
     */
    public function getSolicitudesExcluyentesPorRunOtros(
        $ciudadano,
        $otrasSolicitudes = null,
        $verificaIncorporacionDesvinculacion = null,
        $todasSolicitudes = null
    ) {
        $runCiudadano = $ciudadano->PER_RUN;
        $datosDeSolicitudPorRunCiudadano = null;
        $arraySolicitudesExcluyentesPorRunCiudadano = [
            Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
            Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
            Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
            Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO,
            Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
            Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
            Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
        ];
        $arraySolicitudesExcluyentesPorDatoNuevoPerRun = [
            Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
            Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
        ];


            $beneficiariosDelGrupoFamiliar = BeneficiarioGrupoFamiliar::findBeneficiariosByRun($runCiudadano);
            $runsPorIdGrupoFamiliar = array_column($beneficiariosDelGrupoFamiliar->toArray(), 'PER_RUN');
            $datosDeSolicitudPorRunCiudadano = $this->getModel()
                ->select([
                    'id_solicitud',
                    'id_tipo_solicitud',
                    'solicitud',
                    'run_ciudadano',
                    'id_estado',
                    'dato_nuevo',
                    'dato_actual',
                ])
                ->whereIn('id_tipo_solicitud', array_merge($arraySolicitudesExcluyentesPorRunCiudadano,
                    $arraySolicitudesExcluyentesPorDatoNuevoPerRun))
                ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                    [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                ->get();

            if ($otrasSolicitudes != null) {
                // utilizado para todas aquellas "OTRAS" solicitudes distintasa de las declaradas en el array_merge
                $datosDeSolicitudPorRunCiudadano = $this->getModel()
                    ->select([
                        'id_solicitud',
                        'id_tipo_solicitud',
                        'solicitud',
                        'run_ciudadano',
                        'id_estado',
                        'dato_nuevo',
                        'dato_actual',
                    ])
                    ->whereNotIn('id_tipo_solicitud', array_merge($arraySolicitudesExcluyentesPorRunCiudadano,
                        $arraySolicitudesExcluyentesPorDatoNuevoPerRun))
                    ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                        [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                    ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                    ->whereIn('run_ciudadano', $runsPorIdGrupoFamiliar)
                    ->get();
            }
            if ($verificaIncorporacionDesvinculacion != null) {
                // Se utiliza en la incoporacion y desvinculacion en caso que la persona tenga una solicitud excluyente
                // que haya sido gestionada para esta persona, a diferencia de las anteriores que son para el grupo familiar
                $datosDeSolicitudPorRunCiudadano = $this->getModel()
                    ->select([
                        'id_solicitud',
                        'id_tipo_solicitud',
                        'solicitud',
                        'run_ciudadano',
                        'id_estado',
                        'dato_nuevo',
                        'dato_actual',
                    ])
                    ->whereIn('id_tipo_solicitud', $arraySolicitudesExcluyentesPorRunCiudadano)
                    ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                        [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                    ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                    ->where('run_ciudadano', $runCiudadano)
                    ->get();
            }
        if (is_array($datosDeSolicitudPorRunCiudadano)) {
            return $datosDeSolicitudPorRunCiudadano;
        } else {
            return ($datosDeSolicitudPorRunCiudadano->toArray());
        }
    }

}
