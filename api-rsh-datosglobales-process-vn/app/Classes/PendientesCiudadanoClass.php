<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Constantes\ConstantePersonaRunificador;
use Illuminate\Support\Facades\Log;

class PendientesCiudadanoClass
{
    private $pendientesCiudadano;

    /**
     * @param null $runCiudadano
     * @return mixed
     */
    public function __invoke($runCiudadano = null)
    {
        $this->pendientesCiudadano = [
            'code' => 1000,
            'message' => ''
        ];

        $classPoseeRSH = new PoseeRSHClass;
        $respuestaPoseeRSHData = $classPoseeRSH($runCiudadano);
        $this->pendientesCiudadano['seccion-posee-rsh'] = $respuestaPoseeRSHData;

        $classSituacionBasica = new SituacionBasicaClass;
        $respuestaSituacionBasica = $classSituacionBasica($runCiudadano);
        $this->pendientesCiudadano['seccion-situacion-basica'] = $respuestaSituacionBasica;

        if(isset($this->pendientesCiudadano['seccion-situacion-basica']['code'])){
            $this->pendientesCiudadano['code'] = $this->pendientesCiudadano['seccion-situacion-basica']['code'];
        }

        if(isset($respuestaPendientesCiudadano['message'])){
            $this->pendientesCiudadano['message'] = $this->pendientesCiudadano['seccion-situacion-basica']['message'];
        }

        $classTramitesPendientes = new TramitesPendientesClass;
        $respuestaTramitesPendientesData = $classTramitesPendientes($runCiudadano);
        $this->pendientesCiudadano['seccion-tramites-pendientes'] = $respuestaTramitesPendientesData;

        $classAlertas = new AlertasClass;
        $respuestaAlertas = $classAlertas($runCiudadano);
        $this->pendientesCiudadano['seccion-alertas'] = $respuestaAlertas;

        $classDatosContacto = new DatosContactoClass;
        $respuestaDatosContacto = $classDatosContacto($runCiudadano);
        $this->pendientesCiudadano['seccion-datos-de-contacto'] = $respuestaDatosContacto;


        $classDatosTramo = new TramoClass;
        $respuestaDatosTramo = $classDatosTramo($runCiudadano);
        $this->pendientesCiudadano['seccion-datos-tramo'] = $respuestaDatosTramo;
        if(isset($this->pendientesCiudadano['seccion-datos-tramo']['code'])){
            $this->pendientesCiudadano['code'] = $this->pendientesCiudadano['seccion-datos-tramo']['code'];
        }

        if(isset($respuestaDatosTramo['message'])){
            $this->pendientesCiudadano['message'] = $this->pendientesCiudadano['seccion-datos-tramo']['message'];
        }

        $classDatosComunaGrupoFamiliarExportClass = new ComunaGrupoFamiliarExportClass;
        $respuestaDatosComunaGrupoFamiliarExport = $classDatosComunaGrupoFamiliarExportClass($runCiudadano);
        $this->pendientesCiudadano['seccion-datos-comuna'] = $respuestaDatosComunaGrupoFamiliarExport;
        if(isset($this->pendientesCiudadano['seccion-datos-comuna']['code'])){
            $this->pendientesCiudadano['code'] = $this->pendientesCiudadano['seccion-datos-comuna']['code'];
        }

        if(isset($respuestaDatosComunaGrupoFamiliarExport['message'])){
            $this->pendientesCiudadano['message'] = $this->pendientesCiudadano['seccion-datos-comuna']['message'];
        }

        $classDatosValidaSolicitudTerceroClass = new ValidaSolicitudTerceroClass();
        $respuestaDatosValidaSolicitudTercero = $classDatosValidaSolicitudTerceroClass($runCiudadano);

        $this->pendientesCiudadano['seccion-datos-solicitudes-validacion-tercero'] = $respuestaDatosValidaSolicitudTercero;
        if(isset($this->pendientesCiudadano['seccion-datos-solicitudes-validacion-tercero']['code'])){
            $this->pendientesCiudadano['code'] = $this->pendientesCiudadano['seccion-datos-solicitudes-validacion-tercero']['code'];
        }

        if(isset($this->pendientesCiudadano['seccion-datos-solicitudes-validacion-tercero']['message'])){
            $this->pendientesCiudadano['message'] = $this->pendientesCiudadano['seccion-datos-solicitudes-validacion-tercero']['message'];
        }


        return $this->pendientesCiudadano;
    }

}
