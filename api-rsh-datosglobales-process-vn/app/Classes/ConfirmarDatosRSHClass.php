<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ConfirmarDatosRSHClass
{
    public function __invoke($runCiudadano = null)
    {


        $dataResponse = ['email'=>
                ['estado' =>false,
                'fecha-creacion' =>null],
                'telefono'=>['estado' =>false,
                'fecha-creacion' =>null]
        ];
        $classDatosContacto = new DatosContactoClass;
        $respuestaDatosContacto = $classDatosContacto($runCiudadano);
        if($respuestaDatosContacto){
            $persona = $respuestaDatosContacto["persona"];
        } else {
            $persona['telefono'] = null;
            $persona['email'] = null;
        }

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-run-solicitante/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataSolicitud = [];
        if($apiresponse->http_code == 200 && $apiresponse->data) {
            $apiresponse = json_decode(json_encode($apiresponse->data), true);
            $dataSolicitud = $apiresponse["solicitud"];
        } else {
            $dataResponse["code"] = $apiresponse->code;
            $dataResponse["message"] = $apiresponse->message;
        }
        $hace12Meses = Carbon::now()->subMonths(12);
        if(isset($dataSolicitud) && count($dataSolicitud)>0){
            $fechaUltimaSolicitud = $dataSolicitud["fecha-creacion"];
            if($fechaUltimaSolicitud >= $hace12Meses){
                $dataResponse['email']['estado'] = true;
                $dataResponse['email']['fecha-creacion'] = $fechaUltimaSolicitud;
                $dataResponse['telefono']['estado'] = true;
                $dataResponse['telefono']['fecha-creacion'] = $fechaUltimaSolicitud;
            } else {
                if(isset($persona['telefono'])){
                    $dataResponse['telefono']['fecha-creacion'] = $fechaUltimaSolicitud;
                }
                if(isset($persona['email'])){
                    $dataResponse['email']['fecha-creacion'] = $fechaUltimaSolicitud;
                }
            }
        }

        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/log-datos-contacto-run/' . $runCiudadano);
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $dataLogDatosContacto = [];
        if($apiresponse->http_code == 200 && $apiresponse->data) {
            $apiresponse = json_decode(json_encode($apiresponse->data), true);
            $dataLogDatosContacto = $apiresponse["log-datos-contacto"];
        } else {
            $dataResponse["code"] = $apiresponse->code;
            $dataResponse["message"] = $apiresponse->message;
        }
        if(isset($dataLogDatosContacto) && count($dataLogDatosContacto)>0){
            foreach ($dataLogDatosContacto as $logDatosContacto) {
                if($logDatosContacto['fecha'] >= $hace12Meses) {
                    if (in_array($logDatosContacto['tipo-solicitud'],
                            [Constante::TIPO_SOLICITUD_203_ACTUALIZACION_DATOS_TELEFONO,
                                Constante::TIPO_SOLICITUD_209_CONFIRMACION_TELEFONO])

                    ) {
                        $dataResponse['telefono']['estado'] = true;
                        $dataResponse['telefono']['fecha-creacion'] = $logDatosContacto['fecha'];
                    } else if (in_array($logDatosContacto['tipo-solicitud'],
                        [Constante::TIPO_SOLICITUD_204_ACTUALIZACION_DATOS_MAIL,
                            Constante::TIPO_SOLICITUD_210_CONFIRMACION_MAIL])

                    ) {
                        $dataResponse['email']['estado'] = true;
                        $dataResponse['email']['fecha-creacion'] = $logDatosContacto['fecha'];
                    }
                }
            }
        }

        return $dataResponse;
    }
}
