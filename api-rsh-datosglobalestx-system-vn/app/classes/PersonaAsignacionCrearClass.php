<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Personas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PersonaAsignacionCrearClass
{
    public function __invoke(Request $request)
    {
        $runCiudadano = $request["per-run"];
        $persona = Personas::findByRunCiudadano($runCiudadano);
        $fechaNacim = null;
        if (empty($persona)) {
            $personaNew = new Personas();
            if ($request["per-fechadefuncion"] == 0) {
                $fechaNacim = $request["per-fechanacimiento"];
            }
            $personaNew->per_id = Sequence::nextValue('seq_ab_personas');
            $personaNew->per_run = $request["per-run"];
            $personaNew->per_dv = $request["per-dv"];
            $personaNew->per_nombres = $request["per-nombres"];
            $personaNew->per_apellidopaterno = $request["per-apellidopaterno"];
            $personaNew->per_apellidomaterno = $request["per-apellidomaterno"];
            $personaNew->per_nacionalidad = $request["per-nacionalidad"];
            $personaNew->per_estadocivil = $request["per-estadocivil"];
            $personaNew->per_vigente = $request["per-vigente"];
            $personaNew->per_calculoactivo = $request["per-calculoactivo"];
            $personaNew->per_sexo = $request["per-sexo"];

            if ($request["per-fechadefuncion"] != 0) {
                $personaNew->per_fechadefuncion = date('Ymd', strtotime($request["per-fechadefuncion"]));
            } else {
                $fechaNacim = $request["per-fechanacimiento"];
            }
            if (!(is_null($fechaNacim))) {
                $personaNew->per_edad = Utiles::getEdad($fechaNacim);
            }

            $personaNew->per_run_padre = !is_null($request["per-run-padre"]) ? $request["per-run-padre"] : $personaNew->per_run_padre;
            $personaNew->per_run_madre = !is_null($request["per-run-madre"]) ? $request["per-run-madre"] : $personaNew->per_run_madre;
            $personaNew->per_fecha_actualizacion_padres = !is_null($request["per-fecha-actualizacion-padres"]) ? $request["per-fecha-actualizacion-padres"] : $personaNew->per_fecha_actualizacion_padres;
            $p2 = Personas::where('per_run', $request["per-run"])
                ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
            if ($p2) {
                Log::notice('Duplicidad. El RUN existe en la BD: ' . $request["per-run"]);
                return $p2;
            } else {
                $personaNew->save();
            }
            return $personaNew;
        }
        return $persona;
    }
}
