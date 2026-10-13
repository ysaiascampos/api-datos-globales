<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

use Firebase\JWT\JWT;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('APISYSTEM', function ($name) {

            $api_env_prefix = "APISYSTEM_${name}";

            $responseTimeout = env("${api_env_prefix}_RESPONSE_TIMEOUT", 30);
            $connectTimeout  = env("${api_env_prefix}_CONNECT_TIMEOUT", 10);

            $http = Http::throw()
                ->timeout($responseTimeout)
                ->connectTimeout($connectTimeout)
                ->baseUrl(env("${api_env_prefix}_URL"));

            // Siempre se debe enviar Api-Token (con o sin kong)
            $http->withHeaders([
                'Api-Token' => env("${api_env_prefix}_HEADER_TOKEN"),
            ]);
            if(env("${api_env_prefix}_JWT_SECRET", null) !== null){
                $http->withToken(JWT::encode(
                    [
                      "iss" => env("${api_env_prefix}_JWT_KEY"),
                      "exp" => time()+env("${api_env_prefix}_JWT_EXP", 600)
                    ],
                    base64_decode(env("${api_env_prefix}_JWT_SECRET")),
                    'HS256'
                ));
            }
            return $http;
        });

        $this->app['view']->addNamespace('l5-swagger', base_path('resources/views/vendor/l5-swagger'));
    }

}
