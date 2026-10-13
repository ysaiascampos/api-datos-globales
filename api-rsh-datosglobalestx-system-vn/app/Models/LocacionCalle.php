<?php

namespace App\Models;

use App\Constantes\Constante;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * App\Models\LocacionCalle
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionCalle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionCalle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionCalle query()
 * @mixin Eloquent
 */
class LocacionCalle extends Model
{
    protected $table = 'vas_calle';
    public $timestamps = false;

    public static function getCallePorComunaLocalidadYidCalle(
        $comunaCodigo,
        $idLocalidadGrupoFamiliar,
        $idCalleGrupoFamiliar,
        $nombreCalleGrupoFamiliar
    ) {
        $locacionCalle = null;
        try {
            if ($idCalleGrupoFamiliar >= Constante::ID_CALLE_11011_CORTE_GRUPO_FAMILIAR) {
                $locacionLocalidad = Ciudad::where('id_localidad', $idLocalidadGrupoFamiliar)
                    ->where('comuna_ine', $comunaCodigo)
                    ->first();
                if (!empty($locacionLocalidad)) {
                    $locacionCalle = self::where('comuna_ine', $comunaCodigo)
                        ->where('id_localidad', $idLocalidadGrupoFamiliar)
                        ->where('id_calle', $idCalleGrupoFamiliar)
                        ->first();
                    if (!isset($locacionCalle)) {
                        $locacionCalle = (object)[
                            'nombre' => $nombreCalleGrupoFamiliar,
                            'comuna_ine' => $comunaCodigo,
                            'id_localidad' => $idLocalidadGrupoFamiliar,
                            'id_calle' => $idCalleGrupoFamiliar
                        ];
                    }
                } else {
                    $locacionCalle = self::where('comuna_ine', $comunaCodigo)
                        ->where('id_calle', $idCalleGrupoFamiliar)
                        ->first();
                }
            } else {
                $locacionCalle = (object)[
                    'nombre' => $nombreCalleGrupoFamiliar,
                    'comuna_ine' => $comunaCodigo,
                    'id_localidad' => $idLocalidadGrupoFamiliar,
                    'id_calle' => $idCalleGrupoFamiliar
                ];
                $locacionLocalidad = null;
            }
        } catch (Throwable $ex) {
            throw $ex;
        }
        return $locacionCalle;
    }
}
