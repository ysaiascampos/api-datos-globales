<?php

namespace App\classes;

//use App\Clases\DatosDeSolicitud;
use App\Constantes\Constante;
//use App\Exceptions\ExcepcionConsistencia;
use App\Models\BeneficiarioGrupoFamiliar;
use App\Models\Calle;
use App\Models\ComplementoIngresos;
use App\Models\GrupoFamiliar;
use App\Models\Personas;
//use App\Models\PersonasExport;
use App\Models\VWPropiedad;
use App\Models\Solicitud;
use App\Models\SubTipoSolicitud;
use App\Models\TipoSolicitud;
use App\Models\Usuario;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Utiles
{
    const EXTERNO = 'EXTERNO';
    const INTERNO = 'INTERNO';

    public static function getFormateaFecha($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }

    public static function getFormateaFechaHora($date)
    {
        return Carbon::parse($date)->format('d-m-Y H:i');
    }

    public static function getEdad($fechaNacimiento)
    {
        if ($fechaNacimiento == 0) {
            $fechaNacimiento = Constante::DEFAULT_FECHA_NACIMIENTO;
        }
        $interval = date_diff(date_create(), date_create($fechaNacimiento));
        return (int)$interval->format('%y');
    }

    public static function getEdad_old($fecha)
    {
        $fecha = date('d-m-Y', strtotime($fecha));
        $dias = explode('-', $fecha, 3);
        $dias = mktime(0, 0, 0, $dias[1], $dias[0], $dias[2]);
        return (int)((time() - $dias) / 31556926);
    }

    /**
     * array con edad completa [ [0] => año, [1] => mes, [2] => dia ]
     */
    public static function getEdadCompleta($fecha)
    {
        $interval = date_diff(date_create(), date_create($fecha));
        // $edadYMD = $interval->format('%Y-%M-%d-%H-%i-%s'); // edad años-meses-dias-horas-minutos-segundos
        $edadYMD = $interval->format('%Y-%M-%d'); // edad años-meses-dias
        return explode('-', $edadYMD);
    }

    public static function getNombreCalle($comuna, $localidad, $calle)
    {
        if ($comuna > 0 && $localidad > 0 && $calle > 0) {
            $n_calle = Calle::where(
                [
                    'comuna_ine' => $comuna,
                    'id_localidad' => $localidad,
                    'id_calle' => $calle
                ])
                ->first();
            return $n_calle->nombre;
        } else {
            return 'SIN CALLE';
        }
    }


    /**
     * Diferencia en días entre dos fechas formato DATE (2017-11-24 13:00:04)
     * si fecha2 no viene, se calcula con la fecha actual
     */
    public static function diferenciaEntreFechasEnDias($fecha1, $fecha2 = 'actual')
    {
        if ($fecha2 == 'actual') {
            $fecha2 = date('Y-m-j'); // ej: 2018-09-10
        }
        $fecha1EnSeg = strtotime($fecha1); // Segundos desde el año 1969
        $fecha2EnSeg = strtotime($fecha2); // Segundos desde el año 1969
        $difEntreFechasEnSeg = abs($fecha2EnSeg - $fecha1EnSeg);
        $difEntreFechasEnDias = ((($difEntreFechasEnSeg / 60) / 60) / 24); // convierte los segundos a días
        return floor($difEntreFechasEnDias); // quita la parte decimal
    }

    /**
     * Suma dias a una fecha
     * si fecha2 no viene, se calcula con la fecha actual
     */
    public static function sumarDiasAFecha($sumaDeDias, $fecha = 'actual')
    {
        if ($fecha == 'actual') {
            $fecha = date('Y-m-j'); // ej: 2018-09-10
        }
        $nuevafecha = strtotime('+' . $sumaDeDias . ' day', strtotime($fecha));
        return date('d-m-Y', $nuevafecha);
    }

    public static function verificaSolicitud($tipo, $runCiudadano)
    {
        $solicitud = Solicitud::where('id_tipo_solicitud', $tipo)
            ->where('run_ciudadano', $runCiudadano)
            ->whereNotIn('id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS)
            ->where('vigente', 1)
            ->first();
        if (!empty($solicitud)) {
            Log::error('Duplicidad. Ya existe en la BD una solicitud de iguales caracteristicas: ' . $solicitud->id_solicitud);
            return true;
        }
        return false;
    }

    /**
     * Retorna el nombre de un mes xx
     * 1 = Enero, etc
     */
    public static function obtenerMes($n)
    {
        $n = intval($n);
        if (array_key_exists($n, Constante::$MESES)) {
            return Constante::$MESES[$n];
        } else {
            return 'Error: No se pudo obtener el Mes';
        }
    }

    public static function getCleanField($str)
    {
        $str = str_replace("'", '´', $str);
        return $str;
    }

    // 05/08/2015 -> 20150805
    // Esto debería ser DEPRECATED por el metodo de carbot
    public static function fechaDateToNumber($fecha)
    {
        $fecha_aux = explode('/', $fecha);
        $fechaAnio = (int)$fecha_aux[2] * 10000;
        $fechaMes = (int)$fecha_aux[1] * 100;
        $fechaDia = (int)$fecha_aux[0];
        return (int)$fechaAnio + (int)$fechaMes + ($fechaDia);
    }

    public static function formatFecha($fecha, $serador = '/')
    {
        if ($fecha != -1) {
            $anio = floor($fecha / 10000);
            $diezmil = 10000;
            //$cien = 100;
            $mes = floor(($fecha % $diezmil) / 100);
            $dia = ($fecha % $diezmil) % 100;
            return str_pad($dia, 2, '0', STR_PAD_LEFT) . $serador .
                str_pad($mes, 2, '0', STR_PAD_LEFT) . $serador .
                str_pad($anio, 4, '0', STR_PAD_LEFT);
        } else {
            return '';
        }
    }

    /**
     * Retorna el YYYY-mm-dd espera YYYYmmdd
     */
    public static function formatFechaSeparado($fecha, $serador = '-')
    {
        if ($fecha != -1) {
            return str_pad(substr($fecha, 0, 4), 4, '0', STR_PAD_LEFT) . $serador .
                str_pad(substr($fecha, 4, 2), 2, '0', STR_PAD_LEFT) . $serador .
                str_pad(substr($fecha, 6, 2), 2, '0', STR_PAD_LEFT);
        } else {
            return '';
        }
    }

    public static function formatFechaAnioMes($fecha, $serador = '-')
    {
        if ($fecha != -1) {
            $anio = floor($fecha / 100);
            $diezmil = 100;
            //$cien = 100;
            $mes = floor(($fecha % $diezmil) / 1);
            return str_pad($anio, 4, '0', STR_PAD_LEFT) . $serador .
                str_pad($mes, 2, '0', STR_PAD_LEFT);
        } else {
            return '';
        }
    }

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

    public static function checkPasswordNumerico($pwd)
    {
        if (!preg_match('#[0-9]+#', $pwd)) {
            return false;
        }
        return true;
    }

    public static function checkPasswordLetra($pwd)
    {
        if (!preg_match('#[a-zA-Z]+#', $pwd)) {
            return false;
        }
        //return ($errors == $errors_init);
        return true;
    }

    /**
     * se agrega funcion que valida si la solicituda la resuelve el nivel central
     */
    function validaResuelveNivelCentral($id_tipo_solicitud)
    {
        //Los tipos de solicitudes que resuelve el Nivel Central son (70,71,72,73,74,76)
        if (in_array($id_tipo_solicitud, [
            Constante::TIPO_SOLICITUD_70_RECTIFICACION_INGRESO_TRABAJADOR_DEPENDIENTE,
            Constante::TIPO_SOLICITUD_71_RECTIFICACION_INGRESO_TRABAJADOR_INDEPENDIENTE,
            Constante::TIPO_SOLICITUD_72_RECTIFICACION_DE_INGRESOS_DE_CAPITAL,
            Constante::TIPO_SOLICITUD_73_RECTIFICACION_DE_COTIZACIONES_DE_SALUD,
            Constante::TIPO_SOLICITUD_74_RECTIFICACION_DE_MATRICULA_EN_ESTABLECIMIENTO_EDUCACIONAL,
            Constante::TIPO_SOLICITUD_76_RECTIFICACION_DE_PROPIEDAD_DE_VEHICULO
        ])) {
            return true;
        } else {
            return false;
        }
    }

    public static function generaContrasena($rut)
    {
        $datos = Utiles::getPersona($rut);
        $apellido_paterno = self::cambiaTildeASuLetra(trim($datos->per_apellidopaterno));
        if (strlen($apellido_paterno) >= 4) {
            $passwd = ucfirst(strtolower(trim(substr($apellido_paterno, 0, 4))));
        } else {
            $passwd = ucfirst(strtolower(trim($apellido_paterno)));
        }
        $largo = strlen($passwd);
        $aleatorio = rand(1000, 9999);
        $aleatorio2 = rand(0, 99);
        $passwd = (substr($passwd, 0, 1) . $aleatorio2 . substr($passwd, 1, $largo) . '.' . $aleatorio);
        return $passwd;
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

    /**
     * Pone puntos a un número
     */
    public static function ponePuntosNumero($numero)
    {
        try {
            return number_format($numero, 0, ',', '.');
        } catch (Exception $ex) {
        }
        return null;
    }

    /**
     * FUNCION DEPRECATED USAR textoEstadoSolicitudCiudadano()
     * 1  Pendiente Entregar Documentos
     * 2  Agendado
     * 3  En Gestión Municipal
     * 4  Pendiente por Resolver Ejecutor - Ex Encargado Comunal
     * 5  Aprobado
     * 6  En Espera Ciudadano
     * 7  Rechazada
     * 8  Por Aprobar SIT
     * 9  Desistida
     * 10 En Nivel Central
     * 11 En Digitacion
     * 12 Por aprobar UGIT
     * 14 Inactivo por Defuncion
     * 15 En Ruta
     * 16 Supervisión
     */
    public static function textoEstadoSolicitudHomeCiudadano($idEstado)
    {
        $pasoActual = '';
        if (in_array($idEstado, Constante::$ESTADO_SOLICITUD_NO_FINALIZADOS)) {
            $pasoActual .= '1/3';
        }
        if (in_array($idEstado, Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS)) {
            $pasoActual .= '3/3';
        }
        if (in_array($idEstado, [])) {
            $pasoActual .= '2/3';
        }

        $icono = '';
        if (in_array($idEstado, Constante::$ESTADO_SOLICITUD_NO_FINALIZADOS)) { // en tramite
            $icono .= '<img src="' . asset('assets2.0/img/icons/ico-pause.svg') . '" class="px-2">';
        }
        if (in_array($idEstado, [
            Constante::ESTADO_SOLICITUD_5_APROBADO
        ])) {
            $icono .= '<img src="' . asset('assets2.0/img/icons/ico-aprobada.svg') . '" class="px-2">';
        }
        if (in_array($idEstado, Constante::$ESTADO_SOLICITUD_7_9_RECHAZADOS_DESISTIDOS)) {
            $icono .= '<img src="' . asset('assets2.0/img/icons/ico-anulada.svg') . '" class="px-2">';
        }
        if (in_array($idEstado, [])) { // en trámite
            $icono .= '<img src="' . asset('assets2.0/img/icons/ico-agenda.svg') . '" class="px-2">';
        }

        $textoEstado = '';
        if (in_array($idEstado, Constante::$ESTADO_SOLICITUD_NO_FINALIZADOS)) { // en tramite
            $textoEstado .= 'En tramitación';
        }
        if (in_array($idEstado, [5])) { // aprobada
            $textoEstado .= 'Aprobada';
        }
        if (in_array($idEstado, [7, 9])) { // anulada
            $textoEstado .= 'Anulada';
        }
        if (in_array($idEstado, [])) { // en trámite
            $textoEstado .= 'En trámite';
        }
        return '<span class="td-texto rsh_content-flex">' . $pasoActual . ' ' . $icono . ' <span>' .
            $textoEstado . '</span></span>';
    }

    public static function iconoPorEstadoCiudadanoSolicitud($nombreEstadoCiudadano)
    {
        switch (mb_convert_case($nombreEstadoCiudadano, MB_CASE_LOWER)) {
            case 'en trámite':
                return 'ico-agenda.svg';
            case 'aprobado':
            case 'confirmado':
                return 'ico-aprobada.svg';
            case 'desistida':
            case 'rechazada':
                return 'ico-anulada.svg';
            case 'borrador':
                return 'ico-borrador.svg';
            default;
                return 'ico-blank.png';
        }
    }

    /* Capitaliza solo la primera palabra en una oración*/
    public static function capitalizaPrimeraPalabra($texto) {
        $textoCapitalizadoPrimeraPalabra = ucfirst(strtolower($texto));
        return $textoCapitalizadoPrimeraPalabra;
    }

    /**
     * Funcion traida desde Registro Calle
     * Separa texto por ' ' o '.' y Capitaliza los segmentos, no capitaliza si pertenece al array $noCapitalizar
     */
    public static function capitalizaTexto($texto, $noCapitalizarParam = [])
    {
        try {
            $noCapitalizarPredeterminados = [
                'el',
                'la',
                'los',
                'las',
                'un',
                'unos',
                'una',
                'unas',
                'a',
                'y',
                'de',
                'del',
                'con'
            ];
            $noCapitalizar = array_merge($noCapitalizarPredeterminados, $noCapitalizarParam);
            $contador = 0;
            $textoConcatenado = '';
            $textoPorEspacio = explode(' ', $texto);
            foreach ($textoPorEspacio as $valorEsp) {
                $textoPorPunto = explode('.', $valorEsp);
                foreach ($textoPorPunto as $valorPunto) {
                    $textoConcatenado .= (in_array(mb_strtolower($valorPunto), $noCapitalizar) && $contador != 0) ?
                        mb_strtolower($valorPunto) . '.' :
                        mb_convert_case($valorPunto, MB_CASE_TITLE, 'UTF-8') . '.';
                    $contador++;
                }
                $textoConcatenado = substr($textoConcatenado, 0, -1) . ' ';
            }
            return substr($textoConcatenado, 0, -1);
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * Quita el segundo nombre de per_nombres
     */
    public static function quitaPalabrasLuegoDelEspacio($texto)
    {
        return explode(' ', $texto)[0];
    }

    /**
     * Convierte 201801 a Enero 2018
     */
    static function conviertePeriodoATexto($periodoYYYYMM)
    {
        return Utiles::obtenerMes(substr($periodoYYYYMM, 4)) . ' ' . substr($periodoYYYYMM, 0, -2);
    }

    /**
     * Convierte 2018-01 a Enero 2018
     */
    static function conviertePeriodoBTexto($periodoYYYYMM)
    {
        $periodoYYYYMM = explode('-', $periodoYYYYMM);
        return Utiles::obtenerMes($periodoYYYYMM[1]) . ' ' . $periodoYYYYMM[0];
    }

    /**
     * Convierte 20180101 a Enero 2018
     */
    static function conviertePeriodoCTexto($periodoYYYYMMDD)
    {
        return Utiles::obtenerMes(substr($periodoYYYYMMDD, 4, -2)) . ' ' . substr($periodoYYYYMMDD, 0, -4);
    }

    /**
     * Recursivo que devuelve array de periodos entre dos periodos incluyendo el inicio y el final
     * formatos entrada: YYYYMM
     */
    static function devuelvePeriodosEntre($periodoInicio, $periodoFinal, $arrayPeriodos = [])
    {
        $arrayPeriodos[$periodoInicio] = Utiles::conviertePeriodoATexto($periodoInicio);
        if ($periodoInicio == $periodoFinal) {
            return $arrayPeriodos;
        } else {
            return Utiles::devuelvePeriodosEntre(
                Utiles::sumarUnMesAlPeriodo($periodoInicio),
                $periodoFinal,
                $arrayPeriodos
            );
        }
    }

    /**
     * Resta un mes al periodo, de 201801 a 201712
     */
    static function restarUnMesAlPeriodo($periodoYYYYMM)
    {
        $mesIngreso = intval(substr($periodoYYYYMM, 4));
        $anioIngreso = intval(substr($periodoYYYYMM, 0, -2));
        if ($mesIngreso == 1) {
            $mesSalida = 12;
            $anioSalida = $anioIngreso - 1;
        } else {
            $mesSalida = $mesIngreso - 1;
            $anioSalida = $anioIngreso;
        }
        if ($mesSalida < 10) {
            $mesSalida = '0' . $mesSalida;
        }
        return $anioSalida . $mesSalida;
    }

    /**
     * Suma un mes al periodo, de 201712 a 201801
     */
    static function sumarUnMesAlPeriodo($periodoYYYYMM)
    {
        $mesIngreso = intval(substr($periodoYYYYMM, 4));
        $anioIngreso = intval(substr($periodoYYYYMM, 0, -2));
        if ($mesIngreso == 12) {
            $mesSalida = 1;
            $anioSalida = $anioIngreso + 1;
        } else {
            $mesSalida = $mesIngreso + 1;
            $anioSalida = $anioIngreso;
        }
        if ($mesSalida < 10) {
            $mesSalida = '0' . $mesSalida;
        }
        return $anioSalida . $mesSalida;
    }

    /**
     * Limpia de caracteres espaciales un string para poder ocuparlo como nombre de variable
     */
    public static function cleanNombreVariables($stringConCaracteresEspeciales)
    {
        $stringConCaracteresEspecialesConSeparador = str_replace(
            [' ', '/', '?', '-'],
            '_',
            $stringConCaracteresEspeciales
        );
        return preg_replace('/[^A-Za-z0-9\_]/', '', $stringConCaracteresEspecialesConSeparador);
    }



    public static function getRealIP()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return 'Desconocido';
        }
    }

    /*
     * Trae el texto para los tramos de ingreso del trabajo, pensiones, capital y total
     */
    public static function traeTramoIngreso($tramoMin, $tramoMax)
    {
        if ($tramoMin == 0) {
            return 'menor que $' . number_format($tramoMax, 0, ',', '.');
        } else {
            return 'entre $' . number_format($tramoMin, 0, ',', '.') . ' y $' .
                number_format($tramoMax, 0, ',', '.');
        }
    }

    public static function traeTramoIngresoAsignacion($tramo)
    {
        if ($tramo <= 50000) {
            return 'menor que $50.000';
        } else {
            return 'entre $' . number_format($tramo - 50000, 0, ',', '.') .
                ' y $' . number_format($tramo, 0, ',', '.');
        }
    }

    /**
     * Esta función debe reemplazarse por lo que trae el servicio api/datos-globales
     */
    public static function mensajeTramoQuintilCartola($tamoQuintil)
    {
        $porcentajeTramoText = '';
        if ($tamoQuintil == 40) {
            $porcentajeTramoText = '0% y el 40% de menores ingresos o mayor ';
        } elseif ($tamoQuintil == 50) {
            $porcentajeTramoText = '41% y el 50% de menores ingresos o mayor ';
        } elseif ($tamoQuintil == 60) {
            $porcentajeTramoText = '51% y el 60% de menores ingresos o mayor ';
        } elseif ($tamoQuintil == 70) {
            $porcentajeTramoText = '61% y el 70% de menores ingresos o mayor ';
        } elseif ($tamoQuintil == 80) {
            $porcentajeTramoText = '71% y el 80% de mayores ingresos o menor';
        } elseif ($tamoQuintil == 90) {
            $porcentajeTramoText = '81% y el 90% de mayores ingresos o menor';
        } elseif ($tamoQuintil == 100) {
            $porcentajeTramoText = '91% y el 100% de mayores ingresos o menor';
        }
        return $porcentajeTramoText;
    }

    /** Uso
     *    Utiles::tInicio('t1');
     *    function_lenta();
     *    Utiles::tFinal('t1');
     *
     * retorna: tiempo en lavavel.log
     */
    var $tiemposInicio = [];

    public static function tInicio($etiqueta)
    {
        if (env('ambienteDebug',false)) {
            list($usec, $sec) = explode(' ', microtime());
            global $tiemposInicio;
            $tiemposInicio[$etiqueta] = ((float)$usec + (float)$sec);
        }
    }

    public static function tFinal($etiqueta)
    {
        if (env('ambienteDebug',false)) {
            list($usec, $sec) = explode(' ', microtime());
            global $tiemposInicio;
            $time = ((float)$usec + (float)$sec) - $tiemposInicio[$etiqueta];
            Log::info("Tiempo diferencia ($etiqueta): $time segundos\n");
        }
    }

    /**
     * Fin Consulta Modulo
     */
    public static function limpiaErrorFuncionDB($resultado)
    {
        return ((stripos($resultado, 'Error:') === false) ? $resultado : null);
    }

    /**
     * Retorna concatenado el primer y último elemento de un arreglo
     */
    public static function InicioYFin($array, $conector = ' y ')
    {
        return reset($array) . $conector . end($array);
    }

    /**
     * Se quita provincia y comuna al ComunaINE para obtener el nro de la region
     */
    public static function getRegionDesdeCodigoComuna($codigoComuna)
    {
        $region = substr($codigoComuna, 0, -3); // quita los 3 últimos caracteres
        if (Utiles::condicionEntreDosNumeros($region, Constante::$REGIONES_1_A_16_POSIBLES_RESPUESTAS)) {
            return $region;
        } else {
            return null;
        }
    }

    /**
     * Condición si $variable está entre dos números (incluyéndolos)
     * $array siempre es de dos elementos
     */
    public static function condicionEntreDosNumeros($variable, $array)
    {
        return ($array[0] <= $variable && $variable <= $array[1]);
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



    public static function guardaFechaConsultaPersona($runCiudadano)
    {
        //guardar marca fecha de consulta en tabla persona
        $persona = Personas::where('per_run', $runCiudadano)
            ->first();
        if (!empty($persona) > 0) {
            try {
                DB::connection()->getPdo()->beginTransaction();
                $persona->per_fecha_consulta = date('Y-m-d H:i:s');
                $persona->save();
                DB::connection()->getPdo()->commit();
            } catch (Exception $ex) {
                Log::info("Error en guardado Fecha Consulta " . $ex->getMessage());
                DB::connection()->getPdo()->rollback();
            }
        }
    }

    public static function getPuedeSerIncorporado($persona)
    {
        $beneficiarios = null;
        $puedeserincorporado = Personas::where('per_run', $persona->per_run)
            ->select(['bnfgrupfami_grupofamiliar', 'bnfgrupfami_institucionalizado'])
            ->join('beneficiarios_gruposfamiliares', 'per_id', '=', 'bnfgrupfami_persona')
            ->join('ab_solicitud', 'beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'ab_solicitud.id_solicitud')
            ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->whereIn('id_tipo_solicitud', [
                Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
                Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO,
                Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
                Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
                Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA
            ])
            ->whereNotIn('id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS)
            ->first();
        $solicitudtramitable = true;

        if (!empty($puedeserincorporado)) {
            $solicitudtramitable = false;
            $beneficiarios = $puedeserincorporado;
        } else {
            if ((env('SOLICITUD_MENORES_ULTIMOS_MESES')) && $persona->per_edad < Constante::EDAD_18_MAYOR_DE_EDAD) {
                $queryIncorporado = Personas::where('per_run', $persona->per_run)
                    ->select(['bnfgrupfami_grupofamiliar', 'bnfgrupfami_institucionalizado'])
                    ->join('beneficiarios_gruposfamiliares', 'per_id', '=', 'bnfgrupfami_persona')
                    ->join('ab_solicitud', 'beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'ab_solicitud.id_solicitud')
                    ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
                    ->where('vigente', Constante::VIGENTE_1_VIGENTE)
                    ->whereIn('id_tipo_solicitud', [
                        Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                        Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA,
                        Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE
                    ])
                    ->where('ab_solicitud.fecha_creacion', '>=', DB::RAW('ADD_MONTHS(sysdate,-6)'))
                    ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                    ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                    ->where('id_estado', Constante::ESTADO_SOLICITUD_5_APROBADO);
                if (is_null($persona->getAttribute('bnfgrupfami_institucionalizado'))) {
                    $puedeserincorporado = $queryIncorporado->where('bnfgrupfami_institucionalizado', Constante::INSTITUCIONALIZADO_0_NO_INSTITUCIONALIZADO)
                        ->first();
                } elseif ($persona->getAttribute('bnfgrupfami_institucionalizado') == 0) {
                    $puedeserincorporado = $queryIncorporado->first();
                }
            }
            if (!empty($puedeserincorporado)) {
                $solicitudtramitable = false;
                $beneficiarios = $puedeserincorporado;
            } else {
                if($persona->per_edad < Constante::EDAD_18_MAYOR_DE_EDAD){
                    $buscaIngresoRegistro = Solicitud::where('run_ciudadano', $persona->per_run)
                    ->where('id_estado', Constante::ESTADO_SOLICITUD_5_APROBADO)
                    ->whereIn('id_tipo_solicitud', [
                        Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                        Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA,
                    ])
                    ->where('vigente', Constante::VIGENTE_1_VIGENTE)
                    ->first();
                    $diferenciaDias = 31;
                    if (!empty($buscaIngresoRegistro)) {
                        $fecha_actual = strtotime(substr($buscaIngresoRegistro->fecha_modificacion, 0, -9));
                        $fecha_aprobacion = strtotime(date("Y") . "/" . date("m") . "/" . date("d"));
                        $diferenciaDias = round(($fecha_aprobacion - $fecha_actual) / 86400);
                    }
                    if ((!empty($buscaIngresoRegistro)) && ($diferenciaDias <= 30)) {
                        $solicitudtramitable = false;
                        $beneficiarios = $puedeserincorporado;
                    }
                }

                $puedeserincorporado = Solicitud::where('run_ciudadano', $persona->per_run)
                    ->select(['beneficiarios_gruposfamiliares.bnfgrupfami_grupofamiliar',
                        'beneficiarios_gruposfamiliares.bnfgrupfami_institucionalizado'])
                    ->join('beneficiarios_gruposfamiliares',
                        'beneficiarios_gruposfamiliares.bnfgrupfami_idsolicitud', '=', 'ab_solicitud.id_solicitud')
                    ->where('vigente', Constante::VIGENTE_1_VIGENTE)
                    ->whereIn('id_tipo_solicitud', [
                        Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                        Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
                        Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                        Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                        Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO,
                        Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                        Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
                        Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
                        Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA
                    ])
                    ->whereNotIn('id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS)
                    ->first();
                if (!empty($puedeserincorporado)) {
                    $solicitudtramitable = false;
                    $beneficiarios = $puedeserincorporado;
                }
            }
        }
        if ($beneficiarios == null) {
            $beneficiarios = BeneficiarioGrupoFamiliar::select(['bnfgrupfami_grupofamiliar',
                'bnfgrupfami_institucionalizado'])
                ->where('bnfgrupfami_persona', $persona->per_id)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
        }

        $grupoFamiliarId = null;
        if ($beneficiarios) {
            $grupoFamiliar = GrupoFamiliar::select('grupfami_nuevofolio')
                ->where('grupfami_id', $beneficiarios->bnfgrupfami_grupofamiliar)
                ->where('grupfami_estado', Constante::ESTADO_GRUPO_FAMILIAR_7_APROBADO)
                ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->first();
            if (!empty($grupoFamiliar)) {
                if ($beneficiarios->bnfgrupfami_institucionalizado != Constante::INSTITUCIONALIZADO_1_INSTITUCIONALIZADO) {
                    $grupoFamiliarId = $grupoFamiliar->grupfami_nuevofolio;
                    if ($solicitudtramitable == true) {
                        $puedeserincorporado = Solicitud::select(1)
                            ->join('grupos_familiares',
                                'grupos_familiares.grupfami_idsolicitud', '=', 'ab_solicitud.id_solicitud')
                            ->where('grupfami_nuevofolio', $grupoFamiliarId)
                            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
                            ->whereIn('id_tipo_solicitud', [
                                Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO,
                                Constante::TIPO_SOLICITUD_2_CAMBIO_DE_DOMICILIO,
                                Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE,
                                Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA,
                                Constante::TIPO_SOLICITUD_19_CAMBIO_EN_RELACION_DE_PARENTESCO,
                                Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR,
                                Constante::TIPO_SOLICITUD_101_DESVINCULACION_DEL_JEFE_DE_HOGAR,
                                Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA,
                                Constante::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA
                            ])
                            ->whereNotIn('id_estado', Constante::$ESTADO_SOLICITUD_5_7_9_FINALIZADOS)
                            ->first();
                        if (!empty($puedeserincorporado)) {
                            $solicitudtramitable = false;
                        }

                    }
                }
            }
        }
        return [
            'puedeserincorporado' => $solicitudtramitable,
            'grupfami_nuevofolio-asignacion' => $grupoFamiliarId
        ];
    }


    public static function getPensionPorRUN($runCiudadano)
    {
        $regPagadorEloq = ComplementoIngresos::join('ab_solicitud', 'ab_complemento_ingresos.id_solicitud', '=',
            'ab_solicitud.id_solicitud')
            ->where('ab_complemento_ingresos.rut_pagador', $runCiudadano)
            ->where('ab_complemento_ingresos.vigente', Constante::VIGENTE_1_VIGENTE)
            ->orderBy('ab_solicitud.fecha_modificacion', 'DESC')
            ->orderBy('ab_complemento_ingresos.rut_causante', 'ASC')
            ->orderBy('ab_complemento_ingresos.rut_receptor', 'ASC')
            ->get();
        $regReceptorEloq = ComplementoIngresos::join('ab_solicitud', 'ab_complemento_ingresos.id_solicitud', '=',
            'ab_solicitud.id_solicitud')
            ->where('ab_complemento_ingresos.rut_receptor', $runCiudadano)
            ->where('ab_complemento_ingresos.vigente', Constante::VIGENTE_1_VIGENTE)
            ->orderBy('ab_complemento_ingresos.complemento_id', 'DESC')
            ->orderBy('ab_complemento_ingresos.rut_pagador', 'ASC')
            ->orderBy('ab_complemento_ingresos.rut_causante', 'ASC')
            ->get();
        $regBeneficiarioEloq = ComplementoIngresos::join('ab_solicitud', 'ab_complemento_ingresos.id_solicitud', '=',
            'ab_solicitud.id_solicitud')
            ->where('ab_complemento_ingresos.rut_causante', $runCiudadano)// rut_causante = BENEFICIARIO
            ->where('ab_complemento_ingresos.vigente', Constante::VIGENTE_1_VIGENTE)
            ->orderBy('ab_complemento_ingresos.complemento_id', 'DESC')
            ->orderBy('ab_complemento_ingresos.rut_pagador', 'ASC')
            ->orderBy('ab_complemento_ingresos.rut_causante', 'ASC')
            ->get();

        $regPagador = [];
        foreach ($regPagadorEloq as $element) {
            $causante = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_causante)
                ->first();
            $elementFiltroPagador['nombre-causante'] = isset($causante->per_nombres) ? Utiles::capitalizaTexto($causante->per_nombres . ' ' . $causante->per_apellidopaterno . ' ' . $causante->per_apellidomaterno) : null;
            $elementFiltroPagador['edad-causante'] = isset($causante->per_fechanacimiento) ? Utiles::getEdad($causante->per_fechanacimiento) : null;
            $receptor = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_receptor)
                ->first();
            $elementFiltroPagador['nombre-receptor'] = isset($receptor->per_nombres) ? Utiles::capitalizaTexto($receptor->per_nombres . ' ' . $receptor->per_apellidopaterno . ' ' . $receptor->per_apellidomaterno) : null;
            $elementFiltroPagador['edad-receptor'] = isset($receptor->per_fechanacimiento) ? Utiles::getEdad($receptor->per_fechanacimiento) : null;
            $pagador = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_pagador)
                ->first();
            $elementFiltroPagador['nombre-pagador'] = isset($pagador->per_nombres) ? Utiles::capitalizaTexto($pagador->per_nombres . ' ' . $pagador->per_apellidopaterno . ' ' . $pagador->per_apellidomaterno) : null;
            $elementFiltroPagador['edad-pagador'] = isset($pagador->per_fechanacimiento) ? Utiles::getEdad($pagador->per_fechanacimiento) : null;
            $elementFiltroPagador['id'] = $element->complemento_id;
            $elementFiltroPagador['tipo'] = Utiles::getNombreTipoYSubtipoSolicitud($element->id_tipo_solicitud,
                $element->c_tcomplemento_ingreso);
            $elementFiltroPagador['monto'] = $element->monto;
            $elementFiltroPagador['rut-causante'] = $element->rut_causante;
            $elementFiltroPagador['rut-receptor'] = $element->rut_receptor;
//            $elementFiltroPagador['rut-pagador'] = $element->rut_pagador;
            $elementFiltroPagador['periodo-read'] = $element->periodo_read;
            $elementFiltroPagador['fecha-inicio-pago'] = $element->fecha_inicio_pago;
            $elementFiltroPagador['nro-cuotas'] = $element->numero_cuotas;
            $elementFiltroPagador['fecha-vigente'] = $element->fecha_modificacion;
            if (isset($regPagador[$element->rut_causante])) {
                $regPagador[$element->rut_causante]['historico'][] = $elementFiltroPagador;
            } else {
                $regPagador[$element->rut_causante] = $elementFiltroPagador;
            }
        }

        $regReceptor = [];
        foreach ($regReceptorEloq as $element) {
            $causante = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_causante)
                ->first();
            $elementFiltroReceptor['nombre-causante'] = isset($causante->per_nombres) ? Utiles::capitalizaTexto($causante->per_nombres . ' ' . $causante->per_apellidopaterno . ' ' . $causante->per_apellidomaterno) : null;
            $elementFiltroReceptor['edad-causante'] = isset($causante->per_fechanacimiento) ? Utiles::getEdad($causante->per_fechanacimiento) : null;
            $receptor = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_receptor)
                ->first();
            $elementFiltroReceptor['nombre-receptor'] = isset($receptor->per_nombres) ? Utiles::capitalizaTexto($receptor->per_nombres . ' ' . $receptor->per_apellidopaterno . ' ' . $receptor->per_apellidomaterno) : null;
            $elementFiltroReceptor['edad-receptor'] = isset($receptor->per_fechanacimiento) ? Utiles::getEdad($receptor->per_fechanacimiento) : null;
            $pagador = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_pagador)
                ->first();
            $elementFiltroReceptor['nombre-pagador'] = isset($pagador->per_nombres) ? Utiles::capitalizaTexto($pagador->per_nombres . ' ' . $pagador->per_apellidopaterno . ' ' . $pagador->per_apellidomaterno) : null;
            $elementFiltroReceptor['edad-pagador'] = isset($pagador->per_fechanacimiento) ? Utiles::getEdad($pagador->per_fechanacimiento) : null;
            $elementFiltroReceptor['id'] = $element->complemento_id;
            $elementFiltroReceptor['tipo'] = Utiles::getNombreTipoYSubtipoSolicitud($element->id_tipo_solicitud,
                $element->c_tcomplemento_ingreso);
            $elementFiltroReceptor['monto'] = $element->monto;
            $elementFiltroReceptor['rut-causante'] = $element->rut_causante;
//            $elementFiltroReceptor['rut-receptor'] = $element->rut_receptor;
            $elementFiltroReceptor['rut-pagador'] = $element->rut_pagador;
            $elementFiltroReceptor['periodo-read'] = $element->periodo_read;
            $elementFiltroReceptor['fecha-inicio-pago'] = $element->fecha_inicio_pago;
            $elementFiltroReceptor['nro-cuotas'] = $element->numero_cuotas;
            $elementFiltroReceptor['fecha-vigente'] = $element->fecha_modificacion;
            if (isset($regReceptor[$element->rut_causante])) {
                $regReceptor[$element->rut_causante]['historico'][] = $elementFiltroReceptor;
            } else {
                $regReceptor[$element->rut_causante] = $elementFiltroReceptor;
            }
        }

        $regBeneficiario = [];
        foreach ($regBeneficiarioEloq as $element) {
            $causante = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_causante)
                ->first();
            $elementFiltroBeneficiario['nombre-causante'] = isset($causante->per_nombres) ? Utiles::capitalizaTexto($causante->per_nombres . ' ' . $causante->per_apellidopaterno . ' ' . $causante->per_apellidomaterno) : null;
            $elementFiltroBeneficiario['edad-causante'] = isset($causante->per_fechanacimiento) ? Utiles::getEdad($causante->per_fechanacimiento) : null;
            $receptor = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_receptor)
                ->first();
            $elementFiltroBeneficiario['nombre-receptor'] = isset($receptor->per_nombres) ? Utiles::capitalizaTexto($receptor->per_nombres . ' ' . $receptor->per_apellidopaterno . ' ' . $receptor->per_apellidomaterno) : null;
            $elementFiltroBeneficiario['edad-receptor'] = isset($receptor->per_fechanacimiento) ? Utiles::getEdad($receptor->per_fechanacimiento) : null;
            $pagador = Personas::select(['per_nombres', 'per_apellidopaterno', 'per_fechanacimiento'])
                ->where('per_run', $element->rut_pagador)
                ->first();
            $elementFiltroBeneficiario['nombre-pagador'] = isset($pagador->per_nombres) ? Utiles::capitalizaTexto($pagador->per_nombres . ' ' . $pagador->per_apellidopaterno . ' ' . $pagador->per_apellidomaterno) : null;
            $elementFiltroBeneficiario['edad-pagador'] = isset($pagador->per_fechanacimiento) ? Utiles::getEdad($pagador->per_fechanacimiento) : null;
            $elementFiltroBeneficiario['id'] = $element->complemento_id;
            $elementFiltroBeneficiario['tipo'] = Utiles::getNombreTipoYSubtipoSolicitud($element->id_tipo_solicitud,
                $element->c_tcomplemento_ingreso);
            $elementFiltroBeneficiario['monto'] = $element->monto;
//            $elementFiltroBeneficiario['rut-causante'] = $element->rut_causante;
            $elementFiltroBeneficiario['rut-receptor'] = $element->rut_receptor;
            $elementFiltroBeneficiario['rut-pagador'] = $element->rut_pagador;
            $elementFiltroBeneficiario['periodo-read'] = $element->periodo_read;
            $elementFiltroBeneficiario['fecha-inicio-pago'] = $element->fecha_inicio_pago;
            $elementFiltroBeneficiario['nro-cuotas'] = $element->numero_cuotas;
            $elementFiltroBeneficiario['fecha-vigente'] = $element->fecha_modificacion;
//            if (isset($regBeneficiario[$element->rut_pagador])) {
//                $regBeneficiario[]['historico'][] = $elementFiltroBeneficiario;
//            } else {
            $regBeneficiario[] = $elementFiltroBeneficiario;
//            }
        }

        $dataCiudadanoPension = [];
        if (count($regPagador) > 0) {
            $dataCiudadanoPension['pagador']['estado'] = true;
            $dataCiudadanoPension['pagador']['detalle'] = $regPagador;
        } else {
            $dataCiudadanoPension['pagador']['estado'] = false;
        }

        if (count($regReceptor) > 0) {
            $dataCiudadanoPension['receptor']['estado'] = true;
            $dataCiudadanoPension['receptor']['detalle'] = $regReceptor;
        } else {
            $dataCiudadanoPension['receptor']['estado'] = false;
        }

        if (count($regBeneficiario) > 0) {
            $dataCiudadanoPension['beneficiario']['estado'] = true;
            $dataCiudadanoPension['beneficiario']['detalle'] = $regBeneficiario;
        } else {
            $dataCiudadanoPension['beneficiario']['estado'] = false;
        }

//        $dataCiudadanoPension['beneficiario'] = $regBeneficiario;

//        return [
//            'pagador' => [
//                'estado' => (is_null($runCiudadano->PENSION_DESCUENTO) || $runCiudadano->PENSION_DESCUENTO == 0) ?
//                    false :
//                    true,
//                'detalle' => $regPagador,
//            ],
//            'receptor' => [
//                'estado' => (is_null($runCiudadano->PENSION_APORTE) || $runCiudadano->PENSION_APORTE == 0) ?
//                    false :
//                    true,
//                'detalle' => $regReceptor,
//            ],
//            'beneficiario' => $regBeneficiario
//        ];
        return $dataCiudadanoPension;
    }

    public static function getIsaprePorRUN($runCiudadano, $periodoRSHActual, $ciudadanoConsultado = null)
    {
        $dataCiudadanoIsapre = [];
        $dataCiudadanoExport = PersonasExport::where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('per_run', $runCiudadano)
            ->first();

        $dataCiudadanoIsapre['puede-realizar-solicitud-65'] = false;
        $dataCiudadanoIsapre['sub-tipo-solicitud-65'] = [];
        $dataCiudadanoIsapre['puede-realizar-solicitud-51'] = false;
        $dataCiudadanoIsapre['sub-tipo-solicitud-51'] = [];

        $dataCiudadanoIsapre['isapre-alto-costo'] = isset($dataCiudadanoExport->calc_isaprecarga_costoso) && $dataCiudadanoExport->calc_isaprecarga_costoso == 1 ? true : false;
        //------------------------------
        //---
        if ($dataCiudadanoIsapre['isapre-alto-costo'] == true) {
            $busqueda65 = DatosDeSolicitud::busquedaSolicitudexRunIntegrante($runCiudadano,
                Constante::TIPO_SOLICITUD_65_ACTUALIZACION_DE_COTIZACIONES_DE_SALUD);
            $busqueda51 = DatosDeSolicitud::busquedaSolicitudexRunIntegrante($runCiudadano,
                Constante::TIPO_SOLICITUD_51_COMPLEMENTO_DE_COTIZACION_SALUD);
            if (count($busqueda65) == 0 || $busqueda65 == null) {
                if ($ciudadanoConsultado == true) {
                    $dataCiudadanoIsapre['puede-realizar-solicitud-65'] = true;
                    $dataCiudadanoIsapre['sub-tipo-solicitud-65'] = [1, 2];
                } elseif ($dataCiudadanoExport->per_edad >= Constante::EDAD_18_MAYOR_DE_EDAD) {
                    // si es mayor de edad subtipo 1
                    $dataCiudadanoIsapre['puede-realizar-solicitud-65'] = false;
                    $dataCiudadanoIsapre['sub-tipo-solicitud-65'] = [1];
                } elseif ($dataCiudadanoExport->per_edad < Constante::EDAD_18_MAYOR_DE_EDAD) {
                    // si es menor de edad subtipo 2
                    $dataCiudadanoIsapre['puede-realizar-solicitud-65'] = true;
                    $dataCiudadanoIsapre['sub-tipo-solicitud-65'] = [2];
                }
            }
            if (count($busqueda51) == 0 || $busqueda51 == null) {
                if ($ciudadanoConsultado == true) {
                    // Ciudadano por quien se consulta
                    $dataCiudadanoIsapre['puede-realizar-solicitud-51'] = true;
                    $dataCiudadanoIsapre['sub-tipo-solicitud-51'] = [1];
                } elseif ($dataCiudadanoExport->per_edad < Constante::EDAD_18_MAYOR_DE_EDAD) {
                    // Menores de Edad
                    // $dataCiudadanoIsapre['sub-tipo-solicitud-51'] = [];
                    $dataCiudadanoIsapre['puede-realizar-solicitud-51'] = true;
                    $dataCiudadanoIsapre['sub-tipo-solicitud-51'] = [
                        1,
                        2
                    ]; // "1 y revisar si aplica 2"; // revisar si tambien aplica para la 2.
                } elseif ($dataCiudadanoExport->per_edad >= Constante::EDAD_18_MAYOR_DE_EDAD) {
                    $dataCiudadanoIsapre['puede-realizar-solicitud-51'] = false;
                    $dataCiudadanoIsapre['sub-tipo-solicitud-51'] = [];
                }
            }
        }
        //------------------------------
        $query1 = DB::table('ever_super_salud_cotizantes')
            ->where('calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('run', $runCiudadano)
            ->where('periodo_rsh', $periodoRSHActual)
            ->select(DB::raw('1 as marca'));
        $query2 = DB::table('tbl_super_salud_cargas')
            ->where('calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('run_carga', $runCiudadano)
            ->where('periodo_rsh', $periodoRSHActual)
            ->select(DB::raw('2 as marca'));
        $cargaOCotizante = $query1->union($query2)->first();

        $ultimoPeriodoCargadoDelCiudadano = isset($periodoRSHActual) ? $periodoRSHActual : null;

        $ultimoPeriodoCargadoFila = DB::table('isapres_cotizan_meses_cargados')
            ->where('periodo_rsh', $ultimoPeriodoCargadoDelCiudadano)
            ->first();
        if (isset($cargaOCotizante->marca)) {
            switch ($cargaOCotizante->marca) {
                case 1:
                    $dataCiudadanoIsapreEloq = DB::table('ever_super_salud_cotizantes')
                        ->where('run', $runCiudadano)
                        ->where('vigente', Constante::VIGENTE_1_VIGENTE)
                        ->orderBy('periodo_rsh', 'desc')
                        ->first();

                    if (isset($dataCiudadanoIsapreEloq->tipo_trabajador)) {

                        $dataCiudadanoCargasEloq = DB::table('tbl_super_salud_cargas')
                            ->where('run_cotizante', $runCiudadano)
                            ->where('periodo_rsh', $ultimoPeriodoCargadoDelCiudadano)
                            ->get();

                        $dataCiudadanoIsapre['cotizante']['estado'] = true;
                        $dataCiudadanoIsapre['cotizante']['tipo-trabajador'] = $dataCiudadanoIsapreEloq->tipo_trabajador;
                        $dataCiudadanoIsapre['cotizante']['periodo'] = $ultimoPeriodoCargadoDelCiudadano;
                        $dataCiudadanoIsapre['cotizante']['nombre-isapre'] = $dataCiudadanoIsapreEloq->isapre_nom;
                        $dataCiudadanoIsapre['cotizante']['codigo-plan'] = $dataCiudadanoIsapreEloq->identificacion_plan;
                        $dataCiudadanoIsapre['cotizante']['valor-plan'] = $dataCiudadanoIsapreEloq->monto_plan;

                        $dataCiudadanoCargas = [];
                        foreach ($dataCiudadanoCargasEloq as $element) {
                            $elementFiltroCargaAsignacion = Utiles::getPersona($element->run_carga);
                            if($elementFiltroCargaAsignacion instanceof Personas) {
                                $elementFiltroCargaExport = PersonasExport::where('per_vigente',
                                    Constante::VIGENTE_1_VIGENTE)
                                    ->where('per_run', $element->run_carga)
                                    ->first();

                                $elementFiltroCarga['nombre'] = $elementFiltroCargaAsignacion->per_nombres . ' ' . $elementFiltroCargaAsignacion->per_apellidopaterno . ' ' . $elementFiltroCargaAsignacion->per_apellidomaterno;
                                $elementFiltroCarga['edad'] = Utiles::getEdad($elementFiltroCargaAsignacion->per_fechanacimiento);
                                $elementFiltroCarga['isapre-alto-costo'] = isset($elementFiltroCargaExport->calc_isaprecarga_costoso) && $elementFiltroCargaExport->calc_isaprecarga_costoso == 1 ? true : false;

                                $dataCiudadanoCargas[$element->run_carga] = $elementFiltroCarga;
                            }
                        }
                        $dataCiudadanoIsapre['cotizante']['cargas'] = $dataCiudadanoCargas;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_1] = $dataCiudadanoIsapreEloq->renta_dep_00;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_2] = $dataCiudadanoIsapreEloq->renta_dep_01;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_3] = $dataCiudadanoIsapreEloq->renta_dep_02;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_4] = $dataCiudadanoIsapreEloq->renta_dep_03;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_5] = $dataCiudadanoIsapreEloq->renta_dep_04;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_6] = $dataCiudadanoIsapreEloq->renta_dep_05;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_7] = $dataCiudadanoIsapreEloq->renta_dep_06;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_8] = $dataCiudadanoIsapreEloq->renta_dep_07;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_9] = $dataCiudadanoIsapreEloq->renta_dep_08;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_10] = $dataCiudadanoIsapreEloq->renta_dep_09;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_11] = $dataCiudadanoIsapreEloq->renta_dep_10;
                        $dataCiudadanoIsapre['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_12] = $dataCiudadanoIsapreEloq->renta_dep_11;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_1] = $dataCiudadanoIsapreEloq->renta_ind_00;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_2] = $dataCiudadanoIsapreEloq->renta_ind_01;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_3] = $dataCiudadanoIsapreEloq->renta_ind_02;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_4] = $dataCiudadanoIsapreEloq->renta_ind_03;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_5] = $dataCiudadanoIsapreEloq->renta_ind_04;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_6] = $dataCiudadanoIsapreEloq->renta_ind_05;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_7] = $dataCiudadanoIsapreEloq->renta_ind_06;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_8] = $dataCiudadanoIsapreEloq->renta_ind_07;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_9] = $dataCiudadanoIsapreEloq->renta_ind_08;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_10] = $dataCiudadanoIsapreEloq->renta_ind_09;
                        $dataciudadanoisapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_11] = $dataCiudadanoIsapreEloq->renta_ind_10;
                        $dataCiudadanoIsapre['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_12] = $dataCiudadanoIsapreEloq->renta_ind_11;
                    } else {
                        $dataCiudadanoIsapre['cotizante']['estado'] = false;
                    }
                    $dataCiudadanoIsapre['carga']['estado'] = false;
                    break;
                case 2:
                    $dataCiudadanoIsapre['cotizante']['estado'] = false;
                    $dataCiudadanoIsapre['carga']['estado'] = true;
                    $dataCiudadanoCargasEloq = DB::table('tbl_super_salud_cargas tssc')
                        ->where('run_carga', $runCiudadano)
                        //->where('periodo_rsh', $ultimoPeriodoCargadoDelCiudadano)
                        ->orderBy('periodo_rsh', 'desc')
                        ->first();
                    $dataCotizanteEloq = DB::table('ever_super_salud_cotizantes essc')
                        ->where('run', $dataCiudadanoCargasEloq->run_cotizante)
                        ->where('periodo_rsh', $ultimoPeriodoCargadoDelCiudadano)
                        ->where('vigente', 1)
                        ->where('calculoactivo', 1)
                        ->first();
                    if($dataCotizanteEloq) {
                        $dataCotizanteCargasEloq = DB::table('tbl_super_salud_cargas')
                            ->where('run_cotizante', $dataCiudadanoCargasEloq->run_cotizante)
                            ->where('periodo_rsh', $ultimoPeriodoCargadoDelCiudadano)
                            ->where('run_carga',$runCiudadano)
                            ->get();

                        $datosCotizante = self::getPersona($dataCiudadanoCargasEloq->run_cotizante);

                        $dataCiudadanoIsapre['carga']['fecha-nacimiento'] = $dataCiudadanoCargasEloq->fecha_nac;
                        $dataCiudadanoIsapre['carga']['sexo'] = $dataCiudadanoCargasEloq->sexo;
                        $dataCiudadanoIsapre['carga']['tipo-carga'] = $dataCiudadanoCargasEloq->tipo_carga;
                        $dataCiudadanoIsapre['carga']['cotizante'] = [];
                        $dataCiudadanoIsapre['carga']['cotizante']['run'] = $datosCotizante->per_run;
                        $dataCiudadanoIsapre['carga']['cotizante']['nombres'] = $datosCotizante->per_nombres . ' ' . $datosCotizante->per_apellidopaterno . ' ' . $datosCotizante->per_apellidomaterno;
                        $dataCiudadanoIsapre['carga']['cotizante']['tipo-trabajador'] = $dataCotizanteEloq->tipo_trabajador;
                        $dataCiudadanoIsapre['carga']['cotizante']['periodo'] = $ultimoPeriodoCargadoDelCiudadano;
                        $dataCiudadanoIsapre['carga']['cotizante']['nombre-isapre'] = $dataCotizanteEloq->isapre_nom;
                        $dataCiudadanoIsapre['carga']['cotizante']['codigo-plan'] = $dataCotizanteEloq->identificacion_plan;
                        $dataCiudadanoIsapre['carga']['cotizante']['valor-plan'] = $dataCotizanteEloq->monto_plan;
                        $dataCiudadanoCargas = [];
                        foreach ($dataCotizanteCargasEloq as $element) {
                            $elementFiltroCargaAsignacion = Utiles::getPersona($element->run_carga);
                            $elementFiltroCarga['nombre'] = $elementFiltroCargaAsignacion->per_nombres . ' ' . $elementFiltroCargaAsignacion->per_apellidopaterno . ' ' . $elementFiltroCargaAsignacion->per_apellidomaterno;
                            $elementFiltroCarga['edad'] = Utiles::getEdad($elementFiltroCargaAsignacion->per_fechanacimiento);
                            $dataCiudadanoCargas[$element->run_carga] = $elementFiltroCarga;
                        }
                        $dataCiudadanoIsapre['carga']['cotizante']['cargas'] = $dataCiudadanoCargas;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_1] = $dataCotizanteEloq->renta_dep_00;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_2] = $dataCotizanteEloq->renta_dep_01;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_3] = $dataCotizanteEloq->renta_dep_02;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_4] = $dataCotizanteEloq->renta_dep_03;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_5] = $dataCotizanteEloq->renta_dep_04;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_6] = $dataCotizanteEloq->renta_dep_05;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_7] = $dataCotizanteEloq->renta_dep_06;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_8] = $dataCotizanteEloq->renta_dep_07;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_9] = $dataCotizanteEloq->renta_dep_08;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_10] = $dataCotizanteEloq->renta_dep_09;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_11] = $dataCotizanteEloq->renta_dep_10;
                        $dataCiudadanoIsapre['carga']['cotizante']['dependiente'][$ultimoPeriodoCargadoFila->mes_12] = $dataCotizanteEloq->renta_dep_11;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_1] = $dataCotizanteEloq->renta_ind_00;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_2] = $dataCotizanteEloq->renta_ind_01;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_3] = $dataCotizanteEloq->renta_ind_02;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_4] = $dataCotizanteEloq->renta_ind_03;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_5] = $dataCotizanteEloq->renta_ind_04;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_6] = $dataCotizanteEloq->renta_ind_05;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_7] = $dataCotizanteEloq->renta_ind_06;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_8] = $dataCotizanteEloq->renta_ind_07;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_9] = $dataCotizanteEloq->renta_ind_08;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_10] = $dataCotizanteEloq->renta_ind_09;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_11] = $dataCotizanteEloq->renta_ind_10;
                        $dataCiudadanoIsapre['carga']['cotizante']['independiente'][$ultimoPeriodoCargadoFila->mes_12] = $dataCotizanteEloq->renta_ind_11;
                        break;
                    }
                    else {
                        $dataCiudadanoIsapre['cotizante']['estado'] = false;
                        $dataCiudadanoIsapre['carga']['estado'] = false;
                    }
            }
        } else {
            $dataCiudadanoIsapre['cotizante']['estado'] = false;
            $dataCiudadanoIsapre['carga']['estado'] = false;
        }

        return $dataCiudadanoIsapre;
    }

    public static function getBienRaizPorRUN($runCiudadano)
    {
        $dataPropiedadesCiudadanoEloq = VWPropiedad::where('rut', $runCiudadano)
            ->where('vigente', Constante::VIGENTE_1_VIGENTE)
            ->where('calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->get();
        $regPropiedades = [];
        foreach ($dataPropiedadesCiudadanoEloq as $element) {
            $elementFiltroPropiedad['rol-comuna'] = $element->rol_comuna;
            $elementFiltroPropiedad['rol-manzana'] = $element->rol_manzana;
            $elementFiltroPropiedad['rol-predio'] = $element->rol_predio;
            $elementFiltroPropiedad['direccion-ofuscada'] = $element->direccion_ofuscada;
            $elementFiltroPropiedad['rol'] = $element->rol;
            $elementFiltroPropiedad['tasacion'] = $element->avaluo_total;
            $elementFiltroPropiedad['fecha-fuente'] = $element->fechafuente;
            $elementFiltroPropiedad['run-juridico'] = $element->r_juridico;
            $elementFiltroPropiedad['run-sociedad'] = $element->run_sociedad;
            $elementFiltroPropiedad['run-sociedad-ofuscado'] = $element->run_sociedad;
            $regPropiedades[$element->rol] = $elementFiltroPropiedad;
        }
        return $regPropiedades;
    }

    /**
     * Retorna el nombre del trámite por $idTipoSolicitud y/o $idSubTipoSolicitud
     */
    public static function getNombreTipoYSubtipoSolicitud(
        $idTipoSolicitud,
        $idSubTipoSolicitud = null,
        $separador = ' - '
    ) {
        $lstTipoSolicitud = TipoSolicitud::where('vigente', 1)
            ->pluck('tipo_solicitud', 'id_tipo_solicitud');
        $lstSubTipoSolicitud = SubTipoSolicitud::where('vigente', 1)
            ->where('id_tipo_solicitud', $idTipoSolicitud)
            ->pluck('nombre_sub_tipo_solicitud', 'id_sub_tipo_solicitud');
        $textoNombreTipoSolictud = isset($lstTipoSolicitud[$idTipoSolicitud]) ?
            $lstTipoSolicitud[$idTipoSolicitud] :
            'Tipo de solicitud no definido';
        $textoNombreSubTipoSolicitud = isset($lstSubTipoSolicitud[$idSubTipoSolicitud]) ?
            $lstSubTipoSolicitud[$idSubTipoSolicitud] :
            null;
        return is_null($textoNombreSubTipoSolicitud) ?
            $textoNombreTipoSolictud :
            $textoNombreTipoSolictud . $separador . $textoNombreSubTipoSolicitud;
    }

    public static function a_romano($numero)
    {
        $tabla = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );
        $retorno = '';
        while ($numero > 0) {
            foreach ($tabla as $rom => $arb) {
                if ($numero >= $arb) {
                    $numero -= $arb;
                    $retorno .= $rom;
                    break;
                }
            }
        }
        return $retorno;
    }

    public static function verificaRunTieneGrupoFamiliar($runIntegrante)
    {
        $tieneGrupoFamiliar = (object)[ // se inicializa
            'estado' => '0',
            'msg' => 'No posee grupo Grupo Familiar'
        ];
        $persona = Personas::where('per_run', $runIntegrante)
            ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->first();
        if (!empty($persona)) {
            $beneficiario = BeneficiarioGrupoFamiliar::where('bnfgrupfami_persona', $persona->per_id)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->first();
            if (!empty($beneficiario)) {
                $grupoFamiliar = GrupoFamiliar::where('grupfami_id', $beneficiario->bnfgrupfami_grupofamiliar)
                    ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                    ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                    ->first();
                if (!empty($grupoFamiliar)) {
                    $tieneGrupoFamiliar->estado = '1';
                    $tieneGrupoFamiliar->msg = 'Grupo Familiar válido';
                }
            }
        }
        return $tieneGrupoFamiliar;
    }

    public static function verificaRunesTienenMismoGrupoFamiliar($runIntegrante1, $runIntegrante2)
    {
        $tienenMismoGrupoFamiliar = (object)[
            'estado' => '0',
            'msg' => 'No pertenecen al mismo Grupo familiar',
            'nuevofolio' => null
        ];
        $persona1 = Personas::where('per_run', $runIntegrante1)
            ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->first();
        $persona2 = Personas::where('per_run', $runIntegrante2)
            ->where('per_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
            ->where('per_vigente', Constante::VIGENTE_1_VIGENTE)
            ->first();
        if (!empty($persona1) && !empty($persona2)) {
            $beneficiario1 = BeneficiarioGrupoFamiliar::where('bnfgrupfami_persona', $persona1->per_id)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->first();
            $beneficiario2 = BeneficiarioGrupoFamiliar::where('bnfgrupfami_persona', $persona2->per_id)
                ->where('bnfgrupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                ->where('bnfgrupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                ->first();
            if (!empty($beneficiario1) && !empty($beneficiario2)) {
                if ($beneficiario1->bnfgrupfami_grupofamiliar == $beneficiario2->bnfgrupfami_grupofamiliar) {
                    $grupoFamiliar = GrupoFamiliar::where('grupfami_id', $beneficiario1->bnfgrupfami_grupofamiliar)
                        ->where('grupfami_calculoactivo', Constante::CALCULO_ACTIVO_1_APROBADO)
                        ->where('grupfami_vigente', Constante::VIGENTE_1_VIGENTE)
                        ->first();
                    if (!empty($grupoFamiliar)) {
                        $tienenMismoGrupoFamiliar->estado = '1';
                        $tienenMismoGrupoFamiliar->msg = 'Pertenecen al mismo Grupo Familiar';
                        $tienenMismoGrupoFamiliar->nuevofolio = $grupoFamiliar->grupfami_nuevofolio;
                    }
                }
            } elseif (!(!empty($beneficiario1) || !empty($beneficiario2))) {
                $tienenMismoGrupoFamiliar->estado = '-1';
                $tienenMismoGrupoFamiliar->msg = 'Ambos no tiene Grupo familiar';
                $tienenMismoGrupoFamiliar->nuevofolio = null;
            }
        }
        return $tienenMismoGrupoFamiliar;
    }

    public static function busquedaPadres($runCiudadano)
    {
        $data = Personas::where('per_run', $runCiudadano)
            ->first();
        $info['padre'] = $data->per_run_padre;
        $info['madre'] = $data->per_run_madre;
        return ($info);
    }

    public static function quitaPuntos($numero)
    {
        try {
            return str_replace(['.', ' '], '', $numero);
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function remplazaComaPorPunto($numero)
    {
        try {
            $numero = str_replace('.', '', $numero);
            return str_replace(',', '.', $numero);
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function cleanNombreVariables2($stringConCaracteresEspeciales)
    {
        $cambiaEnie = str_replace(['ñ', 'Ñ'], 'n', $stringConCaracteresEspeciales);

        $cambiaEnie = str_replace(['@', '/', '?', '-', ';', '´'], '_', $cambiaEnie);
        return preg_replace('/[^A-Za-z0-9.\_]/', '', $cambiaEnie);
    }

    /*
     * Devuelve la instancia de un objeto en particular en la pila de llamada de la funcion actual
     */
    public static function getCallerInstance(array $options = [])
    {
        $defaults = [
            'className' => null,
            // clase de la instancia a buscar
            'limitTrace' => 0,
            // limita la pila de llamadas a un numero determinado de niveles (ahorro de memoria)
            'startTraceOn' => 0,
            // inicia la busqueda en un nivel en particular
            'ignoreArgs' => true,
            // ignora los argumentos de las funciones en la pila de llamadas (ahorro de memoria)
        ];
        $options = array_replace($defaults, $options);
        $optionsBackTrace = DEBUG_BACKTRACE_PROVIDE_OBJECT | (($options['ignoreArgs']) ? DEBUG_BACKTRACE_IGNORE_ARGS : 0);
        $trace = debug_backtrace($optionsBackTrace, $options['limitTrace']);
        $instance = null;
        $start = 2 + $options['startTraceOn'];  // 2: comienza a partir de un nivel debajo de la instancia que llamó a Utiles::getCallerInstance()
        for ($i = $start; $i < count($trace); $i++) {
            if (!is_null($options['className']) && isset($trace[$i]['object']) && $trace[$i]['object'] instanceof $options['className']) {
                $instance = $trace[$i]['object'];
                break;
            }
        }
        return $instance;
    }

    /*
     * Cambia un termino en camelCase a kebab-case (conocido tambien como dash-case)
     * (No son brochetas de camello ;-) )
     */
    public static function camelToKebab($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }

    public static function obtenerCalleExport($export)
    {
        if ($export->grupfami_c_calle >= Constante::ID_CALLE_11011_CORTE_GRUPO_FAMILIAR) {
            $calle_aux = Calle::where('id_localidad', $export->grupfami_localidad)
                ->where('id_calle', $export->grupfami_c_calle)
                ->where('comuna_ine', $export->grupfami_comunaine)
                ->first();
            if (!empty($calle_aux)) {
                $calle = $calle_aux['nombre'];
            } else {
                $calle_aux = Calle::where('id_calle', $export->grupfami_c_calle)
                    ->where('comuna_ine', $export->grupfami_comunaine)
                    ->first();
                $calle = $calle_aux['nombre'];
            }
            if ((!empty($calle_aux)) < 1) {
                $calle = $export->grupfami_n_calle;
            }
        } elseif ($export->grupfami_n_calle) {
            $calle = $export->grupfami_n_calle;
        } else {
            $calle = "Calle no encontrada";
        }
        return $calle;
    }

    public static function RemoverEspecialChar($str)
    {
        $res = str_replace(array(
            '°',
            '*',
            '|',
            '\'',
            '"',
            ',',
            ';',
            '!',
            '$',
            '¨',
            '´',
            '+',
            '%',
            '[',
            ']',
            '(',
            ')',
            '{',
            '}',
            '?',
            '¿',
            '¡',
            '@'
        ), '', $str);
        return $res;
    }

    public static function evaluarOrigen($s)
    {
        $origen = null;
        if (in_array($s->origen_registro, Constante::$ORIGEN_REGISTRO_1_PLATAFORMA_MUNICIPAL_VALIDOS)) {
            $origen = "Plataforma Municipal";
        } elseif (in_array($s->origen_registro, Constante::$ORIGEN_REGISTRO_2_PLATAFORMA_CIUDADANA_VALIDOS)) {
            $origen = "Plataforma Ciudadana";
        } elseif (in_array($s->origen_registro, Constante::$ORIGEN_REGISTRO_3_PLATAFORMA_CLASE_MEDIA_PROTEGIDA_VALIDOS)) {
            $origen = "Plataforma Red Clase Media";
        }
        if ($origen == null) {
            if ($s->canal == Constante::CANAL_1_SAVE_MUNICIPAL) {
                $origen = "Plataforma Municipal";
            }
            if (($s->canal == Constante::CANAL_2_SAVE_CLAVE_UNICA) ||
                ($s->canal == Constante::CANAL_3_SAVE_RUN_CIUDADANO)
            ) {
                $origen = "Plataforma Ciudadana";
            }
            if ($s->canal == Constante::CANAL_4_SAVE_CHILE_ATIENDE_EJECUTOR) {
                $origen = "Plataforma Municipal - Chile Atiende Ejecutor";
            }
        }
        return ($origen);
    }

    public static function mensajeTramoIngreso($promedio = 0, $tramosIngresos)
    {
        $promedioFormateado = number_format($promedio,0,".","");
        $data = $tramosIngresos;
        $mensaje = "";

        if ($promedioFormateado < 50000){
            return 'menor que $50.000';
        }elseif ($promedioFormateado> 50000 && $promedioFormateado < 100000){
            return 'entre $50.000 y $100.000';
        }

        for ($i = 1; $i <= count($data) - 1; $i++) {
            if ($promedioFormateado > intval($data[$i]['ting_inicial']) && $promedioFormateado < intval($data[$i]['ting_final'])) {
                $mensaje = $data[$i]['ting_descripcion'];
                break;
            }
        }

        return strtolower($mensaje);
    }

    public static function obtenerTramosIngresos(){
        $consultaTramos = DB::table('ab_tramo_ingresos')
            ->get();
        return json_decode(json_encode($consultaTramos), true);
    }
}
