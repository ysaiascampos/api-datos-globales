<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MotivoRectificacion
 *
 * @property int $ID
 * @property string|null $MOT_DESCRIPCION
 * @property int|null $ID_TIPO_SOLICITUD
 * @property int|null $VIGENTE
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion query()
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion whereIDTIPOSOLICITUD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion whereMOTDESCRIPCION($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MotivoRectificacion whereVIGENTE($value)
 * @mixin Eloquent
 */
class MotivoRectificacion extends Model
{
    protected $table = 'ab_motivo_rectificacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getModel()
    {
        return new MotivoRectificacion();
    }
}

