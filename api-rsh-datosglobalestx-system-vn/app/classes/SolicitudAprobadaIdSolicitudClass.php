<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudAprobadaIdSolicitudClass
{
    public function __invoke($idSolicitud)
    {
        $dataSolicitud = Solicitud::findAprobateByIdSolicitud($idSolicitud);
        return $dataSolicitud;
    }
}
