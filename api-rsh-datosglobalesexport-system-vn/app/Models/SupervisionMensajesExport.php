<?php

namespace App\Models;

use App\Classes\Utiles;
use App\Constantes\Constante;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\SupervisionMensajesExport
 *
 * @property int $SV_ID_MENSAJE
 * @property string|null $SV_DESC_MENSAJE
 * @property string|null $SV_MENSAJE
 * @property string|null $SV_MENSAJE_VENCIDO
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport whereSVDESCMENSAJE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport whereSVIDMENSAJE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport whereSVMENSAJE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisionMensajesExport whereSVMENSAJEVENCIDO($value)
 * @mixin Model
 */
class SupervisionMensajesExport extends Model
{
    //protected $connection = 'oracle_export';
    protected $table = 'supervision_mensajes';
    protected $primaryKey = 'sv_id_mensaje';
    public $timestamps = false;

    public static function getMensajes($runIntegrante, $nuevoFolio,$integrantesExport)
    {
        $integrantes = $integrantesExport;
        try {
            $zonaHorariaChileContinental = new DateTimeZone('America/Santiago');
            $fechaActual = new DateTime("now", $zonaHorariaChileContinental);
            $query = self::join('supervision_rsh', 'supervision_rsh.sv_id_mensaje', '=',
                'supervision_mensajes.sv_id_mensaje')
                ->where(function ($query) use ($runIntegrante, $nuevoFolio) {
                    $query->where(function ($query2) use ($runIntegrante) {
                            $query2->where('supervision_rsh.sv_destinatario_mens',2)
                                ->where('supervision_rsh.sv_run', $runIntegrante);
                        })
                        ->orwhere(function ($query2) use ($nuevoFolio) {
                            $query2->where('supervision_rsh.sv_destinatario_mens',3)
                                ->where('supervision_rsh.sv_folio', '=', $nuevoFolio);
                        })->orwhere(function ($query2) use ($nuevoFolio) {
                            $query2->where('supervision_rsh.sv_menor',1)
                                ->where('supervision_rsh.sv_folio', '=', $nuevoFolio);
                        });
                })
                ->join('supervision_tipo_h', 'supervision_tipo_h.sv_tipo_hallazgo_id', '=',
                    'supervision_rsh.sv_tipo_hallazgo_id')
                ->where('supervision_rsh.sv_vigente', Constante::VIGENTE_1_VIGENTE);

            if(env('MENSAJE_BLOQUEO_FECHA')){
                $query
                    ->where('supervision_rsh.sv_fec_ult_dia_mens', '>=', $fechaActual->format('Ymd'));
            }
            $elemento = $query->get();

        } catch (Exception $ex) {
            log::error($ex->getMessage());
            Log::info('Exception en buscar mensajes de supervisión: run '. $runIntegrante . ' - Nuevo Folio [' . $nuevoFolio . ']');
            $elemento = null;
        }
        if($elemento->count()>0){
            $runes = array();
            foreach ($elemento as $key => $item){
                $runMensaje = (int)$item->sv_run;
                if((int)$item->sv_menor == 1 &&
                    (int)$item->sv_run_menor>0 &&
                    !in_array((int)$item->sv_run_menor,$runes)){
                        $runes[]=(int)$item->sv_run_menor;
                }
            }
            $mensaje = "";
            foreach ($integrantes as $integrante){
                if ($integrante->per_run == $runMensaje){
                    $mensaje = "El integrante es del grupo hogar";

                }
            }
            if (empty($mensaje)) {
                $mensaje = "El integrante no esta dentro del grupo hogar";
                $elemento = null;
                return $elemento;
            }
        }
        return $elemento;
    }

    public static function formato_periodorsh($periodo_rsh){
        if(!empty($periodo_rsh)){
            $anoperiodo=substr($periodo_rsh,0,4);
            $mes=substr($periodo_rsh,4,5);
            switch($mes){
                case 01:
                    $mesperiodo=Constante::MES01;
                    break;
                case 02:
                    $mesperiodo=Constante::MES02;
                    break;
                case 03:
                    $mesperiodo=Constante::MES03;
                    break;
                case 04:
                    $mesperiodo=Constante::MES04;
                    break;
                case 05:
                    $mesperiodo=Constante::MES05;
                    break;
                case 06:
                    $mesperiodo=Constante::MES06;
                    break;
                case 07:
                    $mesperiodo=Constante::MES07;
                    break;
                case 8:
                    $mesperiodo=Constante::MES08;
                    break;
                case 9:
                    $mesperiodo=Constante::MES09;
                    break;
                case 10:
                    $mesperiodo=Constante::MES10;
                    break;
                case 11:
                    $mesperiodo=Constante::MES11;
                    break;
                case 12:
                    $mesperiodo=Constante::MES12;
                    break;
                default:
                    break;
            }
            $periodo=$mesperiodo.'  de  '.$anoperiodo;
            return $periodo;
        }
        else
            return 0;
    }
}
