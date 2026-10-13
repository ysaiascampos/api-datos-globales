<?php

namespace App\Classes;

use App\Constantes\Constante;
use App\Classes\Utiles;

class DatosGlobalesClass
{
    public $cargarDatosContacto;
    public $cargarGrupoFamiliarAsignacion;
    public $cargarGrupoFamiliarExport;
    public $cargarSolicitudes;
    public $cargarSolicitudesIncorporarDesvincular;
    public $cargarBeneficios;
    public $cargarSupervision;
    public $cargarSolicitudesValidacionTercero;
    public $cargarModalAviso;
    public $origenRegistro;
    public function __invoke($runCiudadano = null)
    {
        $this->configurarAccesos();
    }
    private function configurarAccesos()
    {
        $this->cargarDatosContacto = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );

        $this->cargarGrupoFamiliarAsignacion = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );

        $this->cargarGrupoFamiliarExport = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );

        $this->cargarSolicitudes = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS,
            Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS
        );

        $this->cargarSolicitudesIncorporarDesvincular = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS
        );

        $this->cargarBeneficios = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );

        $this->cargarSupervision = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );

        $this->cargarSolicitudesValidacionTercero = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );
        $this->cargarModalAviso = Utiles::inArrayMinuscula($this->origenRegistro,
            Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS,
            Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS
        );
    }
}
