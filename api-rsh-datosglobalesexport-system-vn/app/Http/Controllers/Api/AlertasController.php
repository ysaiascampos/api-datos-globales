<?php

namespace App\Http\Controllers\Api;

use App\Classes\DatosDeFuentes;
use App\Constantes\Constante;
use App\Http\ResponseData\AlertasData;
use App\Models\MDSFApiResponse;
use App\Models\PersonasExport;
use App\Models\SupervisionMensajesExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class AlertasController extends Controller
{

    public function __invoke($runCiudadano = null)
    {
        try{
            $respuesta = new MDSFApiResponse();
            $grupoFamiliarExport = PersonasExport::findByRunCiudadanoGrupofamiliarFolioId($runCiudadano);
            $respuestaAlertas = null;
            if($grupoFamiliarExport){
                $integrantesExport = PersonasExport::findByIdGrupoFamiliarExport($grupoFamiliarExport->grupfami_id);
                if($integrantesExport){
                    $respuestaAlertas = SupervisionMensajesExport::getMensajes($runCiudadano,$grupoFamiliarExport->grupfami_nuevofolio,$integrantesExport);
                }
            }
            if ($respuestaAlertas) {
                $alertasData = new AlertasData($respuestaAlertas);
                $respuesta->data = $alertasData->data;
            } else {
                $respuesta->data = null;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de alertas', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data = null;
        }

        return $respuesta->json();
    }
}
