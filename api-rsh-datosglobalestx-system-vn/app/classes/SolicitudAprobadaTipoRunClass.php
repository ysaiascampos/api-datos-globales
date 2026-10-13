<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudAprobadaTipoRunClass
{
    public function __invoke($tipoSolicitud, $runCiudadano)
    {
        $dataSolicitud = Solicitud::findAprobateByTipoAndRunCiudadano($tipoSolicitud, $runCiudadano);
        return $dataSolicitud;
    }
}
