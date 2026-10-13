<?php


namespace App\Http\ResponseData;



class PersonaCortaData extends DataResponse
{
    private $personaCorta;
    public function __construct($persona)
    {
        $this->personaCorta = $persona['data']['persona-asignacion'];
        $this->data['persona-asignacion'] = self::mapPersonaCorta();
    }

    private function mapPersonaCorta()
    {
        $respuestaPersona = [
            'run' => $this->personaCorta['per-run'],
            'dv' => $this->personaCorta['per-dv'],
            'nombres' => $this->personaCorta['per-nombres'],
            'apellido-paterno' => $this->personaCorta['per-apellidopaterno'],
            'apellido-materno' => $this->personaCorta['per-apellidomaterno'],
            'edad' => $this->personaCorta['per-edad'],
        ];

        return $respuestaPersona;
    }
}
