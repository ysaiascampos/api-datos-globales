<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubTipoSolicitud
 *
 * @property int $ID
 * @property int|null $ID_TIPO_SOLICITUD
 * @property int|null $ID_SUB_TIPO_SOLICITUD
 * @property string|null $NOMBRE_SUB_TIPO_SOLICITUD
 * @property int|null $VIGENTE
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud whereIDSUBTIPOSOLICITUD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud whereIDTIPOSOLICITUD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud whereNOMBRESUBTIPOSOLICITUD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubTipoSolicitud whereVIGENTE($value)
 * @mixin Eloquent
 */
class SubTipoSolicitud extends Model
{
    protected $table = 'ab_sub_tipo_solicitud';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
