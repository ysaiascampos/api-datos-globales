<?php

namespace App\Models;

use App\Constantes\Constante;
use App\Exceptions\ExcepcionConsistencia;
use App\Utiles;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;


/**
 * App\Models\GrupoFamiliar
 *
 * @property int $GRUPFAMI_ID
 * @property int|null $GRUPFAMI_FOLIO
 * @property int|null $GRUPFAMI_NUEVOFOLIO
 * @property int|null $GRUPFAMI_COMUNAINE
 * @property int|null $GRUPFAMI_LOCALIDAD
 * @property string|null $GRUPFAMI_UNIDADVECINAL
 * @property int|null $GRUPFAMI_CODIGOAH
 * @property bool|null $GRUPFAMI_C_ZONA
 * @property string|null $GRUPFAMI_C_CALLE
 * @property string|null $GRUPFAMI_N_CALLE
 * @property string|null $GRUPFAMI_SITIO
 * @property string|null $GRUPFAMI_BLOCK
 * @property string|null $GRUPFAMI_DPTO
 * @property string|null $GRUPFAMI_CASA
 * @property string|null $GRUPFAMI_REFERENCIA
 * @property string|null $GRUPFAMI_NUMCLIENTELUZ
 * @property string|null $GRUPFAMI_NUMCLIENTEAGUA
 * @property int|null $GRUPFAMI_TIPOVIVIENDA
 * @property int|null $GRUPFAMI_SITUACIONSITIO
 * @property int|null $GRUPFAMI_SITUACIONVIVIENDA
 * @property int|null $GRUPFAMI_FUENTEAGUA
 * @property int|null $GRUPFAMI_DISTRIBUCIONAGUA
 * @property int|null $GRUPFAMI_SISTEMABANO
 * @property int|null $GRUPFAMI_DORMITORIOSOCUPADOS
 * @property int|null $GRUPFAMI_PRINCIPALOCUPANTE
 * @property int|null $GRUPFAMI_VIGENTE
 * @property int|null $GRUPFAMI_FECHAEXPIRACION
 * @property int|null $GRUPFAMI_IDSOLICITUD
 * @property int|null $GRUPFAMI_CALCULOACTIVO
 * @property int $GRUPFAMI_FUENTE
 * @property int|null $GRUPFAMI_ESTADO
 * @property int|null $GRUPFAMI_MATERIALMURO
 * @property int|null $GRUPFAMI_MATERIALMUROESTADO
 * @property int|null $GRUPFAMI_MATERIALPISO
 * @property int|null $GRUPFAMI_MATERIALPISOESTADO
 * @property int|null $GRUPFAMI_MATERIALTECHO
 * @property int|null $GRUPFAMI_MATERIALTECHOESTADO
 * @property int|null $GRUPFAMI_FECHA_ENCUESTA
 * @property int|null $GRUPFAMI_FECHA_MODIFICACION
 * @property int|null $GRUPFAMI_RUN_ENCUESTADOR
 * @property int|null $GRUPFAMI_RUT_VALIDACION
 * @property string|null $GRUPFAMI_FECHA_VALIDACION
 * @property int|null $GRUPFAMI_RUT_VALIDACION_DIR
 * @property string|null $GRUPFAMI_FECHA_VALIDACION_DIR
 * @property string|null $GRUPFAMI_FECHA_ESTADO
 * @property string|null $GRUPFAMI_MOTIVO_CAMBIO_ESTADO
 * @property int|null $GRUPFAMI_RUT_CAMBIO_ESTADO
 * @property int|null $GRUPFAMI_RUT_VALIDACION_ACT
 * @property string|null $GRUPFAMI_FECHA_VALIDACION_ACT
 * @property string|null $GRUPFAMI_C_CALLE2
 * @property int|null $ID_SUPERVISION
 * @property string|null $GRUPFAMI_NUMDOMICILIO
 * @property int|null $GRUPFAMI_INSTITUCIONALIZADO
 * @property int|null $GRUPFAMI_FCORREC_MAN
 * @property string|null $GRUPFAMI_DESC_CORREC
 * @property string|null $MANZENT MANZENT CODIGO INE DE MANZANA
 * @property string|null $SIST_REFERENCIA_COORD_X_Y SISTEMA DE REFERENCIA DE COORDENADAS GEOGRAFICAS
 * @property string|null $SIST_ORIGEN_X_Y SISTEMA O AREA MDS DE DONDE PROVIENE X e Y COORDENADAS GEOGRAFICAS
 * @property string|null $COORDENADA_X COORDENADA_X
 * @property string|null $COORDENADA_Y COORDENADA_Y
 * @property string|null $DIREC_SIN_SEPARAR_GEO DIREC_SIN_SEPARAR_GEO CADENA COMPLETA DEVUELTA POR GEOREFERENCIA   POR EJEMPLO DE GOOGLE
 * @property string|null $DIREC_CALLE_GEO DIREC_CALLE_GEO DEVUELTA POR GEOREFERENCIA   POR EJEMPLO DE GOOGLE
 * @property string|null $DIREC_NUMERO_GEO DIREC_NUMERO_GEO DEVUELTA POR GEOREFERENCIA   POR EJEMPLO DE GOOGLE
 * @property string|null $UV_GEO UV_GEO DEVUELTA POR GEOREFERENCIA   POR EJEMPLO DE GOOGLE
 * @property int|null $FECHA_GEO FECHA_GEO DE GEOREFERENCIA
 * @property string|null $LOCALIDAD_GEO LOCALIDAD_GEO DEVUELTA POR GEOREFERENCIA   POR EJEMPLO DE GOOGLE
 * @property int|null $GRUPFAMI_RUT_VALIDACION_CIUD
 * @property string|null $GRUPFAMI_FECHA_VALIDACION_CIUD
 * @property string|null $UV_OLD
 * @property bool|null $NOMINADO_SUPERVISION MARCA DE POTENCIAL SUPERVISION, SE HACE DE FORMA MANUAL POR CARGA MASIVA
 * @property string|null $GRUPFAMI_FECHA_SUSPENSION MARCA LA FECHA EN QUE EL FOLIO DEBERIA SER SUSPENDIDO
 * @property string|null $GRUPFAMI_CODIGO_SUSPENSION MARCA EL CODIGO INFICATIVO DE LA SUSPENSION, SE CARGA DE FORMA MANUAL
 * @property int|null $COD_DIRECCION_GIS
 * @method static Builder|GrupoFamiliar newModelQuery()
 * @method static Builder|GrupoFamiliar newQuery()
 * @method static Builder|GrupoFamiliar query()
 * @mixin \Eloquent
 */
