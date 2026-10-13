<?php


namespace App\Http\ResponseData;



class TramoQuintilListaData extends DataResponse
{
    private $TramoQuintilLista;
    public function __construct($TramoQuintilListaData)
    {
        $this->TramoQuintilLista = $TramoQuintilListaData;
        $this->data['tramo-quintil'] = self::mapTramoQuintilLista();
    }

    private function mapTramoQuintilLista()
    {
        $respuesta = [];
        foreach ($this->TramoQuintilLista as $tramoQuintil) {
            $respuesta[$tramoQuintil->id] = [
                "id" => $tramoQuintil->id,
                "rango" => $tramoQuintil->rango,
                "nombre" => $tramoQuintil->nombre,
            ];
        }
        return $respuesta;
    }
}
