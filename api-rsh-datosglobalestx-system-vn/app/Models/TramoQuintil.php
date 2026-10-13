<?php

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\Clock\get;

/**
 * App\Models\TramoQuintil
 *
 * @property int $ID
 * @property string|null $RANGO
 * @property string|null $NOMBRE
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TramoQuintil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TramoQuintil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TramoQuintil query()
 * @mixin \Eloquent
 */
class TramoQuintil extends Model
{
    protected $table = 'ab_tramo_quintil';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $hidden = ['rn'];

    public static function getModel()
    {
        return new TramoQuintil();
    }

    public static function findAll()
    {
        $datosDeTramoQuintil = null;
        try {
            $datosDeTramoQuintil = TramoQuintil::getModel()
                ->orderBy('id', 'asc')
                ->select('rango','nombre', 'id')
                ->get();
        } catch (Exception $ex) {
            throw $ex;
        }
        return $datosDeTramoQuintil;
    }
}
