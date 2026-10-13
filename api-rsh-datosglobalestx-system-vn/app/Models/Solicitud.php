<?php

namespace App\Models;

use App\Constantes\Constante;
use App\Exceptions\ExcepcionConsistencia;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * App\Models\Solicitud
 *
 * @property int $ID_SOLICITUD
 * @property int|null $ID_TIPO_SOLICITUD
 * @property string|null $SOLICITUD
 * @property int|null $ID_USUARIO_CREACION
 * @property string|null $FECHA_CREACION
 * @property int|null $ID_USUARIO_MODIFICACION
 * @property string|null $FECHA_MODIFICACION
 * @property string|null $FECHA_RESOLUCION
 * @property int|null $ID_ESTADO
 * @property int|null $DATO_ACTUAL
 * @property int|null $ID_FUENTE_ACTUAL
 * @property int|null $DATO_NUEVO
 * @property int|null $ID_FUENTE_NUEVA
 * @property int|null $VIGENCIA_DATO
 * @property int|null $ID_PROCESO
 * @property int|null $ID_PADRE_SOLICITUD
 * @property string|null $EMAIL_CIUDADANO
 * @property string|null $FONO_CIUDADANO
 * @property int|null $RUN_CIUDADANO
 * @property int|null $COM_ID
 * @property int|null $ID_USUARIO_RESOLUCION
 * @property string|null $FONO_CIUDADANO2
 * @property int|null $ID_WARNING
 * @property int|null $ID_SUB_ESTADO
 * @property string|null $OBS_SOLICITUD
 * @property int|null $ID_USUARIO_ASIGNADO
 * @property string|null $FECHA_ASIGNACION
 * @property string|null $FECHA_SUB_ESTADO
 * @property int|null $ID_MOTIVO
 * @property int|null $ID_GRUPO_ANTIGUO
 * @property string|null $MD5_EXTRANJERO
 * @property int|null $ID_SUPERVISION
 * @property string|null $COMENTARIO
 * @property string|null $CANAL
 * @property string|null $RESPUESTA
 * @property int|null $NUEVOFOLIO
 * @property string|null $OBSERVACION_RECTIFICACION
 * @property int|null $AVISO_CORREO
 * @property int|null $RUN_INTEGRANTE
 * @property string|null $OBSERVACION_NIVEL_CENTRAL
 * @property int|null $ID_SUB_TIPO_SOLICITUD
 * @property int|null $ID_ESTADO_NC
 * @property int|null $VIGENTE
 * @property int|null $IDENTIDAD_ACREDITADA
 * @property int|null $FECHA_IDENT_ACREDITADA
 * @property int|null $ID_DOC_COMPLEMENTO
 * @property int|null $MIGRACION_COMUNA
 * @property string|null $FECHA_MIGRACION_COMUNA
 * @property int|null $ORIGEN_REGISTRO
 * @property int|null $FIRMA_SIMPLE
 * @property string|null $TOKEN_ACCESO
 * @property float|null $GEO_LATITUD_CREACION Metrica. geolocalización del lugar donde fue creada la solicitud
 * @property float|null $GEO_LONGITUD_CREACION Metrica. geolocalización del lugar donde fue creada la solicitud
 * @property string|null $HORA_INICIO_SOLICITUD Metrica. Hora en la que el usuario inicia la solicitud en el front-end para medición del tiempo que le toma realizar el proceso
 * @property string|null $HORA_TERMINO_SOLICITUD Metrica. Hora en la que se guardó el ultimo cambio en la solicitud
 * @property string|null $FECHA_CREACION_CIUDADANO
 * @method static Builder|Solicitud newModelQuery()
 * @method static Builder|Solicitud newQuery()
 * @method static Builder|Solicitud query()
 * @method static Builder|Solicitud whereAVISOCORREO($value)
 * @method static Builder|Solicitud whereCANAL($value)
 * @method static Builder|Solicitud whereCOMENTARIO($value)
 * @method static Builder|Solicitud whereCOMID($value)
 * @method static Builder|Solicitud whereDATOACTUAL($value)
 * @method static Builder|Solicitud whereDATONUEVO($value)
 * @method static Builder|Solicitud whereEMAILCIUDADANO($value)
 * @method static Builder|Solicitud whereFECHAASIGNACION($value)
 * @method static Builder|Solicitud whereFECHACREACION($value)
 * @method static Builder|Solicitud whereFECHACREACIONCIUDADANO($value)
 * @method static Builder|Solicitud whereFECHAIDENTACREDITADA($value)
 * @method static Builder|Solicitud whereFECHAMIGRACIONCOMUNA($value)
 * @method static Builder|Solicitud whereFECHAMODIFICACION($value)
 * @method static Builder|Solicitud whereFECHARESOLUCION($value)
 * @method static Builder|Solicitud whereFECHASUBESTADO($value)
 * @method static Builder|Solicitud whereFIRMASIMPLE($value)
 * @method static Builder|Solicitud whereFONOCIUDADANO($value)
 * @method static Builder|Solicitud whereFONOCIUDADANO2($value)
 * @method static Builder|Solicitud whereGEOLATITUDCREACION($value)
 * @method static Builder|Solicitud whereGEOLONGITUDCREACION($value)
 * @method static Builder|Solicitud whereHORAINICIOSOLICITUD($value)
 * @method static Builder|Solicitud whereHORATERMINOSOLICITUD($value)
 * @method static Builder|Solicitud whereIDDOCCOMPLEMENTO($value)
 * @method static Builder|Solicitud whereIDENTIDADACREDITADA($value)
 * @method static Builder|Solicitud whereIDESTADO($value)
 * @method static Builder|Solicitud whereIDESTADONC($value)
 * @method static Builder|Solicitud whereIDFUENTEACTUAL($value)
 * @method static Builder|Solicitud whereIDFUENTENUEVA($value)
 * @method static Builder|Solicitud whereIDGRUPOANTIGUO($value)
 * @method static Builder|Solicitud whereIDMOTIVO($value)
 * @method static Builder|Solicitud whereIDPADRESOLICITUD($value)
 * @method static Builder|Solicitud whereIDPROCESO($value)
 * @method static Builder|Solicitud whereIDSOLICITUD($value)
 * @method static Builder|Solicitud whereIDSUBESTADO($value)
 * @method static Builder|Solicitud whereIDSUBTIPOSOLICITUD($value)
 * @method static Builder|Solicitud whereIDSUPERVISION($value)
 * @method static Builder|Solicitud whereIDTIPOSOLICITUD($value)
 * @method static Builder|Solicitud whereIDUSUARIOASIGNADO($value)
 * @method static Builder|Solicitud whereIDUSUARIOCREACION($value)
 * @method static Builder|Solicitud whereIDUSUARIOMODIFICACION($value)
 * @method static Builder|Solicitud whereIDUSUARIORESOLUCION($value)
 * @method static Builder|Solicitud whereIDWARNING($value)
 * @method static Builder|Solicitud whereMD5EXTRANJERO($value)
 * @method static Builder|Solicitud whereMIGRACIONCOMUNA($value)
 * @method static Builder|Solicitud whereNUEVOFOLIO($value)
 * @method static Builder|Solicitud whereOBSERVACIONNIVELCENTRAL($value)
 * @method static Builder|Solicitud whereOBSERVACIONRECTIFICACION($value)
 * @method static Builder|Solicitud whereOBSSOLICITUD($value)
 * @method static Builder|Solicitud whereORIGENREGISTRO($value)
 * @method static Builder|Solicitud whereRESPUESTA($value)
 * @method static Builder|Solicitud whereRUNCIUDADANO($value)
 * @method static Builder|Solicitud whereRUNINTEGRANTE($value)
 * @method static Builder|Solicitud whereSOLICITUD($value)
 * @method static Builder|Solicitud whereTOKENACCESO($value)
 * @method static Builder|Solicitud whereVIGENCIADATO($value)
 * @method static Builder|Solicitud whereVIGENTE($value)
 * @mixin Eloquent
 */
