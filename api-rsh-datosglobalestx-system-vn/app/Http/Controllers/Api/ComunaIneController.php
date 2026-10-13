<?php

namespace App\Http\Controllers\Api;

use App\classes\ComunaIneClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\ComunaIneData;


class ComunaIneController extends Controller
{

    public function __invoke($comunaIne)
    {
        $respuesta = new MDSFApiResponse();
        $classComunaIne = new ComunaIneClass;
        $respuestaComunaIne = $classComunaIne($comunaIne);
        if($respuestaComunaIne){
            $ComunaIneData = new ComunaIneData($respuestaComunaIne);
            $respuesta->data = $ComunaIneData->data;
        } else {
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1201;
            $respuesta->message = "No se encuentra en registrado en bd";
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
