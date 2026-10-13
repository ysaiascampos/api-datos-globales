<?php

namespace App\classes;

use App\Models\Usuario;
use App\Models\UsuarioComuna;
use App\Models\Comuna;
use Illuminate\Support\Facades\DB;

class UsuarioAsignacionClass
{
    public function __invoke($runUsuario = null, $campoAdicional = null)
    {
        $respuestaUsuario = Usuario::where('RUT_USUARIO', $runUsuario)->first();

        if ($respuestaUsuario) {
            $idComuna = UsuarioComuna::obtenerIdComunaPorRut($respuestaUsuario->rut_usuario);
            $respuestaUsuario->com_id = $idComuna ? intval($idComuna) : null;

            if ($campoAdicional && $respuestaUsuario->id_institucion) {
                $camposArray = array_map('trim', explode(',', $campoAdicional));
                if (in_array('nombre-institucion', $camposArray)) {
                    $institucion = DB::table('AB_INSTITUCION')
                        ->where('ID_INSTITUCION', $respuestaUsuario->id_institucion)
                        ->first();
                    if ($institucion) {
                        $respuestaUsuario->nombre_institucion = $institucion->NOMBRE_INSTITUCION ?? $institucion->nombre_institucion ?? null;
                    }
                }
            }
        }

        return $respuestaUsuario;
    }
}
