<?php


namespace App\classes;

use App\Models\Personas;

class SolicitudesGrupoFamiliarViaGrupoFamiliarClass
{
    public function __invoke($runCiudadano)
    {
        $persona = Personas::findByRunCiudadano($runCiudadano);
        $dataSolicitudes = (new DatosDeSolicitud)->getSolicitudesExcluyentesPorRunGrupoFamiliar($persona);
        return $dataSolicitudes;
    }
}
