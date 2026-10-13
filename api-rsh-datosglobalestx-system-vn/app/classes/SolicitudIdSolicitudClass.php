<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudIdSolicitudClass
{
    public function __invoke($idSolicitud)
    {
        $dataSolicitud = Solicitud::findBasicByIdSolicitud($idSolicitud);
        return $dataSolicitud;
    }
}
