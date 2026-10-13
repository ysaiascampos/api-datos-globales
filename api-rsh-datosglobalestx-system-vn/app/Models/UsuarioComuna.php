<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Usuario
 *
 * 
 * @mixin Eloquent
 */
class UsuarioComuna extends Model
{
    protected $table = 'AB_USUARIO_COMUNA';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    public static function getModel()
    {
        return new UsuarioComuna();
    }

    public static function obtenerIdComunaPorRut($rutUsuario)
    {
        return self::getModel()->where('RUT_USUARIO', $rutUsuario)->first()->com_id;
    }

}
