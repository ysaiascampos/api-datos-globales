<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class PersonaAsignacionActualizarClass
{
    public function __invoke(Request $request)
    {
        $runCiudadano = $request["per-run"];
        $persona = Personas::findByRunCiudadano($runCiudadano);
        $fechaNacim = null;
        if (!empty($persona)) {
            if ($persona->per_fechadefuncion == 0) {
                $fechaNacim = $persona->per_fechanacimiento;
            }
            $persona->per_nombres = $request["per-nombres"];
            $persona->per_apellidopaterno = $request["per-apellidopaterno"];
            $persona->per_apellidomaterno = $request["per-apellidomaterno"];
            $persona->per_fechanacimiento = date('Ymd', strtotime($request["per-fechanacimiento"]));
            $persona->per_estadocivil = $request["per-estadocivil"];
            $persona->per_sexo = $request["per-sexo"];
            if ($request["per-fechadefuncion"] != 0) {
                $persona->per_fechadefuncion = date('Ymd', strtotime($request["per-fechadefuncion"]));
            } else {
                $fechaNacim = $request["per-fechanacimiento"];
            }
            if (!(is_null($fechaNacim))) {
                $persona->per_edad = Utiles::getEdad($fechaNacim);
            }
            $ver_temp_parentesco = env('TEMPORAL_PARENTESCO',false);
            if ($ver_temp_parentesco == true) {
                $persona->per_run_padre = !is_null($request["per-run-padre"]) ? $request["per-run-padre"] : $persona->per_run_padre;
                $persona->per_run_madre = !is_null($request["per-run-madre"]) ? $request["per-run-madre"] : $persona->per_run_madre;
                $persona->per_fecha_actualizacion_padres = !is_null($request["per-fecha-actualizacion-padres"]) ? $request["per-fecha-actualizacion-padres"] : $persona->per_fecha_actualizacion_padres;
            }
            $persona->save();
            return $persona;
        }
        return $persona;
    }
}
