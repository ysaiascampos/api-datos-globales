<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Calle;
use App\Models\GrupoFamiliar;
use App\Models\Personas;

class NombreCalleComunaineIdlocalidadClass
{
    public function __invoke($codigoCalle, $comunaIne, $idLocalidad = null)
    {
        $calle = null;
        $calle_aux = null;
        if(isset($idLocalidad)){
            $calle_aux = Calle::findByCodigoCalleIdLocalidadComunaIne($codigoCalle, $idLocalidad, $comunaIne);
        }

        if (!empty($calle_aux)) {
            $calle = $calle_aux->nombre;
        } else {
            $calle_aux = Calle::findByCodigoCalleComunaIne($codigoCalle, $comunaIne);
            if (!empty($calle_aux)) {
                $calle = $calle_aux->nombre;
            }
        }
        return $calle;
    }
}