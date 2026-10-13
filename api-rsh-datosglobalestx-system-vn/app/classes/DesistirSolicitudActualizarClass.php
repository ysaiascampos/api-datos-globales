<?php


namespace App\classes;


use App\Constantes\Constante;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ExcepcionConsistencia;

class DesistirSolicitudActualizarClass
{
    public function __invoke(Request $request)
    {

        $solicitud = Solicitud::findByIdSolicitudRunVigente($request);
        if (!empty($solicitud)) {
            try {
                DB::beginTransaction();
                $solicitud->fecha_modificacion = date("Y-m-d H:i:s");
                $solicitud->id_usuario_modificacion = $request["run-usuario"];
                $solicitud->id_estado = Constante::ESTADO_SOLICITUD_9_DESISTIDA;
                $solicitud->save();
                DB::commit();
            } catch (\Exception $ex) {
                DB::rollBack();
                throw ExcepcionConsistencia::new(Constante::MENSAJE_ERROR_FALLA_ACTUALIZAR_DOSTOS,
                    Constante::ERR_EJECUCION_ERROR_CONSULTA_SQL)
                    ->setContexto(['id_solicitud' => $request["id_solicitud"],]);
            }

        } else {
            $dato = array();
            $solicitud = Solicitud::finBySolicitudEstado($request);
            if (!empty($solicitud)) {
                switch ($solicitud->id_estado) {
                    case Constante::ESTADO_SOLICITUD_9_DESISTIDA:
                        $dato['code'] = 1201;
                        $dato['message'] = Constante::MENSAJE_ESTADO_9_RESPUESTA_DESISTIR;
                        break;
                    case Constante::ESTADO_SOLICITUD_7_RECHAZADA:
                        $dato['code'] = 1202;
                        $dato['message'] = Constante::MENSAJE_ESTADO_7_RESPUESTA_DESISTIR;
                        break;
                    case Constante::ESTADO_SOLICITUD_5_APROBADO:
                        $dato['code'] = 1203;
                        $dato['message'] = Constante::MENSAJE_ESTADO_5_RESPUESTA_DESISTIR;
                        break;
                    default:
                        $dato['code'] = 1204;
                        $dato['message'] = Constante::MENSAJE_OTROS_ESTADO_RESPUESTA_DESISTIR;
                        break;
                }
            } else {
                $dato['code'] = 1205;
                $dato['message'] = Constante::MENSAJE_NO_EXISTE_SOLICITUD_RESPUESTA_DESISTIR;
            }
            $dato['data'] = null;
            $solicitud = $dato;
        }

        return $solicitud;

    }
}
