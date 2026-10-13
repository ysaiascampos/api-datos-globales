<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use App\Models\PersonasTokenMail;
use Illuminate\Support\Facades\Log;

class DatosContacto
{
    public function __invoke($runCiudadano = null)
    {
        $respuesta = null;
        try {
            $respuestaPersonas = Personas::findByRunCiudadanoTelefonoEmail($runCiudadano);
            $respuestaPersonasTokenMail = PersonasTokenMail::findByRunCiudadano($runCiudadano);
            if($respuestaPersonas){
                $respuesta = ['persona' => $respuestaPersonas, 'persona-token-mail' => $respuestaPersonasTokenMail];
            }
        } catch (Exception $ex) {
            Log::critical('Problema al consultar si a Personas Mail');
        }
        return $respuesta;
    }
}
