<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudAprobadaIngresoDomicilioClass
{
    public function __invoke($idSolicitud)
    {
        $dataSolicitud = Solicitud::SolicitudAprobadaIngresoDomicilio($idSolicitud);
        return $dataSolicitud;
    }
}
