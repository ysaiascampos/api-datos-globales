<?php


namespace App\Strategies\BloqueosSolicitudCiudadano;

use App\Classes\AlertasClass;
use App\Classes\DatosContactoClass;
use App\Classes\GrupoFamiliarExportClass;
use App\Classes\ObtenerSolicitudesClass;
use App\Classes\PersonaBasicaClass;
use App\Classes\PersonaByBnfGrupFamiIdSolicitudExportClass;
use App\Classes\PersonaByFichaIdSolicitudExportClass;
use App\Classes\PoseeRSHClass;
use App\Classes\SolicitudesTramitablesClass;
use App\Classes\UltimaFechaCierreGrupoFamiliarClass;
use App\Constantes\Constante;
use App\Strategies\BloqueosSolicitudCiudadanoInterface;

class ActualizacionModuloOcupacionIngreso implements BloqueosSolicitudCiudadanoInterface
{
    private $personaTitularAsignacion;
    private $personaUsuarioAsignacion;
    private $runUsuario;
    private $runTitular;
    private $poseeRSH;
    private $solicitudes;
    private $datosContacto;
    private $alertas;
    private $puedeRealizarSolicitud;
    private $listaMensajesError;

    private $grupoFamiliarExport;
    private $grupoFamiliarExportIdSolicitud;
    private $solicitudesTramitables;
    private $idTipoSolicitud;
    private $ultimaFechaCierreGrupoFamiliar;
    private $ultimaFechaCierreGF;
    private $titularPerId;

    public function __construct($requestData = [],
                                PersonaBasicaClass $personaBasicaClass,
                                PoseeRSHClass $classPoseeRSH,
                                ObtenerSolicitudesClass $classObtenerSolicitudes,
                                DatosContactoClass $classDatosContacto,
                                AlertasClass $classAlertas,
                                GrupoFamiliarExportClass $grupoFamiliarExportClass,
                                SolicitudesTramitablesClass $solicitudesTramitablesClass,
                                UltimaFechaCierreGrupoFamiliarClass $ultimaFechaCierreGrupoFamiliarClass
    ){
        $runCiudadano = $requestData["run-ciudadano"];
        $this->runUsuario = $requestData["run-usuario"];
        $this->runTitular = isset($runCiudadano)?$runCiudadano:$this->runUsuario;
        $origenRegistro = $requestData["origen-registro"];
        $tipoSolicitud = $requestData["tipo-solicitud"];
        $subTipoSolicitud = $requestData["sub-tipo-solicitud"];

        $this->puedeRealizarSolicitud = true;
        $this->listaMensajesError = [];
        $this->idTipoSolicitud = Constante::TIPO_SOLICITUD_14_ACTUALIZACION_OCUPACION_DE_INGRESOS;

        $personaBasica = $personaBasicaClass($this->runUsuario);
        $this->personaUsuarioAsignacion = $personaBasica;

        $personaBasica = $personaBasicaClass($this->runTitular);
        $this->personaTitularAsignacion = $personaBasica;

        $this->titularPerId = isset($this->personaTitularAsignacion['data']['persona-asignacion']['per-id'])?
            $this->personaTitularAsignacion['data']['persona-asignacion']['per-id']:0;

        $this->poseeRSH = $classPoseeRSH($this->runTitular);

        $this->alertas = $classAlertas($this->runTitular);

        $this->datosContacto = $classDatosContacto($this->runUsuario);

        $this->grupoFamiliarExport = $grupoFamiliarExportClass($this->runTitular);
        $this->grupoFamiliarExportIdSolicitud = 0;
        if(isset($this->grupoFamiliarExport['grupo-familiar-export']) && count($this->grupoFamiliarExport['grupo-familiar-export']) > 0){
            if(isset($this->grupoFamiliarExport['grupo-familiar-export']['grupfami-idsolicitud'])){
                $this->grupoFamiliarExportIdSolicitud = $this->grupoFamiliarExport['grupo-familiar-export']['grupfami-idsolicitud'];
            }
        }
        $this->ultimaFechaCierreGF = (new \DateTime())->sub(new \DateInterval('P3M'));
        $this->ultimaFechaCierreGrupoFamiliar = $ultimaFechaCierreGrupoFamiliarClass();
        if(isset($this->ultimaFechaCierreGrupoFamiliar['data']['fecha-cierre'])){
            $this->ultimaFechaCierreGF = (new \DateTime($this->ultimaFechaCierreGrupoFamiliar['data']['fecha-cierre']));
        }
        $respuestaObtenerSolicitudes = $classObtenerSolicitudes($this->runUsuario);
        self::filtrarSolicitudes($respuestaObtenerSolicitudes);

        $this->solicitudesTramitables = $solicitudesTramitablesClass($this->runUsuario);
    }

