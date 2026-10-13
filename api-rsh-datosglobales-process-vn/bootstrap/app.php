<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //Siempre retornar json
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            \Log::error("Exception Route(" . session()->getId() . "): " . $request->fullUrl());
            return true;
        });

        $exceptions->respond(function (Response $response) {
            $dataToSet = json_decode($response->getContent());
            \Log::error("Exception Data(" . session()->getId() ."): " . json_encode($dataToSet,JSON_PRETTY_PRINT));
            unset($dataToSet->trace);
            return response()->json(            [
                'code' => 1499,
                'message' => 'Error no capturado en controlador (' . env('APP_NAME') .')',
                'time' => (new \DateTime())->format('Y-m-d H:i:s.u'),
                'data' => $dataToSet

            ], 404);
        });
    })->create();
