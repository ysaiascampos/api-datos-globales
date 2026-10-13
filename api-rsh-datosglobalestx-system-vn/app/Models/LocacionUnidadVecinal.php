<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LocacionUnidadVecinal
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionUnidadVecinal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionUnidadVecinal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocacionUnidadVecinal query()
 * @mixin Eloquent
 */
class LocacionUnidadVecinal extends Model
{
    protected $table = 'vas_unidad_vecinal';
    public $timestamps = false;
}
