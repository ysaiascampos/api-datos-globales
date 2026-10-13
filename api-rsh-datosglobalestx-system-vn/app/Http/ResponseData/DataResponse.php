<?php


namespace App\Http\ResponseData;


use App\Constantes\Constante;

class DataResponse
{
    protected function entero($campo)
    {
        if (is_null($campo)) {
            $out = null;
        } elseif (is_numeric($campo)) {
            $out = intval($campo);
        } else {
            $out = $campo;
        }
        return $out;
    }

    protected function flotante($campo)
    {
        if (is_null($campo)) {
            $out = null;
        } elseif (is_numeric($campo)) {
            $out = floatval($campo);
        } else {
            $out = $campo;
        }
        return $out;
    }

    protected function booleano($campo)
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
}
