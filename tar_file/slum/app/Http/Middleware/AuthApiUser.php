<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

use DB;

class AuthApiUser
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
        //prd($request->all());
        //$api_token = 'd4db8ddc820037e284ee19a11bfb72a8';
        $header_session_token = ($request->header('session-token'))?$request->header('session-token'):'';
        $user_id = ($request->has('user_id'))?$request->user_id:'';

        if(empty($user_id) || empty($header_session_token)){
            return response()->json(['result'=>false, 'error'=>'User ID or User Session Token not found!']);
        }
        else if(is_numeric($user_id)){
            $user = DB::table('users')->where(['id'=>$user_id, 'session_token'=>$header_session_token])->select('id', 'session_token')->first();

            //prd($user);

            if(!empty($user) && count($user) > 0 && $user->id == $user_id && $user->session_token == $header_session_token){
                return $next($request);
            }
            else{
                return response()->json(['result'=>false, 'error'=>'Invalid user_id or session-token!']);
            }
        }

        //prd($request->header('api-token'));        
    }
}