    public function getPuedeRealizarSolicitud()
    {

        if(isset($this->personaUsuarioAsignacion['code']) && $this->personaUsuarioAsignacion['code'] == 1301) {
            $this->listaMensajesError[] = [
                "codigo" => $this->personaTitularAsignacion['code'],
                "mensaje" => $this->personaTitularAsignacion['mensaje']];
            return $this->puedeRealizarSolicitud = false;
        }

        if($this->personaUsuarioAsignacion['situacion'] == false){
            $this->listaMensajesError[] = [
                "codigo" => $this->personaTitularAsignacion['code'],
                "mensaje" => $this->personaTitularAsignacion['mensaje']];
            return $this->puedeRealizarSolicitud = false;
        }

        if(isset($this->personaUsuarioAsignacion['code']) && $this->personaUsuarioAsignacion['code'] != 1000) {
            $this->listaMensajesError[] = [
                "codigo" => $this->personaTitularAsignacion['codigo'],
                "mensaje" => $this->personaTitularAsignacion['mensaje']];
            return $this->puedeRealizarSolicitud = false;
        }

        if($this->personaUsuarioAsignacion['data']['persona-asignacion']['per-edad'] < Constante::EDAD_18_MAYOR_DE_EDAD){
            $this->listaMensajesError[] = [
                "codigo" => Constante::NUMERO_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD,
                "mensaje" => Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD]];
            return $this->puedeRealizarSolicitud = false;
        }

        if(isset($this->personaTitularAsignacion['code']) && $this->personaTitularAsignacion['code'] == 1301) {
            $this->listaMensajesError[] = [
                "codigo" => $this->personaTitularAsignacion['code'],
                "mensaje" => $this->personaTitularAsignacion['mensaje']];
            return $this->puedeRealizarSolicitud = false;
        }

        if($this->personaTitularAsignacion['situacion'] == false){
            $this->listaMensajesError[] = [
                "codigo" => $this->personaTitularAsignacion['code'],
                "mensaje" => $this->personaTitularAsignacion['mensaje']];
            return $this->puedeRealizarSolicitud = false;
        }

        if(isset($this->personaTitularAsignacion['code']) && $this->personaTitularAsignacion['code'] != 1000) {
            $this->listaMensajesError[] = [
                "codigo" => $this->personaTitularAsignacion['codigo'],
                "mensaje" => $this->personaTitularAsignacion['mensaje']];
            return $this->puedeRealizarSolicitud = false;
        }

