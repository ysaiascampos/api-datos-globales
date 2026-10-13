<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudesGrupoFamiliarViaRunClass
{
    public function __invoke($runCiudadano)
    {
        $dataSolicitudes = (new DatosDeSolicitud)->getSolicitudesExcluyentesPorRunViaRun($runCiudadano);
        return $dataSolicitudes;
    }
}
