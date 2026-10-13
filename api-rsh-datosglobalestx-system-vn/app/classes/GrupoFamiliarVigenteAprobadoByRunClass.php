<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\GrupoFamiliar;
use App\Models\Personas;
use App\Models\Solicitud;
use stdClass;

class GrupoFamiliarVigenteAprobadoByRunClass
{
    public $ciudadano;
    public function __invoke($runCiudadano)
    {
        $grupoFamiliar = GrupoFamiliar::findBasicVigenteAprobadoByRunCiudadano($runCiudadano, false);
        return $grupoFamiliar;
    }
}
