<?php

namespace App\Http\Controllers\Api;

use App\Classes\ComunaGrupoFamiliarExportClass;
use App\Constantes\Constante;
use App\Http\ResponseData\ComunaData;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ComunaController extends Controller
{

    public function __invoke($runCiudadano = null)
    {

        $respuesta = new MDSFApiResponse();
        try{
            $classComunaGrupoFamiliarExport = new ComunaGrupoFamiliarExportClass;
            $respuestaComunaGrupoFamiliarExport = $classComunaGrupoFamiliarExport($runCiudadano);
            if($respuestaComunaGrupoFamiliarExport) {
                $datosComuna = new ComunaData($respuestaComunaGrupoFamiliarExport["comuna-grupo-familiar-export"]);
                $respuesta->data = $datosComuna->data;
            } else {
                $respuesta->data = ["comuna"=>[]];
            }


            if(isset($respuestaComunaGrupoFamiliarExport['code'])){
                $respuesta->code = $respuestaComunaGrupoFamiliarExport['code'];
            }
            if(isset($respuestaComunaGrupoFamiliarExport['message'])){
                $respuesta->message = $respuestaComunaGrupoFamiliarExport['message'];
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al consultar la comuna', $e);
            $respuesta->code = 1301;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
