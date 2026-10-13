<?php


namespace App\Http\ResponseData;



class MensajeTramoData extends DataResponse
{
    private $respuestaMensajeTramo;
    public function __construct( $mensajeTramo)
    {
        $this->respuestaMensajeTramo = $mensajeTramo;
        $this->data['mensaje-tramo'] = self::mapMensajeTramo();
    }

    private function mapMensajeTramo()
    {
        return [
            "descripcion-larga" => $this->respuestaMensajeTramo->fue_descripcion_larga,
        ];
    }
}
