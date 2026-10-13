<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Classes\Utiles;

class SolicitudesTramitablesClass
{
    public $runCiudadano;

    public $ciudadano;
    public $ciudadanoExport;
    public $grupoFamiliarAsignacion;

    public $grupoFamiliarExport;

    public $origenRegistro;
    public $cargarDatosContacto;
    public $cargarGrupoFamiliarAsignacion;
    public $cargarGrupoFamiliarExport;
    public $cargarSolicitudes;
    public $cargarSolicitudesIncorporarDesvincular;
    public $cargarBeneficios;
    public $cargarSupervision;
    public $cargarSolicitudesValidacionTercero;
    public $cargarModalAviso;
    public $solicitudesTramitables;
    public $institucionalizado;
    public $ultimaFechaCierreGF;
    public $grupoFamiliarVigenteAprobadoRun;

    public $solicitud;

    public function __invoke($runCiudadano)
    {
        $this->origenRegistro = Constante::ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA;
        $this->configurarAccesos();

        $cacheKeyPre = env('SOLICITUDES_TRAMITABLES_CACHE_KEY_PRE','SOLICITUDES_TRAMITABLES_');
        $cacheKey = $cacheKeyPre.$runCiudadano;
        $ttl = (int) config('cache.ttl.short', 60);
        $cacheActivar = config('cache.features.solicitudes_tramitables', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () use ($runCiudadano) {
                return $this->obtenerDatos($runCiudadano);
            },
            $cacheActivar
        );

    }

