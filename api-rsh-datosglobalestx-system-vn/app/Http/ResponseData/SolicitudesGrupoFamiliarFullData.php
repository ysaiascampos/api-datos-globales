<?php


namespace App\Http\ResponseData;

use App\Constantes\Constante;

class SolicitudesGrupoFamiliarFullData extends DataResponse
{
    private $solicitudesGrupoFamiliar;
    public function __construct($solicitudesGrupoFamiliarData)
    {
        $this->solicitudesGrupoFamiliar = $solicitudesGrupoFamiliarData;
        $this->data['solicitud-grupo-familiar'] = self::mapSolicitudesGrupoFamiliar();
    }
    private function mapSolicitudesGrupoFamiliar()
    {
        $solicitudesGrupoFamiliar = [];
        foreach ($this->solicitudesGrupoFamiliar as $solicitudGrupoFamiliar) {
            $solicitudesGrupoFamiliar[] = self::mapGrupoFamiliar($solicitudGrupoFamiliar);
        }
        return $solicitudesGrupoFamiliar;
    }

    private function mapGrupoFamiliar($solicitudGrupoFamiliar)
    {
        return [
            "id-padre-solicitud" => $solicitudGrupoFamiliar["id_padre_solicitud"],
            "id-solicitud" => $solicitudGrupoFamiliar["id_solicitud"],
            "id-tipo-solicitud" => $solicitudGrupoFamiliar["id_tipo_solicitud"],
            "id-sub-tipo-solicitud" => $solicitudGrupoFamiliar["id_sub_tipo_solicitud"],
            "solicitud" => $solicitudGrupoFamiliar["solicitud"],
            "run-ciudadano" => $solicitudGrupoFamiliar["run_ciudadano"],
            "id-estado" => $solicitudGrupoFamiliar["id_estado"],
            "run-titular"=>$solicitudGrupoFamiliar["run_titular"],
            //"estado" => $solicitudGrupoFamiliar["nombre_estado"],
            "estado" => self::mapMensajeEstado($solicitudGrupoFamiliar["id_estado"],$solicitudGrupoFamiliar["id_sub_estado"]),
            "dato-nuevo" => $solicitudGrupoFamiliar["dato_nuevo"],
            "dato-actual" => $solicitudGrupoFamiliar["dato_actual"],
            "vigente" => $solicitudGrupoFamiliar["vigente"],
            "fecha-modificacion" => $solicitudGrupoFamiliar["fecha_modificacion"],
            "fecha-creacion" => $solicitudGrupoFamiliar["fecha_creacion"],
            "fecha-asignacion" => $solicitudGrupoFamiliar["fecha_asignacion"],
            "fecha-creacion-ciudadano" => $solicitudGrupoFamiliar["fecha_creacion_ciudadano"],
            "comentario" => $solicitudGrupoFamiliar["comentario"],
            "observacion-solicitud" => $solicitudGrupoFamiliar["obs_solicitud"],
            "observacion-solicitud-nivel-central" => $solicitudGrupoFamiliar["observacion_nivel_central"],
            "id-sub-estado" => $solicitudGrupoFamiliar["id_sub_estado"],
            "sub-estado" => $solicitudGrupoFamiliar["nombre_sub_estado"],
            "canal" => $solicitudGrupoFamiliar["canal"],
            "nombre-canal" => self::mapNombreCanal($solicitudGrupoFamiliar["canal"]),
            "observaciones" => $solicitudGrupoFamiliar["observaciones"],
            "codigo-comuna" => $solicitudGrupoFamiliar["com_id"],
            "comuna" => $solicitudGrupoFamiliar["com_descripcion"],
            "codigo-region" => $solicitudGrupoFamiliar["reg_id"],
            "region" => $solicitudGrupoFamiliar["reg_descripcion"],
            "origen-registro" => $solicitudGrupoFamiliar["id_origen_registro"],
            "nombre-origen-registro" => self::mapNombreOrigenRegistro($solicitudGrupoFamiliar["nombre_origen_registro"]),

            //"mensaje" => null
        ];
    }

    private static function mapValorCanal($canal)
    {
        if (array_key_exists($canal, Constante::$canalSaveSegunCanalMinuscula)) {
            $valorCanal = Constante::$canalSaveSegunCanalMinuscula[$canal];
        } else {
            $valorCanal = $canal;
        }
        return $valorCanal;
    }

    private static function mapNombreCanal($canal)
    {
        if (array_key_exists($canal, Constante::$canalTextoCapitalizado)) {
            $nombreCanal = Constante::$canalTextoCapitalizado[$canal];
        } else {
            $nombreCanal = $canal;
        }
        return $nombreCanal;
    }

    private static function mapNombreOrigenRegistro($origenRegistro)
    {
        if (array_key_exists($origenRegistro, Constante::$ORIGEN_REGISTRO_TEXTO)) {
            $nombreOrigenRegistro = Constante::$ORIGEN_REGISTRO_TEXTO[$origenRegistro];
        } else {
            $nombreOrigenRegistro = $origenRegistro;
        }
        return $nombreOrigenRegistro;
    }
    private static function mapMensajeEstado($idEstado,$idSubEstado=null)
    {
        $mensajeEstado = Constante::MENSAJE_EN_REVISION;
        switch ($idEstado) {
            case Constante::ESTADO_SOLICITUD_5_APROBADO:
                $mensajeEstado = Constante::MENSAJE_APROBADO;
                break;
            case Constante::ESTADO_SOLICITUD_7_RECHAZADA:
                $mensajeEstado = Constante::MENSAJE_RECHAZADA;
                break;
            case Constante::ESTADO_SOLICITUD_9_DESISTIDA:
                $mensajeEstado = Constante::MENSAJE_DESISTIDA;
                break;
            case Constante::ESTADO_SOLICITUD_17_CONFIRMADO:
                $mensajeEstado = Constante::MENSAJE_CONFIRMADO;
                break;
            case Constante::ESTADO_SOLICITUD_19_BORRADOR_PENDIENTE_FIRMA_DIGITAL:
                $mensajeEstado = Constante::MENSAJE_PENDIENTE_FIRMA_DIGITAL;
                break;
            case Constante::ESTADO_SOLICITUD_11_EN_DIGITACION:
            case Constante::ESTADO_SOLICITUD_18_EN_ESPERA_APROBACION_CIUDADANO:
                if ($idSubEstado != Constante::ESTADO_SOLICITUD_14_INACTIVO_POR_DEFUNCION) {
                    if ($idSubEstado == Constante::TIPO_SOLICITUD_103_SOLICITUD_DE_SUPERVISION){
                        $mensajeEstado = Constante::MENSAJE_PENDIENTE_COMPLETAR;
                    } else {
                        $mensajeEstado = Constante::MENSAJE_BORRADOR;
                    }
                }
                break;
        }
        return $mensajeEstado;
    }
}
