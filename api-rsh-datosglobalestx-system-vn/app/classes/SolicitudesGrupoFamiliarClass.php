<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudesGrupoFamiliarClass
{
    public function __invoke($runCiudadano,$runsPorIdGrupoFamiliar)
    {
        $persona = Personas::findBasicByRunCiudadano($runCiudadano);
        $dataSolicitudes = (new DatosDeSolicitud)->getSolicitudDatosGlobales2($persona, $runsPorIdGrupoFamiliar);
        return $dataSolicitudes;
    }
}
