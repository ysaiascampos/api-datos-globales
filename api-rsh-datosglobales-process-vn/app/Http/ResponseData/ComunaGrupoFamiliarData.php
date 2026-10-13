<?php


namespace App\Http\ResponseData;



class ComunaGrupoFamiliarData extends DataResponse
{
    private $comunaGrupoFamiliarExport;
    public function __construct( $respuestaComunaGrupoFamiliarExport)
    {
        $this->comunaGrupoFamiliarExport = $respuestaComunaGrupoFamiliarExport;
        $this->data["comuna"] = self::mapComuna();
    }

    private function mapComuna()
    {
        return [
            'comunaine' => $this->comunaGrupoFamiliarExport["grupfami-comunaine"],
            'descripcion' => $this->comunaGrupoFamiliarExport["descripcion"],
        ];
    }
}
