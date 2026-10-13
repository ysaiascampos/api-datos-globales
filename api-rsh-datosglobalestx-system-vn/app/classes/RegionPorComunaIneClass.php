<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\LocacionRegion;

class RegionPorComunaIneClass
{
    public function __invoke($comunaIne)
    {
        $dataRegionPorComunaIne = LocacionRegion::getRegionPorComuna($comunaIne);
        if(!$dataRegionPorComunaIne){

        }
        return $dataRegionPorComunaIne;
    }
}