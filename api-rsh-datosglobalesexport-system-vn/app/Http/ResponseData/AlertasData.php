<?php


namespace App\Http\ResponseData;



class AlertasData extends DataResponse
{
    private $dataAlertas;
    public function __construct( $alertas)
    {
        $this->dataAlertas = $alertas;
        $this->data['alertas'] = self::mapAlertas();
    }

    private function mapAlertas()
    {
        $respuestaAlertas = [
            'mensajes' => [],
        ];
        foreach ($this->dataAlertas as $alerta) {
            $respuestaAlertas['mensajes'][] = self::mapMensajes($alerta);
        }

        return $respuestaAlertas;
    }

    private function mapMensajes($alerta)
    {
            $mensajeIndividual = [
                'id-mensaje' => $alerta->sv_id_mensaje,
                'desc-mensaje' => $alerta->sv_desc_mensaje,
                'mensaje' => $alerta->sv_mensaje,
                'mensaje-correo' => $alerta->sv_mensaje_correo,
                'gravedad' => $alerta->sv_gravedad,
                'tipo-hallazgo-id' => $alerta->sv_tipo_hallazgo_id,
                'tipo-hallazgo' => $alerta->sv_tipo_hallazgo_desc,
                'tipo-sol-relevante-1' => $alerta->sv_tipo_sol_relevante_1,
                'tipo-sol-relevante-2' => $alerta->sv_tipo_sol_relevante_2,
                'tipo-sol-relevante-3' => $alerta->sv_tipo_sol_relevante_3,
                'menor' => $alerta->sv_menor,
                'run-menor' => $alerta->sv_run_menor,
                'run' => $alerta->sv_run,
                'destinatario-mens' => $alerta->sv_destinatario_mens,
                'periodo-rsh' => $alerta->sv_periodo_rsh

            ];
        return $mensajeIndividual;
    }
}
