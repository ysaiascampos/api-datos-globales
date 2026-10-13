<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\GrupoFamiliar;
use App\Models\Personas;

class PersonaAsignacionClass
{
    public function __invoke($runCiudadano = null)
    {

        $respuestaPersona = Personas::findByRunCiudadano($runCiudadano);
        return $respuestaPersona;
    }
}