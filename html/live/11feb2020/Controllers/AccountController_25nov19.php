<?php

namespace App\Http\Controllers;

use App\User;
use App\UserCartItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Helpers\CustomHelper;
use DB;
use Validator;

use LaravelMsg91;


class AccountController extends Controller {
	
	/**
	 * URL: /account
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */

	private $fbAppId;
	private $fbAppSecret;

	public function __construct(){
		$this->middleware('guest');

		$this->fbAppId = '392770148048906';
		$this->fbAppSecret = '49e7e29d59cfc0ce44776bf7466167e0';

		/*$segments = request()->segments();

		prd($segments);*/

		$referer = (isset(request()->referer))?request()->referer:'';

		if(!empty($referer)){
			session(['referer'=>$referer]);
		}
	}

	public function index(){
		echo "index"; die;
	}

	private function getRedirectUrlAfterLogin(){
		$redirectUrl = url('users');

		$referer = (session()->has('referer'))?session('referer'):'';

		if(!empty($referer)){
			$redirectUrl = url($referer);
		}

		return $redirectUrl;
	}

	public function login(Request $request){

		$data = [];

		$referer = (isset($request->referer))?$request->referer:'';

		session(['referer'=>$referer]);

		$method = $request->method();

		if($method == 'POST'){

			//prd($request->toArray());

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

						$sessionToken = csrf_token();

						$userId = $user->id;

						$this->updateCartToUser($userId, $sessionToken);

						/*if(!empty($referer)){
							return redirect(url($referer));
						}
						return redirect(url('users'));*/

						return redirect($this->getRedirectUrlAfterLogin());
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


	/* ajax_login */
	public function ajaxLogin(Request $request){

		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST'){

			//prd($request->toArray());

			$rules = [];

			$rules['email'] = 'required|email';
			$rules['password'] = 'required|min:6';

			//$this->validate($request, $rules);

			$validator = Validator::make($request->all(), $rules);

			if($validator->passes()){

				$email = $request->email;
				$password = $request->password;
				$remember = (isset($request->remember))?$request->remember:'';

				$user_where = [];
				$user_where['email'] = $email;

				$user = User::where($user_where)->first();

				if(!empty($user) && count($user) > 0){

					if($user->status == 1){
						if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {

							$sessionToken = csrf_token();

							$userId = $user->id;

							$this->updateCartToUser($userId, $sessionToken);

							$response['success'] = true;
						}
						else{
							$errors['password'] = ['Invalid password'];
						}
					}
					else{
						$errors['email'] = 'Your account is not active, please contact administrator.';
					}
				}

				$errors['email'] = 'invalid credentials!';
			}
			else{
				$errors = $validator->errors();
			}
		}

		//prd($errors);

		$response['errors'] = $errors;

		return response()->json($response);
	}

	public function updateCartToUser($userId, $sessionToken){

		if(is_numeric($userId) && $userId > 0){

			$userCart = UserCartItem::where(['session_token'=>$sessionToken])->get();

			if(!empty($userCart) && count($userCart) > 0){
				foreach($userCart as $cart){
					$where = [];
					$where['user_id'] = $userId;
					$where['product_id'] = $cart->product_id;
					$where['size_id'] = $cart->size_id;
					$where['qty'] = $cart->qty;

					$existCount = UserCartItem::where($where)->count();

					if(empty($existCount) || $existCount == 0){
						$cart->user_id = $userId;

						$cart->save();
					}
				}
			}
		}

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
			$user->status = 1;

			//prd($user->toArray());

			$is_saved = 0;

			$is_saved = $user->save();

			if($is_saved){

				/*$email = $request->email;

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


				$is_mail = CustomHelper::sendEmail('emails.register_verify', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);*/

				/*$emailView = view('emails.register_verify', $email_data)->render();
				prd($emailView);*/

				/*if(!empty($referer)){
					return redirect(url('account/register?referer='.$referer))->with('alert-success', 'You have successfully registered, please check your email to verify your account.');
				}
				return redirect(url('account/register'))->with('alert-success', 'You have successfully registered, please check your email to verify your account.');*/

				return redirect(url('account/login?referer='.$referer))->with('alert-success', 'You have successfully registered, please login.');
			}
		}

		return view('account.register', $data);
	}


