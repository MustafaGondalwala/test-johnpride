<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

use DB;

class AuthenticateAdmin
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
        //prd($request->method());
        //echo "<pre>"; print_r(auth()->user()); die;

        //$user_id = auth()->user()->id;

        //prd(auth()->user()->toArray());


        if($request->session()->has('impersonate'))
        {
            $user = DB::table('users')->where('id', $request->session()->get('impersonate'))->first();
            //prd($user);

            if(isset($user->type) && $user->type == 'admin'){
                $onceUsingId = Auth::onceUsingId($request->session()->get('impersonate'));
            }
        }

        /*if(!$request->session()->has('product_total_order'))
        {
            CustomHelper::UpdateProductTotalOrder();
        }*/


        //DB::table('role_user')
        //if (Auth::guard($guard)->guest() || auth()->user()->type != 'admin') {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/admin/login');
            }
        }

        if($guard != 'api'){
            if(auth()->user()->type == 'User' || auth()->user()->type == 'user'){
                Auth::logout();
                return redirect('/');
            }
        }
		
        return $next($request);
    }
}
