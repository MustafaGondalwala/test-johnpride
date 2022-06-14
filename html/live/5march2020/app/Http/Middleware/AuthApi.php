<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    
    public function handle($request, Closure $next, $guard = null)
    {
        $api_token = config('custom.api_token');
        //$api_token = 'd4db8ddc820037e284ee19a11bfb72a8';
        $header_api_token = ($request->header('api-token'))?$request->header('api-token'):'';

        

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
// cache for 1 day
            header('Access-Control-Max-Age: 86400');
        }

// Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }

        //prd($request->header());


        if($api_token == $header_api_token){
            return $next($request);
        }
        else{
            return response()->json(['result'=>false, 'error'=>'Unauthorized access.']);
        }

        //prd($request->header('api-token'));        
    }
}
