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
        $this->data['datos-de-contacto'] = self::mapDatosDeContacto();
    }

    private function mapDatosDeContacto()
    {
        $validacionEmailData = new ValidacionEmailData($this->personaTokenMail);
        return [
            'telefono' => $this->persona->per_telefono,
            'email' => $this->persona->per_email,
            'validacion-email' => $validacionEmailData->data
        ];
    }
}