class Solicitud extends Model
{
    protected $table = 'ab_solicitud';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    public static function getModel()
    {
        return new Solicitud();
    }

    public function TipoSolicitud()
    {
        return $this->hasOne(TipoSolicitud::class, 'id_tipo_solicitud', 'id_tipo_solicitud');
    }

    public function Ciudadano()
    {
        return $this->hasOne(Personas::class, 'per_run', 'run_ciudadano');
    }

    public function GrupoFamiliar()
    {
        return $this->hasOne(GrupoFamiliar::class, 'grupfami_id', 'dato_actual');
    }

    public function Integrante()
    {
        return $this->hasOne(BeneficiarioGrupoFamiliar::class, 'bnfgrupfami_idsolicitud', 'id_solicitud');
    }

    public function MotivoRectificacion()
    {
        return $this->hasOne(MotivoRectificacion::class, 'id', 'id_motivo');
    }

    public function DetalleTasacionVehiculos()
    {
        return $this->hasOne(DetalleTasacionVehiculos::class, 'veh_id', 'dato_nuevo');
    }

    public function EverSii()
    {
        return $this->hasOne(Sii::class, 'idsolicitud', 'id_solicitud');
    }

    public function setCanalAttribute($value)
    {
        $this->attributes['canal'] = Constante::$canalSaveSegunCanalMinuscula[strtolower($value)];
    }

