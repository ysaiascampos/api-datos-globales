<?php


namespace App\Http\ResponseData;



use Carbon\Carbon;

class ConfirmarDatosRSHData extends DataResponse
{
    private $telefono;
    private $email;
    private $fechaActualizacion;
    public function __construct( $datosRSH)
    {
        $this->telefono = $datosRSH['telefono'];
        $this->email = $datosRSH['email'];
        $this->data = self::mapConfirmarDatosRSH();
    }

    private function mapConfirmarDatosRSH()
    {
        return [
            'telefono' => self::mapTelefono(),
            'email' => self::mapMail(),
        ];
    }

    private function mapTelefono()
    {
        return [
            'estado' => $this->telefono['estado'],
            'fecha-actualizacion' => self::fechaEs($this->telefono['fecha-creacion']),
        ];
    }
    private function mapMail()
    {
        return [
            'estado' => $this->email['estado'],
            'fecha-actualizacion' => self::fechaEs($this->email['fecha-creacion']),
        ];
    }
}
