<?php


namespace App\Http\ResponseData;



class DesistirData extends DataResponse
{
    private $solicitud;
    public function __construct($solicitudDesistida)
    {
        $this->solicitud = $solicitudDesistida;
        $this->data['solicitud-asignacion'] = self::mapSolicitudDesistida();
    }

    private function mapSolicitudDesistida()
    {
        return [
            "id-solicitud" => $this->solicitud['solicitud-asignacion']['id_solicitud'],  // Solicitud Eliminada
            "id-padre-solicitud" => $this->solicitud['solicitud-asignacion']['id_padre_solicitud'],
            "id-tipo-solicitud" => $this->solicitud['solicitud-asignacion']['id_tipo_solicitud'],  // tipo solicitud
            "id-sub-tipo-solicitud" => $this->solicitud['solicitud-asignacion']['id_sub_tipo_solicitud'],  // sub tipo de solicitud eliminada
            "usuario-modificacion" => $this->solicitud['solicitud-asignacion']['usuario_modificacion'],  // sub tipo de solicitud eliminada
            "fecha-modificacion" => $this->solicitud['solicitud-asignacion']['fecha_modificacion']
        ];
    }
}
