<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\GrupoFamiliar;
use App\Models\Personas;

class PersonaInstitucionalizadoClass
{
    public function __invoke($runCiudadano = null)
    {

        $respuestaPersona = Personas::findBasicByRunCiudadano($runCiudadano);
        if($respuestaPersona){
            $respuestaPersona = Institucionalizados::evaluarExistenciaInstitucionalizados($respuestaPersona);
        }
        return $respuestaPersona;
    }
}
