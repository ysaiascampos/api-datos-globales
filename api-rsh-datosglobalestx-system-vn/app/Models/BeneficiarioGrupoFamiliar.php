<?php

namespace App\Models;

use App\Classes\Institucionalizados;
use App\classes\Utiles;
use App\Constantes\Constante;
use App\Exceptions\ExcepcionConsistencia;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\BeneficiarioGrupoFamiliar
 *
 * @property int $BNFGRUPFAMI_ID
 * @property int $BNFGRUPFAMI_PERSONA
 * @property int $BNFGRUPFAMI_GRUPOFAMILIAR
 * @property int|null $BNFGRUPFAMI_PARENTESCO
 * @property string|null $BNFGRUPFAMI_FECHA
 * @property int|null $BNFGRUPFAMI_VIGENTE
 * @property string|null $BNFGRUPFAMI_FECHAEXPIRACION
 * @property int|null $BNFGRUPFAMI_IDSOLICITUD
 * @property int|null $BNFGRUPFAMI_CALCULOACTIVO
 * @property int $BNFGRUPFAMI_FUENTE
 * @property int|null $BNFGRUPFAMI_INFORMANTE
 * @property string|null $BNFGRUPFAMI_FECHA_INGRESO
 * @property string|null $BNFGRUPFAMI_FECHA_MODIFICACION
 * @property int|null $BNFGRUPFAMI_INSTITUCIONALIZADO
 * @property int|null $BNFGRUPFAMI_FCORREC_MAN
 * @property string|null $BNFGRUPFAMI_DESC_CORREC
 * @method static Builder|BeneficiarioGrupoFamiliar newModelQuery()
 * @method static Builder|BeneficiarioGrupoFamiliar newQuery()
 * @method static Builder|BeneficiarioGrupoFamiliar query()
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMICALCULOACTIVO($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIDESCCORREC($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIFCORRECMAN($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIFECHA($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIFECHAEXPIRACION($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIFECHAINGRESO($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIFECHAMODIFICACION($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIFUENTE($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIGRUPOFAMILIAR($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIID($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIIDSOLICITUD($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIINFORMANTE($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIINSTITUCIONALIZADO($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIPARENTESCO($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIPERSONA($value)
 * @method static Builder|BeneficiarioGrupoFamiliar whereBNFGRUPFAMIVIGENTE($value)
 * @mixin Eloquent
 */
class BeneficiarioGrupoFamiliar extends Model
{
    protected $table = 'beneficiarios_gruposfamiliares';
    protected $primaryKey = 'bnfgrupfami_id';
    public $timestamps = false;
    protected $hidden = ['RN'];
    protected $appends = [
        'institucionalizado',
        'informante',
        'id_parentesco',
        'nombre_parentesco'
    ];

    public static function getModel()
    {
        return new BeneficiarioGrupoFamiliar();
    }

    public function Persona()
    {
        return $this->hasOne(Personas::class, 'per_id', 'bnfgrupfami_persona');
    }

    public function Parentesco()
    {
        return $this->hasOne(Parentesco::class, 'ID', 'bnfgrupfami_parentesco');
    }

    public function Ficha()
    {
        return $this->hasOne(Ficha::class, 'ficha_persona', 'bnfgrupfami_persona');
    }

    public function FichaMontoMensual()
    {
        return $this->hasOne(FichaMontoMensual::class, 'mensual_id_persona', 'bnfgrupfami_persona');
    }

    public function getInformanteAttribute()
    {
        return $this->attributes['bnfgrupfami_informante'];
    }

    public function getIdParentescoAttribute()
    {
        return $this->attributes['bnfgrupfami_parentesco'];
    }

    public function getNombreParentescoAttribute()
    {
        if (!isset($this->attributes['nombre_parentesco'])) {
            $this->attributes['nombre_parentesco'] = Parentesco::getParentescos($this->attributes['bnfgrupfami_parentesco'])['nombre'];
        }
        return $this->attributes['nombre_parentesco'];
    }

    public function getInstitucionalizadoAttribute()
    {
        if (!isset($this->attributes['institucionalizado'])) {
            $this->attributes['institucionalizado'] = Institucionalizados::evaluarExistenciaInstitucionalizados($this);
        }
        return $this->attributes['institucionalizado'];
    }

    public function getPerEdadAttribute()
    {
        /* disponible solo cuando se consulta beneficiarios_gruposfamiliares join personas */
        $edad = null;
        if (isset($this->attributes['per_fechanacimiento'])) {
            $this->attributes['per_edad'] = Utiles::getEdad($this->attributes['per_fechanacimiento']);
            $edad = $this->attributes['per_edad'];
        }
        return $edad;
    }

    public static function findVigenteByPersona(Personas $persona)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->where('bnfgrupfami_persona', $persona->per_id)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findByIdPersonaIdSolicitud($idPersona, $idSolicitud)
    {
        return BeneficiarioGrupoFamiliar::getModel()
            ->where('bnfgrupfami_persona', $idPersona)
            ->where('bnfgrupfami_idsolicitud', $idSolicitud)
            ->first();
    }

    public static function findPendienteByIdPersonaIdSolicitud($idPersona, $idSolicitud)
    {
        return self::where('bnfgrupfami_persona',$idPersona)
            ->where('bnfgrupfami_idsolicitud', $idSolicitud)
            ->where('bnfgrupfami_vigente', Constante::VIGENTE_2_PENDIENTE)
            ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->first();
    }
    public static function findByIdPersonaIdGrupoFamiliar($idPersona, $idGrupoFamiliar)
    {
        $resultado = BeneficiarioGrupoFamiliar::getModel()
            ->where('bnfgrupfami_grupofamiliar', $idGrupoFamiliar)
            ->where('bnfgrupfami_persona', $idPersona)
            ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->first();
        return $resultado;
    }

    public static function findIdGrupoFamiliarVigenteByIdPersona($idPersona, $conException = true)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->select('bnfgrupfami_grupofamiliar')
                ->where('bnfgrupfami_persona', $idPersona)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('bnfgrupfami_id','desc')
                ->first('idgrupofamiliar');
            if (!$datosBeneficiario and $conException) {
                throw new ExcepcionConsistencia(Constante::MENSAJE_ERROR_NO_GRUPO_FAMILIAR_VIGENTE,
                    Constante::ERR_CONSISTENCIA_REGISTRO_NO_VIGENTE);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findIdGrupoFamiliarVigenteByRun($runPersona, $conException = true)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->select('bnfgrupfami_grupofamiliar')
                ->join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->where('personas.per_run', $runPersona)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('bnfgrupfami_id','desc')
                ->first('idgrupofamiliar');

            if (!$datosBeneficiario and $conException) {
                throw new ExcepcionConsistencia(Constante::MENSAJE_ERROR_NO_GRUPO_FAMILIAR_VIGENTE,
                    Constante::ERR_CONSISTENCIA_REGISTRO_NO_VIGENTE);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosByNuevoIdSolicitud($idSolicitud)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->where('bnfgrupfami_idsolicitud', $idSolicitud)
                ->join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->orderBy('bnfgrupfami_parentesco', 'asc')
                ->orderBy('personas.per_edad', 'desc')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosByGrupoFamiliar(
        GrupoFamiliar $grupoFamiliar,
        $soloPerRun = false,
        $soloJefeHogar = false
    ) {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->where('bnfgrupfami_grupofamiliar', $grupoFamiliar->grupfami_id)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('bnfgrupfami_parentesco');
            if ($soloPerRun) {
                $datosBeneficiario = $datosBeneficiario
                    ->select('personas.per_run');
            }
            if ($soloJefeHogar) {
                $datosBeneficiario = $datosBeneficiario
                    ->where('bnfgrupfami_parentesco', Constante::PARENTESCO_1_JEFE_DE_HOGAR);
            }
            $datosBeneficiario = $datosBeneficiario->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosFichaPersonaByGrupoFamiliar($grupoFamiliar, $soloPerRun = false)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->join('fichas', 'fichas.ficha_persona', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->leftJoin('ficha_monto_mensual', function ($join) {
                    $join->on('ficha_monto_mensual.mensual_id_persona', '=', 'fichas.ficha_persona')
                        ->on('ficha_monto_mensual.mensual_idsolicitud', '=', 'fichas.ficha_idsolicitud')
                        ->on('ficha_monto_mensual.mensual_vigente', '=', Constante::VIGENTE_1_VIGENTE)
                        ->on('ficha_monto_mensual.mensual_calculoactivo', '=', Constante::CALCULO_ACTIVO_1_APROBADO);
                })
                ->where('bnfgrupfami_grupofamiliar', $grupoFamiliar->GRUPFAMI_ID)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('ficha_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('ficha_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('bnfgrupfami_parentesco');
            if ($soloPerRun) {
                $datosBeneficiario = $datosBeneficiario->select('personas.per_run');
            }
            $datosBeneficiario = $datosBeneficiario->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosByRun($runPersona)
    {
        $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
            ->join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
            ->whereIn('bnfgrupfami_grupofamiliar', function ($query) use ($runPersona) {
                $query->select('bnfgrupfami_grupofamiliar')
                    ->from('beneficiarios_gruposfamiliares')
                    ->join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                    ->where('personas.per_run', $runPersona)
                    ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                    ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                    ->get();
            })
            ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->orderBy('bnfgrupfami_parentesco')
            ->get();
        return $datosBeneficiario;
    }

    public static function findBeneficiariosFichasByIdSolicitudCalculoActivo($idSolicitud)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->leftJoin('personas', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'personas.per_id')
                ->leftJoin('fichas', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                        ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
                })
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_idsolicitud', $idSolicitud)
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosFichasByIdSolicitud($idSolicitud, $tieneMontoMensual = false)
    {
        if ($tieneMontoMensual) {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->join('ab_solicitud', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                        'ab_solicitud.dato_nuevo')
                        ->on('id_solicitud', '=', 'bnfgrupfami_idsolicitud');
                })
                ->leftJoin('personas', 'bnfgrupfami_persona', '=', 'personas.per_id')
                ->leftJoin('fichas', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                        ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
                })
                ->leftJoin('ficha_monto_mensual', function ($join) {
                    $join->on('fichas.ficha_persona', '=', 'ficha_monto_mensual.mensual_id_persona')
                        ->on('fichas.ficha_idsolicitud', '=', 'ficha_monto_mensual.mensual_idsolicitud');
                })
                ->join('grupos_familiares', 'beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                    'grupos_familiares.grupfami_id')
                ->where('id_solicitud', $idSolicitud)
                ->get();
        } else {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->join('ab_solicitud', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                        'ab_solicitud.dato_nuevo')
                        ->on('id_solicitud', '=', 'bnfgrupfami_idsolicitud');
                })
                ->leftJoin('personas', 'bnfgrupfami_persona', '=', 'personas.per_id')
                ->leftJoin('fichas', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                        ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
                })
                ->join('grupos_familiares', 'beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                    'grupos_familiares.grupfami_id')
                ->where('id_solicitud', $idSolicitud)
                ->get();
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosFichasByIdSolicitudReparado($idSolicitud)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
                ->leftJoin('personas', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'personas.per_id')
                ->leftJoin('fichas', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                        ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
                })
                ->where('bnfgrupfami_idsolicitud', $idSolicitud)
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosFichasByIdPadreSolicitud(
        $idPadreSolicitud,
        $idTipoSolicitud = null,
        $idSolicitud = null
    ) {
        $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
            ->join('ab_solicitud', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'ab_solicitud.dato_nuevo')
                    ->on('id_solicitud', '=', 'bnfgrupfami_idsolicitud');
            })
            ->leftJoin('personas', 'ab_solicitud.dato_nuevo', '=', 'personas.per_id')
            ->leftJoin('fichas', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                    ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
            })
            ->join('grupos_familiares', function ($join) {
                $join->on('ab_solicitud.dato_actual', '=', 'grupos_familiares.grupfami_nuevofolio')
                    ->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                        'grupos_familiares.grupfami_id');
            })
            ->where('id_padre_solicitud', $idPadreSolicitud)
            ->whereNotIn('ab_solicitud.id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS);
        if (!is_null($idTipoSolicitud)) {
            $datosBeneficiario = $datosBeneficiario->where('id_tipo_solicitud', $idTipoSolicitud);
        }
        if (!is_null($idSolicitud)) {
            $datosBeneficiario = $datosBeneficiario->where('id_solicitud', $idSolicitud);
        }
        $datosBeneficiario = $datosBeneficiario->get([
            'beneficiarios_gruposfamiliares.*',
            'personas.*',
            'fichas.*'
        ]);
        return $datosBeneficiario;
    }

    /**
     * réplica de findBeneficiariosFichasByIdPadreSolicitudIncorporacionCambioJefe()
     * para verificar el asunto de que en ver cambio de JH, sólo trae un integrante
     */
    public static function findBeneficiariosFichasByIdPadreSolicitudIncorporacionNuevoIntegrante(
        $idPadreSolicitud,
        $idTipoSolicitud = null,
        $tramitable = null,
        $tieneMontoMensual = false
    ) {
        $listaDeCampos = [
            'ab_solicitud.id_solicitud',
            'beneficiarios_gruposfamiliares.*',
            'personas.*',
            'fichas.*',
        ];
        $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
            ->join('ab_solicitud', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'ab_solicitud.dato_nuevo')
                    ->on('id_solicitud', '=', 'bnfgrupfami_idsolicitud');
            })
            ->leftJoin('personas', 'ab_solicitud.dato_nuevo', '=', 'personas.per_id')
            ->leftJoin('fichas', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                    ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
            });

        if ($tieneMontoMensual) {
            $datosBeneficiario->leftJoin('ficha_monto_mensual', function ($join) {
                $join->on('fichas.ficha_persona', '=', 'ficha_monto_mensual.mensual_id_persona')
                    ->on('fichas.ficha_idsolicitud', '=', 'ficha_monto_mensual.mensual_idsolicitud');
            });
            $listaDeCampos[] = 'ficha_monto_mensual.*';
        }

        $datosBeneficiario->join('grupos_familiares', function ($join) {
            $join->on('ab_solicitud.dato_actual', '=', 'grupos_familiares.grupfami_nuevofolio')
                ->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=', 'grupos_familiares.grupfami_id');
        });
        $datosBeneficiario->where('id_padre_solicitud', $idPadreSolicitud);
        if ($tramitable) {
            $datosBeneficiario->whereNotIn('ab_solicitud.id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS);
        }
        if (!is_null($idTipoSolicitud)) {
            $datosBeneficiario = $datosBeneficiario->where('id_tipo_solicitud', $idTipoSolicitud);
        }
        return $datosBeneficiario->get($listaDeCampos);
    }

    /**
     * réplica de findBeneficiariosFichasByIdPadreSolicitudIncorporacionNuevoIntegrante()
     * para verificar el asunto de que en ver cambio de JH, sólo trae un integrante
     */
    public static function findBeneficiariosFichasByIdPadreSolicitudIncorporacionCambioJefe(
        $idPadreSolicitud,
        $idTipoSolicitud = null,
        $tramitable = null,
        $tieneMontoMensual = false
    ) {
        $listaDeCampos = [
            'beneficiarios_gruposfamiliares.*',
            'personas.*',
            'fichas.*'
        ];
        $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
            ->join('ab_solicitud', function ($join) {
                $join->on('id_solicitud', '=', 'bnfgrupfami_idsolicitud');
            })
            ->leftJoin('personas', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'personas.per_id');
            })
            ->leftJoin('fichas', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'fichas.ficha_persona')
                    ->on('beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'fichas.ficha_idsolicitud');
            });

        if ($tieneMontoMensual) {
            $datosBeneficiario->leftJoin('ficha_monto_mensual', function ($join) {
                $join->on('fichas.ficha_persona', '=', 'ficha_monto_mensual.mensual_id_persona')
                    ->on('fichas.ficha_idsolicitud', '=', 'ficha_monto_mensual.mensual_idsolicitud');
            });
            $listaDeCampos[] = 'ficha_monto_mensual.*';
        }

        $datosBeneficiario->join('grupos_familiares', function ($join) {
            $join->on('ab_solicitud.dato_actual', '=', 'grupos_familiares.grupfami_nuevofolio')
                ->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=', 'grupos_familiares.grupfami_id');
        });
        $datosBeneficiario->where('id_padre_solicitud', $idPadreSolicitud);
        if ($tramitable) {
            $datosBeneficiario->whereNotIn('ab_solicitud.id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS);
        }
        if (!is_null($idTipoSolicitud)) {
            $datosBeneficiario = $datosBeneficiario->where('id_tipo_solicitud', $idTipoSolicitud);
        }
        return $datosBeneficiario->get($listaDeCampos);
    }

    public static function findBeneficiariosByIdPadreSolicitud(
        $idPadreSolicitud,
        $idTipoSolicitud = null,
        $idSolicitud = null
    ) {
        $datosBeneficiario = BeneficiarioGrupoFamiliar::getModel()
            ->join('ab_solicitud', function ($join) {
                $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'ab_solicitud.dato_nuevo')
                    ->on('beneficiarios_gruposfamiliares.bnfgrupfami_vigente', '=', Constante::VIGENTE_1_VIGENTE);
            })
            ->leftJoin('personas', 'ab_solicitud.dato_nuevo', '=', 'personas.per_id')
            ->join('grupos_familiares', function ($join) {
                $join->on('ab_solicitud.dato_actual', '=', 'grupos_familiares.grupfami_nuevofolio')
                    ->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                        'grupos_familiares.grupfami_id')
                    ->on('grupos_familiares.grupfami_calculoactivo', '=', Constante::CALCULO_ACTIVO_1_APROBADO);
            })
            ->where('id_padre_solicitud', $idPadreSolicitud);
        if (!is_null($idTipoSolicitud)) {
            $datosBeneficiario = $datosBeneficiario->where('id_tipo_solicitud', $idTipoSolicitud);
        }
        if (!is_null($idSolicitud)) {
            $datosBeneficiario = $datosBeneficiario->where('id_solicitud', $idSolicitud);
        }
        $datosBeneficiario = $datosBeneficiario->get([
            'beneficiarios_gruposfamiliares.*',
            'personas.*'
        ]);

        if (count($datosBeneficiario) == 0) {
            $datosBeneficiario2 = BeneficiarioGrupoFamiliar::getModel()
                ->join('ab_solicitud', function ($join) {
                    $join->on('beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'ab_solicitud.dato_nuevo')
                        ->on('beneficiarios_gruposfamiliares.bnfgrupfami_vigente', '=', Constante::VIGENTE_2_PENDIENTE);
                })
                ->leftJoin('personas', 'ab_solicitud.dato_nuevo', '=', 'personas.per_id')
                ->join('grupos_familiares', function ($join) {
                    $join->on('ab_solicitud.dato_actual', '=', 'grupos_familiares.grupfami_nuevofolio')
                        ->on('beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar', '=',
                            'grupos_familiares.grupfami_id')
                        ->on('grupos_familiares.grupfami_calculoactivo', '=', Constante::CALCULO_ACTIVO_2_PENDIENTE);
                })
                ->where('id_padre_solicitud', $idPadreSolicitud);

            $datosBeneficiario2 = $datosBeneficiario2->get([
                'beneficiarios_gruposfamiliares.*',
                'personas.*'
            ]);
            if (count($datosBeneficiario2) != 0) {
                $datosBeneficiario = $datosBeneficiario2;
            }
        }

        return $datosBeneficiario;
    }

    public static function getBeneficiariosGrupoFamiliarActivo($folio)
    {
        return self::Join('personas', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona', '=', 'personas.per_id')
            ->whereIn('personas.per_id', function ($query2) use ($folio) {
                $query2->select('bnfgrupfami_persona')
                    ->from('beneficiarios_gruposfamiliares')
                    ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                    ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                    ->whereIn('bnfgrupfami_grupofamiliar', function ($query3) use ($folio) {
                        $query3->select('grupfami_id')
                            ->from('grupos_familiares')
                            ->whereIn('grupfami_estado', Constante::$ESTADO_GRUPO_FAMILIAR_3_5_7)
                            ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                            ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                            ->where('grupfami_nuevofolio', $folio);
                    });
            })
            ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->orderBy('bnfgrupfami_parentesco')
            ->get([
                'per_id',
                'per_run',
                'per_dv',
                'per_nombres',
                'per_apellidopaterno',
                'per_apellidomaterno',
                'per_fechanacimiento',
                'bnfgrupfami_parentesco',
                'per_nacionalidad'
            ]);
    }

    public static function findBeneficiariosFichaPersonaByIdGrupoFamiliar($idGrupoFami, $soloPerRun = false)
    {
        $datosBeneficiario = null;
        try {
            $datosBeneficiario = self::join('personas', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->join('fichas', 'fichas.ficha_persona', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
                ->leftJoin('ficha_monto_mensual', function ($join) {
                    $join->on('ficha_monto_mensual.mensual_id_persona', '=', 'fichas.ficha_persona')
                        ->on('ficha_monto_mensual.mensual_idsolicitud', '=', 'fichas.ficha_idsolicitud')
                        ->on('ficha_monto_mensual.mensual_vigente', '=', Constante::VIGENTE_1_VIGENTE)
                        ->on('ficha_monto_mensual.mensual_calculoactivo', '=', Constante::CALCULO_ACTIVO_1_APROBADO);
                })
                ->where('bnfgrupfami_grupofamiliar', $idGrupoFami)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('ficha_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('ficha_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('bnfgrupfami_parentesco');
            if ($soloPerRun) {
                $datosBeneficiario = $datosBeneficiario->select('personas.per_run');
            }
            $datosBeneficiario = $datosBeneficiario->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosBeneficiario;
    }

    public static function findBeneficiariosByRunAnalisis($runPersona)
    {
        $datosBeneficiario = Personas::leftJoin('beneficiarios_gruposfamiliares', 'personas.per_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_persona')
            ->leftJoin('grupos_familiares', 'grupos_familiares.grupfami_id', '=', 'beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar')
            ->leftJoin('ab_solicitud', 'ab_solicitud.id_solicitud','=','beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud')
            ->where('personas.per_run', $runPersona)
            ->where('personas.per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('personas.per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('grupos_familiares.grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('grupos_familiares.grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('beneficiarios_gruposfamiliares.bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('beneficiarios_gruposfamiliares.bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->orderBy('grupos_familiares.grupfami_nuevofolio','desc')
            ->orderBy('beneficiarios_gruposfamiliares.bnfgrupfami_id','desc')
            ->first();

        return $datosBeneficiario;
    }
}
