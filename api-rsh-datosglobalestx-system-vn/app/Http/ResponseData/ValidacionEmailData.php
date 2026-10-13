<?php


namespace App\Http\ResponseData;


class ValidacionEmailData extends DataResponse
{
    private $validacionEmail;
    public function __construct($ciudadanoValidacionEmail)
    {
        $this->validacionEmail = $ciudadanoValidacionEmail;
        $this->data = self::mapValidacionEmail();
    }

    private function mapValidacionEmail()
    {
        return [
                "email-temporal" => isset($this->validacionEmail)?$this->validacionEmail->per_mail_temp:null,
                "estado-token" => isset($this->validacionEmail)?$this->validacionEmail->estado_token:null,
        ];
    }
}
