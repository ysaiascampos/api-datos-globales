<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\LocacionComuna;
use App\Models\TramoQuintil;

class TramoQuintilListaClass
{
    public function __invoke()
    {
        $dataTramoQuintilLista = TramoQuintil::findAll();
        if(!$dataTramoQuintilLista){

        }
        return $dataTramoQuintilLista;
    }
}