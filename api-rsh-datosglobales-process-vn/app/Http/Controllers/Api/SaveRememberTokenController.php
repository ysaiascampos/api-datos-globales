<?php

namespace App\Http\Controllers\Api;

use App\Classes\RenovarRememberTokenClass;
use App\Http\Controllers\Controller;
use App\Http\ResponseData\SaveRememberTokenData;
use App\Constantes\ConstanteUsuario;
use App\Models\MDSFApiResponse;
use Illuminate\Http\Request;

class SaveRememberTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $runUsuario = $request->input('run');
        $respuesta = new MDSFApiResponse();
        try {
            $classRememberToken = new RenovarRememberTokenClass;
            $respuestaRememberToken = $classRememberToken($runUsuario);
            if(isset($respuestaRememberToken["data"]) && $respuestaRememberToken["data"]) {
                $datosRememberToken = new SaveRememberTokenData($respuestaRememberToken);
                $respuesta->data = $datosRememberToken->data['result'];
                $respuesta->code = $datosRememberToken->data['code'];
                $respuesta->message = $datosRememberToken->data['message'];
            } else {
                $respuesta->data = false;
                $respuesta->code = ConstanteUsuario::ERR_NEGOCIO_NO_SE_PUDO_GENERAR_REMEMBER_TOKEN;
                $respuesta->http_code = 401;
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $respuesta->data = false;
            $respuesta->code = $e->response->json()['code'];
            $respuesta->message = $e->response->json()['message'];
            $respuesta->http_code = 403;
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse(ConstanteUsuario::MENSAJE_ERR_NEGOCIO_NO_SE_PUDO_GENERAR_REMEMBER_TOKEN, $e);
            $respuesta->code = ConstanteUsuario::ERR_EJECUCION_GENERAR_REMEMBER_TOKEN;
            $respuesta->http_code = 500;
        }
        return $respuesta->json();
    }
}
