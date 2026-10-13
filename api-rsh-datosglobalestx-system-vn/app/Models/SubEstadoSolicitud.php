<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubEstadoSolicitud
 *
 * @property int $ID_SUB_ESTADO
 * @property string|null $NOMBRE_SUB_ESTADO
 * @method static \Illuminate\Database\Eloquent\Builder|SubEstadoSolicitud newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubEstadoSolicitud newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubEstadoSolicitud query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubEstadoSolicitud whereIDSUBESTADO($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubEstadoSolicitud whereNOMBRESUBESTADO($value)
 * @mixin Eloquent
 */
class SubEstadoSolicitud extends Model
{
    protected $table = 'ab_sub_estado_solicitud';
    protected $primaryKey = 'id_sub_estado';
    public $timestamps = false;
}
