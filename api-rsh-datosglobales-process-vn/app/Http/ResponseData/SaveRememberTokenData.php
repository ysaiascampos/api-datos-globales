<?php


namespace App\Http\ResponseData;



class SaveRememberTokenData extends DataResponse
{
    private $generateRememberToken;
    public $data;

    public function __construct($respuestaRememberToken)
    {
        $this->generateRememberToken = $respuestaRememberToken;
        $this->data = self::mapRememberToken();
    }

    private function mapRememberToken()
    {
        return [
            'code' => $this->generateRememberToken['code'],
            'message' => $this->generateRememberToken['message'],
            'result' => ['remember_token' => $this->generateRememberToken['data']],
        ];
    }
}
