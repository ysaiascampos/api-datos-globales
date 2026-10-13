<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Helpers\CacheHelper;
use App\Models\MDSFApiResponse;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SoapClient;

class Utiles
{
    /**
     * Retorna el dv, de un run que viene sin puntos ni dv
     */
    public static function dv($r)
    {
        $s = 1;
        for ($m = 0; $r != 0; $r /= 10) {
            $s = ($s + $r % 10 * (9 - $m++ % 6)) % 11;
        }
        return chr($s ? $s + 47 : 75);
    }

    /**
     * Verifica si el dv de un run xxx.xxx-y corresponde realmente al dv correcto
     */
    public static function validaRUN($value)
    {
        $tmp = explode('-', $value);
        if (count($tmp) == 2) {
            return !strcmp(Utiles::dv(str_replace('.', '', $tmp[0])), strtoupper($tmp[1]));
        }
        return false;
    }

    /**
     * Consume el webservice de RC para actualizar la tupla de la tabla personas de asignacion, correspondiente al run
     * si esta no existe el run en dicha tabla, consume el webservice de RC y la guarda en la tabla personas
     * en ambos casos devuelve la tupla de la tabla personas actualizada o nueva
     */
    public static function getPersona($runSinPuntosNiGuionNiDv)
    {
        if (!is_numeric($runSinPuntosNiGuionNiDv)) {
            return -1;
        }
        if (strpos(strtolower($runSinPuntosNiGuionNiDv), 'e') !== false) {
            return -1;
        }

        $cacheKeyPre = env('PERSONA_CACHE_KEY_PRE','PERSONA_');
        $cacheKey = $cacheKeyPre.$runSinPuntosNiGuionNiDv;
        $ttl = (int) config('cache.ttl.medium', 300);
        $cacheActivar = config('cache.features.persona', true);

        return CacheHelper::rememberSafe(
            $cacheKey,
            $ttl,
            function () use ($runSinPuntosNiGuionNiDv) {

                $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get('/persona-asignacion/' . $runSinPuntosNiGuionNiDv);
                $apiresponse = MDSFApiResponse::cast($apiresponse);

                $apiresponse = json_decode(json_encode($apiresponse->data), true);
                $persona = (isset($apiresponse["persona-asignacion"])) ? $apiresponse["persona-asignacion"] : null;

                $fechaNacim = null;
                if (!empty($persona)) {

                    $personaData = [
                        "per-id" => $persona["per-id"],
                        "per-run" => $persona["per-run"],
                        "per-dv" => $persona["per-dv"],
                        "per-nombres" => $persona["per-nombres"],
                        "per-apellidopaterno" => $persona["per-apellidopaterno"],
                        "per-apellidomaterno" => $persona["per-apellidomaterno"],
                        "per-fechanacimiento" => $persona["per-fechanacimiento"],
                        "per-edad" => $persona["per-edad"],
                        "per-sexo" => $persona["per-sexo"],
                        "per-nacionalidad" => $persona["per-nacionalidad"],
                        "per-direccion" => $persona["per-direccion"],
                        "per-email" => $persona["per-email"],
                        "per-telefono" => $persona["per-telefono"],
                        "per-celular" => $persona["per-celular"],
                        "per-fechadefuncion" => $persona["per-fechadefuncion"],
                        "per-fechafuente" => $persona["per-fechafuente"],
                        "per-fechaactualizacion" => $persona["per-fechaactualizacion"],
                        "per-caracterizacion" => $persona["per-caracterizacion"],
                        "per-fechacalculo" => $persona["per-fechacalculo"],
                        "per-vigente" => $persona["per-vigente"],
                        "per-fechaexpiracion" => $persona["per-fechaexpiracion"],
                        "per-idsolicitud" => $persona["per-idsolicitud"],
                        "per-calculoactivo" => $persona["per-calculoactivo"],
                        "per-fuente" => $persona["per-fuente"],
                        "no-registrado-srcei" => $persona["no-registrado-srcei"],
                        "per-situacionvisa" => $persona["per-situacionvisa"],
                        "per-identidadgenero" => $persona["per-identidadgenero"],
                        "per-md5" => $persona["per-md5"],
                        "per-nroidentificadornac" => $persona["per-nroidentificadornac"],
                        "per-estadocivil" => $persona["per-estadocivil"],
                        "per-fcorrec-man" => $persona["per-fcorrec-man"],
                        "per-desc-correc" => $persona["per-desc-correc"],
                        "per-fecha-consulta" => $persona["per-fecha-consulta"],
                        "ingreso-manual" => $persona["ingreso-manual"],
                        "per-run-padre" => $persona["per-run-padre"],
                        "per-run-madre" => $persona["per-run-madre"],
                        "per-fecha-actualizacion-padres" => $persona["per-fecha-actualizacion-padres"]];

                    if ($persona["per-fechadefuncion"] == 0) {
                        $fechaNacim = $persona["per-fechadefuncion"];
                    }
                    $runificar = env('CONSUMIR_WS_RUNIFICADOR', Constante::ENV_DEFECTO_CONSUMIR_WS_RUNIFICADOR);
                    if ($runificar) {
                        $runificarRest = env('RUNIFICADOR_REST_CONSUMIR_WS', Constante::ENV_DEFECTO_CONSUMIR_REST_RUNIFICADOR);
                        if ($runificarRest) {
                            $apiRunificador = new ApiRunificador;
                            $personaWs = $apiRunificador($runSinPuntosNiGuionNiDv, $persona["per-dv"]);
                        } else {
                            $WSRunificador = new WSRunificador;
                            $personaWs = $WSRunificador($runSinPuntosNiGuionNiDv);
                        }

                        if (is_numeric($personaWs)) {
                            Log::info("El runificador devuelve error pero persona Persona existe en BD y no se permite continuar", ['wsPersona' => $personaWs]);
                            return $personaWs;
                        }
                        if ($personaWs) {
                            $personaData["per-nombres"] = $personaWs->Nombres;
                            $personaData["per-apellidopaterno"] = $personaWs->ApellidoPaterno;
                            $personaData["per-apellidomaterno"] = $personaWs->ApellidoMaterno;
                            $personaData["per-fechanacimiento"] = date('Ymd', strtotime($personaWs->FechaNacimiento));
                            $personaData["per-estadocivil"] = $personaWs->EstadoCivil;
                            if ($personaWs->Sexo == 'MASCULINO') {
                                $personaData["per-sexo"] = Constante::SEXO_1_MASCULINO;
                            } else {
                                $personaData["per-sexo"] = Constante::SEXO_2_FEMENINO;
                            }
                            if ($personaWs->FechaDefuncion != 0) {
                                $personaData["per-fechadefuncion"] = date('Ymd', strtotime($personaWs->FechaDefuncion));
                            } else {
                                $fechaNacim = $personaWs->FechaNacimiento;
                            }
                        }
                    }

                    if (!(is_null($fechaNacim))) {
                        $personaData["per-edad"] = Utiles::getEdad($fechaNacim);
                    }
                    $ver_temp_parentesco = env('TEMPORAL_PARENTESCO', false);
                    if ($ver_temp_parentesco == true) {
                        if (is_null($personaData["per-run-padre"]) || is_null($personaData["per-run-madre"])) {
                            $padres = Utiles::consultarAscendencia($persona["per-run"], $persona["per-dv"]);
                            $personaData["per-run-padre"] = !is_null($padres->padre->rut) ? $padres->padre->rut : $persona["per-run-padre"];
                            $personaData["per-run-madre"] = !is_null($padres->madre->rut) ? $padres->madre->rut : $persona["per-run-padre"];
                            $personaData["per-fecha-actualizacion-padres"] = !is_null($padres->fechaConsulta) ? $padres->fechaConsulta : $persona["per-fecha-actualizacion-padres"];
                        }
                    }


                    $apiresponse = Http::APISYSTEM('TX_ACCESOS')->put('/persona-asignacion-actualizar', $personaData);
                    $apiresponse = MDSFApiResponse::cast($apiresponse);

                    $apiresponse = json_decode(json_encode($apiresponse->data), true);

                    if (!isset($apiresponse["persona-asignacion"])) {
                        return Constante::ERROR_CATEGORIA_EJECUCION;
                    }

                    $nacionalidad = ($apiresponse["persona-asignacion"]['per-nacionalidad'] == 1) ? "CHILENA" : "EXTRANJERO";
                    $apiresponse["persona-asignacion"]['nacionalidad'] = $nacionalidad;

                    $sexo = [null, 'Hombre', 'Mujer'][intval($apiresponse["persona-asignacion"]['per-sexo'])];
                    $apiresponse["persona-asignacion"]['sexo'] = $sexo;

                    $situacion = ($apiresponse["persona-asignacion"]['per-fechadefuncion'] != 0) ? false : true;
                    $apiresponse["persona-asignacion"]['situacion'] = $situacion;
                    $persona = $apiresponse["persona-asignacion"];
                    return $persona;
                } else {

                    try {
                        $runificarRest = env('RUNIFICADOR_REST_CONSUMIR_WS', Constante::ENV_DEFECTO_CONSUMIR_REST_RUNIFICADOR);
                        if ($runificarRest) {
                            $apiRunificador = new ApiRunificador;
                            $personaWs = $apiRunificador($runSinPuntosNiGuionNiDv, Utiles::dv($runSinPuntosNiGuionNiDv));
                        } else {
                            $WSRunificador = new WSRunificador;
                            $personaWs = $WSRunificador($runSinPuntosNiGuionNiDv);
                        }
                        if (is_numeric($personaWs)) {
                            Log::info("El runificador devuelve error pero persona Persona existe en BD y se permite continuar", ['wsPersona' => $personaWs]);
                            return $personaWs;
                        }

                        if ($personaWs->Nombres == "") {
                            Log::error("La data vacia enviada del runificador", ['wsPersona' => $personaWs]);
                            return Constante::DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL;
                        }
                        if ($personaWs->FechaNacimiento == 0) {
                            Log::error("La fecha de nacimiento no puede ser 0 ", ['wsPersona' => $personaWs]);
                            return Constante::DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL;
                        }

                        if ($personaWs) {
                            $personaWs = $personaWs->obtenerPersonaResult;
                            if ($personaWs->Estado == 'OK' || $personaWs->Estado == 'OK-CACHE') {

                                $personaData = [
                                    "per-run" => null,
                                    "per-dv" => null,
                                    "per-nombres" => null,
                                    "per-apellidopaterno" => null,
                                    "per-apellidomaterno" => null,
                                    "per-fechanacimiento" => null,
                                    "per-edad" => null,
                                    "per-sexo" => null,
                                    "per-nacionalidad" => null,
                                    "per-direccion" => null,
                                    "per-email" => null,
                                    "per-telefono" => null,
                                    "per-celular" => null,
                                    "per-fechadefuncion" => null,
                                    "per-fechafuente" => null,
                                    "per-fechaactualizacion" => null,
                                    "per-caracterizacion" => null,
                                    "per-fechacalculo" => null,
                                    "per-vigente" => null,
                                    "per-fechaexpiracion" => null,
                                    "per-idsolicitud" => null,
                                    "per-calculoactivo" => null,
                                    "per-fuente" => null,
                                    "no-registrado-srcei" => null,
                                    "per-situacionvisa" => null,
                                    "per-identidadgenero" => null,
                                    "per-md5" => null,
                                    "per-nroidentificadornac" => null,
                                    "per-estadocivil" => null,
                                    "per-fcorrec-man" => null,
                                    "per-desc-correc" => null,
                                    "per-fecha-consulta" => null,
                                    "ingreso-manual" => null,
                                    "per-run-padre" => null,
                                    "per-run-madre" => null,
                                    "per-fecha-actualizacion-padres" => null];

                                $personaData["per-run"] = $personaWs->RUN;
                                $personaData["per-dv"] = $personaWs->DV;
                                $personaData["per-nacionalidad"] = Constante::NACIONALIDAD_1_CHILENA; // lo agrega como chileno (el ws de rc no trae nacionalidad)
                                $personaData["per-nombres"] = $personaWs->Nombres;
                                $personaData["per-apellidopaterno"] = $personaWs->ApellidoPaterno;
                                $personaData["per-apellidomaterno"] = $personaWs->ApellidoMaterno;
                                $personaData["per-fechanacimiento"] = date('Ymd', strtotime($personaWs->FechaNacimiento));
                                if ($personaWs->FechaDefuncion == 0) {
                                    $fechaNacim = $personaWs->FechaDefuncion;
                                } else {
                                    $fechaNacim = $personaData["per-fechanacimiento"];
                                }

                                $personaData["per-estadocivil"] = $personaWs->EstadoCivil;
                                if ($personaWs->Sexo == 'MASCULINO') {
                                    $personaData["per-sexo"] = Constante::SEXO_1_MASCULINO;
                                } else {
                                    $personaData["per-sexo"] = Constante::SEXO_2_FEMENINO;
                                }


                                if ($personaWs->FechaDefuncion != 0) {
                                    $personaData["per-fechadefuncion"] = date('Ymd', strtotime($personaWs->FechaDefuncion));
                                } else {
                                    $fechaNacim = $personaWs->FechaNacimiento;
                                }
                                $personaData["per-vigente"] = Constante::VIGENTE_1_VIGENTE;
                                $personaData["per-calculoactivo"] = Constante::CALCULO_ACTIVO_1_APROBADO;

                                if (!(is_null($fechaNacim))) {
                                    $personaData["per-edad"] = Utiles::getEdad($fechaNacim);
                                }
                                $ver_temp_parentesco = env('TEMPORAL_PARENTESCO', false);
                                if ($ver_temp_parentesco == true) {
                                    if (is_null($personaData["per-run-padre"]) || is_null($personaData["per-run-madre"])) {
                                        $padres = Utiles::consultarAscendencia($personaWs->RUN, $personaWs->DV);
                                        $personaData["per-run-padre"] = !is_null($padres->padre->rut) ? $padres->padre->rut : $personaData["per-run-padre"];
                                        $personaData["per-run-madre"] = !is_null($padres->madre->rut) ? $padres->madre->rut : $personaData["per-run-madre"];
                                        $personaData["per-fecha-actualizacion-padres"] = !is_null($padres->fechaConsulta) ? $padres->fechaConsulta : $personaData["per-fecha-actualizacion-padres"];
                                    }
                                }
                                $apiresponse = Http::APISYSTEM('TX_ACCESOS')->post('/persona-asignacion-crear', $personaData);
                                $apiresponse = MDSFApiResponse::cast($apiresponse);

                                $apiresponse = json_decode(json_encode($apiresponse->data), true);
                                if (!isset($apiresponse["persona-asignacion"])) {
                                    return Constante::ERROR_CATEGORIA_EJECUCION;
                                }

                                $nacionalidad = ($apiresponse["persona-asignacion"]['per-nacionalidad'] == 1) ? "CHILENA" : "EXTRANJERO";
                                $apiresponse["persona-asignacion"]['nacionalida'] = $nacionalidad;

                                $sexo = [null, 'Hombre', 'Mujer'][intval($apiresponse["persona-asignacion"]['per-sexo'])];
                                $apiresponse["persona-asignacion"]['sexo'] = $sexo;

                                $situacion = ($apiresponse["persona-asignacion"]['per-fechadefuncion'] != 0) ? false : true;
                                $apiresponse["persona-asignacion"]['situacion'] = $situacion;

                                $persona = $apiresponse["persona-asignacion"];

                                return $persona;

                            } else {
                                return Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR;
                            }
                        }
                    } catch (Exception $e) {
                        Log::error('PERSONA: ' . $e->getMessage());
                        return Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR;
                    }
                }
                return Constante::DATOS_DE_CONTACTO_2_ERROR_INESPERADO;
            }
            ,
            $cacheActivar
        );

    }

