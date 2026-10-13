<?php


namespace App\Http\ResponseData;



class DatosContactoData extends DataResponse
{
    private $persona;
    private $personaTokenMail;
    public function __construct( $ciudadano)
    {
        $this->persona = $ciudadano["persona"];
        $this->personaTokenMail = $ciudadano["persona-token-mail"];
        $this->data = self::mapDatosDeContacto();
    }

    private function mapDatosDeContacto()
    {
        $validacionEmailData = new ValidacionEmailData($this->personaTokenMail);
        return [
            'telefono' => $this->persona['telefono'],
            'email' => $this->persona['email'],
            'validacion-email' => $validacionEmailData->data
        ];
    }
}