class GrupoFamiliar extends Model
{
    protected $table = 'grupos_familiares';
    protected $primaryKey = 'grupfami_id';
    public $timestamps = false;
    protected $hidden = ['rn'];
    protected $filled = [];

    public function Integrantes()
    {
        return $this->hasMany('App\Models\BeneficiarioGrupoFamiliar', 'bnfgrupfami_grupofamiliar', 'grupfami_id');
    }

    public function getNombreUnidadVecinalAttribute()
    {
        if (empty($this->attributes['nombre_unidad_vecinal'])) {
            $this->attributes['nombre_unidad_vecinal'] = $this->obtenerNombreUnidadVecinal();
        }
        return $this->attributes['nombre_unidad_vecinal'];
    }

    public function getNombreAgrupacionHabitacionalAttribute()
    {
        if (empty($this->attributes['nombre_agrupacion_habitacional'])) {
            $this->attributes['nombre_agrupacion_habitacional'] = $this->obtenerNombreAgrupacionHabitacional();
        }
        return $this->attributes['nombre_agrupacion_habitacional'];
    }

    public function getComunaAttribute()
    {
        if (empty($this->attributes['comuna'])) {
            $this->attributes['comuna'] = LocacionComuna::findByComunaIne($this->attributes['grupfami_comunaine'])[0];
        }
        return $this->attributes['comuna'];
    }

    public function getRegionAttribute()
    {
        if (empty($this->attributes['region'])) {
            $comuna = $this->COMUNA;
            $this->attributes['region'] = ($comuna) ? LocacionRegion::getRegionPorComuna($comuna->codigo) : null;
        }
        return $this->attributes['region'];
    }

    public function getLocalidadAttribute()
    {
        if (empty($this->attributes['localidad'])) {
            $comuna = $this->comuna;
            $this->attributes['localidad'] = ($comuna) ?
                LocacionLocalidad::getLocalidad($comuna->codigo, $this->grupfami_localidad) : null;
        }
        return $this->attributes['localidad'];
    }

