<?php

namespace App\Http\Controllers\Api;

use App\Classes\TramitesPendientesClass;
use App\Constantes\Constante;
use App\Http\Controllers\Controller;
use App\Http\ResponseData\TramitesPendientesData;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Models\MDSFApiResponse;

use Symfony\Component\HttpFoundation\StreamedResponse;

class TramitesPendientesController extends Controller
{
    public function __invoke($runCiudadano)
    {
        $respuesta = new MDSFApiResponse();

        try {

            $classTramitesPendientes = new TramitesPendientesClass;
            $respuestaTramitesPendientesData = $classTramitesPendientes($runCiudadano);
            if($respuestaTramitesPendientesData) {
                $datosTramitesPendientesData = new TramitesPendientesData($respuestaTramitesPendientesData);
                $respuesta->data = $datosTramitesPendientesData->data;
            } else {
                $respuesta->data = [
                    'ciudadano' => [],
                    'municipal' => [],
                ];
            }
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener posee rsh', $e);
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1402;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
