<?php

namespace App\Http\Controllers\Api;

use App\Classes\ValidaRememberTokenClass;
use App\Http\Controllers\Controller;
use App\Http\ResponseData\ValidaRememberTokenData;
use App\Constantes\ConstanteUsuario;
use App\Models\MDSFApiResponse;
use Illuminate\Http\Request;

class ValidaRememberTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $respuesta = new MDSFApiResponse();
        $rememberToken = $request->input('token');

        try {
            $classValidarToken = new ValidaRememberTokenClass;
            $respuestaRememberToken = $classValidarToken($rememberToken);
            if(isset($respuestaRememberToken["data"]) && $respuestaRememberToken["data"]) {
                $datosRememberToken = new ValidaRememberTokenData($respuestaRememberToken);
                $respuesta->data = $datosRememberToken->data['result'];
                $respuesta->code = $datosRememberToken->data['code'];
                $respuesta->message = $datosRememberToken->data['message'];
            } else {
                $respuesta->data = false;
                $respuesta->code = isset($respuestaRememberToken["code"]) ? $respuestaRememberToken["code"] : 1;
                $respuesta->message = isset($respuestaRememberToken["message"]) ? $respuestaRememberToken["message"] : ConstanteUsuario::MENSAJE_ERR_NEGOCIO_NO_SE_PUDO_VALIDAR_REMEMBER_TOKEN;
                $respuesta->http_code = 401;
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $respuesta->data = false;
            $respuesta->code = $e->response->json()['code'];
            $respuesta->message = $e->response->json()['message'];
            $respuesta->http_code = 403;
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse(ConstanteUsuario::MENSAJE_ERR_NEGOCIO_NO_SE_PUDO_VALIDAR_REMEMBER_TOKEN, $e);
            $respuesta->code = ConstanteUsuario::ERR_EJECUCION_VALIDAR_REMEMBER_TOKEN;
            $respuesta->http_code = 500;
        }
        return $respuesta->json();
    }
}
