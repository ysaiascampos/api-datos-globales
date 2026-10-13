<?php


namespace App\Http\ResponseData;



class ComunaIneData extends DataResponse
{
    private $comunaIne;
    public function __construct($comunaIneData)
    {
        $this->comunaIne = $comunaIneData;
        $this->data['comuna-ine'] = self::mapComunaIne();
    }

    private function mapComunaIne()
    {
        return [
            "codigo" => $this->comunaIne->codigo,
            "descripcion" => $this->comunaIne->descripcion,
        ];
    }
}
