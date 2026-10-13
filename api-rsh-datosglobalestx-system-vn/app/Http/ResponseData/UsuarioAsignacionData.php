<?php

namespace App\Http\ResponseData;

class UsuarioAsignacionData extends DataResponse
{
    private $usuario;
    private $campoAdicional;
    public $data = [];

    public function __construct( $usuarioAsignacion, $campoAdicional = null)
    {
        $this->usuario = $usuarioAsignacion;
        $this->campoAdicional = $campoAdicional;
        $this->data['usuario-asignacion'] = self::mapUsuarioAsignacion();
    }

    private function mapUsuarioAsignacion()
    {
        $out = [
            "rut-usuario" => $this->usuario->rut_usuario,
            "id-estado" => $this->usuario->id_estado,
            "nombres-usuario" => $this->usuario->nombres_usuario,
            "apat-usuario" => $this->usuario->apat_usuario,
            "amat-usuario" => $this->usuario->amat_usuario,
            "username" => $this->usuario->username,
            "fecha-creacion" => $this->usuario->fecha_creacion,
            "fecha-modificacion" => $this->usuario->fecha_modificacion,
            "fecha-caducidad" => $this->usuario->fecha_caducidad,
            "mail" => $this->usuario->mail,
            "id-perfil" => intval($this->usuario->id_perfil),
            "id-institucion" => intval($this->usuario->id_institucion),
            "reg-id" => intval($this->usuario->reg_id),
            "com-id" => $this->usuario->com_id
        ];

        if ($this->campoAdicional) {
            $camposArray = array_map('trim', explode(',', $this->campoAdicional));
            if (in_array('nombre-institucion', $camposArray)) {
                $out['nombre-institucion'] = $this->usuario->nombre_institucion ?? null;
            }
        }

        return $out;
    }
}
