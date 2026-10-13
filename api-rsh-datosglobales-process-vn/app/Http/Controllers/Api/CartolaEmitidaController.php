<?php

namespace App\Http\Controllers\Api;

use App\Constantes\Constante;
use App\Http\Controllers\Controller;
use App\Http\ResponseData\ValidarCartolaEmitidaData;
use App\Models\MDSFApiResponse;
use Illuminate\Http\Request;
use App\Classes\CartolaEmitidaClass;

class CartolaEmitidaController extends Controller
{
    protected $consumer;

    public function __construct(CartolaEmitidaClass $consumer)
    {
        $this->consumer = $consumer;
    }

    public function store(Request $request)
    {

        try {
            $respuesta = new MDSFApiResponse();
            $response = $this->consumer->create($request->all());
            if ($response) {
                $respuesta->code = $response['code'];
                $respuesta->http_code = $response['http_code'];
                $respuesta->message = $response['message'];
                $respuesta->data = $response['data'];
            } else {
                $respuesta->data['cartola-emitida'] = null;
            }
        } catch (\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de cartola emitida', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data['cartola-emitida'] = null;
        }
        return $respuesta->json();
    }

    public function find(Request $request)
    {
        $data = [
            'resultado' => ['valor'=>false,
                'mensaje'=>'no encontrado'],
            'datos' => [],
        ];
        try{
            $respuesta = new MDSFApiResponse();
            $response = $this->consumer->find($request->all());
            if ($response) {
                $respuesta->code = $response['code'];
                $respuesta->http_code = $response['http_code'];
                $responseData = new ValidarCartolaEmitidaData($response['data']);
                //->message = $response['message'];
                $respuesta->data = $responseData->data;
            } else {
                $respuesta->data['cartola-emitida'] = $data;
            }
        } catch(\Throwable $e) {
            $respuesta = MDSFApiResponse::exceptionResponse('Problemas al obtener datos de cartola emitida', $e);
            $respuesta->code = 1404;
            $respuesta->http_code = Constante::STATUS_404_ERROR_NOT_FOUND;
            $respuesta->data['cartola-emitida'] = $data;
        }
        return $respuesta->json();
    }

}
