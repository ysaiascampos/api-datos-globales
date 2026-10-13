<?php


namespace App\Http\ResponseData;



use function Symfony\Component\String\folded;

class TramitesPendientesData extends DataResponse
{
    private $personaTramitesPendientesMunicipal;
    private $personaTramitesPendientesCiudadano;
    public function __construct( $respuestaTramitesPendientes)
    {
        $this->personaTramitesPendientesMunicipal = $respuestaTramitesPendientes["municipal"];
        $this->personaTramitesPendientesCiudadano = $respuestaTramitesPendientes["ciudadano"];
        $this->data["tramites-pendientes"] = self::mapTramitesPendientes();
    }

    private function mapTramitesPendientes()
    {
        return [
            'ciudadano' => self::mapTramitesCiudadano(),
            'municipal' => self::mapTramitesMunicipal(),
        ];
    }
    private function mapTramitesCiudadano()
    {
        $solicitudes = $this->personaTramitesPendientesCiudadano;
        $reaponseSolicitud = [];
        foreach ($solicitudes as $solicitud) {
            $reaponseSolicitud[] = self::mapTramiteCiudadano($solicitud);
        }
        return $reaponseSolicitud;
    }
    private function mapTramitesMunicipal()
    {
        $solicitudes = $this->personaTramitesPendientesMunicipal;
        $reaponseSolicitud = [];
        foreach ($solicitudes as $solicitud) {
            $reaponseSolicitud[] = self::mapTramiteMunicipal($solicitud);
        }
        return $reaponseSolicitud;
    }

    private function mapTramiteCiudadano($solicitud)
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
            //"fecha-creacion" => $solicitud["fecha-creacion"],
            //"fecha-modificacion" => $solicitud["fecha-modificacion"],
            "canal" => self::entero($solicitud["canal"]),
            "nombre-canal" => $solicitud["nombre-canal"],
            "origen-registro" => self::entero($solicitud["origen-registro"]),
            "nombre-origen-registro" => $solicitud["nombre-origen-registro"],
            "observaciones" => $solicitud["observaciones"],
            //"dato-actual" => self::entero($solicitud["dato-actual"]),
            //"dato-nuevo" => self::entero($solicitud["dato-nuevo"]),
            //"identidad-acreditada" => $solicitud["identidad-acreditada"],
            "vigente" => self::entero($solicitud["vigente"]),
            /*"id-usuario-asignado" => self::entero($solicitud["id-usuario-asignado"]),
            "fecha-asignacion" => $solicitud["fecha-asignacion"],
            "fecha-creacion-ciudadano" => $solicitud["fecha-creacion-ciudadano"],
            "ciudadano-asociado" => $solicitud["ciudadano-asociado"],
            "comentario" => $solicitud["comentario"],
            "numero-documentos" => self::entero($solicitud["numero-documentos"]),
            "id-fuente-actual" => self::entero($solicitud["id-fuente-actual"]),
            "observacion-solicitud" => $solicitud["observacion-solicitud"],
            "observacion-solicitud-nivel-central" => $solicitud["observacion-solicitud-nivel-central"],
            "run-titular" => self::entero($solicitud["run-titular"]),
            "run-beneficiario" => $solicitud["run-beneficiario"],
            "migrante" => self::entero($solicitud["migrante"])*/
            "mensaje"=> null,

        ];
        return $reaponseSolicitud;
    }

    private function mapTramiteMunicipal($solicitud)
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
            //"fecha-creacion" => $solicitud["fecha-creacion"],
            //"fecha-modificacion" => $solicitud["fecha-modificacion"],
            "canal" => self::entero($solicitud["canal"]),
            "nombre-canal" => $solicitud["nombre-canal"],
            "origen-registro" => self::entero($solicitud["origen-registro"]),
            "nombre-origen-registro" => $solicitud["nombre-origen-registro"],
            "observaciones" => $solicitud["observaciones"],
            //"dato-actual" => self::entero($solicitud["dato-actual"]),
            //"dato-nuevo" => self::entero($solicitud["dato-nuevo"]),
            //"identidad-acreditada" => $solicitud["identidad-acreditada"],
            "vigente" => self::entero($solicitud["vigente"]),
            /*"id-usuario-asignado" => self::entero($solicitud["id-usuario-asignado"]),
            "fecha-asignacion" => $solicitud["fecha-asignacion"],
            "fecha-creacion-ciudadano" => $solicitud["fecha-creacion-ciudadano"],
            "ciudadano-asociado" => $solicitud["ciudadano-asociado"],
            "comentario" => $solicitud["comentario"],
            "numero-documentos" => self::entero($solicitud["numero-documentos"]),
            "id-fuente-actual" => self::entero($solicitud["id-fuente-actual"]),
            "observacion-solicitud" => $solicitud["observacion-solicitud"],
            "observacion-solicitud-nivel-central" => $solicitud["observacion-solicitud-nivel-central"],
            "run-titular" => self::entero($solicitud["run-titular"]),
            "run-beneficiario" => $solicitud["run-beneficiario"],
            "migrante" => self::entero($solicitud["migrante"])*/
            "mensaje"=> null,

        ];
        return $reaponseSolicitud;
    }
}
