<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Models\MDSFApiResponse;
use Illuminate\Support\Facades\Http;

class TodasSolicitudesClass
{
    public $solicitudById;
    public function __invoke(
        $runCiudadano,
        $edad = null,
        $origenRegistro = null,
        $parametrosAdicionales = null
    ) {
        $tipoSolicitudClass = new TipoSolicitudClass;
        $responseData = $tipoSolicitudClass($runCiudadano);
        $listaSolicitudes =  null;
        if(isset($responseData) &&
            isset($responseData["data"]['tipo-solicitud'])){
            $listaSolicitudes = $responseData["data"]['tipo-solicitud'];
            foreach ($listaSolicitudes as $key => $solicitud) {
                $listaSolicitudes[$key] = $solicitud["tipo_solicitud"];
            }
        }

        if ($parametrosAdicionales) {
            $personaConsultada = $parametrosAdicionales['persona'];
            $personaExportConsultada = $parametrosAdicionales['personaExport'];
            $gAsignacion = $parametrosAdicionales['gAsignacion'];
            $gExport = $parametrosAdicionales['gExport'];
            $institucionalizado = $parametrosAdicionales['institucionalizado'];
            $ultimaFechaCierreGF = $parametrosAdicionales['ultimaFechaCierreGF'];
        } else {
            $personaBasicaClass = new PersonaBasicaClass;
            $personaBasica = $personaBasicaClass($runCiudadano);
            $personaConsultada = $personaBasica['data']['persona-asignacion'];
            $personaExportConsultada = null;
            $gAsignacion = null;
            $gExport = null;
            $institucionalizado = null;
            $ultimaFechaCierreGF = null;
        }

        if ($gAsignacion && $gExport) {
            $folioAsignacion = $gAsignacion["grupfami-nuevo-folio"] ?: null;
            $folioExport = $gExport["grupfami-nuevofolio"] ?: null;

            if ($folioAsignacion != $folioExport) {
                $idSolicitud = $gAsignacion["grupfami-id-solicitud"];
                $tipoSolicitud = 0;
                if($idSolicitud){
                    $solicitudByIdClass = new SolicitudByIdClass();
                    $solicitudById = $solicitudByIdClass($idSolicitud);
                    $this->solicitudById = $solicitudById["data"]['solicitud'];
                    if($this->solicitudById){
                        $tipoSolicitud = $this->solicitudById["id-tipo-solicitud"];
                    }
                }
                $arregloRespuestaNo = [];
                if($tipoSolicitud != 0) {
                    foreach ($listaSolicitudes as $key => $value) {
                        $arregloRespuestaNo[$key] = [
                            "puede-realizar-solicitud" => false,
                            'mensaje-error' => [isset(Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[$tipoSolicitud]) ?
                                Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[$tipoSolicitud] :
                                Constante::$MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO[Constante::MENSAJE_SERVICIO_CODIGO_DEFECTO_0]]
                        ];
                    }
                }

                return $arregloRespuestaNo;
            }
        }

        $solicitudesTramitables = $listaSolicitudes;
        $solicitudSemiBloqueante = Constante::$solicitudSemiBloqueantexRun;
        $solicitudEspeciales = Constante::$solicitudEspecialesCONST;
        /*
         * con la finalidad de evitar ciclo de unset del array
         * ya correspoden a casos que se tratan de caracteristicas distintas del resto
         * que es una mezcla entre Especiales(3,4,101) y actualizacion (+ 5 + 6 + 14)
         */
        //**************************************
        $casoBloqueanteTotalExtremo = 0;
        $casoBloqueanteTotal = 0;
        $casoTotalBloqueanteGrupal = 0;
        $casoSemiBloqueante = 0;
        $casoEspeciales = 0;
        $casoSemiBloqueanteGrupal = 0;
        $casoEspecialBloqueanteGrupal = 0;
        $reglaEspecialConfirmar = false;
        //**************************************
        $nombreSolicitudes = [];
        $nombreSolicitudesOtrasPorMi = [];
        $solicitudesNoTramitables = [];
        $msjCiudadano = [];
        $msjGrupoFamiliar = [];
        $msjCiudadano0 = [];
        $concatenarSinRSH = null;
        $arregloRespuestaSi = [];
        $arregloRespuestaNo = [];
        $solicitudesRCM[1] = Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO;
        $solicitudesRCM[2] = Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO;
        $solicitudesRCM[3] = Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE;
        $solicitudesRCM[4] = Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE;
        $variable = 0;
        $solicitudesEnMiNombre = false;
        $bloqueoESO_solicitudes_100_7=false;
        $mensajeCiudadanoUX = null;
        $solicitudesSemiBloqueanteRCM = [
            Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
            Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
        ];

        if (Utiles::inArrayMinuscula($origenRegistro,
            Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS)) {
            // debe quedar antes ya que se elimina con el unset mas abajo
            $solicitudesTramitables = $solicitudesRCM;
        }
        if ($edad >= Constante::EDAD_18_MAYOR_DE_EDAD ||
            $edad == null
        ) {
            $noCumpleRequisitosEdad = false;
            $personaIncorporarDesvincular = $personaConsultada;
            if ($personaIncorporarDesvincular == null || is_numeric($personaIncorporarDesvincular)) {
                return $personaIncorporarDesvincular;
            }

            if ($personaIncorporarDesvincular) {
                $perId = $personaIncorporarDesvincular['per-id'];
            }
            $contadorParaSiMismo = 0;
            $contadorParaOtraPersona = 0;
            $contadorGrupoFamiliar = 0;
            $solicitudParaGrupoFamiliar = [];
            $solicitudParaSiMismo = [];
            $solicitudParaOtraPersona = [];

            //$viaRun = "VIA RUN";
            $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitudes-grupo-familiar-run/' . $runCiudadano);
            $apiresponse = MDSFApiResponse::cast($apiresponse);
            $solicitudesActuales = [];
            if($apiresponse->data) {
                $responseData = json_decode(json_encode($apiresponse->data), true);
                $solicitudesActuales = $responseData["solicitud-grupo-familiar"];
            }
            //"VIA DATO_NUEVO";
            $porId = $personaConsultada["per-id"];
            $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitudes-grupo-familiar-dato-nuevo/' . $porId);
            $apiresponse = MDSFApiResponse::cast($apiresponse);
            $solicitudesRealizadaPorOtrosEnMiNombre = [];
            if($apiresponse->data) {
                $responseData = json_decode(json_encode($apiresponse->data), true);
                $solicitudesRealizadaPorOtrosEnMiNombre = $responseData["solicitud-grupo-familiar"];
            }
            //"VIA GRUPO_FAMILIAR";
            $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitudes-grupo-familiar-run/' . $runCiudadano);
            $apiresponse = MDSFApiResponse::cast($apiresponse);
            $solicitudesRealizadaPorGrupoFamiliar = [];
            if($apiresponse->data) {
                $responseData = json_decode(json_encode($apiresponse->data), true);
                $solicitudesRealizadaPorGrupoFamiliar = $responseData["solicitud-grupo-familiar"];
            }


            //-----------------------------------------------------------------//
            /**
             * Limpieza de solicitudes Individuales v/s otros por mi
             */
            $solicitudesRealizadaPorOtrosTEMP = $solicitudesRealizadaPorOtrosEnMiNombre;
            if ($solicitudesRealizadaPorOtrosEnMiNombre) {
                $largoSolicitudesOtros = isset($solicitudesRealizadaPorOtrosEnMiNombre)?count($solicitudesRealizadaPorOtrosEnMiNombre):0;
                $largoSolicitudesActuales = isset($solicitudesActuales)? count($solicitudesActuales) : 0;
                if ($largoSolicitudesOtros > 0 && $largoSolicitudesActuales > 0) {
                    for ($i = 0; $i < $largoSolicitudesActuales; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudesOtros; $ii++) {
                            if ($solicitudesRealizadaPorOtrosEnMiNombre[$ii] == $solicitudesActuales[$i]) {
                                unset($solicitudesRealizadaPorOtrosTEMP[$ii]);
                            }
                        }
                    }
                }
            }
            $solicitudesRealizadaPorOtrosEnMiNombre = $solicitudesRealizadaPorOtrosTEMP;
            /**
             * Limpieza de solicitudes Grupales v/s individuales/otros por mi
             */
            $solicitudesRealizadaPorGrupoFamiliarTEMP = $solicitudesRealizadaPorGrupoFamiliar;
            $array_mergeTemporal = array_merge($solicitudesActuales, $solicitudesRealizadaPorOtrosEnMiNombre);
            $largoSolicitudesGrupo = isset($solicitudesRealizadaPorGrupoFamiliar)?count($solicitudesRealizadaPorGrupoFamiliar):0;
            $largoSolicitudesActuales = isset($array_mergeTemporal)?count($array_mergeTemporal):0;
            if ($largoSolicitudesGrupo > 0) {
                if ($largoSolicitudesGrupo > 0 && $largoSolicitudesActuales > 0) {
                    for ($i = 0; $i < $largoSolicitudesActuales; $i++) {
                        for ($ii = 0; $ii < $largoSolicitudesGrupo; $ii++) {
                            if ($solicitudesRealizadaPorGrupoFamiliar[$ii] == $array_mergeTemporal[$i]) {
                                unset($solicitudesRealizadaPorGrupoFamiliarTEMP[$ii]);
                            }
                        }
                    }
                }
            }
            $solicitudesRealizadaPorGrupoFamiliar = $solicitudesRealizadaPorGrupoFamiliarTEMP;

            /**
             * CASO A --- CUANDO UNA PERSONA REALIZA UNA SOLICITUD PARA SI MISMO.
             * Query sobre RUN_CIUDADANO
             */
            if (count($solicitudesActuales)) {
                foreach ($solicitudesActuales as $idTipoSolicitudActual) {
                    if (in_array($idTipoSolicitudActual['id-tipo-solicitud'], Constante::$solicitudBloqueanteCONST)) {
                        $casoBloqueanteTotalExtremo++;
                    }
                    if (in_array($idTipoSolicitudActual['id-tipo-solicitud'],
                        Constante::$solicitudSemiBloqueanteCONST)) {
                        /**
                         * se elimina la solicitud que es del tipo SEMIBLOQUEANTE (incluyendo aquellos realizada
                         * por otros que son del mismo grupo familiar.
                         */
                        if ($idTipoSolicitudActual['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA) {
                            $reglaEspecialConfirmar = true;
                        }

                        $casoSemiBloqueante++;
                        $solicitudParaGrupoFamiliar[$runCiudadano] = $contadorGrupoFamiliar++;
                        unset($solicitudesTramitables[($idTipoSolicitudActual['id-tipo-solicitud'])]);
                        $nombreSolicitudes [] = $idTipoSolicitudActual['solicitud'];
                    }
                    if (in_array($idTipoSolicitudActual['id-tipo-solicitud'], Constante::$solicitudEspecialesCONST)) {
                        //$nombreSolicitudes [] = $idTipoSolicitudActual['solicitud'];
                        $nombreSolicitudes [] = $idTipoSolicitudActual['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR ?
                            "Desvincular Jefe de Hogar" : $idTipoSolicitudActual['solicitud'];
                        if ($perId == $idTipoSolicitudActual['dato-nuevo']) {
                            // La solicitud de (ingreso/desvinculacion/desvinculacionJH) es para la misma persona
                            $solicitudParaSiMismo[$runCiudadano] = $contadorParaSiMismo++;
                            $casoBloqueanteTotal++;
                            unset($solicitudesTramitables[($idTipoSolicitudActual['id-tipo-solicitud'])]);
                        } elseif ($idTipoSolicitudActual['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR) {
                            // Creo una solicitud de desvinculacion de JH, pero fue a nombre de otra persona.
                            $casoSemiBloqueante++;
                            $solicitudParaOtraPersona[$runCiudadano] = $contadorParaOtraPersona++;
                            unset($solicitudesTramitables[($idTipoSolicitudActual['id-tipo-solicitud'])]);
                        } else {
                            // Creo una solicitud de incorporacion(3)/desvinculacion(4) a nombre de otra persona.
                            $casoEspeciales++;
                            $solicitudParaOtraPersona[$runCiudadano] = $contadorParaOtraPersona++;
                        }
                    }
                    if (in_array($idTipoSolicitudActual['id-tipo-solicitud'],
                        Constante::$solicitudesActualizacionESO)) {
                        $nombreSolicitudes [] = $idTipoSolicitudActual['solicitud'];
                        if ($perId == $idTipoSolicitudActual['dato-actual']) {
                            $solicitudParaSiMismo[$runCiudadano] = $contadorParaSiMismo++;
                            // La persona solicito un tramite para si misma.
                            //SE AGREGA ESTA VALDIACION SI TIENE UNA ESO EN TRAMITE NO PUEDE REALIZAR UNA SOLICITUD 100 Y 7
                            // SEGUN LA INCIDENCIA RSH-1599 (30-01-2025)
                            $bloqueoESO_solicitudes_100_7=true;
                            unset($solicitudesTramitables[Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR]);
                            /*unset($solicitudesTramitables[Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA]);*/
                            unset($solicitudesTramitables[($idTipoSolicitudActual['id-tipo-solicitud'])]);
                        } else {
                            $solicitudParaOtraPersona[$runCiudadano] = $contadorParaOtraPersona++;
                        }
                    }
                    // se elimna la solicitud que tiene el ciudadano en curso excepto para solicitudEspeciales
                    // ya que se elimino mas arriba
                    if (!(in_array($idTipoSolicitudActual['id-tipo-solicitud'], Constante::$omiteEliminarSolicitud))) {
                        $solicitudParaSiMismo[$runCiudadano] = $contadorParaSiMismo++;
                        if (!(in_array($idTipoSolicitudActual['id-tipo-solicitud'], Constante::$omiteEliminarSolicitud)))
                        {
                            unset($solicitudesTramitables[($idTipoSolicitudActual['id-tipo-solicitud'])]);
                        }
                        $nombreSolicitudes [] = $idTipoSolicitudActual['solicitud'];
                    }
                }
            }
            $concatenarxRun = null;
            $contarSolicitudesxRun = self::contarSolicitudes($nombreSolicitudes);
            // $concatenarxRun0 = "Total Solicitudes Creadas por el Ciudadano ".count($solicitudesActuales).". Para si mísmo :".$contadorParaSiMismo.", para otros: "
            //    .$contadorParaOtraPersona.", Con afectación al grupo Familiar ".$contadorGrupoFamiliar .". Detalle: ".implode(" | ",$contarSolicitudesxRun);
            $concatenarxRun0 = "Total solicitudes creadas por el Ciudadano ".count($solicitudesActuales).", Detalle: ".implode(" | ",
                    $contarSolicitudesxRun);
            $concatenarxRun = count($contarSolicitudesxRun) > 0 ? $concatenarxRun0 : null;

            /**
             * CASO B --- CUANDO OTRA PERSONA REALIZA UNA SOLICITUD EN MI NOMBRE.
             * Query sobre DATO_NUEVO y DATO_ACTUAL
             * es decir El Tramite fue solicitado para esta persona
             */

            if (count($solicitudesRealizadaPorOtrosEnMiNombre)) {
                foreach ($solicitudesRealizadaPorOtrosEnMiNombre as $otrasEnMiNombre) {
                    if (in_array($otrasEnMiNombre['id-tipo-solicitud'], Constante::$solicitudBloqueanteCONST)) {
                        if ($perId == $otrasEnMiNombre['dato-nuevo']) {
                            $casoBloqueanteTotalExtremo++;
                        }
                    }
                    if (in_array($otrasEnMiNombre['id-tipo-solicitud'], Constante::$solicitudSemiBloqueanteCONST)) {
                        $casoSemiBloqueante++;
                        $nombreSolicitudesOtrasPorMi [] = $otrasEnMiNombre['solicitud'];
                        unset($solicitudesTramitables[($otrasEnMiNombre['id-tipo-solicitud'])]);
                        if ($idTipoSolicitudActual['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA) {
                            $reglaEspecialConfirmar = true;
                        }
                    }
                    if (in_array($otrasEnMiNombre['id-tipo-solicitud'], Constante::$solicitudEspecialesCONST)) {
                        // La solicitud de incorporacion/desvinculacion/desvinculacion JH, a nombre de la persona.
                        $nombreSolicitudesOtrasPorMi [] = $otrasEnMiNombre['id-tipo-solicitud'] == Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR ?
                            "Desvincular Jefe de Hogar" : $otrasEnMiNombre['solicitud'];
                        unset($solicitudesTramitables[($otrasEnMiNombre['id-tipo-solicitud'])]);
                        $casoBloqueanteTotal++;
                    }
                    if (in_array($otrasEnMiNombre['id-tipo-solicitud'], Constante::$solicitudesActualizacionESO)) {
                        $nombreSolicitudesOtrasPorMi [] = $otrasEnMiNombre['solicitud'];
                        unset($solicitudesTramitables[($otrasEnMiNombre['id-tipo-solicitud'])]);
                    }
                    // se elimna la solicitud que tiene el ciudadano en curso excepto para solicitudEspeciales
                    if (!(in_array($otrasEnMiNombre['id-tipo-solicitud'], Constante::$omiteEliminarSolicitud))) {
                        unset($solicitudesTramitables[($otrasEnMiNombre['id-tipo-solicitud'])]);
                        $nombreSolicitudesOtrasPorMi [] = $otrasEnMiNombre['solicitud'];
                    }
                }
            }
            $concatenarxPerId = null;
            $contarSolicitudesxPerId = self::contarSolicitudes($nombreSolicitudesOtrasPorMi);
            $concatenarxPerId0 = "Solicitudes realizadas por otros ".count($nombreSolicitudesOtrasPorMi)." . Detalle: ".implode(" | ",
                    $contarSolicitudesxPerId);
            $concatenarxPerId = count($contarSolicitudesxPerId) > 0 ? $concatenarxPerId0 : null;

            /**
             * CASO C --- CUANDO EL GRUPO FAMILIAR TIENE SOLICITUDES.
             * solo verifica si no hay solicitudes mayores.
             */

            $tramiteGrupoFamiliar = null;
            $concatenarRutCantidad = [];
            $solicitudSemiBloqueanteGrupal = [
                Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
            ];

            if (count($solicitudesRealizadaPorGrupoFamiliar)) {
                foreach ($solicitudesRealizadaPorGrupoFamiliar as $otrasGrupoFamiliar) {
                    if (in_array($otrasGrupoFamiliar['id-tipo-solicitud'], Constante::$solicitudBloqueanteCONST)) {
                        $casoSemiBloqueanteGrupal++;
                        $analizaRun[] = $otrasGrupoFamiliar['run-ciudadano'];
                        $tramiteGrupoFamiliar[] = [
                            $otrasGrupoFamiliar['run-ciudadano'],
                            $otrasGrupoFamiliar['solicitud'],
                            $otrasGrupoFamiliar['dato-nuevo'],
                        ];
                    }
                    if (in_array($otrasGrupoFamiliar['id-tipo-solicitud'], Constante::$solicitudEspecialesCONST)) {
                        $casoEspecialBloqueanteGrupal++;
                        $analizaRun [] = $otrasGrupoFamiliar['run-ciudadano'];
                        $otrasGrupoFamiliarSolicitudes = ($otrasGrupoFamiliar['id-tipo-solicitud']) == Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR ?
                            "Desvincular Jefe de Hogar" : $otrasGrupoFamiliar['solicitud'];
                        $tramiteGrupoFamiliar[] = [
                            $otrasGrupoFamiliar['run-ciudadano'],
                            $otrasGrupoFamiliarSolicitudes,
                            $otrasGrupoFamiliar['dato-nuevo'],
                        ];
                    }
                    if (in_array($otrasGrupoFamiliar['id-tipo-solicitud'],
                        Constante::$solicitudBloqueanteTotalGrupal)) {
                        $casoTotalBloqueanteGrupal++;
                        $analizaRun [] = $otrasGrupoFamiliar['run-ciudadano'];
                        $tramiteGrupoFamiliar [] = [
                            $otrasGrupoFamiliar['run-ciudadano'],
                            $otrasGrupoFamiliar['solicitud'],
                            $otrasGrupoFamiliar['dato-nuevo'],
                        ];
                    }
                }
            }
            if ($tramiteGrupoFamiliar) {
                $tramitesPorLaMismaPersona = array_count_values($analizaRun);
                foreach ($tramitesPorLaMismaPersona as $rut => $vecesRepetidas) {
                    for ($i = 0; $i < (count($tramiteGrupoFamiliar)); $i++) {
                        if ($rut == $tramiteGrupoFamiliar[$i][0]) {
                            $mismoTramite[] = $tramiteGrupoFamiliar[$i][1];
                        }
                    }
                    $abc = array_count_values($mismoTramite);
                    foreach ($abc as $nombre => $valor) {
                        $concatenarTramitesCantidad [] = $nombre.' ('.$valor.')';
                    }
                    $tramite = array_count_values($mismoTramite);
                    $concatenarRutCantidad [] = " RUN ".$rut."-".Utiles::dv($runCiudadano).
                        ' realizo '.$vecesRepetidas.' tramites de : '.implode(", ", $concatenarTramitesCantidad);
                }
            }

            $concatenarxGrupo = 'El grupo familiar cuenta con solicitudes vigentes, '.
                'han sido realizados por las siguientes personas.'.implode(" | ", $concatenarRutCantidad);
            $concatenarxGrupo = count($concatenarRutCantidad) > 0 ? $concatenarxGrupo : null;

            if (Utiles::inArrayMinuscula($origenRegistro,
                Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS)) {
                $solicitudSemiBloqueante = $solicitudesSemiBloqueanteRCM;
                $listaSolicitudes = $solicitudesRCM;
            }

            /**
             * Tabla Prioridad
             */
            $consultaExistenciaPersonaenExport = $personaExportConsultada;
            if ($casoBloqueanteTotalExtremo > 0) {
                $variable = 6; // Caso Bloqueante Total Extremo
            } elseif ($casoBloqueanteTotal > 0) {
                $variable = 3; // Caso Bloqueante Total
            } elseif ($casoSemiBloqueante > 0) {
                $variable = 1; // Caso Semi Bloqueante
            } elseif ($casoTotalBloqueanteGrupal > 0) {
                $variable = 5; // Caso Total Bloqueante Grupal
            } elseif ($casoEspeciales > 0) {
                $variable = 2; // Caso Especiales
            } elseif ($casoSemiBloqueanteGrupal > 0) {
                $variable = 7; // Caso Semi Bloqueante Grupal
            } elseif ($casoEspecialBloqueanteGrupal > 0) {
                $variable = 4; // Caso Especial Bloqueante Grupal
            } else {
                $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-aprobada-tipo-run/'.
                    Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO."/" . $runCiudadano);
                $apiresponse = MDSFApiResponse::cast($apiresponse);
                $buscaIngresoRegistro = null;
                if($apiresponse->data) {
                    $responseData = json_decode(json_encode($apiresponse->data), true);
                    $buscaIngresoRegistro = $responseData["solicitud"];
                }

                $poseeRSHClass = new PoseeRSHClass();
                $respuestaPoseeRSHClass = $poseeRSHClass($runCiudadano);
                $poseeRSH = $respuestaPoseeRSHClass["posee-rsh"];

                $variable = 0; // Todos los otros casos donde no existe un bloqueo, si no verificacion de forma individual
                if (is_null($consultaExistenciaPersonaenExport) || ($poseeRSH == false)) {
                    if(isset($consultaExistenciaPersonaenExport) && $poseeRSH == false){
                        $concatenarSinRSH = "Ciudadano no posee registro RSH o fue desvinculado recientemente, puede realizar Ingreso al Registro Social de Hogares o descargar la cartola en el siguiente enlace <a href='http://localhost/rsh-municipal/public/printcartola'>Aquí</a> ";
                    }
                    elseif(is_null($consultaExistenciaPersonaenExport) && $poseeRSH == false){
                        $concatenarSinRSH = "Ciudadano no posee registro RSH, debe realizar Ingreso al Registro Social de Hogares o Incorporación a grupo familiar.";
                    }
                    $variable = 3; // Caso Bloqueante Total
                    $mensajeCiudadanoUX = "Usted no cuenta con un registro activo, lo invitamos a realizar una solicitud de Ingreso al Registro Social de Hogares";
                    $incorporado = false;
                    if (!$buscaIngresoRegistro) {
                        $perId = $personaConsultada["per-id"];
                        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-aprobada-tipo-per-id/'.
                            Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE."/" . $perId);
                        $apiresponse = MDSFApiResponse::cast($apiresponse);
                        $buscaIngresoRegistro = null;
                        if($apiresponse->data) {
                            $responseData = json_decode(json_encode($apiresponse->data), true);
                            $buscaIngresoRegistro = $responseData["solicitud"];
                        }
                        if(!empty($buscaIngresoRegistro)){
                            $incorporado = true;
                        }
                        else{
                            $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/beneficiarios-by-run-analisis/' . $runCiudadano);
                            $apiresponse = MDSFApiResponse::cast($apiresponse);
                            $beneficiario = null;
                            if($apiresponse->data) {
                                $responseData = json_decode(json_encode($apiresponse->data), true);
                                $beneficiario = $responseData["beneficiarios-run-analisis"];
                            }
                            if(!empty($beneficiario['bnfgrupfami-idsolicitud'])){
                                $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-aprobada-tipo-id-solicitud/' . $beneficiario['bnfgrupfami-idsolicitud']);
                                $apiresponse = MDSFApiResponse::cast($apiresponse);
                                $buscaIngresoRegistro = null;
                                if($apiresponse->data) {
                                    $responseData = json_decode(json_encode($apiresponse->data), true);
                                    $buscaIngresoRegistro = $responseData["solicitud"];
                                }
                            }
                        }
                    }
                    $diferenciaDias = 31;
                    if ($buscaIngresoRegistro) {
                        $fecha_aprobacion = strtotime(substr($buscaIngresoRegistro['fecha-modificacion'], 0, -9));
                        $fecha_actual = strtotime(date("Y") . "/" . date("m") . "/" . date("d"));
                        $diferenciaDias = round(( $fecha_actual - $fecha_aprobacion) / 86400);
                    }

                    if ($buscaIngresoRegistro && ($diferenciaDias <= 30)) {
                        if($incorporado){
                            $concatenarSinRSH = 'Ciudadano en espera de tramo. Tiene solicitud de Incorporación aprobada.';
                            $mensajeCiudadanoUX = 'Usted se encuentra en espera de tramo, ya posee una solicitud de Incorporación aprobada.';
                        } else {
                            $concatenarSinRSH = 'Ciudadano en espera de tramo. Tiene solicitud de Ingreso al registro aprobada.';
                            $mensajeCiudadanoUX = 'Usted se encuentra en espera de tramo, ya posee una solicitud de Ingreso al registro aprobada.';
                        }
                        $variable = 6;
                    } else {
                        $poseeRSH = false;
                    }
                }
                if(($consultaExistenciaPersonaenExport) &&
                    ($buscaIngresoRegistro) &&
                    isset($consultaExistenciaPersonaenExport["fic-grupofamiliar-id"])
                    && $variable != 6){
                    $esperaTramo = false;
                    if(isset($gExport["grupo-familiar-export"]["grupfami-id"])
                    && $consultaExistenciaPersonaenExport["fic-grupofamiliar-id"] == $gExport["grupo-familiar-export"]["grupfami-id"]){
                        $grupoFamiliar = $gExport["grupo-familiar-export"];
                    } else {
                        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/grupo-familiar-export-id-grupo-familiar/' .
                        $consultaExistenciaPersonaenExport["fic-grupofamiliar-id"]);
                        $apiresponse = MDSFApiResponse::cast($apiresponse);
                        $grupoFamiliar = null;
                        if($apiresponse->data) {
                            $responseData = json_decode(json_encode($apiresponse->data), true);
                            $grupoFamiliar = $responseData["grupo-familiar-export"];
                        }
                    }
                    if ($grupoFamiliar && ($grupoFamiliar["fecha-calificacion"] < $buscaIngresoRegistro["fecha-modificacion"])) {
                        $esperaTramo = true;
                    }
                    if ($esperaTramo) {
                        $concatenarSinRSH = 'Ciudadano en espera de tramo. Tiene solicitud de Ingreso al registro aprobada.';
                        $mensajeCiudadanoUX = 'Usted se encuentra en espera de tramo, ya posee una solicitud de Ingreso al registro aprobada.';
                        $variable = 6;
                    }
                }
            }

            $msjCiudadano[] = $concatenarSinRSH." ".$concatenarxRun." ".$concatenarxPerId." ".$concatenarxGrupo;

            switch ($variable) {
                case 0:
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    break;
                case 1:
                    // construye un solo array de acuerdo al tipo de regla.
                    array_push($solicitudSemiBloqueante, Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO);
                    if (!Utiles::inArrayMinuscula($origenRegistro,
                        Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS)) {
                        array_push($solicitudSemiBloqueante, Constante::TIPO_SOLICITUD_103_SOLICITUD_DE_SUPERVISION);
                        if ($reglaEspecialConfirmar) {
                            array_push($solicitudSemiBloqueante, Constante::TIPO_SOLICITUD_205_CONFIRMAR_DATOS);
                        }
                    }
                    for ($cont2 = 0; ($cont2 < count($solicitudEspeciales));) {
                        array_push($solicitudSemiBloqueante, $solicitudEspeciales[$cont2]);
                        $cont2++;
                    }
                    // Se eliminan todas las solicitudes a excepcion de las definidas como OTRAS.
                    for ($cont = 0; ($cont < count($solicitudSemiBloqueante));) {
                        unset($solicitudesTramitables[($solicitudSemiBloqueante[$cont])]);
                        $cont++;
                    }
                    // $solicitudesTramitables !!
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    break;
                case 2:
                    $solicitudesTemp = Constante::$solicitudSemiBloqueantexRun;
                    array_push($solicitudesTemp, Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO);
                    array_push($solicitudesTemp, Constante::TIPO_SOLICITUD_103_SOLICITUD_DE_SUPERVISION);
                    // Solo solicitudes especiales, es decir OTRAS + aquellas del tipo 3 y 4.
                    for ($cont = 0; ($cont < count($solicitudesTemp));) {
                        unset($solicitudesTramitables[($solicitudesTemp[$cont])]);
                        $cont++;
                    }
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    // $solicitudesTramitables !!
                    break;
                case 3:
                    // Solamente solicitudes bloqueantes (Ingreso al Registro), No puede hacer ninguna otra solicitud.
                    $solicitudesTemp = $listaSolicitudes;
                    unset($solicitudesTemp[1]);
                    $solicitudesTramitables2 = $solicitudesTramitables;
                    $solicitudesTramitables = array_diff($solicitudesTramitables2, $solicitudesTemp);
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    break;
                case 4:
                    // *** CASOS GRUPALES ***
                    // para el caso de la solicitud numero 1..
                    array_push($solicitudSemiBloqueanteGrupal, Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO);
                    array_push($solicitudSemiBloqueanteGrupal, Constante::TIPO_SOLICITUD_103_SOLICITUD_DE_SUPERVISION);
                    $temporal = Constante::$solicitudBloqueanteTotalGrupal;
                    $otrasSolicitudes = $solicitudesTramitables;
                    foreach ($temporal as $indiceArray2) {
                        unset($otrasSolicitudes[$indiceArray2]);
                    }
                    $solicitudesTramitables = $otrasSolicitudes;
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    break;
                case 5:
                    // CASOS GRUPALES
                    // Solamente solicitudes bloqueantes (Ingreso al Registro), No puede hacer ninguna otra solicitud.
                    $temporal = [
                        Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                        Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
                        Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                        Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO,
                        Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                        Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
                        Constante::TIPO_SOLICITUD_103_SOLICITUD_DE_SUPERVISION,
                        Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
                    ];
                    $otrasSolicitudes = $solicitudesTramitables;
                    foreach ($temporal as $indiceArray2) {
                        unset($otrasSolicitudes[$indiceArray2]);
                    }
                    $solicitudesTramitables = $otrasSolicitudes;
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    break;
                case 6:
                    // No puede hacer ninguna otra solicitud ya que tiene una bloqueante TOTAL.
                    $solicitudesTramitables = [];
                    $solicitudesNoTramitables = $listaSolicitudes;
                    break;
                case 7:
                    // CASOS GRUPALES
                    // para el caso de la solicitud numero 3, 4 ,101..
                    array_push($solicitudSemiBloqueanteGrupal, Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO);
                    $temporal = [
                        Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
                        Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                        Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO,
                        Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                        Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
                    ];
                    $otrasSolicitudes = $solicitudesTramitables;
                    foreach ($temporal as $indiceArray2) {
                        unset($otrasSolicitudes[$indiceArray2]);
                    }
                    $solicitudesTramitables = $otrasSolicitudes;
                    $solicitudesNoTramitables = array_diff_key($listaSolicitudes, $solicitudesTramitables);
                    break;
            }
        } else {
            return "personas menores de 18 anos no pueden hacer tramite";
            // personas menores de 18 anos no pueden hacer tramites.
            $solicitudesTramitables = [];
            if (Utiles::inArrayMinuscula($origenRegistro,
                Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS)) {
                $listaSolicitudes = $solicitudesRCM;
            }

            $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/persona-ingresos-pension-export/' . $runCiudadano);
            $apiresponse = MDSFApiResponse::cast($apiresponse);
            $oPersonaExport = null;
            if($apiresponse->data) {
                $responseData = json_decode(json_encode($apiresponse->data), true);
                $oPersonaExport = $responseData["grupo-familiar-export"];
            }
            if($oPersonaExport){
                $solicitudesTramitables[Constante::TIPO_SOLICITUD_63_ACTUALIZACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION]
                    = $listaSolicitudes[Constante::TIPO_SOLICITUD_63_ACTUALIZACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION];
                unset($listaSolicitudes[Constante::TIPO_SOLICITUD_63_ACTUALIZACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION]);
            }
            //else{
            $solicitudesNoTramitables = $listaSolicitudes;
            $msjCiudadano[] = "El ciudadano RUN " . $runCiudadano . "-" . Utiles::dv($runCiudadano)
                . " es menor de edad (" . $edad . ") lamentablemente no puede realizar esta solicitud, no cumple con los requisitos mínimos.";
            $noCumpleRequisitosEdad = true;
            //}

        }

        /**
         * ARRAY --- MENSAJES
         */
        $consultaExistenciaPersonaenExport = $personaExportConsultada;
        $solicitudARCBloqueada = array();

        //try {
        $apiresponse = Http::APISYSTEM('EXPORT_ACCESOS')->get('/fuentes');
        $apiresponse = MDSFApiResponse::cast($apiresponse);
        $listadoFuentes["tipos-fuentes"] = null;
        $listadoFuentes["solicitudes"] = null;
        if($apiresponse->data) {
            $responseData = json_decode(json_encode($apiresponse->data), true);
            $listadoFuentes["tipos-fuentes"] = $responseData["tipos-fuentes"];
            $listadoFuentes["solicitudes"] = $responseData["solicitudes"];
        }
        $grupoFamiliarCiudadano = $gAsignacion;
        $grupoFamiliarCiudadanoNuevoFolio = isset($grupoFamiliarCiudadano) ?
                                            $grupoFamiliarCiudadano["grupfami-nuevo-folio"]:null;
        foreach ($solicitudesNoTramitables as $tipoSolicitud => $value) {
            if (in_array($tipoSolicitud, Constante::$solicitudesComplementoARC)) {
                unset($solicitudesNoTramitables[($tipoSolicitud)]);
                $solicitudesTramitables[$tipoSolicitud]= $listaSolicitudes[$tipoSolicitud];
            }
        }

        foreach ($solicitudesTramitables as $key => $value) {

            //aplicacion de regla para solicitudes Actualizacion RA
            //Falta agregar un mensaje acorde ya que actualmente solo posee mensajes genericos de no poder ya que hay solicitudes
            //por tanto se deja como no posible tramitar pero sin mensaje.
            //Issue #37 Proyecto municipal gitlab.mds.cl


            //no puede hacer 202 si es mayor de 18 y es institucionalizado
            if ($key == Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA) {
                if ($institucionalizado == true) {
                    unset($solicitudesTramitables[Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA]);
                    $arregloRespuestaNo[$key] = [
                        "puede-realizar-solicitud" => false,
                        "mensaje" => 'Usuario cuenta con registro como persona institucionalizada, debe realizar un nuevo ingreso al registro',
                        "mensaje-ciudadano" => 'Usuario cuenta con registro como persona institucionalizada, debe realizar un nuevo ingreso al registro',
                        'mensaje-error' => isset(Constante::$MENSAJE_DE_ERROR_SERVICIO_COMPLETO[Constante::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO]) ?
                            Constante::$MENSAJE_DE_ERROR_SERVICIO_COMPLETO[Constante::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO] :
                            Constante::$MENSAJE_DE_ERROR_SERVICIO_COMPLETO[Constante::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST]
                    ];
                }
            }

            if ($key == Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO) {
                if (!is_null($grupoFamiliarCiudadanoNuevoFolio)) {
                    $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/solicitud-no-finalizadas-tipo-dato-actual/' .
                        Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO ."/"
                        . $grupoFamiliarCiudadanoNuevoFolio);
                    $apiresponse = MDSFApiResponse::cast($apiresponse);
                    $solAux19 = null;
                    if($apiresponse->data) {
                        $responseData = json_decode(json_encode($apiresponse->data), true);
                        $solAux19 = $responseData["solicitud"];
                    }
                    if ($solAux19) {
                        unset($solicitudesTramitables[$key]);
                        $solicitudesNoTramitables[$key] = $value;
                    }
                }
            }
            if ($key == Constante::TIPO_SOLICITUD_79_ACTUALIZACION_DE_PARTICIPACION_JURIDICA) {
                $valSol79 = false;
                if (!empty($consultaExistenciaPersonaenExport)){
                    if ($consultaExistenciaPersonaenExport["p-juridica"] == 420) {
                        $valSol79 = true;
                    }
                }
            }

            $bloqueoSolicitud = false;
            if ((!empty($consultaExistenciaPersonaenExport)) && (!(is_null($listadoFuentes['solicitudes'])))) {
                switch ($key) {
                    Case Constante::TIPO_SOLICITUD_60_ACTUALIZACION_INGRESO_TRABAJADOR_DEPENDIENTE:
                        $listadoFuentes60 = $listadoFuentes['solicitudes'][60]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"],
                            $listadoFuentes60)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_61_ACTUALIZACION_INGRESO_TRABAJADOR_INDEPENDIENTE:
                        $listadoFuentes61 = $listadoFuentes['solicitudes'][61]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"],
                            $listadoFuentes61)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_62_ACTUALIZACION_INGRESO_TRABAJADOR_DEPENDIENTE_E_INDEPENDIENTE:
                        $listadoFuentes62 = $listadoFuentes['solicitudes'][62]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"],
                            $listadoFuentes62)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_50_COMPLEMENTO_POR_PENSION_DE_ALIMENTOS:
                        if ($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"] == Constante::FUENTE_0_SIN_INGRESOS &&
                            $consultaExistenciaPersonaenExport["calc-ingreso-capital"] == Constante::FUENTE_0_SIN_INGRESOS &&
                            $consultaExistenciaPersonaenExport["calc-fuente-pension"] == Constante::FUENTE_0_SIN_INGRESOS
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_63_ACTUALIZACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION:
                        $listadoFuentes63 = $listadoFuentes['solicitudes'][63]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-pension"], $listadoFuentes63)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_64_ACTUALIZACION_DE_INGRESOS_DE_CAPITAL:
                        $listadoFuentes64 = $listadoFuentes['solicitudes'][64]['fuentes'];
                        $fuenteCapitalCodigo = (!(is_null($consultaExistenciaPersonaenExport["calc-ingreso-capital"])) &&
                            (int)$consultaExistenciaPersonaenExport["calc-ingreso-capital"] > 0) ?
                            Constante::FUENTE_200_INGRESO_DE_CAPITAL : null;
                        if (!in_array($fuenteCapitalCodigo, $listadoFuentes64)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_53_COMPLEMENTO_POR_PROPIEDAD_COMPARTIDA_DE_BIEN_RAIZ:
                        if ($consultaExistenciaPersonaenExport["calc-propiedades-costosas"] == 0) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_67_ACTUALIZACION_DE_PROPIEDAD_DE_BIEN_RAIZ:
                        if ($consultaExistenciaPersonaenExport["calc-propiedades-costosas"] == 0) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_68_ACTUALIZACION_DE_PROPIEDAD_DE_AUTOMOVIL:
                        if ($consultaExistenciaPersonaenExport["calc-vehiculos-costosos"] == 0
                            && $consultaExistenciaPersonaenExport["dnaves-ind-muy-alto-valor"] == 0) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_52_COMPLEMENTO_POR_PAGO_DE_ARANCEL_DE_ESTABLECIMIENTO_EDUCACIONAL:
                        if ($consultaExistenciaPersonaenExport["calc-colegio-costoso"] == 0) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_66_ACTUALIZACION_DE_MATRICULA_EN_ESTABLECIMIENTO_EDUCACIONAL:
                        if ($consultaExistenciaPersonaenExport["calc-colegio-costoso"] == 0) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_70_RECTIFICACION_INGRESO_TRABAJADOR_DEPENDIENTE:
                        $listadoFuentes70 = $listadoFuentes['solicitudes'][70]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"],
                            $listadoFuentes70)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_71_RECTIFICACION_INGRESO_TRABAJADOR_INDEPENDIENTE:
                        $listadoFuentes71 = $listadoFuentes['solicitudes'][71]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"],
                            $listadoFuentes71)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_72_RECTIFICACION_DE_INGRESOS_DE_CAPITAL:
                        $listadoFuentes72 = $listadoFuentes['solicitudes'][72]['fuentes'];
                        $fuenteCapitalCodigo = (!(is_null($consultaExistenciaPersonaenExport["calc-ingreso-capital"])) &&
                            (int)$consultaExistenciaPersonaenExport["calc-ingreso-capital"] > 0) ?
                            Constante::FUENTE_200_INGRESO_DE_CAPITAL : null;
                        if (!in_array($fuenteCapitalCodigo, $listadoFuentes72)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_77_RECTIFICACION_DE_INGRESOS_MONETARIOS_POR_PENSION_Y_O_JUBILACION:
                        $listadoFuentes77 = $listadoFuentes['solicitudes'][77]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-pension"], $listadoFuentes77)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_78_RECTIFICACION_INGRESO_DEPENDIENTE_E_INDEPENDIENTE:
                        $listadoFuentes78 = $listadoFuentes['solicitudes'][78]['fuentes'];
                        if (!in_array($consultaExistenciaPersonaenExport["calc-fuente-ingreso-laboral"],
                            $listadoFuentes78)
                        ) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                    Case Constante::TIPO_SOLICITUD_79_ACTUALIZACION_DE_PARTICIPACION_JURIDICA:
                        if ($valSol79 == false) {
                            $bloqueoSolicitud = true;
                        }
                        break;
                }
                if ($bloqueoSolicitud == true) {
                    unset($solicitudesTramitables[$key]);
                    $solicitudesNoTramitables[$key] = $value;
                    //No cumple con los reglas de negocio
                    $solicitudARCBloqueada[$key] = $key;
                }
            }
        }

        //Bloqueo de tramites por validación de email (para todos los usuarios por defecto)
       /* if($origenRegistro == Constante::ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA){

            $DatosContactoClass = new DatosContactoClass();
            $resValidacionPersona = $DatosContactoClass($runCiudadano);
            $resValidacionPersona = isset($resValidacionPersona['persona-token-mail'])?$resValidacionPersona['persona-token-mail']:null;

            //Realiza el bloqueo por defecto a todas las personas sin validar.
            if (isset($resValidacionPersona['estado-validacion']) && (
                $resValidacionPersona['estado-validacion'] != Constante::ESTADO_VALIDADO_VALIDA_EMAIL ||
                is_null($resValidacionPersona['email-temporal']))) {

                # solo bloquea cuando no existe una validacion de email
                foreach ($solicitudesTramitables as $key => $value) {
                    $arregloRespuestaSi[$key] = [
                        "puede-realizar-solicitud" => false,
                        "mensaje" => Constante::MSJ_BLOQUEO_VALIDA_EMAIL,
                        "mensaje-ciudadano" => Constante::MSJ_BLOQUEO_VALIDA_EMAIL
                    ];
                }

            } else {

                foreach ($solicitudesTramitables as $key => $value) {
                    $arregloRespuestaSi[$key] = [
                        "puede-realizar-solicitud" => true,
                        "mensaje" => "",
                        "mensaje-ciudadano" => ""
                    ];
                }

            }
        } else {*/
            foreach ($solicitudesTramitables as $key => $value) {
                $arregloRespuestaSi[$key] = [
                    "puede-realizar-solicitud" => true,
                    "mensaje" => "",
                    "mensaje-ciudadano" => "",
                    "mensaje-error" => []
                ];
            }
        //}
        //::

        if (count($solicitudesNoTramitables)) {
            if (!isset($mensajeCiudadanoUX)) {
                $mensajeCiudadanoUX = $noCumpleRequisitosEdad == true ? implode(" - ", $msjCiudadano) :
                    "No puede realizar esta solicitud, debido a que tiene una solicitud suya o de su grupo familiar en curso.";
            }
            foreach ($solicitudesNoTramitables as $key => $value) {
                if (in_array($key, $solicitudARCBloqueada)) {
                    $mensajeCiudadano = Constante::MENSAJE_NO_CUMPLE_REGLA_CIUDADANO;
                    $msjMunicipal = Constante::MENSAJE_NO_CUMPLE_REGLA_MUNICIPAL;
                } else {
                    if($bloqueoESO_solicitudes_100_7){
                        if($key == Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR || $key == Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA){
                            $msjMunicipal = implode(" - ", $msjCiudadano);
                            $mensajeCiudadano = 'No puede realizar esta solicitud, usted o algún miembro de su grupo familiar tiene una solicitud ESO en trámite';
                        }
                        else{
                            $msjMunicipal = implode(" - ", $msjCiudadano);
                            $mensajeCiudadano = $mensajeCiudadanoUX;
                        }
                    }
                    else{
                        $msjMunicipal = implode(" - ", $msjCiudadano);
                        $mensajeCiudadano = $mensajeCiudadanoUX;
                    }

                }
                $mensajeError = [];
                if(isset($mensajeCiudadano)){
                    if($mensajeCiudadano == "No puede realizar esta solicitud, debido a que tiene una solicitud suya o de su grupo familiar en curso."){
                        $mensajeError=isset(Constante::$MENSAJE_DE_ERROR_SERVICIO_COMPLETO[Constante::NUMERO_ERROR_15_POSEE_MENSAJE_BLOQUEANTE]) ?
                            Constante::$MENSAJE_DE_ERROR_SERVICIO_COMPLETO[Constante::NUMERO_ERROR_15_POSEE_MENSAJE_BLOQUEANTE] :
                            Constante::$MENSAJE_DE_ERROR_SERVICIO_COMPLETO[Constante::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST];
                    }else {
                        $mensajeError = isset($mensajeCiudadano)?[
                            Constante::MENSAJE_SERVICIO_CODIGO => Constante::NUMERO_ERROR_120_VARIOS,
                            Constante::MENSAJE_SERVICIO_MENSAJE => $mensajeCiudadano,
                        ]:[];
                    }
                }

                $arregloRespuestaNo[$key] = [
                    "puede-realizar-solicitud" => false,
                    "mensaje" => $msjMunicipal,
                    "mensaje-ciudadano" => $mensajeCiudadano,
                    "mensaje-error" => $mensajeError,
                ];
            }
        }

        return ($arregloRespuestaSi + $arregloRespuestaNo);
    }

    public static function contarSolicitudes($nombreSolicitudes)
    {
        $concatenarNombreCantidad = [];
        if (count($nombreSolicitudes) > 0) {
            //$solicitudesEnMiNombre = True;
            foreach ($nombreSolicitudes as $nombre1 => $valor1) {
                if ($valor1 == null) {
                    $nombreSolicitudes[$nombre1] = 'Sol. SIN NOMBRE';
                }
            }

            $abc = array_count_values($nombreSolicitudes);
            foreach ($abc as $nombre => $valor) {
                $concatenarNombreCantidad[] = $nombre.' ('.$valor.')';
            }
        }

        return ($concatenarNombreCantidad);
    }
}
