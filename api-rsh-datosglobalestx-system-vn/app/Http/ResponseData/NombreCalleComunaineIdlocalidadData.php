<?php


namespace App\Http\ResponseData;



class NombreCalleComunaineIdlocalidadData extends DataResponse
{
    private $calle;
    public function __construct($calleData)
    {
        $this->calle = $calleData;
        $this->data['calle'] = self::mapCalle();
    }

    private function mapCalle()
    {
        return [
            "nombre-calle" => $this->calle,
        ];
    }
}
