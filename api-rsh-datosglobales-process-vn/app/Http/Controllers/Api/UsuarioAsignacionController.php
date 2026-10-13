<?php

namespace App\Http\Controllers\Api;

use App\Classes\UsuarioAsignacionClass;
use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\ResponseData\UsuarioAsignacionData;
use Illuminate\Support\Facades\Cache;


class UsuarioAsignacionController extends Controller
{

    public function __invoke(Request $request)
    {
        $runUsuario = $request->input('runUsuario');
        $campoAdicional = $request->input('campo-adicional', '');

        $cacheKey = "usuario_asignacion_{$runUsuario}_" . md5($campoAdicional);

        $respuestaData = Cache::remember($cacheKey, 300, function () use ($runUsuario, $campoAdicional) {
            $classUsuarioAsignacion = new UsuarioAsignacionClass;
            $respuestaUsuarioAsignacion = $classUsuarioAsignacion($runUsuario, $campoAdicional);
            if($respuestaUsuarioAsignacion) {
                $usuarioAsignacionData = new UsuarioAsignacionData($respuestaUsuarioAsignacion, $campoAdicional);
                return $usuarioAsignacionData->data;
            }
            return null;
        });

        $respuesta = new MDSFApiResponse();
        if($respuestaData) {
            $respuesta->data = $respuestaData;
            $respuesta->message = "Usuario encontrado";
        } else {
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->code = 1201;
            $respuesta->message = "Problemas al obtener datos de usuario";
            $respuesta->data = null;
        }
        return $respuesta->json();
    }
}