    public static function consultarAscendencia($run, $dv)
    {
        $runCompleto = $run . '-' . $dv;
        $salida = (object)[
            'padre' => (object)['rut' => null, 'dv' => null],
            'madre' => (object)['rut' => null, 'dv' => null],
            'fechaConsulta' => null
        ];

        if (env('CERTIFICADO_NACIMIENTO_WS_URL',false)) {
            try {
                $data = WSCertificadoNacimiento::get($runCompleto, $runCompleto, 1);
            } catch (Exception $ex) {
                return $salida;
            }
            if ($data->estado == Constante::STATUS_200_SUCCESS_OK) {
                $salida->padre->rut = $data->rutpadre;
                $salida->padre->dv = $data->dvpadre;
                $salida->madre->rut = $data->rutmadre;
                $salida->madre->dv = $data->dvmadre;
            } else {
                $salida->padre->rut = Constante::WS_CERTIFICADO_NACIMIENTO_ERROR_FUENTE;
                $salida->padre->dv = Constante::WS_CERTIFICADO_NACIMIENTO_ERROR_FUENTE;
                $salida->madre->rut = Constante::WS_CERTIFICADO_NACIMIENTO_ERROR_FUENTE;
                $salida->madre->dv = Constante::WS_CERTIFICADO_NACIMIENTO_ERROR_FUENTE;
            }
            $salida->fechaConsulta = date('Y-m-d H:i:s');
        }
        return $salida;
    }

