<?php

namespace App\Classes;

class UsuarioAsignacionClass
{
    public function __invoke($runUsuario = null, $campoAdicional = null)
    {
        $respuestaUsuario = Utiles::getUsuarioAsignacion($runUsuario, $campoAdicional);
        return $respuestaUsuario;
    }
}
