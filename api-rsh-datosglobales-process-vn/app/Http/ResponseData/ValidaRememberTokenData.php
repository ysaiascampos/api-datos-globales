<?php


namespace App\Http\ResponseData;



class ValidaRememberTokenData extends DataResponse
{
    private $validarRememberToken;
    public $data;

    public function __construct($respuestaRememberToken)
    {
        $this->validarRememberToken = $respuestaRememberToken;
        $this->data = self::mapRememberToken();
    }

    private function mapRememberToken()
    {
        return [
            'code' => $this->validarRememberToken['code'],
            'message' => $this->validarRememberToken['message'],
            'result' => $this->validarRememberToken['data'],
        ];
    }
}