    public function getCalleAttribute()
    {
        if (empty($this->attributes['calle'])) {
            $comuna = $this->comuna;
            $this->attributes['calle'] = ($comuna) ? $this->obtenerNombreCalle() : null;
        }
        return $this->attributes['calle'];
    }

    public static function getModel()
    {
        return new GrupoFamiliar();
    }

    /**
     * Trae grupo familiar por idsolicitud o datoActual
     */
    public static function findBySolicitud(Solicitud $solicitud, string $campoFolio = null)
    {
        $datosDeGrupoFamiliar = null;
        try {
            if ($campoFolio) {
                $datosDeGrupoFamiliar = self::getModel()
                    ->where('grupfami_nuevofolio', $solicitud->$campoFolio)
                    ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                    ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                    ->whereIn('grupfami_estado', [
                        Constante::ESTADO_GRUPO_FAMILIAR_3_ESPERANDO_PUNTAJE,
                        Constante::ESTADO_GRUPO_FAMILIAR_5_PROVISORIO,
                        Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO
                    ])
                    ->first();
            } else {
                $datosDeGrupoFamiliar = self::where('grupfami_idsolicitud', $solicitud->id_solicitud)
                    ->first();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamiliar;
    }

    public static function findByIdGrupoFamiliar($idGrupoFamiliar)
    {
        $datosDeGrupoFamliar = null;
        try {
            $datosDeGrupoFamiliar = self::getModel()
                ->find($idGrupoFamiliar);
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamiliar;
    }

    public static function findVigenteAprobadoByRunCiudadano($runCiudadano, $validar = true)
    {
        $datosDeGrupoFamiliar = null;
        try {
            $datosDeGrupoFamiliar = self::join('beneficiarios_gruposfamiliares bgf', 'bgf.bnfgrupfami_grupofamiliar', '=', 'grupfami_id')
                ->join('personas p', 'p.per_id', '=', 'bgf.bnfgrupfami_persona')
                ->where('per_run', $runCiudadano)
                ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('grupfami_estado', Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('grupfami_id','desc')
                ->orderBy('bnfgrupfami_id','desc')
                ->orderBy('per_id','desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamiliar;
    }

    public static function findBasicVigenteAprobadoByRunCiudadano($runCiudadano, $validar = true)
    {
        $datosDeGrupoFamiliar = null;
        try {
            $datosDeGrupoFamiliar = self::join('beneficiarios_gruposfamiliares bgf', 'bgf.bnfgrupfami_grupofamiliar', '=', 'grupfami_id')
                ->select([
                    'grupfami_id',
                    'grupfami_idsolicitud',
                    'grupfami_comunaine',
                    'grupfami_c_calle'
                ])
                ->join('personas p', 'p.per_id', '=', 'bgf.bnfgrupfami_persona')
                ->where('per_run', $runCiudadano)
                ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('grupfami_estado', Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->orderBy('grupfami_id','desc')
                ->orderBy('bnfgrupfami_id','desc')
                ->orderBy('per_id','desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamiliar;
    }

    public static function findNuevoFolioVigenteAprobadoByRunCiudadano($runCiudadano, $validar = true)
    {
        $datosDeGrupoFamiliar = null;
        try {
            $datosDeGrupoFamiliar = self::join('beneficiarios_gruposfamiliares bgf', 'bgf.bnfgrupfami_grupofamiliar', '=', 'grupfami_id')
            ->select([
                'grupfami_nuevofolio',
            ])
            ->join('personas p', 'p.per_id', '=', 'bgf.bnfgrupfami_persona')
            ->where('per_run', $runCiudadano)
            ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('grupfami_estado', Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO)
            ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->orderBy('grupfami_id','desc')
            ->orderBy('bnfgrupfami_id','desc')
            ->orderBy('per_id','desc')
            ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamiliar;
    }
    public static function findByNuevoFolio($nuevoFolio, array $opciones = [])
    {
        $defaults = [
            'vigente' => Constante::VIGENTE_1_VIGENTE,
            'calculoActivo' => Constante::CALCULO_ACTIVO_1_APROBADO,
            'estado' => Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO
        ];
        $opciones = array_replace($defaults, $opciones);

        return self::where('grupfami_nuevofolio', $nuevoFolio)
            ->where('grupfami_vigente', $opciones['vigente'])
            ->where('grupfami_calculoactivo', $opciones['calculoActivo'])
            ->where('grupfami_estado', $opciones['estado'])
            ->orderBy('grupfami_id','desc')
            ->first();
    }

    public function findTramosdeIngreso()
    {
        $consultaTramo = null;
        $query = "select FN_RANGO_DE_INGRESOS_G(?) from dual";
        $consultaTramo01 = DB::select($query, [$this->bnfgrupfami_id]);
        if (count($consultaTramo01)) {
            foreach ($consultaTramo01[0] as $llave => $valor) {
                $consultaTramo = $valor;
            }
        }
        return ($consultaTramo);
    }

    public function getFechaValidacion(Personas $ciudadano)
    {
        $fecha = null;
        $resp = new stdClass();
        $resp->runValidacion = $ciudadano->per_run;
        $resp->fechaEncuesta = null;
        $resp->fechaValidacion=null;
        if(!is_null($this->grupfami_fecha_encuesta)) {
            if (strlen($this->grupfami_fecha_encuesta) == 8) {
                // formato ano mes dia
                $fecha = substr($this->grupfami_fecha_encuesta, 0, 4) . "-" .
                    substr($this->grupfami_fecha_encuesta, 4, 2) . "-" .
                    substr($this->grupfami_fecha_encuesta, 6, 2);
            }
            $resp->fechaEncuesta = $fecha;
            $resp->fechaValidacion = $fecha; // aca se cambia por la fecha ingeso.
        }
        if(!is_null($this->grupfami_fecha_validacion_ciud)) {
            if (date($this->grupfami_fecha_validacion_ciud) >= date($fecha)) {
                $resp->fechaValidacion = $this->grupfami_fecha_validacion_ciud;
                $resp->runValidacion = $this->grupfami_rut_validacion_ciud;
            }
        }
        return $resp;

    }

    public function obtenerNombreAgrupacionHabitacional()
    {
        try {
            $codigoah = !empty($this->grupfami_codigoah) ? $this->grupfami_codigoah : NULL;
            $c_calle = !empty($this->grupfami_c_calle) ? $this->grupfami_c_calle : NULL;
            $comunaine = !empty($this->grupfami_comunaine) ? $this->grupfami_comunaine : NULL;

            $sqlAH = "SELECT FN_OBTENER_NOMBREAGRUPHAB_CHAR(?,?,?) AS nombreah FROM dual";
            $nombreAH = DB::select($sqlAH, [$c_calle, $codigoah, $comunaine])[0];

        } catch (Exception $exception) {
            Log::error('Excepcion al usar la funcion FN_OBTENER_NOMBREAGRUPHAB_CHAR con el folio ' . $this->grupfami_nuevofolio . ', llamada ' . $exception);
            $nombreAH = new stdClass;
            $nombreAH->NOMBREAH = 'Error:';
        }
        return Utiles::limpiaErrorFuncionDB($nombreAH->NOMBREAH);
    }

    public function obtenerNombreUnidadVecinal()
    {
        try {
            $c_calle = !empty($this->grupfami_c_calle) ? $this->grupfami_c_calle : "NULL";
            $localidad = empty($this->grupfami_localidad) ? "0" : $this->grupfami_localidad;
            $unidadVecinal = $this->GRUPFAMI_UNIDADVECINAL;

            $sqlUV = "SELECT FN_OBTENER_UNIDADVECINAL(?, ?, ?) AS nombreuv FROM dual";
            $nombreUV = DB::select($sqlUV, [$c_calle, $localidad, $unidadVecinal])[0];
        } catch (Exception $exception) {
            Log::error('Excepcion al usar la funcion FN_OBTENER_UNIDADVECINAL con el folio' . $this->grupfami_nuevofolio . ' llamada ' . $exception);
            $nombreUV = new stdClass;
            $nombreUV->NOMBREUV = 'Error:';
        }
        return Utiles::limpiaErrorFuncionDB($nombreUV->NOMBREUV);
    }

    public function obtenerNombreLocalidad()
    {
        try {
            $sqlNomLocalidad = "SELECT fn_Obtener_Nom_Localidad_C_Pob(?) as nomlocalidad FROM dual";
            $nombreLocalidad = DB::select($sqlNomLocalidad, [$this->grupfami_nuevofolio])[0];

        } catch (Exception $exception) {
            Log::error('Excepcion al usar la funcion fn_Obtener_Nom_Localidad_C_Pob con el folio' . $this->grupfami_nuevofolio . ' llamada ' . $exception);
            $nombreLocalidad = new stdClass;
            $nombreLocalidad->NOMLOCALIDAD = 'Error:';
        }
        return Utiles::limpiaErrorFuncionDB($nombreLocalidad->NOMLOCALIDAD);
    }

    public function obtenerNombreCalle()
    {
        if(!empty($this->grupfami_nuevofolio)) {
            $sqlNomCalle = "SELECT fn_Obtener_Nombre_Calle(?) AS nomcalle FROM dual";
            $nombreCalle = DB::select($sqlNomCalle, [$this->grupfami_nuevofolio])[0];

            //Excepción para nuevofolio en form ingreso sin direccion
            if($nombreCalle->NOMCALLE == "ERROR: Calle no encontrada")
            {
                $infoCalle = LocacionCalle::getCallePorComunaLocalidadYidCalle(
                    $this->grupfami_comunaine,
                    $this->grupfami_localidad,
                    $this->grupfami_c_calle,
                    $this->grupfami_n_calle
                );
                return (isset($infoCalle->nombre))?$infoCalle->nombre:null;

            }else{
                return Utiles::limpiaErrorFuncionDB($nombreCalle->NOMCALLE);
            }
        }else{
            $infoCalle = LocacionCalle::getCallePorComunaLocalidadYidCalle(
                $this->grupfami_comunaine,
                $this->grupfami_localidad,
                $this->grupfami_c_calle,
                $this->grupfami_n_calle
            );
            return $infoCalle->nombre;
        }
    }

    public function obtenerEstadoGrupoFamiliar()
    {
        $estadosGrupoFamiliar = [
            1 => Constante::ESTADO_FOLIO_SOLICITADO,
            2 => Constante::ESTADO_FOLIO_DIGITACION,
            3 => Constante::ESTADO_FOLIO_ESPERANDO_PUNTAJE,
            4 => Constante::ESTADO_FOLIO_SUSPENDIDO,
            5 => Constante::ESTADO_FOLIO_PROVISORIO,
            6 => Constante::ESTADO_FOLIO_ANULADO,
            7 => Constante::ESTADO_FOLIO_APROBADO,
            14 => Constante::ESTADO_FOLIO_INACTIVO_X_DEFUNCION
        ];
        if (array_key_exists($this->grupfami_estado, $estadosGrupoFamiliar)) {
            $estado = $estadosGrupoFamiliar[$this->grupfami_estado];
        } else {
            $estado = Constante::ESTADO_FOLIO_SIN_INFORMACION;
        }
        return $estado;
    }

    public function obtenerConsistenciaGrupoFamiliar($ciudadano)
    {
        $estado = "Inconsistente";
        $vigentePorBeneficiario = BeneficiarioGrupoFamiliar::findBeneficiariosByRunAnalisis($ciudadano->per_run);
        if($vigentePorBeneficiario) {
            if (in_array($vigentePorBeneficiario->id_tipo_solicitud, [Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE]) &&
                in_array($vigentePorBeneficiario->id_estado, [Constante::ESTADO_SOLICITUD_5_APROBADO])) {
                $vigentePorBeneficiario = null;
            }
            if ($vigentePorBeneficiario && $this->grupfami_id == $vigentePorBeneficiario->bnfgrupfami_grupofamiliar) {
                $estado = "Consistente";
            }
        }
        return $estado;
    }

    public static function findVigenteAprobadoByRunCiudadanoInconsistente($runCiudadano, $validar = true)
    {
        $datosDeGrupoFamiliar = self::join('beneficiarios_gruposfamiliares bgf', 'bgf.bnfgrupfami_grupofamiliar', '=', 'grupfami_id')
            ->join('personas p', 'p.per_id', '=', 'bgf.bnfgrupfami_persona')
            ->where('per_run', $runCiudadano)
//            ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
//            ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('grupfami_estado', Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO)
            ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->orderBy('grupfami_id','desc')
            ->first();
        if (!$datosDeGrupoFamiliar && $validar) {
            throw new ExcepcionConsistencia('No se encuentra el grupo familiar',
                Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE);
        }
        return $datosDeGrupoFamiliar;
    }
}
