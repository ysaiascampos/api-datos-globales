<?php

namespace App\classes;

use App\Models\Usuario;
use App\Models\UsuarioComuna;
use App\Models\Comuna;
use App\Models\LocacionRegion;

class ValidaRememberTokenClass
{
    public function __invoke($id_institucion = null, $token = null)
    {
        $respuestaUsuario = Usuario::validateRememberToken($id_institucion, $token);
        if ($respuestaUsuario) {
            # Se elimina el token
            Usuario::updateRememberToken($respuestaUsuario['rut_usuario'], null);
            $idComuna = UsuarioComuna::obtenerIdComunaPorRut($respuestaUsuario['rut_usuario']);
            $comuna = $idComuna ? Comuna::obtenerNombreComuna($idComuna) : null;
            $region = LocacionRegion::getRegionPorId($respuestaUsuario['reg_id'] ? intval($respuestaUsuario['reg_id']) : null);
            return [
                'rut_usuario' => intval($respuestaUsuario['rut_usuario']),
                'id_estado' => $respuestaUsuario['id_estado'] ? intval($respuestaUsuario['id_estado']) : null,
                'nombres_usuario' => $respuestaUsuario['nombres_usuario'],
                'apat_usuario' => $respuestaUsuario['apat_usuario'],
                'amat_usuario' => $respuestaUsuario['amat_usuario'],
                'username' => $respuestaUsuario['username'],
                'fecha_creacion' => $respuestaUsuario['fecha_creacion'],
                'fecha_modificacion' => $respuestaUsuario['fecha_modificacion'],
                'fecha_caducidad' => $respuestaUsuario['fecha_caducidad'],
                'mail' => $respuestaUsuario['mail'],
                'id_perfil' => $respuestaUsuario['id_perfil'] ? intval($respuestaUsuario['id_perfil']) : null,
                'id_institucion' => $respuestaUsuario['id_institucion'] ? intval($respuestaUsuario['id_institucion']) : null,
                'reg_id' => $respuestaUsuario['reg_id'] ? intval($respuestaUsuario['reg_id']) : null,
                'reg_nombre' => $region ? $region->reg_descripcion : null,
                'com_id' => $idComuna ? intval($idComuna) : null,
                'com_nombre' => $comuna ? $comuna : null
            ];
        }
        
        return false;
    }

}