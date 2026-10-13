<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use Exception;
use Illuminate\Support\Facades\Http;

class PersonasTokenMailClass
{
    public function __invoke($datosDePersona = null)
    {
        $dataResponse = [
            'email-temporal' => null,
            'estado-validacion'=> Constante::ESTADO_NO_GENERADO_VALIDA_EMAIL,
            'mensaje-ciudadano' => Constante::MSJ_BLOQUEO_VALIDA_EMAIL,
        ];

        try {

            if (isset($datosDePersona['estado-token'])){

                if ($datosDePersona['estado-token'] != Constante::ESTADO_VALIDADO_VALIDA_EMAIL){

                    $dataResponse = [
                        'email-temporal'=>$datosDePersona['email-temporal'],
                        'estado-validacion'=>$datosDePersona['estado-token'],
                        'mensaje-ciudadano' => Constante::MSJ_BLOQUEO_VALIDA_EMAIL,
                    ];

                }else{

                    $dataResponse = [
                        'email-temporal'=>$datosDePersona['email-temporal'],
                        'estado-validacion'=>$datosDePersona['estado-token'],
                        'mensaje-ciudadano' => '',
                    ];

                }

            }

        } catch (Exception $ex) {
            throw $ex;
        }

        return $dataResponse;
    }
}
