<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Helpers\CustomHelper;
use DB;
use Validator;


class AccountController extends Controller {
    
    /**
     * Homepage
     * URL: /
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function __construct(){
    	$this->middleware('guest');

        /*$segments = request()->segments();

        prd($segments);*/
    }

    public function index(){
        echo "index"; die;
    }

    public function login(Request $request){

        $data = [];

        $method = $request->method();

        if($method == 'POST'){

            //prd($request->toArray());
            $referer = (isset($request->referer))?$request->referer:'';

            $rules = [];

            $rules['email'] = 'required|email';
            $rules['password'] = 'required|min:6';

            $this->validate($request, $rules);

            $email = $request->email;
            $password = $request->password;
            $remember = (isset($request->remember))?$request->remember:'';

            $user_where = [];
            $user_where['email'] = $email;

            $user = User::where($user_where)->first();
            
            if(!empty($user) && count($user) > 0){

                if($user->status == 1){
                    if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {

                        if(!empty($referer)){
                            return redirect(url($referer));
                        }
                        return redirect(url('users'));
                    }
                }
                else{
                    return back()->withInput()->with('alert-danger', 'Your account is not active, please contact administrator.');
                }
            }

          return back()->withInput()->with('alert-danger', 'invalid credentials!');
        }

        $data['meta_title'] = 'SlumberJill - Login';

        return view('account.login', $data);
    }

    public function register(Request $request){

        $data = [];

        $method = $request->method();

        if($method == 'POST'){

            $rules = [];

            $rules['email'] = 'required|email|unique:users';
            $rules['password'] = 'required|min:6';
            $rules['gender'] = 'required';

            $this->validate($request, $rules);

            $referer = (isset($request->referer))?$request->referer:'';

            $user = new User;

            $verify_token = generateToken(40);

            $password = bcrypt($request->password);

            $role_id = 2;

            $user->role_id = $role_id;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = $password;
            $user->gender = $request->gender;
            $user->verify_token = $verify_token;
            $user->referer = $referer;

            //prd($user->toArray());

            $is_saved = 0;

            $is_saved = $user->save();

            if($is_saved){

                $email = $request->email;

                $verify_token = $user->verify_token;

                $to_email = $email;

                $subject = 'Verify account - SlumberJill';
                
                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                if(empty($ADMIN_EMAIL)){
                    $ADMIN_EMAIL = config('custom.admin_email');
                }

                $from_email = $ADMIN_EMAIL;

                $verify_link = '<a href="'.url('account/verify?t='.$verify_token).'">Click here to verify</a>';

                $email_data = [];
                $email_data['email'] = $email;
                $email_data['verify_link'] = $verify_link;


                $is_mail = CustomHelper::SendMail('emails.register_verify', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

                /*$emailView = view('emails.register_verify', $email_data)->render();
                prd($emailView);*/

                if(!empty($referer)){
                    return redirect(url('account/register?referer='.$referer))->with('alert-success', 'You have successfully registered, please check your email to verify your account.');
                }
                return redirect(url('account/register'))->with('alert-success', 'You have successfully registered, please check your email to verify your account.');
            }
        }

        return view('account.register', $data);
    }

    public function verify(Request $request){

        $data = [];

        $isVerified = false;

        $token = (isset($request->t))?$request->t:'';

        $referer = '';

        if(!empty($token)){
            $user = User::where('verify_token', $token)->first();

            if(!empty($user) && count($user) > 0){
                //prd($user->toArray());
                $user->verify_token = '';
                $user->status = 1;
                $user->save();

                $isVerified = true;

                $referer = (isset($user->referer))?$user->referer:'';
            }
        }

        $data['isVerified'] = $isVerified;
        $data['referer'] = $referer;


        return view('account.verify', $data);
    }

    public function forgot(Request $request){

        $data = [];

        $method = $request->method();

        if($method == 'POST'){

            $rules = [];

            $rules['email'] = 'required|email';

            $this->validate($request, $rules);

            $msg_type = 'danger';

            $message = 'Please check your email';

            $email = $request->email;

            $user = User::where('email', $email)->first();

            $forgot_token = generateToken(40);

            if($email){

                $referer = (isset($request->referer))?$request->referer:'';

                $email = $request->email;

                $to_email = $email;

                $subject = 'Reset password - SlumberJill';
                
                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                if(empty($ADMIN_EMAIL)){
                    $ADMIN_EMAIL = config('custom.admin_email');
                }

                $from_email = $ADMIN_EMAIL;

                $reset_link = '<a href="'.url('account/reset?t='.$forgot_token).'">Click here to reset password</a>';

                $email_data = [];
                $email_data['reset_link'] = $reset_link;


                $is_mail = CustomHelper::SendMail('emails.reset_password', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

                if($is_mail && !empty($user) && count($user) > 0){

                    $user->referer = $referer;
                    $user->forgot_token = $forgot_token;

                    $user->save();

                    $msg_type = 'success';

                    $message = 'Reset password link has been sent to your email, please check.';
                }

                /*$emailView = view('emails.reset_password', $email_data)->render();
                prd($emailView);*/

                if(!empty($referer)){
                    return redirect(url('account/forgot?referer='.$referer))->with('alert-'.$msg_type, $message);
                }

                return redirect(url('account/forgot'))->with('alert-'.$msg_type, $message);
            }
        }

        return view('account.forgot', $data);
    }

    public function reset(Request $request){

        $data = [];

        $isVerified = false;
        $isValidToken = false;

        $token = (isset($request->t))?$request->t:'';

        if(!empty($token)){

            $user = User::where('forgot_token', $token)->first();

            if(!empty($user) && count($user) > 0){

                $isValidToken = true;

                $method = $request->method();

                if($method == 'POST'){

                    $rules = [];

                    $rules['email'] = 'required|email';
                    $rules['password'] = 'required|min:6';
                    $rules['confirm_password'] = 'required|same:password';

                    $this->validate($request, $rules);

                    $msg_type = 'danger';

                    $message = 'Please check your email';

                    $email = $request->email;

                    $user = User::where('email', $email)->first();

                    $referer = (isset($user->referer))?$user->referer:'';

                    $forgot_token = generateToken(40);

                    if($user->email == $email){

                        //prd($user->toArray());

                        $password = bcrypt($request->password);

                        $user->password = $password;
                        $user->forgot_token = '';

                        $isSaved = $user->save();

                        if($isSaved){
                            $msg_type = 'success';
                            $message = 'Your password has been updated successfully, please login.';
                        }

                        if(!empty($referer)){
                            return redirect(url('account/login?referer='.$referer))->with('alert-'.$msg_type, $message);
                        }

                        return redirect(url('account/login'))->with('alert-'.$msg_type, $message);
                    }
                }
            }

            

            /*$user = User::where('verify_token', $token)->first();

            if(!empty($user) && count($user) > 0){
                //prd($user->toArray());
                $user->verify_token = '';
                $user->save();

                $isVerified = true;
            }*/
        }

        $data['isVerified'] = $isVerified;
        $data['isValidToken'] = $isValidToken;


        return view('account.reset', $data);
    }


/* end of controller */
}