    public function getRunTitularAttribute()
    {
        //ToDo: Normalizar esta función
        try {
            if (!isset($this->attributes['run_titular'])) {
                if($this->attributes['id_tipo_solicitud'] == Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE) {
                    $titular = Personas::find($this->attributes['dato_nuevo']);
                    $this->attributes['run_titular'] = isset($titular->per_run) ? $titular->per_run : null;
                }else{
                    $this->attributes['run_titular'] = null;
                }
            }
            return $this->attributes['run_titular'];
        } catch (Throwable $ex) {
            Log::error($ex);
            $this->attributes['run_titular'] = null;
            return null;
        }
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function findByIdSolicitud($idSolicitud, $tipoSolicitud = null)
    {
        $datosDeSolicitud = self::where('id_solicitud', $idSolicitud)
            ->whereIn('vigente', [
                Constante::VIGENTE_SOLICITUD_1_VIGENTE,
                Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR
            ]);
        if (!is_null($tipoSolicitud)) {
            $datosDeSolicitud->where('id_tipo_solicitud', $tipoSolicitud);
        }
        $datosDeSolicitud = $datosDeSolicitud->first();

        if (empty($datosDeSolicitud)) {
            throw ExcepcionConsistencia::new(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE)
                ->setContexto(['idSolicitud' => $idSolicitud, 'tipoSolicitud' => $tipoSolicitud]);
        }
        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud[]|Collection
     * @throws ExcepcionConsistencia
     */
    public static function findByIdPadreSolicitud($idPadreSolicitud, $tipoSolicitud = null)
    {
        $datosDeSolicitud = self::where('id_padre_solicitud', $idPadreSolicitud)
            ->whereIn('vigente', [
                Constante::VIGENTE_SOLICITUD_1_VIGENTE,
                Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR
            ]);
        if (!is_null($tipoSolicitud)) {
            if (is_array($tipoSolicitud)) {
                $datosDeSolicitud->whereIn('id_tipo_solicitud', $tipoSolicitud);
            } else {
                $datosDeSolicitud->where('id_tipo_solicitud', $tipoSolicitud);
            }
        }
        $datosDeSolicitud = $datosDeSolicitud->get();

        if (is_null($datosDeSolicitud)) {
            throw ExcepcionConsistencia::new(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE)
                ->setContexto(['idPadreSolicitud' => $idPadreSolicitud, 'tipoSolicitud' => $tipoSolicitud]);
        }
        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function findByRunCiudadano($runCiudadano)
    {
        $datosDeSolicitud = self::where('run_ciudadano', $runCiudadano)
            ->whereIn('vigente', [
                Constante::VIGENTE_SOLICITUD_1_VIGENTE,
                Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR
            ]);

        $datosDeSolicitud = $datosDeSolicitud->get();

        if (empty($datosDeSolicitud)) {
            throw ExcepcionConsistencia::new(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE)
                ->setContexto(['runCiudadano' => $runCiudadano]);
        }
        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function findByIdSolicitudRunVigente($dataSolicitud)
    {
        $datosDeSolicitud = self::where('id_solicitud', $dataSolicitud["id-solicitud"])
            ->where('id_tipo_solicitud', $dataSolicitud["id-tipo-solicitud"])
            ->where('vigente', Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR)
            ->where('id_estado', Constante::ESTADO_SOLICITUD_11_EN_DIGITACION)
            ->where('run_ciudadano', $dataSolicitud["run-ciudadano"])
            ->where('origen_registro', $dataSolicitud["origen-registro"])
            ->where('id_usuario_creacion', $dataSolicitud["run-usuario"])
            ->first();
        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function finBySolicitudEstado($dataSolicitud){
        $datosDeSolicitud = self::where('id_solicitud', $dataSolicitud["id-solicitud"])->first();
        return $datosDeSolicitud;
    }

    public static function SolicitudPorRunCiudadanoViaRunVigente3($runCiudadano)
    {
        try {
            $datosDeSolicitudPorRunCiudadanoVigente3 = self::getModel()
                ->select([
                    'id_solicitud',
                    'id_tipo_solicitud',
                    'id_sub_tipo_solicitud',
                    'solicitud',
                    'run_ciudadano',
                    'id_estado',
                    'dato_nuevo',
                    'dato_actual',
                    'vigente',
                    'fecha_modificacion',
                ])
                ->where('id_estado', Constante::ESTADO_SOLICITUD_19_BORRADOR_PENDIENTE_FIRMA_DIGITAL)
                ->where('vigente', Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR)
                ->where('run_ciudadano', $runCiudadano)
                ->get();

        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeSolicitudPorRunCiudadanoVigente3;
    }

    public static function SolicitudPorRunCiudadanoViaRunVigente1($runCiudadano)
    {
        try {
            $datosDeSolicitudPorRunCiudadanoVigente1 = self::getModel()
                ->select([
                    'id_solicitud',
                    'id_tipo_solicitud',
                    'id_sub_tipo_solicitud',
                    'solicitud',
                    'run_ciudadano',
                    'id_estado',
                    'dato_nuevo',
                    'dato_actual',
                    'vigente',
                    'fecha_modificacion',
                ])
                ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                    [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->where('run_ciudadano', $runCiudadano)
                ->get();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeSolicitudPorRunCiudadanoVigente1;
    }

    public static function SolicitudPorRunCiudadanoViaDatoNuevoVigente1($perId)
    {
        try {
            $datosDeSolicitudPorRunCiudadanoVigente1 = self::getModel()
                ->select([
                    'id_solicitud',
                    'id_tipo_solicitud',
                    'id_sub_tipo_solicitud',
                    'solicitud',
                    'run_ciudadano',
                    'id_estado',
                    'dato_nuevo',
                    'dato_actual',
                    'vigente',
                    'fecha_modificacion',
                ])
                ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                    [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->whereIn('id_tipo_solicitud', Constante::$solicitudBusquedaDatoNuevo)// parentezco
                ->where('dato_nuevo', $perId)
                ->get();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeSolicitudPorRunCiudadanoVigente1;
    }

    public static function SolicitudPorRunCiudadanoViaDatoNuevoESOVigente1($perId)
    {
        try {
            $datosDeSolicitudPorRunCiudadanoVigente1 = self::getModel()
                ->select([
                    'id_solicitud',
                    'id_tipo_solicitud',
                    'id_sub_tipo_solicitud',
                    'solicitud',
                    'run_ciudadano',
                    'id_estado',
                    'dato_nuevo',
                    'dato_actual',
                    'vigente',
                    'fecha_modificacion',
                ])
                ->whereNotIn('id_estado', array_merge(Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS,
                    [Constante::ESTADO_SOLICITUD_17_CONFIRMADO]))
                ->whereIn('id_tipo_solicitud', Constante::$solicitudesActualizacionESO)
                ->where('vigente', Constante::VIGENTE_SOLICITUD_1_VIGENTE)
                ->where('dato_actual', $perId)
                ->get();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeSolicitudPorRunCiudadanoVigente1;
    }

    public static function SolicitudPorRunCiudadanoViaDatoNuevoIncDesVigente3($perId)
    {
        try {
            $datosDeSolicitudPorRunCiudadano = self::getModel()
                ->select([
                    'id_solicitud',
                    'id_tipo_solicitud',
                    'id_sub_tipo_solicitud',
                    'solicitud',
                    'run_ciudadano',
                    'id_estado',
                    'dato_nuevo',
                    'dato_actual',
                    'vigente',
                    'fecha_modificacion',
                ])
                ->where('id_estado', Constante::ESTADO_SOLICITUD_19_BORRADOR_PENDIENTE_FIRMA_DIGITAL)
                ->where('vigente', Constante::VIGENTE_SOLICITUD_3_SOLICITUD_BORRADOR)
                ->whereIn('id_tipo_solicitud', Constante::$solicitudBusquedaDatoNuevo)// parentezco
                ->where('dato_nuevo', $perId)
                ->get();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeSolicitudPorRunCiudadano;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function findAprobateByTipoAndRunCiudadano($tipoSolicitud, $runCiudadano)
    {
        $datosDeSolicitud = self::where('run_ciudadano', $runCiudadano)
            ->select([
                'id_solicitud',
                'id_tipo_solicitud',
                'id_sub_tipo_solicitud',
                'solicitud',
                'run_ciudadano',
                'id_estado',
                'dato_nuevo',
                'dato_actual',
                'vigente',
                'fecha_modificacion',
            ])
            ->where('id_tipo_solicitud', $tipoSolicitud)
            ->where('id_estado', Constante::ESTADO_SOLICITUD_5_APROBADO)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->first();

        return $datosDeSolicitud;
    }

    public static function findAprobateByTipoAndPerId($tipoSolicitud, $perId)
    {
        $datosDeSolicitud = self::where('dato_nuevo', $perId)
            ->select([
                'id_solicitud',
                'id_tipo_solicitud',
                'id_sub_tipo_solicitud',
                'solicitud',
                'run_ciudadano',
                'id_estado',
                'dato_nuevo',
                'dato_actual',
                'vigente',
                'fecha_modificacion',
            ])
            ->where('id_estado', Constante::ESTADO_SOLICITUD_5_APROBADO)
            ->where('id_tipo_solicitud', Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->first();

        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function findAprobateByIdSolicitud($idSolicitud)
    {
        $datosDeSolicitud = self::where('id_solicitud', $idSolicitud)
            ->select([
                'id_solicitud',
                'id_tipo_solicitud',
                'id_sub_tipo_solicitud',
                'solicitud',
                'run_ciudadano',
                'id_estado',
                'dato_nuevo',
                'dato_actual',
                'vigente',
                'fecha_modificacion',
            ])
            ->where('id_estado', Constante::ESTADO_SOLICITUD_5_APROBADO)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->first();

        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function findBasicByIdSolicitud($idSolicitud)
    {
        $datosDeSolicitud = self::where('id_solicitud', $idSolicitud)
            ->select([
                'id_solicitud',
                'id_tipo_solicitud',
                'id_sub_tipo_solicitud',
                'solicitud',
                'run_ciudadano',
                'id_estado',
                'dato_nuevo',
                'dato_actual',
                'vigente',
                'fecha_modificacion',
            ])
            ->first();

        return $datosDeSolicitud;
    }

    public static function findNoFinalizadasByTipoAndDatoActual($tipoSolicitud, $datoActual)
    {
        $datosDeSolicitud = self::where('id_tipo_solicitud', $tipoSolicitud)
            ->select([
                'id_solicitud',
                'id_tipo_solicitud',
                'id_sub_tipo_solicitud',
                'solicitud',
                'run_ciudadano',
                'id_estado',
                'dato_nuevo',
                'dato_actual',
                'vigente',
                'fecha_modificacion',
            ])
            ->where('dato_actual', $datoActual)
            ->whereNotIn('id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS)
            ->where('vigente', 1)
            ->first();

        return $datosDeSolicitud;
    }

    /**
     * @return Solicitud
     * @throws ExcepcionConsistencia
     */
    public static function SolicitudAprobadaIngresoDomicilio($idSolicitud)
    {
        $datosDeSolicitud = self::where('id_solicitud', $idSolicitud)
            ->select([
                'id_solicitud',
                'id_tipo_solicitud',
                'id_sub_tipo_solicitud',
                'solicitud',
                'run_ciudadano',
                'id_estado',
                'dato_nuevo',
                'dato_actual',
                'vigente',
                'fecha_modificacion',
            ])
            ->whereIn('id_tipo_solicitud', [
                Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA])
            ->where('id_estado', Constante::ESTADO_SOLICITUD_5_APROBADO)
            ->where('fecha_modificacion', '>=', DB::RAW('ADD_MONTHS(sysdate,-6)'))
            ->first();

        return $datosDeSolicitud;
    }

}
