<?php


namespace App\Http\ResponseData;



class PersonaCortaData extends DataResponse
{
    private $persona;
    public function __construct( $persona)
    {
        $this->persona = $persona;
        $this->data = self::mapPersona();
    }

    private function mapPersona()
    {
        $respuestaPersona = [
            'run' => self::entero($this->persona->per_run),
            'dv' => $this->persona->per_dv,
            'nombres' => $this->persona->per_nombres,
            'apellido-paterno' => $this->persona->per_apellidopaterno,
            'apellido-materno' => $this->persona->per_apellidomaterno,
            'edad' => self::entero($this->persona->per_edad),
        ];

        return $respuestaPersona;
    }
}
