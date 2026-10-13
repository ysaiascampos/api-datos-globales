<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DatosContactoClass
{
    public function __invoke($runCiudadano = null)
    {

            $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/datosdecontacto/' . $runCiudadano);
            $apiresponse = MDSFApiResponse::cast($apiresponse);


            if($apiresponse->http_code == 200 && $apiresponse->data) {
                $apiresponse = json_decode(json_encode($apiresponse->data), true);
                $datosDeContacto = $apiresponse["datos-de-contacto"];
                $classPersonasTokenMail = new PersonasTokenMailClass;
                $respuestaPersonasTokenMail = $classPersonasTokenMail($datosDeContacto["validacion-email"]);
                $dataResponse = ['persona' => $datosDeContacto, 'persona-token-mail' => $respuestaPersonasTokenMail];
            } else {
                $dataResponse["code"] = $apiresponse->code;
                $dataResponse["message"] = $apiresponse->message;
            }

        return $dataResponse;
    }
}
