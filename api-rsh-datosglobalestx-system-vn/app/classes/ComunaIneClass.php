<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\LocacionComuna;

class ComunaIneClass
{
    public function __invoke($comunaIne)
    {
        $dataComunaIne = LocacionComuna::findByComunaIne($comunaIne);
        if(!$dataComunaIne){

        }
        return $dataComunaIne;
    }
}