    public static function getEdad($fechaNacimiento)
    {
        if ($fechaNacimiento == 0) {
            $fechaNacimiento = Constante::DEFAULT_FECHA_NACIMIENTO;
        }
        $interval = date_diff(date_create(), date_create($fechaNacimiento));
        return (int)$interval->format('%y');
    }

    /**
     * Pone puntos al run. Si no viene $dv pinta .XXX-X al final como run enmascarado
     */
    public static function ponePuntosAlRun($runSinPuntosSinDv, $dv = '')
    {
        $out = null;
        try {
            $runConPuntos = number_format($runSinPuntosSinDv, 0, ',', '.');
            if ($dv == '') {
                $out = substr($runConPuntos, 0, -4) . '.XXX-X';
            } else {
                $out = $runConPuntos . '-' . $dv;
            }
        } catch (Exception $ex) {
        }
        return $out;
    }

    public static function obtenerCalleExport($codigoCalle,$nombreCalleGrupoFamiliar, $comunaIne, $idLocalidad = null)
    {
        $calle = "Calle no encontrada";
        if ($codigoCalle >= Constante::ID_CALLE_11011_CORTE_GRUPO_FAMILIAR) {
            $apiresponse = Http::APISYSTEM('TX_ACCESOS')->get("/calle/".$codigoCalle."/".$comunaIne."/".$idLocalidad);
            $apiresponse = MDSFApiResponse::cast($apiresponse);
            $calleResponse = json_decode(json_encode($apiresponse->data), true);
            $calle_aux = isset($calleResponse["calle"])?$calleResponse["calle"]["nombre-calle"]:null;

            if ((!empty($calle_aux)) < 1) {
                $calle = $nombreCalleGrupoFamiliar;
            }
        } elseif ($nombreCalleGrupoFamiliar) {
            $calle = $nombreCalleGrupoFamiliar;
        } else {
            $calle = "Calle no encontrada";
        }
        return $calle;
    }