	/* ajax_register */
	public function ajaxRegister(Request $request){

		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST'){

			//prd($request->toArray());

			$rules = [];
			$validation_msg = [];
			$attributesArr = [];

			$rules['email'] = 'required|email|unique:users';
			$rules['password'] = 'required|min:6';
			$rules['phone'] = 'required|digits:10';
			$rules['gender'] = 'required';

			//$this->validate($request, $rules);

			$attributesArr['phone'] = 'mobile number';

			$validator = Validator::make($request->all(), $rules, $validation_msg);

			$validator->setAttributeNames($attributesArr);

			$phone = $request->phone;

			if(CustomHelper::isSmsGateway() ){

				if(isset($request->register) && $request->register == '1'){
					$otp = $request->otp;

					$validator->after(function($validator) use ($phone, $otp){

						$verifyOtpResult = [];

						$verifyOtpResult['message'] = 'otp_verified';
						$verifyOtpResult['type'] = 'success';

						$verifyOtpResult = (object)$verifyOtpResult;
						$verifyOtpResult = LaravelMsg91::verifyOtp($phone, $otp, ['raw' => true]);

					/*pr($phone);
					pr($otp);
					prd($verifyOtpResult);*/

					if(!isset($verifyOtpResult->type) || $verifyOtpResult->type != 'success'){
						$validator->errors()->add('otp', 'Invalid OTP.');
					}
					
				});
				}
			}

			if($validator->passes()){

				$referer = (isset($request->referer))?$request->referer:'';

				$user = new User;

				$verify_token = generateToken(40);

				if(isset($request->send_otp) && $request->send_otp == '1'){
					/*echo '$request->send_otp=';
					prd($request->toArray());*/

					$otpResult = [];

					$otpResult['message'] = '39687172335a323931333837';
					$otpResult['type'] = 'success';

					$otpResult = (object)$otpResult;

					$otp = randomNumber(6);
					//prd($otp);

					$otpResult = LaravelMsg91::sendOtp($phone, $otp);

					//prd($otpResult);

					/*stdClass Object
					(
					    [message] => 39687172335a323931333837
					    [type] => success
					)*/

					if(isset($otpResult->type) && $otpResult->type == 'success'){
						$response['success'] = true;

						$response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP has been sent. </div>';
					}

					//prd($otpResult);
				}
				elseif(isset($request->resend_otp) && $request->resend_otp == '1'){//register
					$otpResult = [];

					$otpResult['message'] = '39687172335a323931333837';
					$otpResult['type'] = 'success';

					$otpResult = (object)$otpResult;

					$otpResult = LaravelMsg91::resendOtp($phone);
					//prd($otpResult);

					if(isset($otpResult->type) && $otpResult->type == 'success'){
						$response['success'] = true;

						$response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP has been sent. </div>';
					}

					//prd($otpResult);
				}
				elseif(isset($request->register) && $request->register == '1'){
					/*echo '$request->register=';
					prd($request->toArray());*/

					$password = bcrypt($request->password);
					$otp = $request->otp;

					$role_id = 2;

					$user->role_id = $role_id;
					$user->email = $request->email;
					$user->phone = $request->phone;
					$user->password = $password;
					$user->gender = $request->gender;
					$user->verify_token = $verify_token;
					$user->referer = $referer;
					$user->status = 1;

				//prd($user->toArray());

					$is_saved = 0;

					$is_saved = $user->save();

					if($is_saved){
						$response['success'] = true;
					//$response['message'] = "you have registered successfully, please login.";
						$response['message'] = '<div class="alert alert-success"> you have registered successfully, please login. </div>';
					}

				}
				
			}
			else{
				$errors = $validator->errors();
			}
		}

		$response['errors'] = $errors;

		return response()->json($response);
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

