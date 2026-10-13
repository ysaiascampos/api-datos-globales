<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constantes\Constante;

/**
 * App\Models\PersonasExport
 *
 * @property int $PER_RUN
 * @property string|null $PER_DV
 * @property string|null $PER_NOMBRES
 * @property string|null $PER_APELLIDOPATERNO
 * @property string|null $PER_APELLIDOMATERNO
 * @property int|null $PER_FECHANACIMIENTO
 * @property int|null $PER_EDAD
 * @property int|null $PER_SEXO_ID
 * @property string|null $PER_SEXO
 * @property int|null $PER_NACIONALIDAD_ID
 * @property string|null $PER_NACIONALIDAD
 * @property string|null $PER_DIRECCION
 * @property string|null $PER_EMAIL
 * @property string|null $PER_TELEFONO
 * @property string|null $PER_CELULAR
 * @property int|null $PER_FECHADEFUNCION
 * @property string|null $PER_FIC_TUTORLEGAL
 * @property string|null $PER_FIC_PUEBLOORIGINARIO
 * @property int|null $PER_FIC_NUMPAREJA
 * @property int|null $PER_FIC_INGRESOANUAL
 * @property int|null $PER_FIC_INGRESOJUBILACION
 * @property int|null $PER_FIC_INGRESOOTROS
 * @property int|null $PER_FIC_FECHAAPLICACION
 * @property int|null $PER_FIC_FECHAREVISION
 * @property int|null $PER_FIC_PENSIONADMIN
 * @property int|null $PER_FIC_DIFICVISUAL
 * @property int|null $PER_FIC_DIFICAUDITIVA
 * @property int|null $PER_FIC_DIFICHABLA
 * @property int|null $PER_FIC_DIFICFISICA
 * @property int|null $PER_FIC_DIFICMENTAL
 * @property int|null $PER_FIC_DIFICPSIQUICA
 * @property int|null $PER_FIC_CALLESINAYUDA
 * @property int|null $PER_FIC_MEDICOSINAYUDA
 * @property int|null $PER_FIC_ASEOSINAYUDA
 * @property int|null $PER_FIC_DESPLAZAMIENTOENCASA
 * @property int|null $PER_FIC_CTRLESFINTERES
 * @property int|null $PER_FIC_ASISTEESTABEDUCACIONAL
 * @property int|null $PER_FIC_CURSO
 * @property int|null $PER_FIC_TIPOEDUCACION_ID
 * @property string|null $PER_FIC_TIPOEDUCACION
 * @property int|null $PER_MAT_MENSUALIDAD
 * @property int|null $PER_MAT_CODIGODEPENDENCIA
 * @property int|null $PER_FIC_TRABAJA
 * @property string|null $PER_FIC_OCUPACIONACTUAL
 * @property int|null $PER_FIC_PARENTESCOID
 * @property string|null $PER_FIC_PARENTESCO
 * @property int|null $PER_FIC_GRUPOFAMILIAR_ID
 * @property int|null $PER_COT_MESESCOTIZADOS
 * @property int|null $PER_COT_TOTALRENTAIMPONIBLE
 * @property string|null $PER_COT_TIPOTRABAJADOR
 * @property int|null $PER_F22_CALCULADO
 * @property int|null $PER_DJF22_HON_1879
 * @property int|null $PER_DJF22_HON_1812
 * @property int|null $PER_DJF22_HON_1887
 * @property int|null $PER_AFC_MESESCOTIZADOS
 * @property int|null $PER_AFC_TOTALRENTAIMPONIBLE
 * @property string|null $PER_AFC_TIPOCONTRATO
 * @property int|null $PER_VEH_TOTALVEHICULOS
 * @property int|null $PER_VEH_TOTALAVALUO
 * @property int|null $PER_PRO_TOTALPROPIEDADES
 * @property int|null $PER_PRO_TOTALAVALUO
 * @property string|null $PER_FECHAINGRESO
 * @property int|null $CALC_COLEGIO_COSTOSO
 * @property int|null $CALC_ISAPRECARGA_COSTOSO
 * @property int|null $CALC_VEHICULOS_COSTOSOS
 * @property int|null $CALC_PROPIEDADES_COSTOSAS
 * @property int|null $CALC_INGRESO_PENSION
 * @property int|null $CALC_INGRESO_LABORAL
 * @property int|null $CALC_FUENTE_INGRESOLABORAL
 * @property int|null $CALC_INGRESO_CAPITAL
 * @property int|null $CALC_INGRESO_HONORARIO
 * @property int|null $CALC_INGRESO_DEFINITIVO
 * @property int|null $CONDICION_DEPENDENCIA
 * @property string|null $CALC_PESO
 * @property int|null $PER_PEN_MESESCOTIZADOS
 * @property string|null $PER_PEN_CALCULADO
 * @property int|null $PER_FIC_INFORMANTE
 * @property int|null $PER_FIC_NOASISTEMOTIVO
 * @property int|null $PER_FIC_HORASTRABAJO
 * @property int|null $PER_FIC_BUSCOTRABAJO
 * @property int|null $PER_FIC_NOBUSCOTRABAJOMOTIVO
 * @property string|null $PER_F22_COD_110
 * @property string|null $PER_F22_COD_161
 * @property int|null $PER_AFP_RENTA_DEPENDIENTE
 * @property int|null $PER_AFP_RENTA_INDEPENDIENTE
 * @property int|null $PER_AFP_MESES_DEPENDIENTE
 * @property int|null $PER_AFP_MESES_INDEPENDIENTE
 * @property int|null $PER_PEN_COD_INSTIT
 * @property int|null $PER_PEN_COD_BENEF
 * @property int|null $PER_PEN_COD_TIPOBE
 * @property int|null $PER_COT_COTIZACIONPACTADA
 * @property int|null $PER_SII_INFORME
 * @property int $PER_ID
 * @property int|null $CALC_TRAMO_LABORAL_MIN
 * @property int|null $CALC_TRAMO_LABORAL_MAX
 * @property int|null $CALC_TRAMO_CAPITAL_MIN
 * @property int|null $CALC_TRAMO_CAPITAL_MAX
 * @property int|null $CALC_TRAMO_PENSION_MIN
 * @property int|null $CALC_TRAMO_PENSION_MAX
 * @property int|null $CALC_TRAMO_DEF_MIN
 * @property int|null $CALC_TRAMO_DEFL_MAX
 * @property int|null $CALC_FUENTE_PENSION
 * @property int|null $DISCAP_POSTRADO
 * @property int|null $DISCAP_MODERADO
 * @property int|null $DISCAP_AUTOVALENTE
 * @property int|null $DISCAP_LEVE
 * @property int|null $PER_FIC_CODIGOSRAMA
 * @property int|null $PER_FIC_CODIGOCONTRATO
 * @property int|null $PER_FIC_RELACIONCONTRACTUAL
 * @property int|null $PER_VIGENTE
 * @property int|null $PER_EMBARAZO
 * @property int|null $PER_INGRESO_EQ_CORREG
 * @property int|null $PER_FIC_TEMPPERMANEN
 * @property int|null $FICHA_ID
 * @property int|null $FICHA_IDSOLICITUD
 * @property int|null $BNFGRUPFAMI_ID
 * @property int|null $BNFGRUPFAMI_IDSOLICITUD
 * @property int|null $CALC_ISAPRECARGA_MCOSTOSO
 * @property int|null $CALC_VEHICULOS_MCOSTOSOS
 * @property int|null $CALC_PROPIEDADES_MCOSTOSAS
 * @property int|null $PENSION_APORTE
 * @property int|null $PENSION_DESCUENTO
 * @property int|null $MEDIO_PADRE_NO_PRESEN_AMB
 * @property int|null $RND_CSE
 * @property int|null $ESCOLARIDAD
 * @property string|null $FUENTE_ESCOLARIDAD
 * @property int|null $FUENTE_K
 * @property int|null $COTIZANTE_CARGA
 * @property int|null $ED_SUPERIOR
 * @property int|null $ESTUDIA_TRABAJA
 * @property int|null $ESTUDIA
 * @property int|null $P_JURIDICA
 * @property int|null $DEPEN_SEVERA
 * @property int|null $MARCA_NEE
 * @property int|null $FUENTE_DEPENDENCIA
 * @property int|null $CUIDADOR
 * @property int|null $DEPENDIENTE
 * @property int|null $CUIDADOR_PRINCIPAL
 * @property string|null $ID_RUN_FECHA_ORIGEN_ESTADO
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonasExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonasExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonasExport query()
 * @mixin \Eloquent
 */