    private function obtenerDatos($runCiudadano)
    {

        $this->solicitudesTramitables = [
            'code' => 1000,
            'message' => '',
            'data' => '',
        ];

        $this->runCiudadano = $runCiudadano;
        $personaBasicaClass = new PersonaBasicaClass;
        $personaBasica = $personaBasicaClass($this->runCiudadano);
        if(isset($dataResponse['code']) && $dataResponse['code'] == 1000){
            return  $dataResponse;
        }
        $this->ciudadano = isset($personaBasica['data']['persona-asignacion'])?
            $personaBasica['data']['persona-asignacion']:null;

        $institucionalizadoClass = new InstitucionalizadoClass;
        $responseData = $institucionalizadoClass($this->runCiudadano);
        $this->institucionalizado = $responseData["data"]["institucionalizado"];

        $personaExportBasicaClass = new PersonaExportBasicaClass;
        $personaExportBasica = $personaExportBasicaClass($this->runCiudadano);
        $this->ciudadanoExport = $personaExportBasica['data']['persona-export'];

        $grupoFamiliarClass = new GrupoFamiliarClass;
        $grupoFamiliar = $grupoFamiliarClass($this->runCiudadano);
        $this->grupoFamiliarAsignacion = $grupoFamiliar['data']['grupo-familiar'];

        $ultimaFechaCierreGrupoFamiliarClass = new UltimaFechaCierreGrupoFamiliarClass;
        $ultimaFechaCierreGrupoFamiliar = $ultimaFechaCierreGrupoFamiliarClass();
        $this->ultimaFechaCierreGF = isset($ultimaFechaCierreGrupoFamiliar["data"]['fecha-cierre'])?
            $ultimaFechaCierreGrupoFamiliar["data"]['fecha-cierre']:null;

        $grupoFamiliarExportClass = new GrupoFamiliarExportClass;
        $grupoFamiliarExport = $grupoFamiliarExportClass($this->runCiudadano);
        $this->grupoFamiliarExport = $grupoFamiliarExport['grupo-familiar-export'];

        $tipoSolicitudClass = new TipoSolicitudClass;
        $responseData = $tipoSolicitudClass($runCiudadano);
        $listaSolicitudes =  isset($responseData) &&
        isset($responseData["data"]['tipo-solicitud'])?$responseData["data"]['tipo-solicitud']:null;

        $parametrosSolicitudesTramitables = [
            'persona' => $this->ciudadano,
            'personaExport' => $this->ciudadanoExport,
            'gAsignacion' => $this->grupoFamiliarAsignacion,
            'gExport' => $this->grupoFamiliarExport,
            'institucionalizado' =>$this->institucionalizado,
            'ultimaFechaCierreGF' =>$this->ultimaFechaCierreGF
        ];

        $todasSolicitudesClass = new TodasSolicitudesClass;
        $solicitudesTramitables = $todasSolicitudesClass(
            $this->runCiudadano,
            $this->ciudadano["per-edad"],
            $this->origenRegistro,
            $parametrosSolicitudesTramitables
        );
        $poseeRSHClass = new PoseeRSHClass();
        $respuestaPoseeRSHClass = $poseeRSHClass($runCiudadano);
        $poseeRSH = $respuestaPoseeRSHClass["posee-rsh"];
        if (isset($this->solicitudes) && $poseeRSH == true) {

            $grupoFamiliarVigenteAprobadoRunClass = new GrupoFamiliarVigenteAprobadoRunClass;
            $grupoFamiliarVigenteAprobadoRun = $grupoFamiliarVigenteAprobadoRunClass($this->runCiudadano);
            $this->grupoFamiliarVigenteAprobadoRun = $grupoFamiliarVigenteAprobadoRun["data"]['grupo-familiar'];

            $grupoFamiliarSolicitud = $this->grupoFamiliarVigenteAprobadoRun["grupo-familiar"];
            if (isset($grupoFamiliarSolicitud["grupfami-idsolicitud"])) {
                $solicitudAprobadaIngresoDomicilioClass = new SolicitudAprobadaIngresoDomicilioClass;
                $solicitudAprobadaIngresoDomicilio = $solicitudAprobadaIngresoDomicilioClass($grupoFamiliarSolicitud["grupfami-idsolicitud"]);
                $this->solicitud = $solicitudAprobadaIngresoDomicilio["data"]['solicitud'];

                if (isset($this->solicitud["id-tipo-solicitud"]) && $this->solicitud["id-tipo-solicitud"] ==
                    Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA) {
                    if (!isset($grupoFamiliarSolicitud) || !isset($this->grupoFamiliarExport) || (
                            $grupoFamiliarSolicitud["grupfami-comunaine"] !== $this->grupoFamiliarExport["grupfami-comunaine"]
                            || $grupoFamiliarSolicitud["grupfami-c-calle"] !== $this->grupoFamiliarExport["grupfami-c-calle"])) {

                        $solicitudNoTramitable = ['puede-realizar-solicitud' => false,
                            'mensaje-error' => [isset(Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA]) ?
                                Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA] :
                                Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[Constante::MENSAJE_SERVICIO_CODIGO_DEFECTO_0]]];
                        foreach ($solicitudesTramitables as $key => $solicitudesTramitable) {
                            if (!in_array($key, array_merge(Constante::$solicitudesActualizacionARC, Constante::$solicitudesComplementoARC, Constante::$solicitudesRectificacionARC))) {
                                $solicitudesTramitables[($key)] = $solicitudNoTramitable;
                            }
                        }
                    }
                }
            }
        }

        if (is_numeric($solicitudesTramitables)) {
            if ($solicitudesTramitables == Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR) {
                $codigo = Constante::NUMERO_ERROR_25_SOAP_COMUNICACION_RUNIFICADOR;
                $mensaje = Constante::MENSAJE_ERROR_SOAP_COMUNICACION_RUNIFICADOR;
            } elseif ($solicitudesTramitables == Constante::DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL) {
                $codigo = Constante::NUMERO_ERROR_26_NO_DATO_CONTACTO;
                $mensaje = Constante::MENSAJE_ERROR_NO_DATO_CONTACTO;
            } else {
                $codigo = Constante::NUMERO_ERROR_26_NO_DATO_CONTACTO;
                $mensaje = Constante::MENSAJE_ERROR_NO_DATO_CONTACTO;
            }
            $solicitudesTramitables[] = ['puede-realizar-solicitud' => false,
                'mensaje-error' => [
                    "codigo" => $codigo,
                    "mensaje" => $mensaje]];
        }

        if ($this->institucionalizado == true && $this->ciudadano["per-edad"] >= Constante::EDAD_18_MAYOR_DE_EDAD) {
            foreach ($solicitudesTramitables as $key => $solicitudesTramitable) {
                if ($key != Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO) {
                    $solicitudesTramitables[$key] = ['puede-realizar-solicitud' => false,
                        'mensaje-error' => [
                            "codigo" => Constante::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO,
                            "mensaje" => Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO]]];
                }
            }
        }
        $dataResponse = $solicitudesTramitables;
        return $dataResponse;
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
