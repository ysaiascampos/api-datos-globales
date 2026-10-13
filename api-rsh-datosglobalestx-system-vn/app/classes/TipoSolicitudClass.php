<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\TipoSolicitud;

class TipoSolicitudClass
{
    public function __invoke()
    {
        $dataListaSolicitudes = TipoSolicitud::solicitudesVigentes();
        if(!$dataListaSolicitudes){
            $dataListaSolicitudes = [];
        }
        return $dataListaSolicitudes;
    }
}
