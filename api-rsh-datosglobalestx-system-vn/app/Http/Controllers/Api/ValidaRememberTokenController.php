<?php

namespace App\Http\Controllers\Api;

use App\classes\ValidaRememberTokenClass;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use App\Constantes\ConstanteExcepcion;
use App\Constantes\Constante;
use Illuminate\Http\Request;

class ValidaRememberTokenController extends Controller
{
        public function __invoke(Request $request)
    {
        $token = explode('~', $request->input('token'));
        $id_institucion = $token[0];
        $remember_token = $token[1].'~'.$token[2];
        
        $is_valid = $this->__validate($id_institucion, $remember_token);
        if($is_valid->http_code != 200) {
            return $is_valid->json();
        }

        $respuesta = new MDSFApiResponse();
        
        $classRememberToken = new ValidaRememberTokenClass;
        $respuestaRememberToken = $classRememberToken($id_institucion, $request->input('token'));
        if($respuestaRememberToken) {
            $respuesta->message = Constante::MENSAJE_REMEMBER_TOKEN_VALIDO;
            $respuesta->data = $respuestaRememberToken;
        } else {
            $respuesta->code = ConstanteExcepcion::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE;
            $respuesta->message = Constante::MENSAJE_ERROR_TOKEN_NO_VALIDO;
            $respuesta->data = false;
            $respuesta->http_code = 400;
        }
        return $respuesta->json();
    }

    public function __validate($id_institucion, $remember_token)
    {
        $minutos_token_valido = 15;
        $response = new MDSFApiResponse();
        if(!ctype_digit($id_institucion)) {
            $response->code = 1201;
            $response->message = Constante::MENSAJE_ERROR_ID_INSTITUCION_NO_VALIDO;
            $response->data = false;
            $response->http_code = 400;
        }
        if (!$remember_token || strlen($remember_token) < 48 || substr_count($remember_token, '~') !== 1) {
            $response->code = 9999;  
            $response->message = Constante::MENSAJE_ERROR_TOKEN_NO_VALIDO;
            $response->data = false;
            $response->http_code = 400;
            return $response;
        }
        $remember_token = explode('~', $remember_token);
        if(count($remember_token) != 2) {
            $response->code = 9998;
            $response->message = Constante::MENSAJE_ERROR_TOKEN_NO_VALIDO;
            $response->data = false;
            $response->http_code = 400;
            return $response;
        }
        $date_token = $remember_token[1];
        date_default_timezone_set('America/Santiago');
        $date_now = date('YmdHi', strtotime('-'.$minutos_token_valido.' minutes'));
        if($date_now > $date_token) {
            $response->code = 9997;
            $response->message = Constante::MENSAJE_ERROR_TOKEN_EXPIRADO;
            $response->data = false;
            $response->http_code = 400;
            return $response;
        }
        return $response;
    }
}
