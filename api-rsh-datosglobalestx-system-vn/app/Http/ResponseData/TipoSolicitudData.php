<?php


namespace App\Http\ResponseData;



class TipoSolicitudData extends DataResponse
{
    private $tipoSolicitud;
    public function __construct($tipoSolicitudData)
    {
        $this->tipoSolicitud = $tipoSolicitudData;
        $this->data['tipo-solicitud'] = self::mapTipoSolicitud();
    }

    private function mapTipoSolicitud()
    {
        $listadoTipoSolicitud = [];
        foreach ($this->tipoSolicitud as $key => $item) {
            $listadoTipoSolicitud[$key] = $item;
        }
        return $listadoTipoSolicitud;
    }
}
