<?php


namespace App\Http\ResponseData;


use App\Constantes\Constante;
use DateTime;
use Exception;

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

    protected function fecha($campo)
    {
        $fecha = (strlen($campo) == 8) ?
            DateTime::createFromFormat('Ymd', $campo)->format('Y-m-d') :
            $campo;
        return $fecha;
    }
    protected function fechaHora($campo)
    {
        if (empty($campo)) {
            return null;
        }

        // Si ya es un objeto DateTime (PDO puede devolverlo así)
        if ($campo instanceof DateTime) {
            return $campo->format('d/m/Y H:i:s');
        }

        // Convertimos a string por seguridad
        $campo = trim((string)$campo);

        if (strlen($campo) == 14 && ctype_digit($campo)) {
            // YYYYMMDDHH24MISS
            $fecha = DateTime::createFromFormat('YmdHis', $campo);
            return $fecha ? $fecha->format('d/m/Y H:i:s') : null;
        }

        // Intento genérico (por si Oracle devuelve algo tipo "2024-06-24 15:30:45")
        try {
            $fecha = new DateTime($campo);
            return $fecha->format('d/m/Y H:i:s');
        } catch (Exception $e) {
            return $campo; // si no se puede parsear, se devuelve tal cual
        }
    }

    protected function fechaEs($campo)
    {
        if (empty($campo)) {
            return null;
        }

        // Si ya es un objeto DateTime (PDO puede devolverlo así)
        if ($campo instanceof DateTime) {
            return $campo->format('d-m-Y');
        }

        // Convertimos a string por seguridad
        $campo = trim((string)$campo);

        if (strlen($campo) == 14 && ctype_digit($campo)) {
            // YYYYMMDDHH24MISS
            $fecha = DateTime::createFromFormat('YmdHis', $campo);
            return $fecha ? $fecha->format('d-m-Y') : null;
        }

        // Intento genérico (por si Oracle devuelve algo tipo "2024-06-24 15:30:45")
        try {
            $fecha = new DateTime($campo);
            return $fecha->format('d-m-Y');
        } catch (Exception $e) {
            return $campo; // si no se puede parsear, se devuelve tal cual
        }
    }
}
