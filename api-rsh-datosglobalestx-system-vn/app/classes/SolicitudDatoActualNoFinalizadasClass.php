<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\Solicitud;

class SolicitudDatoActualNoFinalizadasClass
{
    public function __invoke($tipoSolicitud, $datoActual)
    {
        $dataSolicitudes = Solicitud::findNoFinalizadasByTipoAndDatoActual($tipoSolicitud, $datoActual);
        return $dataSolicitudes;
    }
}
