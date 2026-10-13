<?php

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LocacionLocalidad
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionLocalidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionLocalidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionLocalidad query()
 * @mixin Eloquent
 */
class LocacionLocalidad extends Model
{
    protected $table = 'vas_localidad';
    public $timestamps = false;

    public static function getModel()
    {
        return new LocacionLocalidad;
    }

    public static function getLocalidadPorComuna($comunaCodigo)
    {
        $locacionLocalidad = null;
        try {
            $locacionLocalidad = LocacionLocalidad::getModel()
                ->select(['id_localidad as objeto_codigo', 'nombre as objeto_nomb', 'zona'])
                ->where('comuna_ine', $comunaCodigo)
                ->orderBy('nombre', 'asc')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionLocalidad;
    }

    public static function getLocalidadPorComunaSelect($comunaCodigo)
    {
        $locacionLocalidad = null;
        try {
            $locacionLocalidad = LocacionLocalidad::getModel()
                ->where('comuna_ine', $comunaCodigo)
                ->orderBy('nombre', 'asc')
                ->pluck('nombre', 'id_localidad');
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionLocalidad;
    }

    public static function getLocalidadPorComunaFull($comunaCodigo)
    {
        $locacionLocalidad = null;
        try {
            $locacionLocalidad = LocacionLocalidad::getModel()
                ->where('comuna_ine', $comunaCodigo)
                ->orderBy('nombre', 'asc')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionLocalidad;
    }

    public static function getLocalidad($comunaCodigo, $idLocalidad)
    {
        $locacionLocalidad = null;
        try {
            $locacionLocalidad = LocacionLocalidad::getModel()
                ->select(['id_localidad as objeto_codigo', 'nombre', 'zona'])
                ->where('id_localidad', $idLocalidad)
                ->where('comuna_ine', $comunaCodigo)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionLocalidad;
    }

}
