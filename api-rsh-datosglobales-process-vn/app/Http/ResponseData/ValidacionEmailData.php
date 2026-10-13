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
                "email-temporal" => $this->validacionEmail['email-temporal'],
                "estado-validacion" => $this->validacionEmail['estado-validacion'],
                "mensaje-ciudadano" => $this->validacionEmail['mensaje-ciudadano']
        ];
    }
}
