<?php

namespace App\Http\ResponseData;

use DateTime;

class PersonaData extends DataResponse
{
    private $persona;
    private $personaCorta;
    public function __construct($ciudadano)
    {
        $personaCortaData = new PersonaCortaData($ciudadano);
        $this->personaCorta = $personaCortaData->data;
        $this->persona = $ciudadano['data']['persona-asignacion'];
        $this->data['persona-asignacion'] = self::mapPersona();
    }

    private function mapPersona()
    {
        $respuestaPersona = array_merge($this->personaCorta['persona-asignacion'],[
            'id' => self::entero($this->persona['per-id']),
            'fecha-nacimiento' => self::fecha($this->persona['per-fechanacimiento']),
            'estado-civil' => $this->persona['per-estadocivil'],
            'telefono' => self::entero($this->persona['per-telefono']),
            'telefono-movil' => self::entero($this->persona['per-celular']),
            'email' => $this->persona['per-email'],
            'sexo' => $this->persona['per-sexo'],
            'fecha-actualizacion' => $this->persona['per-fechaactualizacion'],
            'situacion' => self::booleano($this->persona['situacion']),
            //'tiene-rsh' => self::booleano($this->persona->TIENE_RSH),
            'nacionalidad' => $this->persona['nacionalidad'],
            //'validacion-email' => $this->persona->validacion_email,
        ]);
        return $respuestaPersona;
    }
}
