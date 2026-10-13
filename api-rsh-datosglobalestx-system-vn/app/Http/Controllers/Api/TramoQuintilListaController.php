<?php

namespace App\Http\Controllers\Api;

use App\classes\TramoQuintilListaClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\TramoQuintilListaData;


class TramoQuintilListaController extends Controller
{

    public function __invoke()
    {

        try {
            $respuesta = new MDSFApiResponse();
            $classTramoQuintilLista = new TramoQuintilListaClass;
            $respuestaTramoQuintilLista = $classTramoQuintilLista();
            if($respuestaTramoQuintilLista){
                $TramoQuintilListaData = new TramoQuintilListaData($respuestaTramoQuintilLista);
                $respuesta->data = $TramoQuintilListaData->data;
            } else {
                $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
                $respuesta->code = 1404;
                $respuesta->message = "Problemas al obtener listado de tramos";
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener listado de tramos', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
