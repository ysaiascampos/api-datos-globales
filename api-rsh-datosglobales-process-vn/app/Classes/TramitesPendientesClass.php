<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Http\ResponseData\ObtenerSolicitudesData;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class TramitesPendientesClass
{
    public function __invoke($runCiudadano = null)
    {
        $classObtenerSolicitudes = new ObtenerSolicitudesClass;
        $respuestaObtenerSolicitudes = $classObtenerSolicitudes($runCiudadano);
        $respuestaSolicitudes = ['municipal'=> [],
            'ciudadano'=> []];
        if(count($respuestaObtenerSolicitudes["solicitudes"])>0){
            foreach ($respuestaObtenerSolicitudes["solicitudes"] as $solicitud) {
                if($solicitud["origen-registro"] == Constante::ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA) {
                    if($solicitud["vigente"] == Constante::VIGENTE_3_SOLICITUD_BORRADOR &&
                    $solicitud["id-estado"] == Constante::ESTADO_SOLICITUD_11_EN_DIGITACION) {
                        $respuestaSolicitudes['ciudadano'][] = $solicitud;
                    }
                } else {
                    if($solicitud["id-estado"] == Constante::ESTADO_SOLICITUD_1_PENDIENTE_ENTREGAR_DOCUMENTOS &&
                        $solicitud["vigente"] == Constante::VIGENTE_1_VIGENTE) {
                        $respuestaSolicitudes['municipal'][] = $solicitud;
                    }
                }
            }
        }



        return $respuestaSolicitudes;
    }
}
