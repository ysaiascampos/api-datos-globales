<?php


namespace App\Http\ResponseData;



class PoseeRSHData extends DataResponse
{
    private $personaPoseeRSH;
    public function __construct( $respuestaPoseeRSH)
    {
        $this->personaPoseeRSH = $respuestaPoseeRSH;
        $this->data = self::mapPoseeRSH();
    }

    private function mapPoseeRSH()
    {
        return [
            'posee-rsh' => $this->personaPoseeRSH['posee-rsh'],
            'mensaje' => null
        ];
    }
}
