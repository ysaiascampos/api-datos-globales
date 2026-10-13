<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Models\PersonasExport;
use App\Classes\Utiles;
use stdClass;

class OptenerIntegrantesExportClass
{
    public $cargarGrupoFamiliarExport;
    public $grupoFamiliarExport;
    public function __invoke($grupfamiId)
    {
        $integrantes = null;
        if (!empty($grupfamiId)) {

            $integrantes = PersonasExport::findBasicByIdGrupoFamiliarExport($grupfamiId);
        }
        return $integrantes;
    }

}
