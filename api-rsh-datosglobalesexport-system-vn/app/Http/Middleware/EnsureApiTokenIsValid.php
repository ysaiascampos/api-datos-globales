<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(env("API_SERVER_HEADER_TOKENS") != null){
            $tokens = explode(",", env("API_SERVER_HEADER_TOKENS"));
            foreach($tokens as $token){
                if($token === $request->header("Api-Token")){
                    return $next($request);
                }
            }
            return response()->json(['error' => 'Unauthorized.'], 401);

        }
        else {
            return response()->json(['error' => 'API Server Header Token no configurado en servidor.'], 401);
        }
    }
}
