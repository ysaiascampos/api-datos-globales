<?php


namespace App\Http\ResponseData;



class ValidaSolicitudTerceroData extends DataResponse
{
    public function __construct($solicitudes)
    {
        $this->data['solicitudes-validacion-tercero'] = self::mapValidacionTercero($solicitudes['solicitudes-validacion-tercero']);
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
            "id-validacion" => self::entero($solicitud["id-validacion"]),
            "run-ciudadano-asociado" => self::entero($solicitud["run-ciudadano-asociado"]),
            "nombre-ciudadano-asociado" => self::entero($solicitud["nombre-ciudadano-asociado"]),
            "id-estado-validacion" => self::entero($solicitud["id-estado-validacion"]),
            "estado-validacion" => $solicitud["estado-validacion"],
            "correo" => $solicitud["correo"],
            "fecha-validacion" => $solicitud["fecha-validacion"],
            "aviso-correo-solicitud" => self::entero($solicitud["aviso-correo-solicitud"]),
            "aviso-correo-validacion" => self::entero($solicitud["aviso-correo-validacion"]),

            //datos titular solicitud
            "run-titular-solicitud" => self::entero($solicitud["run-titular-solicitud"]),
            "nombre-titular-solicitud" => $solicitud["nombre-titular-solicitud"],

            //datos solicitud
            "id-padre-solicitud" => self::entero($solicitud["id-padre-solicitud"]),
            "id-solicitud" => self::entero($solicitud["id-solicitud"]),
            "vigente-solicitud" => self::entero($solicitud["vigente-solicitud"]),
            "id-estado-solicitud" => self::entero($solicitud["id-estado-solicitud"]),
            "estado-solicitud" => $solicitud["estado-solicitud"],
            "id-sub-estado-solicitud" => self::entero($solicitud["id-sub-estado-solicitud"]),
            "sub-estado-solicitud" => $solicitud["sub-estado-solicitud"],
            "id-tipo-solicitud" => self::entero($solicitud["id-tipo-solicitud"]),
            "tipo-solicitud" => $solicitud["tipo-solicitud"],
            "id-sub-tipo-solicitud" => self::entero($solicitud["id-sub-tipo-solicitud"]),
            "sub-tipo-solicitud" => $solicitud["sub-tipo-solicitud"],
            "codigo-comuna-solicitud" => self::entero($solicitud["codigo-comuna-solicitud"]),
            "comuna-solicitud" => $solicitud["comuna-solicitud"],
            "codigo-region-solicitud" => self::entero($solicitud["codigo-region-solicitud"]),
            "region-solicitud" => $solicitud["region-solicitud"],
            "fecha-creacion-solicitud" => $solicitud["fecha-creacion-solicitud"],
            "fecha-modificacion-solicitud" => $solicitud["fecha-modificacion-solicitud"],
            "fecha-creacion-ciudadano-solicitud" => $solicitud["fecha-creacion-ciudadano-solicitud"],
            "comentario-solicitud" => $solicitud["comentario-solicitud"]
        ];
        return $campos;
    }

}