class PersonasExport extends Model
{
    use HasFactory;
    protected $table = 'personas';
    protected $primaryKey = 'per_id';
    public $timestamps = false;
    protected $hidden = ['rn'];

    public static function getModel()
    {
        return new PersonasExport();
    }

    public static function findByRunCiudadano($runCiudadano)
    {
        $datosDePersona = null;
        try {
            $datosDePersona = self::getModel()
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where("per_run",$runCiudadano)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDePersona;
    }

    public static function findByRunCiudadanoGrupofamiliar($runCiudadano)
    {
        $datosDeGrupoFamliar = null;
        try {
            $datosDeGrupoFamliar = self::getModel()
                ->where("per_run",$runCiudadano)
                ->join("grupos_familiares","grupos_familiares.grupfami_id","=","personas.per_fic_grupofamiliar_id")
                ->where("personas.per_vigente",Constante::VIGENTE_1_VIGENTE)
                ->where("grupos_familiares.grupfami_vigente",Constante::VIGENTE_1_VIGENTE)
                ->orderBy('per_id', 'desc')
                ->orderBy('grupfami_id','desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamliar;
    }

    public static function findByRunCiudadanoGrupofamiliarFolioId($runCiudadano)
    {
        $datosDeGrupoFamliar = null;
        try {
            $datosDeGrupoFamliar = self::getModel()
                ->select("grupfami_nuevofolio","grupfami_id")
                ->where("per_run",$runCiudadano)
                ->join("grupos_familiares","grupos_familiares.grupfami_id","=","personas.per_fic_grupofamiliar_id")
                ->where("personas.per_vigente",Constante::VIGENTE_1_VIGENTE)
                ->where("grupos_familiares.grupfami_vigente",Constante::VIGENTE_1_VIGENTE)
                ->orderBy('per_id', 'desc')
                ->orderBy('grupfami_id','desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamliar;
    }

    public static function findByIdGrupoFamiliarExport($idGrupoFamiliar)
    {
        $datosDePersona = null;
        try {
            $datosDePersona = self::getModel()
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_fic_grupofamiliar_id', $idGrupoFamiliar)
                ->orderBy('per_fic_parentescoid')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDePersona;
    }

    public static function findBasicByIdGrupoFamiliarExport($idGrupoFamiliar)
    {
        $datosDePersona = null;
        try {
            $datosDePersona = self::getModel()
                ->select("per_run","per_dv","per_nombres","per_apellidopaterno","per_apellidomaterno","per_edad")
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_fic_grupofamiliar_id', $idGrupoFamiliar)
                ->orderBy('per_fic_parentescoid')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDePersona;
    }

    public static function findPoseeRSHByRunCiudadano($runCiudadano)
    {
        $poseeRSH = false;
        try {
            $responsePoseeRSH = self::getModel()
                    ->where("PER_RUN",$runCiudadano)
                    ->join("grupos_familiares","grupos_familiares.grupfami_id","=","personas.per_fic_grupofamiliar_id")
                    ->where("personas.per_vigente",Constante::VIGENTE_1_VIGENTE)
                    ->where("grupos_familiares.grupfami_vigente",Constante::VIGENTE_1_VIGENTE)
                    ->whereNotNull("calc_quintil_validacion2")
                    ->first();
            if($responsePoseeRSH){
                $poseeRSH = true;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $poseeRSH;
    }

    public static function findByFichaIdSolicitud($idSolicitud)
    {
        $datosDePersona = null;
        try {
            $datosDePersona = self::getModel()
                ->where("ficha_idsolicitud",$idSolicitud)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDePersona;
    }

    public static function findByBnfGrupFamiIdSolicitud($idSolicitud)
    {
        $datosDePersona = null;
        try {
            $datosDePersona = self::getModel()
                ->where("bnfgrupfami_idsolicitud",$idSolicitud)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDePersona;
    }

    public static function findByRunCiudadanoIngresosPension($runCiudadano)
    {
        $datosDeIngresosPension = null;
        try {
            $datosDeIngresosPension = self::getModel()
                ->where("per_run",$runCiudadano)
                ->where("per_vigente",Constante::VIGENTE_1_VIGENTE)
                ->where("calc_ingreso_pension",">",0)
                ->orderBy('per_id', 'desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeIngresosPension;
    }
}
