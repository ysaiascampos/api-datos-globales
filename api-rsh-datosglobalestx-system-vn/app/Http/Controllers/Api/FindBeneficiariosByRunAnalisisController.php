<?php

namespace App\Http\Controllers\Api;

use App\classes\FindBeneficiariosByRunAnalisisClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\FindBeneficiariosByRunAnalisisData;


class FindBeneficiariosByRunAnalisisController extends Controller
{

    public function __invoke($runCiudadano)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classFindBeneficiariosByRunAnalisis = new FindBeneficiariosByRunAnalisisClass;
            $respuestaFindBeneficiariosByRunAnalisis = $classFindBeneficiariosByRunAnalisis($runCiudadano);
            if($respuestaFindBeneficiariosByRunAnalisis) {
                $solicitudData = new FindBeneficiariosByRunAnalisisData($respuestaFindBeneficiariosByRunAnalisis);
                $respuesta->data = $solicitudData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de beneficiarios', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
