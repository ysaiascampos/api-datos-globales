<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudesGrupoFamiliarViaOtrosClass
{
    public function __invoke($runCiudadano,$runsPorIdGrupoFamiliar)
    {
        $persona = Personas::findByRunCiudadano($runCiudadano);
        $dataSolicitudes = (new DatosDeSolicitud)->getSolicitudesExcluyentesPorRunOtros($persona, $runsPorIdGrupoFamiliar);
        return $dataSolicitudes;
    }
}
