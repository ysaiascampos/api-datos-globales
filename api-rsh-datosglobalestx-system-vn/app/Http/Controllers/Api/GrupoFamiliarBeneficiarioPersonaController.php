<?php

namespace App\Http\Controllers\Api;

use App\classes\GrupoFamiliarBeneficiarioPersonaClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\GrupoFamiliarBeneficiarioPersonaData;


class GrupoFamiliarBeneficiarioPersonaController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classGrupoFamiliarBeneficiarioPersona = new GrupoFamiliarBeneficiarioPersonaClass;
            $respuestaGrupoFamiliarBeneficiarioPersona = $classGrupoFamiliarBeneficiarioPersona($runCiudadano);
            if($respuestaGrupoFamiliarBeneficiarioPersona) {
                $grupoFamiliarBeneficiarioPersonaData = new GrupoFamiliarBeneficiarioPersonaData($respuestaGrupoFamiliarBeneficiarioPersona);
                $respuesta->data = $grupoFamiliarBeneficiarioPersonaData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de solicitudes grupo familiar', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

return $respuesta->json();
    }
}
