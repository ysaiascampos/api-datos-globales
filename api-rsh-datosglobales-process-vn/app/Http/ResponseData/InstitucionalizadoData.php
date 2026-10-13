<?php


namespace App\Http\ResponseData;



class InstitucionalizadoData extends DataResponse
{
    private $institucionalizado;
    public function __construct($respuestaInstitucionalizado)
    {
        $this->institucionalizado = $respuestaInstitucionalizado;
        $this->data = self::mapInstitucionalizado();
    }

    private function mapInstitucionalizado()
    {
        return [
            'institucionalizado' => $this->institucionalizado['institucionalizado'],
        ];
    }
}
