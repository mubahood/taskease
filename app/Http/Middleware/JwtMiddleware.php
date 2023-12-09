<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Support\Str;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $except = [
        'login',
        'register',
        'min/login',
    ];

    public function handle($request, Closure $next)
    {
        if (!$request->expectsJson()) {
            return $next($request);
        }


        if (
            Str::contains($_SERVER['REQUEST_URI'], 'login') ||
            Str::contains($_SERVER['REQUEST_URI'], 'register')
        ) {
            return $next($request);
        }

        // If request starts with api then we will check for token
        if (!$request->is('api/*')) {
            return $next($request);
        }

        //$request->headers->set('Authorization', $headers['authorization']);// set header in request
        try {
            //$headers = apache_request_headers(); //get header
            $headers = getallheaders(); //get header

            $Authorization = "";
            $Authorization = $headers['tok'];
            if (isset($headers['Authorization']) && $headers['Authorization'] != "") {
                $Authorization = $headers['Authorization'];
            } else if (isset($headers['authorization']) && $headers['authorization'] != "") {
                $Authorization = $headers['authorization'];
            } else if (isset($headers['Authorizations']) && $headers['Authorizations'] != "") {
                $Authorization = $headers['Authorizations'];
            } else if (isset($headers['authorizations']) && $headers['authorizations'] != "") {
                $Authorization = $headers['authorizations'];
            } else if (isset($headers['tok']) && $headers['tok'] != "") {
                $Authorization = $headers['tok'];
            }

            var_dump($Authorization);
            die();

            $request->headers->set('Authorization', $Authorization); // set header in request
            $request->headers->set('authorization', $Authorization); // set header in request

            $user = FacadesJWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired']);
            } else {
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
