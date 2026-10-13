<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Calle;
use Exception;

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
}