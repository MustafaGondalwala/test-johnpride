<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Customer;
use App\Order;
use App\Invoice;
use App\Coupon;
use App\Product;
use App\Category;
use App\Attribute;
use App\Inventory;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;

use DB;
use Hash;

class HomeController extends Controller {

    // Dashboard - URL: /admin
    public function index(){
        //echo "hello";
        // Stats
        $stats = [];

        return view('admin.index', compact('stats'));
    }


    public function verify_old_password(Request $request){


        $back_url = (isset($request->back_url))?$request->back_url:'';

        if(!empty($back_url)){

            if($request->method() == 'POST'){
                prd($request);

                $auth_user = auth()->guard('admin')->user();

                $message = [];
                $rules = [];

                $rules['password'] = 'required';

                $validator = Validator::make($request->all(), $rules, $message);

                $validator->after(function($validator) use ($auth_user){
                    if (!Hash::check(request('password'), $auth_user->password)){
                        $validator->errors()->add('password', 'Password did not matched!');
                    }
                    else{
                        session(['verify_password'=>TRUE, 'verify_time'=>date('Y-m-d H:i:s')]);
                    }
                });

                if ($validator->fails()){
                    return back()->withErrors($validator);
                }
                elseif(!empty($back_url)){
                    return redirect(url($back_url));
                }
                else{
                    return back()->with('success', 'Password has been verified!');
                }
            }
            return view('admin.verify_password');
        }
        else{
            return back();
        }

    }

    public function verify_password(Request $request){

            if($request->method() == 'POST'){
                //prd($request->toArray());

                $auth_user = auth()->guard('admin')->user();

                $message = [];
                $rules = [];

                $rules['password'] = 'required';

                $validator = Validator::make($request->all(), $rules, $message);

                $validator->after(function($validator) use ($auth_user){
                    if (!Hash::check(request('password'), $auth_user->password)){
                        $validator->errors()->add('password', 'Password did not matched!');
                    }
                    else{
                        session(['verify_password'=>TRUE, 'verify_time'=>date('Y-m-d H:i:s')]);
                    }
                });

                if ($validator->fails()){
                    return back()->withErrors($validator);
                }
                elseif(!empty($back_url)){
                    return redirect(url($back_url));
                }
                else{
                    return back()->with('success', 'Password has been verified!');
                }
            }
            else{
                return back();
            }

    }


    /* end of controller */
}