				$is_mail = CustomHelper::sendEmail('emails.reset_password', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

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

	/* ajax_forgot */
	public function ajaxForgot(Request $request){

		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST'){

			$rules = [];

			$rules['email'] = 'required|email';

			//$this->validate($request, $rules);

			$validator = Validator::make($request->all(), $rules);

			if($validator->passes()){

				$msg_type = 'danger';

				$message = 'Please check your email';

				$email = $request->email;

				$user = User::where('email', $email)->first();

				$forgot_token = generateToken(40);

				if(!empty($email)){

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

					//$emailView = view('emails.reset_password', $email_data)->render();
					//prd($emailView);

					$is_mail = CustomHelper::sendEmail('emails.reset_password', $email_data, $to=$to_email, $from_email, $replyTo = $to_email, $subject);
					//prd($is_mail);

					if($is_mail && !empty($user) && count($user) > 0){

						$user->referer = $referer;
						$user->forgot_token = $forgot_token;

						$user->save();

						$response['success'] = true;

						$response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Reset password link has been sent to your email, please check. </div>';
					}
					else{
						$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> something went wrong, please try again. </div>';
					}
				}

			}
			else{
				$errors = $validator->errors();
			}

		}

		$response['errors'] = $errors;

		return response()->json($response);
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

					//$rules['email'] = 'required|email';
					$rules['password'] = 'required|min:6';
					$rules['confirm_password'] = 'required|same:password';

					$this->validate($request, $rules);

					$msg_type = 'danger';

					$message = 'Please check your email';

					$email = $request->email;

					//$user = User::where('email', $email)->first();

					$referer = (isset($user->referer))?$user->referer:'';

					//if($user->email == $email){

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

						return redirect(url('account/reset'))->with('alert-'.$msg_type, $message);
					//}
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


	// glogin
	public function googleLogin(Request $request){
		$google_redirect_url = route('account.gLogin');

		$applicationName = 'slumberjill';
		$clientId = '130273205107-bhvshfv6dtcag3fr2avlspusv8dqaspv.apps.googleusercontent.com';
		$clientSecret = 'Twzl0xdEX3UgqfHoHsF9_A4x';
		$developerKey = 'AIzaSyC5JmWS4pTdAmbJJ2p8Db-TXXuT8X5xzpc';

		$gClient = new \Google_Client();
		$gClient->setApplicationName($applicationName);
		$gClient->setClientId($clientId);
		$gClient->setClientSecret($clientSecret);
		$gClient->setRedirectUri($google_redirect_url);
		$gClient->setDeveloperKey($developerKey);
		$gClient->setScopes(array(
			'https://www.googleapis.com/auth/plus.me',
			'https://www.googleapis.com/auth/userinfo.email',
			'https://www.googleapis.com/auth/userinfo.profile',
		));
		$google_oauthV2 = new \Google_Service_Oauth2($gClient);
		if ($request->get('code')){
			$gClient->authenticate($request->get('code'));
			$request->session()->put('token', $gClient->getAccessToken());
		}
		if ($request->session()->get('token')){
			$gClient->setAccessToken($request->session()->get('token'));
				//pr($gClient->getAccessToken());
				//prd($request->session()->get('token'));
		}
		if ($gClient->getAccessToken()){
				//For logged in user, get details from google using access token
				//prd($google_oauthV2->userinfo->get());
			$guser = $google_oauthV2->userinfo->get();

			$googleId = $guser->id;
			$email = $guser->email;
			$name = trim($guser->givenName.' '.$guser->familyName);

					//$request->session()->put('name', $guser->name);

			$userQuery = User::where('google_id', $googleId);

			if(!empty($email)){
				$userQuery->orWhere('email', $email);
			}

			$user = $userQuery->first();

			$isAuthenticated = false;

			if( isset($user->id) && $user->id > 0 ){
				$user->glogin = 1;
				$user->status = 1;

				if(!empty($userName)){
					$user->name = $name;
				}

				if(!empty($googleId)){
					$user->google_id = $googleId;
				}

				if(!empty($email)){
					$user->email = $email;
				}

				$isSaved = $user->save();

				if($isSaved){
					$isAuthenticated = Auth::loginUsingId($user->id);
				}
			}
			else{
				$userData = [];

				$role_id = 2;

				$userData['role_id'] = $role_id;
				$userData['name'] = $name;
				$userData['email'] = $email;
				$userData['glogin'] = 1;
				$userData['google_id'] = $googleId;
				$userData['status'] = 1;

				$user = User::create($userData);

				if($user){
					$isAuthenticated = Auth::loginUsingId($user->id);
				}
			}

			if($isAuthenticated){
				return redirect($this->getRedirectUrlAfterLogin());
			}
			else{
				return redirect(url('account/login'))->withInput()->with('alert-danger', 'something went wrong, please try again...');
			}

		}
		else{
				//For Guest user, get google login url
			$authUrl = $gClient->createAuthUrl();
			return redirect()->to($authUrl);
		}
	}


	// fblogin
	public function fbLogin(Request $request){

		//$clientToken = '';

		$redirectURL = url('account/fbcallback');

		$fbPermissions = ['email'];

		$fb = new \Facebook\Facebook([
			'app_id' => $this->fbAppId,
			'app_secret' => $this->fbAppSecret,
			'default_graph_version' => 'v2.10',
			//'default_access_token' => $accessToken, // optional
		]);

		// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
		//   $helper = $fb->getRedirectLoginHelper();
		//   $helper = $fb->getJavaScriptHelper();
		//   $helper = $fb->getCanvasHelper();
		//   $helper = $fb->getPageTabHelper();

		$helper = $fb->getRedirectLoginHelper();

		$loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);

		return redirect($loginURL);

	}

