<?php
namespace App\Http\ResponseData;

class ValidarCartolaEmitidaData extends DataResponse
{
    private $resultado;
    private $cartolaEmitida;
    public function __construct( $respuestaCartola)
    {
        $this->resultado = $respuestaCartola["resultado"];
        $this->cartolaEmitida = $respuestaCartola["cartola-emitida"];
        $this->data['cartola-emitida'] = self::mapCartola();
    }

    private function mapCartola()
    {
        return [
            'resultado' => $this->resultado,
            'datos' => self::mapCartolaEmitida(),
        ];
    }

    private function mapCartolaEmitida()
    {    $mensaje = [];
        if(count($this->cartolaEmitida) > 0) {
            $mensaje = [
                'run-emisor' => $this->cartolaEmitida['run_emisor'],
                'nombres-emisor' => $this->cartolaEmitida['nombres_emisor'],
                'comuna' => $this->cartolaEmitida['comuna'],
                'fecha-emision' => self::fechaHora($this->cartolaEmitida['fecha_emision']),
                'tramo' => $this->cartolaEmitida['tramo']['nombre'],

            ];
        }
        return $mensaje;
    }


}
