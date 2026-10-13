<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FuenteExport
 *
 * @property int $FUE_ID
 * @property string|null $FUE_DESCRIPCION
 * @property string|null $FUE_DESCRIPCION_LARGA
 * @property int|null $FUE_INDEPENDIENTE
 * @property int|null $FUE_DEPENDIENTE
 * @property int|null $FUE_PENS_1
 * @property int|null $FUE_PENS_2
 * @property string|null $TABLA
 * @property int|null $N_MESES_INDEPENDIENTE
 * @property int|null $N_MESES_DEPENDIENTE
 * @property bool|null $TIPO_SOLICITUD_60
 * @property bool|null $TIPO_SOLICITUD_61
 * @property bool|null $TIPO_SOLICITUD_62
 * @property bool|null $TIPO_SOLICITUD_63
 * @property bool|null $TIPO_SOLICITUD_64
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport query()
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEDEPENDIENTE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEDESCRIPCION($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEDESCRIPCIONLARGA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEINDEPENDIENTE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEPENS1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereFUEPENS2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereNMESESDEPENDIENTE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereNMESESINDEPENDIENTE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereTABLA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereTIPOSOLICITUD60($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereTIPOSOLICITUD61($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereTIPOSOLICITUD62($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereTIPOSOLICITUD63($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuenteExport whereTIPOSOLICITUD64($value)
 * @mixin Eloquent
 */
class FuenteExport extends Model
{
    protected $table = 'fuentes';
    protected $primaryKey = 'fue_id';
    public $timestamps = false;

    public static function getModel()
    {
        return new FuenteExport();
    }

    public static function getFuentes()
    {
        $FuenteExport = self::getModel()
            ->orderBy('fue_id')
            ->get();
        return $FuenteExport;
    }

    public static function mensajeTramo()
    {
        $result =  self::getModel()
            ->where('fue_id', 999)
            ->select('fue_descripcion_larga')
            ->first();
        return $result;
    }

    public static function findByFuenteDependencia($idFuenteDependencia)
    {
        $fuente = null;
        $resultado = null;
        try {
            $fuente = self::getModel()
                ->select("fue_id","fue_descripcion","fue_descripcion_larga")
                ->where('fue_id', $idFuenteDependencia)
                ->first();

            if (!is_null($fuente) && $fuente->fue_id != 0 && $fuente->fue_id != 430) {
                $resultado = [
                    'fuente'=>$fuente->fue_descripcion
//                    'descripcion'=>$fuente->FUE_DESCRIPCION_LARGA
                ];
            }else{
                $resultado = false;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $resultado;
    }
}