    public function booleano($campo)
    {
        if (is_null($campo)) {
            $out = null;
        } elseif (is_bool($campo)) {
            $out = $campo;
        } elseif (is_numeric($campo)) {
            $out = $campo > 0;
        } elseif (in_array(strtolower($campo), ['si', 'true'])) {
            $out = true;
        } elseif (in_array(strtolower($campo), ['no', 'false'])) {
            $out = true;
        } else {
            $out = $campo;
        }
        return $out;
    }


    /**
     * function inArrayMinuscula($variable, $array1, ... , $arrayN)
     * $arrayMerge = $array1, ... , $arrayN
     *
     * Verifica si $variable está dentro de $arrayMerge sin importar minúsculas, mayúsculas, tildes o eñes
     */
    public static function inArrayMinuscula()
    {
        $argList = func_get_args();
        $variableAComparar = $argList[0];

        $arrayMerge = [];
        for ($i = 1; $i < func_num_args(); $i++) {
            $arrayMerge = array_merge($arrayMerge, $argList[$i]);
        }
        return in_array(mb_strtolower(self::cambiaTildeASuLetra($variableAComparar)), $arrayMerge);
    }

    public static function cambiaTildeASuLetra($string)
    {
        //$string = trim($string);
        $string = str_replace(
            ['á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'],
            ['a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'],
            $string
        );
        $string = str_replace(
            ['é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'],
            ['e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'],
            $string
        );
        $string = str_replace(
            ['í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'],
            ['i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'],
            $string
        );
        $string = str_replace(
            ['ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'],
            ['o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'],
            $string
        );
        $string = str_replace(
            ['ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'],
            ['u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'],
            $string
        );
        $string = str_replace(
            ['ñ', 'Ñ', 'ç', 'Ç'],
            ['n', 'N', 'c', 'C',],
            $string
        );
        return $string;
    }

    public static function getUsuarioAsignacion($runUsuario, $campoAdicional)
    {
        $apiresponse = Http::APISYSTEM('TX_ACCESOS')->post('/usuario-asignacion', [
            'runUsuario' => $runUsuario,
            'campo-adicional' => $campoAdicional
        ]);
        $apiresponse = MDSFApiResponse::cast($apiresponse);

        if ($apiresponse->code == 1000) {
            return json_decode(json_encode($apiresponse->data), true);
        }

        return null;
    }
}
