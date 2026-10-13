<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class TodasSolicitudesGrupoFamiliarClass
{
    public function __invoke($runCiudadano,$runsPorIdGrupoFamiliar)
    {
        $persona = Personas::findByRunCiudadano($runCiudadano);
        $dataSolicitudes = (new DatosDeSolicitud)->getBasicSolicitudDatosGlobales2($persona, $runsPorIdGrupoFamiliar);
        return $dataSolicitudes;
    }
}
