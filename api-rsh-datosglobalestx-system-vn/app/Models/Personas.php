<?php

namespace App\Models;

use App\Constantes\Constante;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * App\Models\Personas
 *
 * @property int $PER_ID
 * @property int|null $PER_RUN
 * @property string|null $PER_DV
 * @property string|null $PER_NOMBRES
 * @property string|null $PER_APELLIDOPATERNO
 * @property string|null $PER_APELLIDOMATERNO
 * @property int|null $PER_FECHANACIMIENTO
 * @property int|null $PER_EDAD
 * @property int|null $PER_SEXO
 * @property int|null $PER_NACIONALIDAD
 * @property string|null $PER_DIRECCION
 * @property string|null $PER_EMAIL
 * @property string|null $PER_TELEFONO
 * @property string|null $PER_CELULAR
 * @property int|null $PER_FECHADEFUNCION
 * @property int|null $PER_FECHAFUENTE
 * @property string|null $PER_FECHAACTUALIZACION
 * @property string|null $PER_CARACTERIZACION
 * @property string|null $PER_FECHACALCULO
 * @property int|null $PER_VIGENTE
 * @property string|null $PER_FECHAEXPIRACION
 * @property int|null $PER_IDSOLICITUD
 * @property int|null $PER_CALCULOACTIVO
 * @property int|null $PER_FUENTE
 * @property int|null $NO_REGISTRADO_SRCEI
 * @property string|null $PER_SITUACIONVISA
 * @property int|null $PER_IDENTIDADGENERO
 * @property string|null $PER_MD5
 * @property string|null $PER_NROIDENTIFICADORNAC
 * @property string|null $PER_ESTADOCIVIL
 * @property int|null $PER_FCORREC_MAN
 * @property string|null $PER_DESC_CORREC
 * @property string|null $PER_FECHA_CONSULTA
 * @property bool|null $INGRESO_MANUAL
 * @property int|null $PER_RUN_PADRE
 * @property int|null $PER_RUN_MADRE
 * @property string|null $PER_FECHA_ACTUALIZACION_PADRES
 * @property-read mixed $per_dv_madre
 * @property-read mixed $per_dv_padre
 * @method static Builder|Personas newModelQuery()
 * @method static Builder|Personas newQuery()
 * @method static Builder|Personas query()
 * @method static Builder|Personas whereINGRESOMANUAL($value)
 * @method static Builder|Personas whereNOREGISTRADOSRCEI($value)
 * @method static Builder|Personas wherePERAPELLIDOMATERNO($value)
 * @method static Builder|Personas wherePERAPELLIDOPATERNO($value)
 * @method static Builder|Personas wherePERCALCULOACTIVO($value)
 * @method static Builder|Personas wherePERCARACTERIZACION($value)
 * @method static Builder|Personas wherePERCELULAR($value)
 * @method static Builder|Personas wherePERDESCCORREC($value)
 * @method static Builder|Personas wherePERDIRECCION($value)
 * @method static Builder|Personas wherePERDV($value)
 * @method static Builder|Personas wherePEREDAD($value)
 * @method static Builder|Personas wherePEREMAIL($value)
 * @method static Builder|Personas wherePERESTADOCIVIL($value)
 * @method static Builder|Personas wherePERFCORRECMAN($value)
 * @method static Builder|Personas wherePERFECHAACTUALIZACION($value)
 * @method static Builder|Personas wherePERFECHAACTUALIZACIONPADRES($value)
 * @method static Builder|Personas wherePERFECHACALCULO($value)
 * @method static Builder|Personas wherePERFECHACONSULTA($value)
 * @method static Builder|Personas wherePERFECHADEFUNCION($value)
 * @method static Builder|Personas wherePERFECHAEXPIRACION($value)
 * @method static Builder|Personas wherePERFECHAFUENTE($value)
 * @method static Builder|Personas wherePERFECHANACIMIENTO($value)
 * @method static Builder|Personas wherePERFUENTE($value)
 * @method static Builder|Personas wherePERID($value)
 * @method static Builder|Personas wherePERIDENTIDADGENERO($value)
 * @method static Builder|Personas wherePERIDSOLICITUD($value)
 * @method static Builder|Personas wherePERMD5($value)
 * @method static Builder|Personas wherePERNACIONALIDAD($value)
 * @method static Builder|Personas wherePERNOMBRES($value)
 * @method static Builder|Personas wherePERNROIDENTIFICADORNAC($value)
 * @method static Builder|Personas wherePERRUN($value)
 * @method static Builder|Personas wherePERRUNMADRE($value)
 * @method static Builder|Personas wherePERRUNPADRE($value)
 * @method static Builder|Personas wherePERSEXO($value)
 * @method static Builder|Personas wherePERSITUACIONVISA($value)
 * @method static Builder|Personas wherePERTELEFONO($value)
 * @method static Builder|Personas wherePERVIGENTE($value)
 * @mixin Eloquent
 */
class Personas extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'per_id';
    public $timestamps = false;

    public static function getModel()
    {
        return new Personas();
    }

    public static function findByIdPersona($idPersona)
    {
        try {
            $datosDeContacto = self::getModel()
                ->where('per_id', $idPersona)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeContacto;
    }

    public static function findByRunCiudadano($run_Ciudadano)
    {
        try {
            $datosDeContacto = self::getModel()
                ->where('per_run', $run_Ciudadano)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeContacto;
    }
    public static function findBasicByRunCiudadano($run_Ciudadano)
    {
        try {
            $datosDeContacto = self::getModel()
                ->select([
                    'per_id',
                    'per_run',
                    'per_nombres',
                    'per_apellidopaterno',
                    'per_apellidomaterno',
                    'per_fechanacimiento',
                    'per_edad'
                ])
                ->where('per_run', $run_Ciudadano)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeContacto;
    }

    public static function findByRunCiudadanoTelefonoEmail($run_Ciudadano)
    {
        try {
            $datosDeContacto = self::getModel()
                ->where('per_run', $run_Ciudadano)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->select('per_telefono','per_email')
                ->orderBy('per_id', 'desc')
                ->first();
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $datosDeContacto;
    }

    public static function situacionUnicoPerID($runPersona)
    {
        try {
            $registros = self::where('per_run', $runPersona)
                ->where('per_vigente', '=', 1)
                ->where('per_calculoactivo', '=', 1)
                ->pluck('per_id')
                ->toArray();
            $situacionUnicoPerID = [
                'situacion' => (count($registros) <= 1),
                'per-id' => $registros,
            ];
        } catch (Exception $e) {
            $situacionUnicoPerID = [
                'situacion' => true,
                'per-id' => [],
            ];
            Log::error("Hubo problemas para analizar el integrante RUN ".$runPersona);
        }

        return $situacionUnicoPerID;
    }

}
