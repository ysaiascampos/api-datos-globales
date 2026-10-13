<?php

namespace App\classes;


use App\Models\Usuario;


class RenovarRememberTokenClass
{
    public function __invoke($runUsuario = null)
    {
        $token = $this->generateSecureToken(rand(48, 64));
        date_default_timezone_set('America/Santiago');
        $date = date('YmdHi');
        $id_institucion = Usuario::obtenerIdInstitucionPorRut($runUsuario);
        $token = $id_institucion.'~'.$token.'~'.$date;
        $respuestaUsuario = Usuario::updateRememberToken($runUsuario, $token);
        return $respuestaUsuario ? $token : false;
    }

    private function generateSecureToken($length)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789()_-.';
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $token;
    }
}