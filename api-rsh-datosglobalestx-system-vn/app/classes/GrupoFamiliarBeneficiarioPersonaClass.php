<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\GrupoFamiliar;

class GrupoFamiliarBeneficiarioPersonaClass
{
    public function __invoke($runCiudadano = null)
    {

        $respuestaGrupoFamiliarBeneficiarioPersona = GrupoFamiliar::findNuevoFolioVigenteAprobadoByRunCiudadano($runCiudadano);

        $respuesta = $respuestaGrupoFamiliarBeneficiarioPersona;
        return $respuesta;
    }
}
