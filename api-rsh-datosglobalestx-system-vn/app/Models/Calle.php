<?php

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Calle
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Calle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calle query()
 * @mixin Eloquent
 */
class Calle extends Model
{
    protected $table = 'vas_calle'; /* vista */
    protected $primaryKey = 'id_calle';
    public $timestamps = false;

    public static function getModel()
    {
        return new Calle();
    }

    public static function listCallePorComunaYLocalidad($comunaCodigo)
    {
        $locacionCalle = null;
        try {
            $locacionCalle = self::getModel()
                ->where('comuna_ine', $comunaCodigo)
                ->orderBy('nombre', 'asc')
                ->pluck('nombre', 'id_calle');
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionCalle;
    }

    public static function findByCodigoCalleIdLocalidad($codigoCalle, $Idlocalidad)
    {
        return self::where('id_localidad', $Idlocalidad)
            ->where('id_calle', $codigoCalle)
            ->first();
    }

    public static function findByCodigoCalleIdLocalidadComunaIne($codigoCalle, $idLocalidad, $comunaIne)
    {
        return self::where('id_calle', $codigoCalle)
        ->where('id_localidad', $idLocalidad)
        ->where('comuna_ine', $comunaIne)
        ->first();
    }

    public static function findByCodigoCalleComunaIne($codigoCalle, $comunaIne)
    {
        return self::where('id_calle', $codigoCalle)
            ->where('comuna_ine', $comunaIne)
            ->first();
    }

}
