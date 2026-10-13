<?php


namespace App\Http\ResponseData;



class SituacionBasicaData extends DataResponse
{
    private $personaSituacionBasica;
    public function __construct( $respuestaSituacionBasica)
    {
        $this->personaSituacionBasica = $respuestaSituacionBasica;
        $this->data = self::mapSituacionBasica();
    }

    private function mapSituacionBasica()
    {
        return [
            'situacion' => $this->personaSituacionBasica['situacion'],
            'mensaje' => $this->personaSituacionBasica['mensaje'],
        ];
    }
}
