<?php

namespace App\Http\Controllers\Admin;   

use File;
use Image;
use Storage;
use Validator;
use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Hash;

class AdminController extends Controller {

    public function index(Request $request) {

        $auth_user = auth()->guard('admin')->user();
        $admin_id = $auth_user->id;

        $method = $request->method();

        //prd($method);

        if($method == 'POST' || $method =="post"){
            $post_data = $request->all();
            $rules = [];

            $rules['old_password'] = 'required|min:6|max:20';
            $rules['new_password'] = 'required|min:6|max:20';
            $rules['confirm_password'] = 'required|min:6|max:20|same:new_password';

            $validator = Validator::make($post_data, $rules);

            if($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }
            else{
                //prd($request->all());

                $old_password = $post_data['old_password'];

                $user = Admin::where(['id'=>$admin_id])->first();

                $existing_password = (isset($user->password))?$user->password:'';

                $hash_chack = Hash::check($old_password, $user->password);

                if($hash_chack){
                    $update_data['password']=bcrypt(trim($post_data['new_password']));

                    $is_updated = DB::table('admins')->where('id', $admin_id)->update($update_data);

                    $message = [];

                    if($is_updated){

                        $message['alert-success'] = "Password updated successfully.";
                    }
                    else{
                        $message['alert-danger'] = "something went wrong, please try again later...";
                    }
                    
                    return back()->with($message);


                }
                else{
                    $validator = Validator::make($post_data, []);
                    $validator->after(function ($validator) {
                        $validator->errors()->add('old_password', 'Invalid Password!');
                    });
                    //prd($validator->errors());
                    return back()->withErrors($validator)->withInput();
                }
            }
        }
        
        $data = [];
        $data['title'] = 'Change Password';
        $data['heading'] = 'Change Password';

        return view('admin.change_password', $data);
    }

    /* End of controller */
}