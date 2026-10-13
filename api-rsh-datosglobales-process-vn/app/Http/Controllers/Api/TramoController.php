<?php

namespace App\Http\Controllers\Api;

use App\Classes\TramoClass;
use App\Constantes\Constante;
use App\Http\ResponseData\TramoData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class TramoController extends Controller
{

    public function __invoke($runCiudadano = null)
    {

        $respuesta = new MDSFApiResponse();
        try{
            $classTramo = new TramoClass;
            $respuestaTramo = $classTramo($runCiudadano);
            if($respuestaTramo) {
                $datosTramo = new TramoData($respuestaTramo["tramo"]);
                $respuesta->data = $datosTramo->data;
            } else {
                $respuesta->data = ["tramo"=>[]];
            }


            if(isset($respuestaTramo['code'])){
                $respuesta->code = $respuestaTramo['code'];
            }
            if(isset($respuestaTramo['message'])){
                $respuesta->message = $respuestaTramo['message'];
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos del tramo', $e);
            $respuesta->code = 1301;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
