<?php


namespace App\Http\ResponseData;


class DesistirSolicitudData extends DataResponse
{
    private $solicitud;

    public function __construct($solicitudAsigancion)
    {
        $this->solicitud = $solicitudAsigancion;
        $this->data['solicitud-asignacion'] = self::mapSolicitudDesistida();
    }

    private function mapSolicitudDesistida()
    {
        return [
            "id_solicitud" => $this->solicitud->id_solicitud,  // Solicitud Eliminada
            "id_padre_solicitud" => $this->solicitud->id_padre_solicitud,
            "id_tipo_solicitud" => $this->solicitud->id_tipo_solicitud,  // tipo solicitud
            "id_sub_tipo_solicitud" => $this->solicitud->id_sub_tipo_solicitud,  // sub tipo de solicitud eliminada
            "usuario_modificacion" => $this->solicitud->id_usuario_modificacion,  // sub tipo de solicitud eliminada
            "fecha_modificacion" => $this->solicitud->fecha_modificacion
        ];
    }
}
