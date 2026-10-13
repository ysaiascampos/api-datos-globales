<?php

namespace App\Models;


use Exception;
use Illuminate\Support\Facades\Log;
use app\Constantes\Constante;

use Illuminate\Database\Eloquent\Model;


class PersonasTokenMail extends Model
{
    protected $table = 'AB_PERSONAS_TOKEN_MAIL';
    protected $primaryKey = 'ID';

    const CREATED_AT = 'FECHA_SOLICITUD';
    const UPDATED_AT = 'FECHA_ACTUALIZACION';


    protected $fillable = [
        'ID',
        'per_run',
        'PER_MAIL_ANTERIOR',
        'PER_MAIL_TEMP',
        'PER_MAIL_NUEVO',
        'TOKEN',
        'ESTADO_TOKEN',
        'CANT_INTENTO',
        'FECHA_ENVIO_MAIL',
        'FECHA_VALIDACION'
    ];

    public static function getModel()
    {
        return new PersonasTokenMail();
    }


    public static function findByRunCiudadano($runCiudadano)
    {
        $datosDePersona = null;

        try {
            $datosDePersona = self::getModel()
                ->select("PER_MAIL_TEMP","ESTADO_TOKEN")
                ->where('per_run', $runCiudadano)
                ->orderBy('id', 'desc')
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }

        return $datosDePersona;
    }

}
