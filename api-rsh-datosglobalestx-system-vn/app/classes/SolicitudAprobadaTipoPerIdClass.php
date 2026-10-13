<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudAprobadaTipoPerIdClass
{
    public function __invoke($tipoSolicitud, $perId)
    {
        $dataSolicitud = Solicitud::findAprobateByTipoAndPerId($tipoSolicitud, $perId);
        return $dataSolicitud;
    }
}
