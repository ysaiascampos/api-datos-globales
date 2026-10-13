<?php

namespace App\Models;

use App\Constantes\Constante;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;


/**
 * App\Models\GrupoFamiliarExport
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
 * @method static Builder|GrupoFamiliarExport newModelQuery()
 * @method static Builder|GrupoFamiliarExport newQuery()
 * @method static Builder|GrupoFamiliarExport query()
 * @mixin \Eloquent
 */
class GrupoFamiliarExport extends Model
{
    use HasFactory;
    protected $table = 'grupos_familiares';
    protected $primaryKey = 'grupfami_id';
    public $timestamps = false;
    protected $hidden = ['rn'];
    protected $filled = [];

    public static function getModel()
    {
        return new GrupoFamiliarExport();
    }

    public static function findByIdGrupoFamiliar($idGrupoFamiliar)
    {
        $datosDeGrupoFamliar = null;
        try {
            $datosDeGrupoFamiliar = self::getModel()
                ->where('grupfami_id', $idGrupoFamiliar)
                ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->orderBy('grupfami_id','desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeGrupoFamiliar;
    }
    public static function findByFechaCierreGrupoFamiliar()
    {
        $maxFecha = null;
        try {
            $maxFecha = self::getModel()
                ->select('FECHA_CIERRE')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $maxFecha;
    }
}
