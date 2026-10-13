<?php

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LocacionComuna
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionComuna newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionComuna newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionComuna query()
 * @mixin Eloquent
 */
class LocacionComuna extends Model
{
    protected $table = 'comunas_ine';
    public $timestamps = false;

    public static function getModel()
    {
        return new LocacionComuna;
    }

    public static function findAll()
    {
        $datosDeComuna = null;
        try {
            $datosDeComuna = LocacionComuna::getModel()
                ->select(['com_id as codigo', 'com_descripcion as descripcion'])
                ->orderBy('descripcion', 'ASC')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeComuna;
    }

    public static function findByComunaIne($comunaIne)
    {
        $datosDeComuna = null;
        try {
            $datosDeComuna = LocacionComuna::getModel()
                ->select(['com_id as codigo', 'com_descripcion as descripcion'])
                ->where('com_id', $comunaIne)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeComuna;
    }

    public static function getAllPorComuna($comunaIne)
    {
        $locacionComuna = null;
        try {
            $locacionComuna = LocacionComuna::getModel()
                ->where('com_id', $comunaIne)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionComuna;
    }

    public static function getComunaPorRegion($regionCodigo)
    {
        $locacionComuna = null;
        try {
            $locacionComuna = LocacionComuna::getModel()
                ->select(['com_id as objeto_codigo', 'com_descripcion as objeto_nomb'])
                ->where('com_region', $regionCodigo)
                ->orderBy('com_descripcion', 'asc')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionComuna;
    }

    public static function getComunaPorRegionSelect($regionCodigo)
    {
        $locacionComuna = null;
        try {
            $locacionComuna = LocacionComuna::getModel()
                ->where('com_region', $regionCodigo)
                ->orderBy('com_descripcion', 'asc')
                ->pluck('com_descripcion', 'COM_ID');
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionComuna;
    }

    public static function getComunaPorRegionFull($regionCodigo)
    {
        $locacionComuna = null;
        try {
            $locacionComuna = LocacionComuna::getModel()
                ->where('com_region', $regionCodigo)
                ->orderBy('com_descripcion', 'asc')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $locacionComuna;
    }
}
