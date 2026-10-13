<?php


namespace App\Http\ResponseData;



class ObtenerSolicitudesData extends DataResponse
{
    private $solicitudes;
    public function __construct( $respuestaSolicitudes)
    {
        $this->solicitudes = $respuestaSolicitudes['solicitudes'];
        $this->data['solicitudes'] = self::mapSolicitudes();
    }

    private function mapSolicitudes()
    {
        $solicitudes = [];
        foreach ($this->solicitudes as $solicitud) {
            $solicitudes[] = self::mapSolicitud($solicitud);
        }
        return $solicitudes;
    }

    private function mapSolicitud($solicitud)
    {
        $reaponseSolicitud = [
            "id-padre-solicitud" => self::entero($solicitud["id-padre-solicitud"]),
            "id-solicitud" => self::entero($solicitud["id-solicitud"]),
            "id-estado" => self::entero($solicitud["id-estado"]),
            "estado" => $solicitud["estado"],
            "sub-estado" => self::entero($solicitud["sub-estado"]),
            "id-tipo-solicitud" => self::entero($solicitud["id-tipo-solicitud"]),
            "id-sub-tipo-solicitud" => self::entero($solicitud["id-sub-tipo-solicitud"]),
            "solicitud" => $solicitud["solicitud"],
            //"aviso-correo" => self::entero($solicitud["aviso-correo"]),
            "codigo-comuna" => self::entero($solicitud["codigo-comuna"]),
            //la comuna se desgloza como region comuna con su respectiva descripcion
            "comuna" => $solicitud["comuna"],
            "codigo-region" => self::entero($solicitud["codigo-region"]),
            "region" => $solicitud["region"],
            "fecha-creacion" => $solicitud["fecha-creacion"],
            "fecha-modificacion" => $solicitud["fecha-modificacion"],
            "canal" => self::entero($solicitud["canal"]),
            "nombre-canal" => $solicitud["nombre-canal"],
            "origen-registro" => self::entero($solicitud["origen-registro"]),
            "nombre-origen-registro" => $solicitud["nombre-origen-registro"],
            "observaciones" => $solicitud["observaciones"],
            //"dato-actual" => self::entero($solicitud["dato-actual"]),
            //"dato-nuevo" => self::entero($solicitud["dato-nuevo"]),
            //"identidad-acreditada" => $solicitud["identidad-acreditada"],
            "vigente" => self::entero($solicitud["vigente"]),
            //"id-usuario-asignado" => self::entero($solicitud["id-usuario-asignado"]),
            "fecha-asignacion" => $solicitud["fecha-asignacion"],
            "fecha-creacion-ciudadano" => $solicitud["fecha-creacion-ciudadano"],
            //"ciudadano-asociado" => $solicitud["ciudadano-asociado"],
            "comentario" => $solicitud["comentario"],
            /*"numero-documentos" => self::entero($solicitud["numero-documentos"]),
            "id-fuente-actual" => self::entero($solicitud["id-fuente-actual"]),*/
            "observacion-solicitud" => $solicitud["observacion-solicitud"],
            "observacion-solicitud-nivel-central" => $solicitud["observacion-solicitud-nivel-central"],
            /*"run-titular" => self::entero($solicitud["run-titular"]),
            "run-beneficiario" => $solicitud["run-beneficiario"],
            "migrante" => self::entero($solicitud["migrante"])*/
            "mensaje"=> null,
        ];
        return $reaponseSolicitud;
    }

}
