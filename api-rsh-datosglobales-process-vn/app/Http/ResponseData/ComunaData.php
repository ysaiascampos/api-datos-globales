<?php


namespace App\Http\ResponseData;



class ComunaData extends DataResponse
{
    private $grupoComunaGrupoFamiliarExport;
    public function __construct( $respuestaComunaGrupoFamiliarExport)
    {
        $this->grupoComunaGrupoFamiliarExport = $respuestaComunaGrupoFamiliarExport;
        $this->data["comuna"] = self::mapComuna();
    }

    private function mapComuna()
    {
        return [
            'comunaine' => $this->grupoComunaGrupoFamiliarExport["grupfami-comunaine"],
            'descripcion' => $this->grupoComunaGrupoFamiliarExport["descripcion"],
        ];
    }
}
