<?php

namespace App\Models;

use App\Constantes\ConstanteUsuario;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Usuario
 *
 * 
 * @mixin Eloquent
 */
class Usuario extends Model
{
    protected $table = 'AB_USUARIO';
    protected $primaryKey = 'RUT_USUARIO';
    public $timestamps = false;

    public static function getModel()
    {
        return new Usuario();
    }

    public static function updateRememberToken($rutUsuario, $token)
    {
        try {
            $usuario = self::getModel()
                ->where('RUT_USUARIO', $rutUsuario)
                ->where('ID_ESTADO', ConstanteUsuario::USUARIO_1_VIGENTE)
                ->update(['REMEMBER_TOKEN' => $token]);

            if ($usuario) {
                if($token) {
                    Log::info('OK generateRememberToken', ['rutUsuario' => $rutUsuario]);
                }
                return true;
            }
            return false;
        } catch (Throwable $ex) {
            throw $ex;
        }

        return $usuario;
    }

    public static function validateRememberToken($id_institucion, $token)
    {
        try {
            $usuario = self::getModel()
                ->where('ID_INSTITUCION', $id_institucion)
                ->where('ID_ESTADO', ConstanteUsuario::USUARIO_1_VIGENTE)
                ->where('REMEMBER_TOKEN', $token)
                ->first();
            if ($usuario) {
                Log::info('OK validateRememberToken', ['id_institucion' => $id_institucion, 'run' => intval($usuario['rut_usuario'])]);
                return $usuario;
            }
            return false;
        } catch (Throwable $ex) {
            throw $ex;
        }
        return false;
    }

    public static function obtenerIdInstitucionPorRut($rutUsuario)
    {
        return self::getModel()->where('RUT_USUARIO', $rutUsuario)->first()->id_institucion;
    }

}
