<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\GrupoFamiliar;
use App\Models\Personas;
use App\Models\Solicitud;
use stdClass;

class GrupoFamiliarClass
{
    public $ciudadano;
    public function __invoke($runCiudadano)
    {
        $this->ciudadano = Personas::findByRunCiudadano($runCiudadano);

        /*if (!$this->cargarGrupoFamiliarAsignacion) {
            return null;
        }*/

         $grupoFamiliar = GrupoFamiliar::findVigenteAprobadoByRunCiudadano($this->ciudadano->per_run, false);
        if ($grupoFamiliar) {

            $grupoFamiliar->estadoGrupo = $grupoFamiliar->obtenerEstadoGrupoFamiliar();
            $grupoFamiliar->consistenciaGrupo = $grupoFamiliar->obtenerConsistenciaGrupoFamiliar($this->ciudadano);
            $grupoFamiliar->confirmacion = $this->obtenerInfoConfirmacionGrupoFamiliar($grupoFamiliar);

            $grupoFamiliar->rangoIngresos = $grupoFamiliar->findTramosdeIngreso();
            $grupoFamiliar->ingresoLaboralGrupoFamiliar = 0;
            $grupoFamiliar->ingresoJubilacionGrupoFamiliar = 0;
            $grupoFamiliar->ingresoOtrosGrupoFamiliar = 0;
            $grupoFamiliar->ingresoTotalGrupoFamiliar = 0;

            $grupoFamiliar->cantidadIntegrantes = 0;
            $grupoFamiliar->cantidadConDiscapacidad = 0;
            $grupoFamiliar->cantidadAdultoMayor = 0;
            $grupoFamiliar->cantidadMenoresDeEdad = 0;

            //inicializacion banderas de solicitud, se asignan mas adelante
            $grupoFamiliar->solicitudSalud = false;
            $grupoFamiliar->solicitudCambioDomicilio = false;
        } else {
            $gf = GrupoFamiliar::findVigenteAprobadoByRunCiudadanoInconsistente($this->ciudadano->per_run, false);
            if ($gf) {
                $grupoFamiliar = new GrupoFamiliar();
                $grupoFamiliar->grupfami_nuevofolio = $gf->grupfami_nuevofolio;
                $grupoFamiliar->estadoGrupo = $gf->obtenerEstadoGrupoFamiliar();
                $grupoFamiliar->consistenciaGrupo = $grupoFamiliar->obtenerConsistenciaGrupoFamiliar($this->ciudadano);
            }
        }
        return $grupoFamiliar;
    }

    private function obtenerInfoConfirmacionGrupoFamiliar($grupoFamiliar)
    {
        $validacion = $grupoFamiliar->getFechaValidacion($this->ciudadano);
        if ($validacion->runValidacion != $this->ciudadano->per_run) {
            $temp = Personas::findByRun($validacion->runValidacion);
            $nombre = $temp->per_nombres . " " . $temp->per_apellidopaterno . " " . $temp->per_apellidomaterno;
        } else {
            $nombre = $this->ciudadano->per_nombres . " " . $this->ciudadano->per_apellidopaterno . " " . $this->ciudadano->per_apellidomaterno;
        }


        $data = new stdClass();
        $data->fechaActualizacion = $validacion->fechaValidacion;
        $data->run = $validacion->runValidacion;
        $data->nombre = $nombre;
        $data->fechaEncuesta = $validacion->fechaEncuesta;
        return $data;
    }
}