        if($this->poseeRSH["posee-rsh"] == true){
            if(isset($this->datosContacto["persona-token-mail"]['estado-validacion']) &&
                $this->datosContacto["persona-token-mail"]['estado-validacion'] != Constante::ESTADO_VALIDADO_VALIDA_EMAIL){
                $this->listaMensajesError[] = [
                    "codigo" => Constante::NUMERO_ERROR_12_NO_POSEE_UN_CORREO_VALIDADO,
                    "mensaje" => Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_12_NO_POSEE_UN_CORREO_VALIDADO]];
                return $this->puedeRealizarSolicitud = false;
            }
        }
        if($this->poseeRSH["posee-rsh"] == false){
            $this->listaMensajesError[] = [
                "codigo" => Constante::NUMERO_ERROR_11_NO_POSEE_RSH,
                "mensaje" => Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_11_NO_POSEE_RSH]];
            return $this->puedeRealizarSolicitud = false;
        } else {
            if(count($this->solicitudes) > 0){
                $solicitudeAprobadas = array();
                foreach ($this->solicitudes as $solicitud) {
                    if($solicitud['id-estado'] == Constante::ESTADO_SOLICITUD_5_APROBADO &&
                        $solicitud['vigente'] == Constante::VIGENTE_1_VIGENTE){
                        $solicitudeAprobadas[] = $solicitud;
                        continue;
                    }
                    if((in_array($solicitud['id-tipo-solicitud'], [
                                Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                                Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                                Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                                Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
                                Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA]) &&
                            (in_array($solicitud['vigente'], [Constante::VIGENTE_1_VIGENTE])))){
                        if($solicitud['vigente'] == Constante::VIGENTE_1_VIGENTE){
                            $this->listaMensajesError[] = [
                                "codigo" => Constante::NUMERO_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE,
                                "mensaje" => Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE]];
                            return $this->puedeRealizarSolicitud = false;
                        } else {
                            $this->listaMensajesError[] = [
                                "codigo" => Constante::NUMERO_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR,
                                "mensaje" => Constante::$MENSAJE_DE_ERROR_SERVICIO[Constante::NUMERO_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR]];
                            return $this->puedeRealizarSolicitud = false;
                        }
                    }
                }
                foreach ($solicitudeAprobadas as $solicitud) {
                    if (in_array($solicitud['id-tipo-solicitud'], [Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                        Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA,
                        Constante::TIPO_SOLICITUD_30_SOLICITUD_DE_INGRESO_PARA_INSTITUCIONALIZADOS])
                        || ($solicitud['dato-nuevo'] == $this->titularPerId &&
                            in_array($solicitud['id-tipo-solicitud'],
                                [Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                                    Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE]))
                    ) {
                        if ($solicitud['id-solicitud'] != $this->grupoFamiliarExportIdSolicitud) {
                            $this->listaMensajesError[] =
                                isset(Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[$solicitud['id-tipo-solicitud']]) ?
                                    Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[$solicitud['id-tipo-solicitud']] :
                                    Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[Constante::MENSAJE_SERVICIO_CODIGO_DEFECTO_0];
                            return $this->puedeRealizarSolicitud = false;
                        }
                    } else {
                        if (in_array($solicitud['id-tipo-solicitud'], [Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                            ]) && $solicitud['run-titular'] == $this->runTitular) {
                            $personaByBnfGrupFamiIdSolicitudExportClass = new PersonaByBnfGrupFamiIdSolicitudExportClass;
                            $personaByBnfGrupFamiIdSolicitud = $personaByBnfGrupFamiIdSolicitudExportClass($solicitud['id-solicitud']);
                            if (!(isset($personaByBnfGrupFamiIdSolicitud['data']['persona-bnf-grup-fami']) && is_array($personaByBnfGrupFamiIdSolicitud['data']['persona-bnf-grup-fami']))) {
                                $this->listaMensajesError[] =
                                    isset(Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[$solicitud['id-tipo-solicitud']]) ?
                                        Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[$solicitud['id-tipo-solicitud']] :
                                        Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[Constante::MENSAJE_SERVICIO_CODIGO_DEFECTO_0];
                                return $this->puedeRealizarSolicitud = false;
                            }
                        }
                    }
                }
            }
        }
        if($this->puedeRealizarSolicitud){
            if(isset($this->solicitudesTramitables[$this->idTipoSolicitud])&&
                $this->solicitudesTramitables[$this->idTipoSolicitud]['puede-realizar-solicitud'] == false)
            {
                $this->puedeRealizarSolicitud = false;
                $this->listaMensajesError[] = isset($this->solicitudesTramitables[$this->idTipoSolicitud]['mensaje-error'])?
                    $this->solicitudesTramitables[$this->idTipoSolicitud]['mensaje-error']:
                    $this->solicitudesTramitables[$this->idTipoSolicitud]['mensaje-ciudadano'];
            }
        }

        return $this->puedeRealizarSolicitud;
    }

    public function getTramitesPendientes(){
        $resultadoSolicitudes = ['municipal'=> [],
            'ciudadano'=> []];
        if($this->poseeRSH["posee-rsh"] == false){
            if(count($this->solicitudes) > 0){
                foreach ($this->solicitudes as $solicitud) {
                    if(in_array($solicitud['id-estado'], [Constante::ESTADO_SOLICITUD_7_RECHAZADA,
                        Constante::ESTADO_SOLICITUD_9_DESISTIDA])){
                        continue;
                    }
                    if(($solicitud['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO &&
                            (in_array($solicitud['vigente'], [Constante::VIGENTE_1_VIGENTE,
                                Constante::VIGENTE_3_SOLICITUD_BORRADOR]))) ||
                        ($solicitud['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE &&
                            (in_array($solicitud['vigente'], [Constante::VIGENTE_1_VIGENTE])))){
                        if($solicitud['vigente'] == Constante::VIGENTE_3_SOLICITUD_BORRADOR){
                            $resultadoSolicitudes['ciudadano'][] = $solicitud;
                        } else {
                            $resultadoSolicitudes['municipal'][] = $solicitud;
                        }
                    }
                }
            }
        } else {
            if(count($this->solicitudes) > 0){
                foreach ($this->solicitudes as $solicitud) {
                    if(in_array($solicitud['id-estado'], [Constante::ESTADO_SOLICITUD_7_RECHAZADA,
                        Constante::ESTADO_SOLICITUD_9_DESISTIDA])){
                        continue;
                    }
                    if($solicitud['dato-nuevo'] != $this->titularPerId &&
                        in_array($solicitud['id-tipo-solicitud'],
                            [Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE]) &&
                        $solicitud['id-estado'] == Constante::ESTADO_SOLICITUD_5_APROBADO){
                        continue;
                    }
                    if(($solicitud['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_6_ACTUALIZACION_DE_MODULO_EDUCACION&&
                            (in_array($solicitud['vigente'], [Constante::VIGENTE_1_VIGENTE,
                                Constante::VIGENTE_3_SOLICITUD_BORRADOR]))) ||
                        (in_array($solicitud['id-tipo-solicitud'], [
                                Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                                Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                                Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                                Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
                                Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA]) &&
                            (in_array($solicitud['vigente'], [Constante::VIGENTE_1_VIGENTE])))){
                        if($solicitud['vigente'] == Constante::VIGENTE_3_SOLICITUD_BORRADOR){
                            $resultadoSolicitudes['ciudadano'][] = $solicitud;
                        } else {
                            $resultadoSolicitudes['municipal'][] = $solicitud;
                        }
                    }
                }
            }
        }
        return $resultadoSolicitudes;
    }

    public function getBloqueosTramites(){
        if($this->puedeRealizarSolicitud == false){
            return $this->listaMensajesError;
        } else {
            return [];
        }
    }

    private function filtrarSolicitudes($respuestaObtenerSolicitudes){
        $this->solicitudes = [];
        if(isset($respuestaObtenerSolicitudes["solicitudes"])) {
            $solicitudesObtenidas = $respuestaObtenerSolicitudes["solicitudes"];
            $solicitudeTipoAprobadas = array();
            foreach ($solicitudesObtenidas as $key => $solicitud) {
                if (in_array($solicitud['id-estado'], [Constante::ESTADO_SOLICITUD_7_RECHAZADA,
                    Constante::ESTADO_SOLICITUD_9_DESISTIDA])) {
                    continue;
                }
                if($solicitud['id-estado'] == Constante::ESTADO_SOLICITUD_5_APROBADO &&
                    $solicitud['vigente'] == Constante::VIGENTE_1_VIGENTE) {
                    $fechaModificacion  = (new \DateTime($solicitud['fecha-modificacion']));
                    if($fechaModificacion < $this->ultimaFechaCierreGF) {
                        continue;
                    }
                    if (in_array($solicitud['id-tipo-solicitud'], [Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                        Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                        Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
                        Constante::TIPO_SOLICITUD_30_SOLICITUD_DE_INGRESO_PARA_INSTITUCIONALIZADOS,
                        Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA])

                    ) {
                        if (isset($solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']])) {
                            if ($solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']]['id-solicitud'] < $solicitud['id-solicitud']) {
                                unset($this->solicitudes[$solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']]['key']]);
                                $solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']] = ['key' => $key,
                                    'id-solicitud' => $solicitud['id-solicitud']];

                            } else {
                                continue;
                            }
                        } else {
                            $solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']] = ['key' => $key,
                                'id-solicitud' => $solicitud['id-solicitud']];
                        }
                    }
                    if (in_array($solicitud['id-tipo-solicitud'], [Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_5_ACTUALIZACION_DE_MODULO_SALUD,
                        Constante::TIPO_SOLICITUD_6_ACTUALIZACION_DE_MODULO_EDUCACION,
                        Constante::TIPO_SOLICITUD_14_ACTUALIZACION_OCUPACION_DE_INGRESOS])

                    ) {
                        if (isset($solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']][$solicitud['run-titular']])) {
                            if ($solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']][$solicitud['run-titular']]['id-solicitud'] < $solicitud['id-solicitud']) {
                                unset($this->solicitudes[$solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']]['key']]);
                                $solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']][$solicitud['run-titular']] = ['key' => $key,
                                    'id-solicitud' => $solicitud['id-solicitud']];

                            } else {
                                continue;
                            }
                        } else {
                            $solicitudeTipoAprobadas[$solicitud['id-tipo-solicitud']][$solicitud['run-titular']] = ['key' => $key,
                                'id-solicitud' => $solicitud['id-solicitud']];
                        }
                    }
                }
                $this->solicitudes[$key] = $solicitud;
            }
        }
    }
}
