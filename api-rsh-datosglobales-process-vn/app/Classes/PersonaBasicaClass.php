<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Constantes\ConstantePersonaRunificador;
use Illuminate\Support\Facades\Log;

class PersonaBasicaClass
{
    public function __invoke($runCiudadano = null)
    {
        $dataResponse = [
            'code' => 1000,
            'message' => '',
            'situacion' => true,
            'mensaje' => '',
            'data' => [],
        ];
        $persona = Utiles::getPersona($runCiudadano);
        if (is_numeric($persona)) {
            //Log::info("El runificador devuelve error pero persona Persona existe en BD y no se permite continuar", ['wsPersona' => $persona]);
            return $dataResponse = [
                'code' => 1301,
                'message' => ConstantePersonaRunificador::$RESPUESTA_RUNIFICADOR[$persona],
                'situacion' => true,
                'mensaje' => ConstantePersonaRunificador::$RESPUESTA_RUNIFICADOR[$persona],
                'data' => [],
            ];
        }
        if ($persona && $persona["per-fechadefuncion"] != 0) {
            $dataResponse = [
                'code' => 1002,
                'message' => ConstantePersonaRunificador::MENSAJE_ERROR_RUNIFICADOR_FALLECIDO,
                'situacion' => false,
                'mensaje' => ConstantePersonaRunificador::MENSAJE_ERROR_RUNIFICADOR_FALLECIDO,
                'data' => [],
            ];
            return $dataResponse;
        }
        $dataResponse['data']['persona-asignacion'] = $persona;
        return $dataResponse;
    }

}
