<?php

namespace App\Http\Controllers\Api;

use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Classes\ObtenerSolicitudesClass;
use App\Http\ResponseData\ObtenerSolicitudesData;
use App\Constantes\Constante;


class ObtenerSolicitudesController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        $respuesta = new MDSFApiResponse();
        $classObtenerSolicitudes = new ObtenerSolicitudesClass;
        $respuestaObtenerSolicitudes = $classObtenerSolicitudes($runCiudadano);
        if($respuestaObtenerSolicitudes) {
            $datosObtenerSolicitudes = new ObtenerSolicitudesData($respuestaObtenerSolicitudes);
            $respuesta->data = $datosObtenerSolicitudes->data;
        } else {
            $respuesta->data = [];
        }

        return $respuesta->json();
    }
}
