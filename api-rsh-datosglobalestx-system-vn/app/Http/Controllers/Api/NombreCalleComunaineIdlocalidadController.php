<?php

namespace App\Http\Controllers\Api;

use App\classes\NombreCalleComunaineIdlocalidadClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\ResponseData\NombreCalleComunaineIdlocalidadData;


class NombreCalleComunaineIdlocalidadController extends Controller
{

    public function __invoke($codigoCalle, $comunaIne, $idLocalidad = null)
    {
        try {
            $respuesta = new MDSFApiResponse();
            $classNombreCalleComunaineIdlocalidad = new NombreCalleComunaineIdlocalidadClass;
            $respuestaNombreCalleComunaineIdlocalidad = $classNombreCalleComunaineIdlocalidad($codigoCalle, $comunaIne, $idLocalidad);
            if($respuestaNombreCalleComunaineIdlocalidad){
                $NombreCalleComunaineIdlocalidadData = new NombreCalleComunaineIdlocalidadData($respuestaNombreCalleComunaineIdlocalidad);
                $respuesta->data = $NombreCalleComunaineIdlocalidadData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener Nombre de la Calle', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
