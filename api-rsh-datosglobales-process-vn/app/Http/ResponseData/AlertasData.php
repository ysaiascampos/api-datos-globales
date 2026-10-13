<?php


namespace App\Http\ResponseData;



class AlertasData extends DataResponse
{
    private $mensajeIndividual;
    private $mensajeGrupal;
    public function __construct( $respuestaAlertas)
    {
        $this->mensajeIndividual = $respuestaAlertas["mensaje-individual"];
        $this->mensajeGrupal = $respuestaAlertas["mensaje-grupal"];
        $this->data['alertas'] = self::mapAlertas();
    }

    private function mapAlertas()
    {
        return [
            'mensaje-individual' => self::mapMensajeIndividual(),
            'mensaje-grupal' => self::mapMensajeGrupal(),
        ];
    }

    private function mapMensajeIndividual()
    {
        $mensajeIndividual = [];
        foreach ($this->mensajeIndividual as $alerta) {
            $mensajeIndividual[] = [
                'id-mensaje' => $alerta['id-mensaje'],
                'run' => $alerta['run'],
                'menor' => $alerta['menor'],
                'run-menor' => $alerta['run-menor'],
                'destinatario' => $alerta['destinatario-mens'],
                'desc-mensaje' => $alerta['desc-mensaje'],
                'mensaje' => $alerta['mensaje'],
                'gravedad' => $alerta['gravedad'],
                'tipo-hallazgo-id' => $alerta['tipo-hallazgo-id'],
                'tipo-hallazgo' => $alerta['tipo-hallazgo'],
                'tipo-sol-relevante-1' => $alerta['tipo-sol-relevante-1'],
                'tipo-sol-relevante-2' => $alerta['tipo-sol-relevante-2'],
                'tipo-sol-relevante-3' => $alerta['tipo-sol-relevante-3'],
                'desc_link_1' => $alerta['desc_link_1'],
                'desc_link_2' => $alerta['desc_link_2'],
                'desc_link_3' => $alerta['desc_link_3'],

            ];

        }
        return $mensajeIndividual;
    }

    private function mapMensajeGrupal()
    {
        $mensajeIndividual = [];
        foreach ($this->mensajeGrupal as $alerta) {
            $mensajeIndividual[] = [
                'id-mensaje' => $alerta['id-mensaje'],
                'run' => $alerta['run'],
                'menor' => $alerta['menor'],
                'run-menor' => $alerta['run-menor'],
                'destinatario' => $alerta['destinatario-mens'],
                'desc-mensaje' => $alerta['desc-mensaje'],
                'mensaje' => $alerta['mensaje'],
                'gravedad' => $alerta['gravedad'],
                'tipo-hallazgo' => $alerta['tipo-hallazgo'],
                'tipo-sol-relevante-1' => $alerta['tipo-sol-relevante-1'],
                'tipo-sol-relevante-2' => $alerta['tipo-sol-relevante-2'],
                'tipo-sol-relevante-3' => $alerta['tipo-sol-relevante-3'],
                'desc_link_1' => $alerta['desc_link_1'],
                'desc_link_2' => $alerta['desc_link_2'],
                'desc_link_3' => $alerta['desc_link_3'],

            ];

        }
        return $mensajeIndividual;
    }

}
