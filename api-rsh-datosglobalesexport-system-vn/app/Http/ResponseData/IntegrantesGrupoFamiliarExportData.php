<?php


namespace App\Http\ResponseData;



class IntegrantesGrupoFamiliarExportData extends DataResponse
{
    private $IntegrantesGrupoFamiliarExport;
    public function __construct( $integrantesGrupoFamiliarExport)
    {
        $this->IntegrantesGrupoFamiliarExport = $integrantesGrupoFamiliarExport;
        $this->data['integrantes-grupo-familiar-export'] = self::mapIntegrantesGrpoFaumiliarExport();
    }

    private function mapIntegrantesGrpoFaumiliarExport()
    {
        $integrantes = [];

        foreach ($this->IntegrantesGrupoFamiliarExport as $integrante) {
            $personaCortaData = new PersonaCortaData($integrante);
            $integrantes[] = $personaCortaData->data;
        }
        return $integrantes;
    }
}