	public function fbCallback(Request $request){

		//prd($request->toArray());

		$referer = (session()->has('referer'))?session('referer'):'';

		$state = (isset($request->state))?$request->state:'';

		//$this->helper->getPersistentDataHandler()->set('state', $request->state);		

		//session(['state' => $state]);

		$fb = new \Facebook\Facebook([
			'app_id' => $this->fbAppId,
			'app_secret' => $this->fbAppSecret,
			'default_graph_version' => 'v2.10',
			//'default_access_token' => $accessToken, // optional
		]);

		// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
		//   $helper = $fb->getRedirectLoginHelper();
		//   $helper = $fb->getJavaScriptHelper();
		//   $helper = $fb->getCanvasHelper();
		//   $helper = $fb->getPageTabHelper();

		$helper = $fb->getRedirectLoginHelper();

		$helper->getPersistentDataHandler()->set('state', $_GET['state']);

		$accessToken = '';

		if (session()->has('facebook_access_token')) {
			$accessToken = session('facebook_access_token');
		} else {
			$accessToken = $helper->getAccessToken();

			//session(['facebook_access_token' => $accessToken]);
		}

		//prd($accessToken);

		$response = '';

		if (isset($accessToken) && !empty($accessToken)) {

				try {
		  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
		  // If you provided a 'default_access_token', the '{access-token}' is optional.
			//$response = $fb->get('/me', '{access-token}');
					$response = $fb->get('/me?fields=id,name,email', $accessToken);
				} catch(\Facebook\Exceptions\FacebookResponseException $e) {
  			// When Graph returns an error
					echo 'Graph returned an error: ' . $e->getMessage();
					exit;
				} catch(\Facebook\Exceptions\FacebookSDKException $e) {
  			// When validation fails or other local issues
					echo 'Facebook SDK returned an error: ' . $e->getMessage();
					exit;
				}
			}

			$graphUser = $response->getGraphUser();

			/*pr($graphUser->getId());
			pr($graphUser->getName());
			pr($graphUser->getEmail());

			prd($graphUser);*/
			if($graphUser->getId() && !empty($graphUser->getId()) ){

				//prd($graphUser);

				$fbId = $graphUser->getId();

				$name = $graphUser->getName();
				$email = $graphUser->getEmail();

				$isAuthenticated = false;

				$userQuery = User::where('fb_id', $fbId);

				if(!empty($email)){
					$userQuery->orWhere('email', $email);
				}

				$user = $userQuery->first();

				if(isset($user->id) && $user->id > 0){
					$user->fblogin = 1;
					$user->status = 1;

					if(!empty($name)){
						$user->name = $name;
					}

					if(!empty($email)){
						$user->email = $email;
					}

					if(!empty($fbId)){
						$user->fb_id = $fbId;
					}

					$isSaved = $user->save();

					if($isSaved){
						$isAuthenticated = Auth::loginUsingId($user->id);
					}
				}
				else{
					$userData = [];

					$role_id = 2;

					$userData['role_id'] = $role_id;
					$userData['name'] = $name;
					$userData['email'] = $email;
					$userData['fblogin'] = 1;
					$userData['fb_id'] = $fbId;
					$userData['status'] = 1;

					$user = User::create($userData);

					if($user){
						$isAuthenticated = Auth::loginUsingId($user->id);
					}
				}

				if($isAuthenticated){
						//$authUser = auth()->user();
						//prd($authUser->toArray());
					/*if(!empty($referer)){
						return redirect(url($referer));
					}
					return redirect(url('users'));*/

					return redirect($this->getRedirectUrlAfterLogin());
				}
			}
			//echo 'Logged in as ' . $me->getName();

			return redirect(url('account/login'))->with('alert-danger', 'something went wrong please try again.');	

	}




/* end of controller */
}
