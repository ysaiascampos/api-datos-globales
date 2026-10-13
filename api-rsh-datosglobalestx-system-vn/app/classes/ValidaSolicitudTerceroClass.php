<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\PersonasTokenMail;
use App\Models\ValidaSolicitudTercero;

class ValidaSolicitudTerceroClass
{
    public function __invoke($runCiudadano = null)
    {
        try {
            $respuesta = ValidaSolicitudTercero::listSolicitudes($runCiudadano);
        } catch (\Exception $ex) {
            throw $ex;
        }
        return $respuesta;
    }
}