<?php


namespace App\Http\ResponseData;



class PersonaInstitucionalizadoData extends DataResponse
{
    private $personaInstitucionalizado;
    public function __construct( $dataPersonaInstitucionalizado)
    {
        $this->personaInstitucionalizado = $dataPersonaInstitucionalizado;
        $this->data['persona-institucionalizado'] = self::mapPersonaInstitucionalizado();
    }

    private function mapPersonaInstitucionalizado()
    {
        return [
            'institucionalizado' => $this->personaInstitucionalizado->ES_INSTITUCIONALIZADO,
        ];
    }
}
