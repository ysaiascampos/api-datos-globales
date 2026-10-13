<?php


namespace App\Http\ResponseData;

class SolicitudNoFinalizadasData extends DataResponse
{
    private $solicitud;
    public function __construct($solicitudData)
    {
        $this->solicitud = $solicitudData;
        if($this->solicitud) {
            $this->data['solicitud'] = self::mapSolicitud();
        } else {
            $this->data = null;
        }
    }
    private function mapSolicitud()
    {
        return [
            "id-solicitud" => $this->solicitud->id_solicitud,
            "id-tipo-solicitud" => $this->solicitud->id_tipo_solicitud,
            "solicitud" => $this->solicitud->solicitud,
//            "id-usuario-creacion" => $this->solicitud->id_usuario_creacion,
//            "fecha-creacion" => $this->solicitud->fecha_creacion,
//            "id-usuario-modificacion" => $this->solicitud->id_usuario_modificacion,
            "fecha-modificacion" => $this->solicitud->fecha_modificacion,
//            "fecha-resolucion" => $this->solicitud->fecha_resolucion,
            "id-estado" => $this->solicitud->id_estado,
            "dato-actual" => $this->solicitud->dato_actual,
//            "id-fuente-actual" => $this->solicitud->id_fuente_actual,
            "dato-nuevo" => $this->solicitud->dato_nuevo,
//            "id-fuente-nueva" => $this->solicitud->id_fuente_nueva,
//            "vigencia-dato" => $this->solicitud->vigencia_dato,
//            "id-proceso" => $this->solicitud->id_proceso,
//            "id-padre-solicitud" => $this->solicitud->id_padre_solicitud,
//            "email-ciudadano" => $this->solicitud->email_ciudadano,
//            "fono-ciudadano" => $this->solicitud->fono_ciudadano,
            "run-ciudadano" => $this->solicitud->run_ciudadano,
//            "com-id" => $this->solicitud->com_id,
//            "id-usuario-resolucion" => $this->solicitud->id_usuario_resolucion,
//            "fono-ciudadano2" => $this->solicitud->fono_ciudadano2,
//            "id-warning" => $this->solicitud->id_warning,
//            "id-sub-estado" => $this->solicitud->id_sub_estado,
//            "obs-solicitud" => $this->solicitud->obs_solicitud,
//            "id-usuario-asignado" => $this->solicitud->id_usuario_asignado,
//            "fecha-asignacion" => $this->solicitud->fecha_asignacion,
//            "fecha-sub-estado" => $this->solicitud->fecha_sub_estado,
//            "id-motivo" => $this->solicitud->id_motivo,
//            "id-grupo-antiguo" => $this->solicitud->id_grupo_antiguo,
//            "md5-extranjero" => $this->solicitud->md5_extranjero,
//            "id-supervision" => $this->solicitud->id_supervision,
//            "comentario" => $this->solicitud->comentario,
//            "canal" => $this->solicitud->canal,
//            "respuesta" => $this->solicitud->respuesta,
//            "nuevofolio" => $this->solicitud->nuevofolio,
//            "observacion-rectificacion" => $this->solicitud->observacion_rectificacion,
//            "aviso-correo" => $this->solicitud->aviso_correo,
//            "run-integrante" => $this->solicitud->run_integrante,
//            "observacion-nivel-central" => $this->solicitud->observacion_nivel_central,
            "id-sub-tipo-solicitud" => $this->solicitud->id_sub_tipo_solicitud,
//            "id-estado-nc" => $this->solicitud->id_estado_nc,
            "vigente" => $this->solicitud->vigente,
//            "identidad-acreditada" => $this->solicitud->identidad_acreditada,
//            "fecha-ident-acreditada" => $this->solicitud->fecha_ident_acreditada,
//            "id-doc-complemento" => $this->solicitud->id_doc_complemento,
//            "migracion-comuna" => $this->solicitud->migracion_comuna,
//            "fecha-migracion-comuna" => $this->solicitud->fecha_migracion_comuna,
//            "origen-registro" => $this->solicitud->origen_registro,
//            "firma-simple" => $this->solicitud->firma_simple,
//            "token-acceso" => $this->solicitud->token_acceso,
//            "geo-latitud-creacion" => $this->solicitud->geo_latitud_creacion,
//            "geo-longitud-creacion" => $this->solicitud->geo_longitud_creacion,
//            "hora-inicio-solicitud" => $this->solicitud->hora_inicio_solicitud,
//            "hora-termino-solicitud" => $this->solicitud->hora_termino_solicitud,
//            "fecha-creacion-ciudadano" => $this->solicitud->fecha_creacion_ciudadano,
//            "alerta-titular-modulo" => $this->solicitud->alerta_titular_modulo,
//            "tipo-validacion-terceros" => $this->solicitud->tipo_validacion_terceros,
//            "migrante" => $this->solicitud->migrante,
//            "visita" => $this->solicitud->visita,
//            "observaciones" => $this->solicitud->observaciones
        ];
    }

}
