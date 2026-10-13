<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudesGrupoFamiliarViaDatoNuevoClass
{
    public function __invoke($perId)
    {
        $dataSolicitudes = (new DatosDeSolicitud)->getSolicitudesExcluyentesPorRunViaDatoNuevo($perId);
        return $dataSolicitudes;
    }
}
