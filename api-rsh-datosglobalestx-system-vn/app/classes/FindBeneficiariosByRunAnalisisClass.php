<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\BeneficiarioGrupoFamiliar;
use App\Models\Personas;
use App\Models\Solicitud;

class FindBeneficiariosByRunAnalisisClass
{
    public function __invoke($runCiudadano)
    {
        $dataSolicitud = BeneficiarioGrupoFamiliar::findBeneficiariosByRunAnalisis($runCiudadano);
        return $dataSolicitud;
    }
}
