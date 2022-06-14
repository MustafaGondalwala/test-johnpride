<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class SetLanguage
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
        $locale_lang = 'en';

        $sess_lang = '';

        if(session()->has('locale_lang')){
            $sess_lang = session('locale_lang');
        }
        elseif($request->has('locale_lang')){
            $sess_lang = $request->locale_lang;
        }

        if(!empty($sess_lang)){
            $locale_lang = $sess_lang;
        }

        //echo $locale_lang; die;

        app()->setLocale($locale_lang);

        return $next($request);       
    }
}
