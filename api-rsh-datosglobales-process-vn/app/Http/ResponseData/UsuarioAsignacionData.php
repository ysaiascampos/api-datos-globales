<?php

namespace App\Http\ResponseData;

class UsuarioAsignacionData extends DataResponse
{
    private $usuario;
    private $campoAdicional;
    public $data = [];

    public function __construct($usuarioAsignacion, $campoAdicional = null)
    {
        $this->usuario = $usuarioAsignacion;
        $this->campoAdicional = $campoAdicional;
        $this->data['usuario-asignacion'] = self::mapUsuarioAsignacion();
    }

    private function mapUsuarioAsignacion()
    {
        $usuario = isset($this->usuario['usuario-asignacion']) ? $this->usuario['usuario-asignacion'] : $this->usuario;
        
        $out = [
            "rut-usuario" => self::entero($usuario['rut-usuario'] ?? $usuario['rut_usuario'] ?? null),
            "id-estado" => self::entero($usuario['id-estado'] ?? $usuario['id_estado'] ?? null),
            "nombres-usuario" => $usuario['nombres-usuario'] ?? $usuario['nombres_usuario'] ?? null,
            "apat-usuario" => $usuario['apat-usuario'] ?? $usuario['apat_usuario'] ?? null,
            "amat-usuario" => $usuario['amat-usuario'] ?? $usuario['amat_usuario'] ?? null,
            "username" => $usuario['username'] ?? null,
            "fecha-creacion" => $usuario['fecha-creacion'] ?? $usuario['fecha_creacion'] ?? null,
            "fecha-modificacion" => $usuario['fecha-modificacion'] ?? $usuario['fecha_modificacion'] ?? null,
            "fecha-caducidad" => $usuario['fecha-caducidad'] ?? $usuario['fecha_caducidad'] ?? null,
            "mail" => $usuario['mail'] ?? null,
            "id-perfil" => self::entero($usuario['id-perfil'] ?? $usuario['id_perfil'] ?? null),
            "id-institucion" => self::entero($usuario['id-institucion'] ?? $usuario['id_institucion'] ?? null),
            "reg-id" => self::entero($usuario['reg-id'] ?? $usuario['reg_id'] ?? null),
            "com-id" => self::entero($usuario['com-id'] ?? $usuario['com_id'] ?? null)
        ];

        if ($this->campoAdicional) {
            $camposArray = array_map('trim', explode(',', $this->campoAdicional));
            if (in_array('nombre-institucion', $camposArray)) {
                $out['nombre-institucion'] = $usuario['nombre-institucion'] ?? $usuario['nombre_institucion'] ?? null;
            }
        }

        return $out;
    }
}
