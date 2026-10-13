<?php

namespace App\Classes;


use Illuminate\Support\Facades\Http;
use App\Models\MDSFApiResponse;
use App\Constantes\Constante;
use Illuminate\Support\Facades\Log;

class CartolaEmitidaClass
{

    public function create(array $cartolaData)
    {
        try {
            $run = $cartolaData['run-emisor'];
            $fechaCierre = (new \DateTime())->sub(new \DateInterval('P3M'));
            $ultimaFechaCierreGrupoFamiliarClass = new UltimaFechaCierreGrupoFamiliarClass;
            $ultimaFechaCierreGrupoFamiliar = $ultimaFechaCierreGrupoFamiliarClass();
            if(isset($ultimaFechaCierreGrupoFamiliar['data']['fecha-cierre'])){
                $fechaCierre = (new \DateTime($ultimaFechaCierreGrupoFamiliar['data']['fecha-cierre']));
            }
            $cartolaData['codigo'] =$this->generarCodigo($run,$fechaCierre);
            $response = Http::APISYSTEM('EXPORT_ACCESOS')
                ->post('/cartola-emitida', $cartolaData);

            // 1. HTTP status real
            $httpStatus = $response->status();

            // 2. Body casteado
            $apiresponse = MDSFApiResponse::cast($response);
            $dataResponse["data"] = $this->toArray($apiresponse->data);
            $dataResponse["code"] = $apiresponse->code;
            if($apiresponse->code == 1409){
                $dataResponse["http_code"] = Constante::STATUS_409_ERROR_CONFLICT;
            } else {
                $dataResponse["http_code"] = $apiresponse->http_code;
            }

            $dataResponse["message"] = $apiresponse->message;

            return $dataResponse;

        } catch (\Exception $e) {
            Log::error('Error consumiendo API');
            Log::error($e);

            return response()->json([
                'code' => 5000,
                'message' => 'Error interno',
                'data' => null
            ], 500);
        }
    }

    public function find(array $cartolaData)
    {
        try {
            $findCartolaData['run-emisor'] = $cartolaData['run-emisor'];
            $findCartolaData['codigo'] = $cartolaData['codigo'];
            $response = Http::APISYSTEM('EXPORT_ACCESOS')
                ->post('/cartola-emitida/buscar', $findCartolaData);

            // 1. HTTP status real
            $httpStatus = $response->status();

            // 2. Body casteado
            $apiresponse = MDSFApiResponse::cast($response);
            $dataResponse["data"] = $this->toArray($apiresponse->data);

            $resultadoBusquedaMensaje = "no encontrado";
            $resultadoBusquedaValor = false;
            if($dataResponse["data"]['cartola-emitida']){
                if(count($dataResponse["data"]['cartola-emitida'])>0) {
                    $resultadoBusquedaMensaje = "encontrado";
                    $resultadoBusquedaValor = true;
                    $tramo =$dataResponse["data"]['cartola-emitida']['tramo'];
                    $dataResponse["data"]['cartola-emitida']['tramo'] = $this->generarTramo($tramo);
                }
            }
            $dataResponse["data"]['resultado']['valor']=$resultadoBusquedaValor;
            $dataResponse["data"]['resultado']['mensaje']=$resultadoBusquedaMensaje;
            $dataResponse["message"] = $apiresponse->message;
            $logCartolaData['run-emisor'] = $cartolaData['run-emisor'];
            $logCartolaData['run-funcionario'] = $cartolaData['run-funcionario'];
            $logCartolaData['nombres-funcionario'] = $cartolaData['nombres-funcionario'];
            $logCartolaData['codigo'] = $cartolaData['codigo'];
            $logCartolaData['resultado-busqueda'] = $resultadoBusquedaMensaje;

            $logBuscaCartolaEmitida = new LogBuscaCartolaEmitidaClass;
            $apiLogResponse = $logBuscaCartolaEmitida->create($logCartolaData);
            $dataLogResponse["data"] = $this->toArray($apiLogResponse["data"]);

            $dataResponse["code"] = $apiresponse->code;
            if($apiresponse->code == 1409){
                $dataResponse["http_code"] = Constante::STATUS_409_ERROR_CONFLICT;
            } else {
                $dataResponse["http_code"] = $apiresponse->http_code;
            }



            return $dataResponse;

        } catch (\Exception $e) {
            Log::error('Error consumiendo API');
            Log::error($e);

            return response()->json([
                'code' => 5000,
                'message' => 'Error interno',
                'data' => null
            ], 500);
        }
    }

    private function toArray($data)
    {
        return is_array($data) ? $data : json_decode(json_encode($data), true);
    }

    private function generarCodigo($run, $fecha = null)
    {
        // Normalizar datos
       // $fecha = $fecha ?? now()->format('Y-m-d');

        // Limpiar RUN (solo números)
        $run = preg_replace('/[^0-9]/', '', $run);

        // Combinar valores
        $base = $run . '|' . $fecha->format('Y-m-d');

        // Generar hash numérico
        $hash = abs(crc32($base));

        // Convertir a 9 dígitos
        return str_pad(substr($hash, 0, 9), 9, '0', STR_PAD_RIGHT);
    }
    private function generarTramo($numerotramoQuintil)
    {
        $dataTramo = ["numero" => 0,
            "nombre" => Constante::MENSAJE_ERROR_11_NO_POSEE_RSH,
            "mensaje" => ""];

        if (isset($numerotramoQuintil)) {

            $TramoQuintalListaClass = new TramoQuintalListaClass();
            $responseTramoQuintalLista = $TramoQuintalListaClass();
            $tramoQuintilLista = $responseTramoQuintalLista["data"]["tramo-quintil"];

            switch ($numerotramoQuintil) {
                case $numerotramoQuintil<= Constante::NUMERO_TRAMO_40:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_40];
                    break;
                case $numerotramoQuintil> Constante::NUMERO_TRAMO_40
                    && $numerotramoQuintil<= Constante::NUMERO_TRAMO_50:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_50];
                    break;
                case $numerotramoQuintil> Constante::NUMERO_TRAMO_50
                    && $numerotramoQuintil<= Constante::NUMERO_TRAMO_60:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_60];
                    break;
                case $numerotramoQuintil> Constante::NUMERO_TRAMO_60
                    && $numerotramoQuintil<= Constante::NUMERO_TRAMO_70:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_70];
                    break;
                case $numerotramoQuintil> Constante::NUMERO_TRAMO_70
                    && $numerotramoQuintil<= Constante::NUMERO_TRAMO_80:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_80];
                    break;
                case $numerotramoQuintil> Constante::NUMERO_TRAMO_80
                    && $numerotramoQuintil<= Constante::NUMERO_TRAMO_90:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_90];
                    break;
                case $numerotramoQuintil> Constante::NUMERO_TRAMO_90
                    && $numerotramoQuintil<= Constante::NUMERO_TRAMO_100:
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_100];
                    break;
                default;
                    $dataTramoQuintil = $tramoQuintilLista[Constante::NUMERO_TRAMO_40];
            }
            $dataTramo["numero"] = $dataTramoQuintil["id"];
            $dataTramo["nombre"] = $dataTramoQuintil["nombre"];

            $MensajeTramoClass = new MensajeTramoClass();
            $responseMensajeTramo = $MensajeTramoClass();
            $mensajeTramoResponse = $responseMensajeTramo["data"];
            if (isset($mensajeTramoResponse["mensaje-tramo"]["descripcion-larga"])) {
                $dataTramo["mensaje"] = $mensajeTramoResponse["mensaje-tramo"]["descripcion-larga"];
            }

        }
        return $dataTramo;

    }

}
