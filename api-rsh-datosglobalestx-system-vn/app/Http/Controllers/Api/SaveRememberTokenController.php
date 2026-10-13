<?php

namespace App\Http\Controllers\Api;

use App\classes\RenovarRememberTokenClass;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use App\Constantes\ConstanteExcepcion;
use App\Constantes\Constante;
use Illuminate\Http\Request;

class SaveRememberTokenController extends Controller
{   
    public function __invoke(Request $request)
    {
        $runUsuario = $request->input('run');
        $is_valid = $this->__validate($runUsuario);
        if($is_valid->http_code != 200) {
            return $is_valid->json();
        }

        $respuesta = new MDSFApiResponse();
        
        $classRememberToken = new RenovarRememberTokenClass;
        $respuestaRememberToken = $classRememberToken($runUsuario);
        if($respuestaRememberToken and strlen($respuestaRememberToken) >= 32) {
            $respuesta->message = Constante::MENSAJE_REMEMBER_TOKEN_RENOVADO;
            $respuesta->data = $respuestaRememberToken;
        } else {
            $respuesta->code = ConstanteExcepcion::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE;
            $respuesta->message = Constante::MENSAJE_ERROR_RUT_MUNICIPAL_NO_EXISTE;
            $respuesta->data = null;
            $respuesta->http_code = 400;
        }
        return $respuesta->json();
    }

    public function __validate($runUsuario)
    {
        $response = new MDSFApiResponse();
        if(!ctype_digit($runUsuario) || strlen($runUsuario) > 8) {
            $response->code = 1201;
            $response->message = Constante::MENSAJE_ERROR_RUT_MUNICIPAL_NO_VALIDO;
            $response->data = null;
            $response->http_code = 400;
        }
        return $response;
    }
}
