<?php

namespace App\Http\Controllers\Api;

use App\Classes\InstitucionalizadoClass;
use App\Constantes\Constante;
use App\Http\Controllers\Controller;
use App\Http\ResponseData\InstitucionalizadoData;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Models\MDSFApiResponse;

use Symfony\Component\HttpFoundation\StreamedResponse;

class InstitucionalizadoController extends Controller
{
    public function __invoke($runCiudadano)
    {
        $respuesta = new MDSFApiResponse();

        try {

            $classInstitucionalizado = new InstitucionalizadoClass;
            $respuestaInstitucionalizadoData = $classInstitucionalizado($runCiudadano);
            if(isset($respuestaInstitucionalizadoData["data"])) {
                $datosInstitucionalizadoData = new InstitucionalizadoData($respuestaInstitucionalizadoData["data"]);
                $respuesta->data = $datosInstitucionalizadoData->data;
            } else {
                $respuesta->data = null;
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->message = 'Problemas al obtener Institucionalizado';
                $respuesta->code = 1402;
            }
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener Institucionalizado', $e);
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1402;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
