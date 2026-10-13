<?php


namespace App\Http\ResponseData;

class SolicitudesGrupoFamiliarViaDatoNuevoData extends DataResponse
{
    private $solicitudesGrupoFamiliar;
    public function __construct($solicitudesGrupoFamiliarData)
    {
        $this->solicitudesGrupoFamiliar = $solicitudesGrupoFamiliarData;
        if($this->solicitudesGrupoFamiliar) {
            $this->data['solicitud-grupo-familiar'] = self::mapSolicitudesGrupoFamiliar();
        } else {
            $this->data = null;
        }


    }
    private function mapSolicitudesGrupoFamiliar()
    {
        $solicitudesGrupoFamiliar = [];
        foreach ($this->solicitudesGrupoFamiliar as $solicitudGrupoFamiliar) {
            $solicitudesGrupoFamiliar[] = self::mapSolicitud($solicitudGrupoFamiliar);
        }
        return $solicitudesGrupoFamiliar;
    }
    private function mapSolicitud($solicitudGrupoFamiliar)
    {
        return [
            "id-solicitud" => $solicitudGrupoFamiliar["id_solicitud"],
            "id-tipo-solicitud" => $solicitudGrupoFamiliar["id_tipo_solicitud"],
            "id-sub-tipo-solicitud" => $solicitudGrupoFamiliar["id_sub_tipo_solicitud"],
            "solicitud" => $solicitudGrupoFamiliar["solicitud"],
            "run-ciudadano" => $solicitudGrupoFamiliar["run_ciudadano"],
            "id-estado" => $solicitudGrupoFamiliar["id_estado"],
            "dato-nuevo" => $solicitudGrupoFamiliar["dato_nuevo"],
            "dato-actual" => $solicitudGrupoFamiliar["dato_actual"],
            "vigente" => $solicitudGrupoFamiliar["vigente"],
            "fecha-modificacion" => $solicitudGrupoFamiliar["fecha_modificacion"],
        ];
    }

}
