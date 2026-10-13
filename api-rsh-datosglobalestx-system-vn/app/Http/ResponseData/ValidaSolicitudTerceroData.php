<?php

namespace App\Http\ResponseData;

use App\Constantes\Constante;
use DateTime;

class ValidaSolicitudTerceroData extends DataResponse
{
    public function __construct($solicitudes)
    {
        $this->data['solicitudes-validacion-tercero'] = self::mapValidacionTercero($solicitudes);
    }

    private function mapValidacionTercero($solicitudes)
    {
        $map = [];
        if (!empty($solicitudes)) {
            foreach ($solicitudes as $solicitud) {
                $map[] = self::mapValidaTercero($solicitud);
            }
        }
        return $map;
    }
    private function mapValidaTercero($solicitud)
    {
        $campos = [
            //DATOS DE TABLA VALIDACIONSOLICITUDTERCERO
            "id-validacion" => self::entero($solicitud->id),
            "run-ciudadano-asociado" => self::entero($solicitud->per_run),
            "nombre-ciudadano-asociado" => self::entero($solicitud->nombre_asociado_solicitud),
            "id-estado-validacion" => self::entero($solicitud->estado_validacion),
            "fecha-validacion" => $solicitud->fecha_validacion,
            "correo" => $solicitud->email,
            "aviso-correo-solicitud" => self::entero($solicitud->envio_email_aviso_solicitud),
            "aviso-correo-validacion" => self::entero($solicitud->envio_email_validacion),

            //datos titular solicitud
            'run-titular-solicitud' => self::entero($solicitud->run_ciudadano),
            "nombre-titular-solicitud" => $solicitud->nombre_titular_solicitud,

            //datos solicitud
            "id-padre-solicitud" => self::entero($solicitud->id_padre_solicitud),
            "id-solicitud" => self::entero($solicitud->idsolicitud),
            "vigente-solicitud" => self::entero($solicitud->vigente),
            "id-estado-solicitud" => self::entero($solicitud->id_estado),
            "id-sub-estado-solicitud" => self::entero($solicitud->id_sub_estado_solicitud),
            "sub-estado-solicitud" => $solicitud->sub_estado_solicitud,
            "id-tipo-solicitud" => self::entero($solicitud->id_tipo_solicitud),
            "tipo-solicitud" => $solicitud->solicitud,
            "id-sub-tipo-solicitud" => self::entero($solicitud->id_sub_tipo_solicitud),
            "sub-tipo-solicitud" => $solicitud->sub_tipo_solicitud,
            "codigo-comuna-solicitud" => self::entero($solicitud->codigo_comuna),
            "comuna-solicitud" => $solicitud->comuna_descripcion,
            "codigo-region-solicitud" => self::entero($solicitud->codigo_region),
            "region-solicitud" => $solicitud->region_descripcion,
            "fecha-creacion-solicitud" => $solicitud->fecha_creacion,
            "fecha-modificacion-solicitud" => $solicitud->fecha_modificacion,
            "fecha-creacion-ciudadano-solicitud" => $solicitud->fecha_creacion_ciudadano,
            "comentario-solicitud" => $solicitud->comentario
        ];
        return $campos;
    }
}
