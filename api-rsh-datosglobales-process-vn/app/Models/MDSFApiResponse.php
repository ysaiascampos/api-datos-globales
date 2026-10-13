<?php

namespace App\Models;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MDSFApiResponse',
    properties: [
        new OA\Property(property: 'code', type: 'integer', example: 1000, description: '1000 = éxito; 13xx/14xx = error controlado'),
        new OA\Property(property: 'message', type: 'string', example: ''),
        new OA\Property(property: 'time', type: 'string', example: '2026-09-11 10:00:00.000000'),
        new OA\Property(property: 'data', type: 'object'),
    ]
)]
class MDSFApiResponse
{
    public $code = 1000;
    public $message = '';
    public $time;

    public $data = [];

    public $http_code = Response::HTTP_OK;

    public function json(){
        return new Response(
            [
                'code' => $this->code,
                'message' => $this->message,
                'time' => (new \DateTime())->format('Y-m-d H:i:s.u'),
                'data' => $this->data
            ],
            $this->http_code);
    }

    public static function cast($response_http){
        $castResponse = new MDSFApiResponse();
        $jsonResponse = json_decode($response_http->getBody());
        $castResponse->code = $jsonResponse->code;
        $castResponse->message = $jsonResponse->message;
        $castResponse->time = $jsonResponse->time;
        $castResponse->data = $jsonResponse->data;
        $castResponse->http_code = $response_http->getStatusCode();
        return $castResponse;
    }

    public static function exceptionResponse($message, $e){
        /*Se responde HTTP_OK dado que es un error controlado*/
        $eResponse = new MDSFApiResponse();
        $eResponse->code = 1400;
        $eResponse->message = $message;
        $eResponse->data = $e->getMessage();
        \Log::error($eResponse->message);
        \Log::error($e);
        return $eResponse;
    }

    public static function strException($message, $e){
        return ($message . " " . (isset($e->response) ? $e->response : $e));
    }

    public function __toString(){
        return json_encode($this);
    }
}
