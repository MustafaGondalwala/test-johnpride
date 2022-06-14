<?php

namespace App\Http\Controllers;

use App\User;
use App\TempUser;
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

		//$this->fbAppId = '392770148048906';
		//$this->fbAppSecret = '49e7e29d59cfc0ce44776bf7466167e0';
		
		//$this->fbAppId = '2408922386071066'; // 5-aug-2020 - Surendra sir
		//$this->fbAppSecret = 'e81d188088f5043c4964665f19b66239'; // 5-aug-2020

		$this->fbAppId = '465270991303584'; // 6-oct-2020 - Surendra sir
		$this->fbAppSecret = '7649f1394d14ffc38b520cdf7dd9e88f'; // 6-oct-2020

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

		$data['meta_title'] = 'Johnpride - Login';

		return view('account.login', $data);
	}


	/* ajax_login */
	public function ajaxLogin(Request $request){;

		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST')
		{
			$email = $request->email;

			if(is_numeric($email))
			{
				if(strlen($email) == 10)
				{
					//**** VERIFY WITH PHONE OTP *****//
					$otp = $request->otp;

					// CHECK PHONE IN DATABASE
					$is_exist_check = User::where('phone',$email)->where('is_verified',1)->first();

					if(isset($is_exist_check) && !empty($is_exist_check))
					{
						$user_otp = $is_exist_check->login_otp ? $is_exist_check->login_otp : '';

						if(isset($user_otp) && !empty($user_otp))
						{

							if($user_otp == $otp)
							{
								Auth::login($is_exist_check);

								$sessionToken = csrf_token();

								$userId = $is_exist_check->id;

								$this->updateCartToUser($userId, $sessionToken);

								$response['success'] = true;

							}
							else
							{
								$response['success'] = false;

								$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP does not match. </div>';

							}


						}
						else
						{
							$response['success'] = false;

							$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Please enter OTP. </div>';
						}



					}
					else
					{
						$response['success'] = false;
						$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> This user does not exist in our system. </div>';
					}


				}
			}
			else
			{
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


		
			
		}

		//prd($errors);

		$response['errors'] = $errors;

		return response()->json($response);
	}



	public function ajax_login_otp_send(Request $request){

		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST')
		{
			$email_phone = $request->email_phone;

			if(!empty($email_phone))
			{ 
				if(is_numeric($email_phone))
				{
					if(strlen($email_phone) == 10)
					{
						// THIS IS PHONE

						$is_exist_check = User::where('phone',$email_phone)->where('is_verified',1)->first();

						if(isset($is_exist_check) && !empty($is_exist_check))
						{
							 $res = $this->send_otp_on_phone($email_phone);

							 if($res){
							 	 $response['success'] = true;
					   			  $response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP sent successfully to your phone no.  </div>';		
							 }
							 else
							 {
							 	$response['success'] = false;
					     	$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> There is problem in sending OTP </div>';	
							 }

						}
						else
						{
						 $response['success'] = false;
					     $response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> This user does not exist in our system  </div>';		
						}



		  			 
					}
					else
					{
						$response['success'] = false;
					    $response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Please input 10 digit number  </div>';	
					}
				}
				else
				{
					$response['success'] = false;
					$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Please input only number  </div>';	
				}

			}

			else
			{
				$response['success'] = false;
				$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Please enter Phone  </div>';
			}


		}

		return response()->json($response);
	

	}


	private function send_otp_on_phone($phone)
	{
		 if($phone)
		 {
		 	$otp = randomNumber(6);
			$smsMessage = $otp." is your OTP. Thanks Johnpride Team";
			$urlencodeMessage = urlencode($smsMessage);

		  $template_id = 	'1507163877289365078';
		  $request_type= 'login_otp';
		  $isSendSMS = CustomHelper::sendSMS($phone,$urlencodeMessage,$template_id,$otp,$request_type);
		  if($isSendSMS == 200)
		  {

		  	// UPDATE in user table
		  	$update_data = array("login_otp"=>$otp);
		  	$res = DB::table('users')
        	->where('phone', $phone)  
        	->limit(1)  
        	->update($update_data);  

        	if($res)
        	{

        		return true;
        	}
        	else
        	{
        		return false;
        	}
		  }
		  else
		  {
		  	return false;
		  }
	  }

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

				$subject = 'Verify account - Johnpride';
				
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



	public function ajaxSendOtp(Request $request)
	{
		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST')
		{
			$rules['phone'] = 'required|digits:10';

			$validator = Validator::make($request->all(), $rules);

			if($validator->passes())
			{	
				$phone = $request->phone;
				$type = $request->type;

				if($phone)
				{
					if(is_numeric($phone))
					{
					  if(strlen($phone) == 10)
						 {
						 	$is_exist_check = User::where('phone',$phone)->where('is_verified',1)->first();

						 	if(isset($is_exist_check) && !empty($is_exist_check))
							{
								// $response['success'] = false;
								// $response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> This number is already exist  </div>';

								// ******** THIS CASE UESD FOR LOGIN *******//

								$otp = randomNumber(4);
								$smsMessage = $otp." is your OTP. Thanks Johnpride Team";
								$urlencodeMessage = urlencode($smsMessage);

							   $template_id = 	'1507163877289365078';
								$request_type= 'login_otp';
								$isSendSMS = CustomHelper::sendSMS($phone,$urlencodeMessage,$template_id,$otp,$request_type);
								//$isSendSMS = 200;

								if($isSendSMS ==200)
								{

									$updated = '';
									$is_temp_user_exist = TempUser::where('phone',$phone)->first();

									if(isset($is_temp_user_exist) && !empty($is_temp_user_exist))	
									{
										$exist_phone = $is_temp_user_exist->phone;
										$update_data =  array("otp"=>$otp);

										$updated = TempUser::where('phone',$exist_phone)->update($update_data);
									}


									$response['success'] = true;
									$response['type'] = 'login';
									$response['phone'] = $phone;
									$response['message'] = 'Sent to '.$phone;
								}
								else
								{
									$response['success'] = false;
									$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> There is Error in sending SMS !  Please Contact with administrator </div>';
								}

							}
							else
							{

								$otp = randomNumber(4);
								$smsMessage = $otp." is your OTP. Thanks Johnpride Team";
								$urlencodeMessage = urlencode($smsMessage);

								$template_id = 	'1507163877289365078';
								$request_type= 'registration_otp';
								$isSendSMS = CustomHelper::sendSMS($phone,$urlencodeMessage,$template_id,$otp,$request_type);
								//$isSendSMS = 200;
								if($isSendSMS == 200)
								{
									//session(['otp'=>$otp]);

									$response['success'] = true;
									$response['type'] = 'register';
									$response['phone'] = $phone;
									$response['message'] = 'Sent to '.$phone;

									/*** CHECK IN TEMP USER TABLE IF EXIST THEN UPDATE OTP *****/
									$updated = '';
									$is_temp_user_exist = TempUser::where('phone',$phone)->first();

									if(isset($is_temp_user_exist) && !empty($is_temp_user_exist))	
									{
										$exist_phone = $is_temp_user_exist->phone;
										$update_data =  array("otp"=>$otp);
										$updated = TempUser::where('phone',$exist_phone)->update($update_data);
									}
									else
									{
											/****** INSERT INTO TEMP USER TABLE ******/	
											$dbInsert = [];
											$dbInsert['phone'] = $phone;
											$dbInsert['otp'] = $otp;
											$updated = TempUser::insert($dbInsert);
									}

									if($updated)
									{
										$response['success'] = true;
										$response['message'] = 'Sent to '.$phone;
									}

								}
								else
								{
									$response['success'] = false;
									$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> There is Error in sending SMS !  Please Contact with administrator </div>';
								}
							}

						}

					  else
					  {
					  	$response['success'] = false;
						$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Mobile no. should be 10 digits  </div>';
					  }
					}

					else
					{

						$response['success'] = false;
						$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Mobile no. should be numeric  </div>';

					}


				}

			}
			else
			{
				$errors = $validator->errors();
			}



		}


	  $response['errors'] = $errors;

	  return response()->json($response);

}


public function ajaxVerifyOtp(Request $request)
	{
		$response = [];
		$response['success'] = false;

		$errors = [];

		$method = $request->method();

		if($method == 'POST')
		{
			$rules['verify_phone'] = 'required|digits:10';
			//$rules['verify_phone'] = 'required|digits:10';

			$validator = Validator::make($request->all(), $rules);

			if($validator->passes())
			{	
				$phone = $request->verify_phone;
				$login_type = $request->login_type;

				$digit1 = $request->digit1;
				$digit2 = $request->digit2;
				$digit3 = $request->digit3;
				$digit4 = $request->digit4;


				$user_otp = $digit1.$digit2.$digit3.$digit4;
				//prd($user_otp);

				if($phone)
				{
					if(is_numeric($phone))
					{
					  if(strlen($phone) == 10)
						 {
						 	//$is_exist_check = User::where('phone',$phone)->where('is_verified',1)->first();

						 	$is_exist_check = TempUser::where('phone',$phone)->first();

						 	if(isset($is_exist_check) && !empty($is_exist_check))
							{
								$otp = $is_exist_check->otp;
								$is_moved = 0;
								if($otp==$user_otp)
								{
									// Logged in OR Register user
									if($login_type == 'register')
									{
										// MOVE USER IN TO MAIN USER

									$user_exist = User::where('phone',$phone)->first();
									if(isset($user_exist) && !empty($user_exist)) 
										{
											$update_user=[];
											$update_user['is_verified'] = 1;
											$update_user['status'] = 1;
											$updated = User::where('phone',$phone)->update($update_user);
										}
										else
										{
											$dbInsert = [];
											$dbInsert['phone'] = $phone;
											$dbInsert['otp'] = $otp;
											$dbInsert['is_verified'] = 1;
											$dbInsert['status'] = 1;
											$updated = User::insert($dbInsert);
										}


										

										$mainUserData = User::where('phone',$phone)->first();

										Auth::login($mainUserData);	

									  $update_to_main = TempUser::where('phone',$phone)->update(array("is_moved_to_main"=>1));

										$is_moved = 1;
									}

									else if($login_type == 'login')
									{	
										$mainUserData = User::where('phone',$phone)->first();
										Auth::login($mainUserData);	
										$is_moved = 1;
									}	


									if($is_moved)
									{
										$response['success'] = true;

										// if($login_type == 'register')
										// {
										// 	$response['redirect_url'] = url('users/update');
										// }
	
									}
									else
									{
										$response['success'] = false;
										$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> There is problem in login. </div>';
									}

								}
								else
								{
									$response['success'] = false;
									$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP does not match </div>';
								}
							}
							else
							{
								$response['success'] = false;
								$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> This user does not exist in our system. </div>';
							}
							

						}

					  else
					  {
					  	$response['success'] = false;
						$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Mobile no. should be 10 digits  </div>';
					  }
					}

					else
					{
						$response['success'] = false;
						$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Mobile no. should be numeric  </div>';

					}


				}

			}
			else
			{
				$errors = $validator->errors();
			}



		}


	  $response['errors'] = $errors;

	  return response()->json($response);

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

			$rules['name'] = 'required';
			$rules['email'] = 'required';
			$rules['password'] = 'required|min:6';
			$rules['phone'] = 'required|digits:10';
			//$rules['gender'] = 'required';

			//$this->validate($request, $rules);

			$attributesArr['phone'] = 'mobile number';

			$validator = Validator::make($request->all(), $rules, $validation_msg);

			$validator->setAttributeNames($attributesArr);

			$email = $request->email;
			$phone = $request->phone;

			/*if(CustomHelper::isSmsGateway() ){

				if(isset($request->register) && $request->register == '1'){
					$otp = $request->otp;

					$validator->after(function($validator) use ($phone, $otp){

					$verifyOtpResult = [];
					$verifyOtpResult = LaravelMsg91::verifyOtp($phone, $otp, ['raw' => true]);

					if(!isset($verifyOtpResult->type) || $verifyOtpResult->type != 'success'){
						$validator->errors()->add('otp', 'Invalid OTP.');
					}
					
				});
				}
			}*/



			if(isset($request->register) && $request->register == '1')
			{
				$otp = $request->otp;

				$validator->after(function($validator) use ($phone, $otp){

				$session_otp = (session()->has('otp'))?session('otp'):'';
				
				if($otp=='' || !($otp!='' && $otp==$session_otp)){
					$validator->errors()->add('otp', 'Invalid OTP.');
				}
				
			});
			}
			

			if($validator->passes()){

				$referer = (isset($request->referer))?$request->referer:'';

				$user = new User;

				$verify_token = generateToken(40);

				if(isset($request->send_otp) && $request->send_otp == '1')
				{
					/*echo '$request->send_otp=';
					prd($request->toArray());*/

					$otpResult = [];

					//$otpResult['message'] = '39687172335a323931333837';
					//$otpResult['type'] = 'success';

					//$otpResult = (object)$otpResult;

					$otp = randomNumber(6);
					$smsMessage = $otp." is your OTP. Thanks Johnpride Team";
					$urlencodeMessage = urlencode($smsMessage);
				

if($phone)
{
   if(is_numeric($phone))
	{
      if(strlen($phone) == 10)
	   {
	 	$is_exist_check = User::where('phone',$phone)->where('is_verified',1)->first();

		if(isset($is_exist_check) && !empty($is_exist_check))
		{

			$response['success'] = false;
			$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> This number is already exist  </div>';
		}
		else
		{
			$template_id = 	'1507163877289365078';
			$request_type= 'registration_otp';
			$isSendSMS = CustomHelper::sendSMS($phone,$urlencodeMessage,$template_id,$otp,$request_type);
			if($isSendSMS == 200)
			{
				session(['otp'=>$otp]);

				$response['success'] = true;
				$response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP has been sent to your phone. </div>';

			}
			else
			{
				$response['success'] = false;
				$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> There is Error in sending SMS  </div>';
			}	
		}
	  }

	  else
	  {
	  	$response['success'] = false;
		$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Mobile no. should be 10 digits  </div>';
	  }

	}
	else
	{
		$response['success'] = false;
		$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Mobile no. should be numeric  </div>';
	}

	


}





					// if($email){
						 
					// 	$to_email = $email;
					// 	$subject = 'Your OTP to varify on Johnpride';

					// 	$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

					// 	if(empty($ADMIN_EMAIL)){
					// 		$ADMIN_EMAIL = config('custom.admin_email');
					// 	}

					// 	$from_email = $ADMIN_EMAIL;				

					// 	$email_data = [];
					// 	$email_data['smsMessage'] = $smsMessage;

					// 	$is_mail = CustomHelper::sendEmail('emails.verify_otp', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

					// 	session(['otp'=>$otp]);

					// }

					


					//prd($otpResult);
				}
				elseif(isset($request->resend_otp) && $request->resend_otp == '1'){//register
					$otpResult = [];

					//$otpResult['message'] = '39687172335a323931333837';
					//$otpResult['type'] = 'success';

					//$otpResult = (object)$otpResult;

					/*$otpResult = LaravelMsg91::resendOtp($phone);

					if(isset($otpResult->type) && $otpResult->type == 'success'){
						$response['success'] = true;

						$response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP has been sent in email. </div>';
					}*/

					$otp = randomNumber(6);
					$smsMessage = $otp." is your OTP. Thanks Johnpride Team";
					$urlencodeMessage = urlencode($smsMessage);

					if($phone){
						

						$template_id = 	'1507163877289365078';
						$request_type= 'resend_otp';
						$isSendSMS = CustomHelper::sendSMS($phone,$urlencodeMessage,$template_id,$otp,$request_type);

						if($isSendSMS == 200)
						{
							session(['otp'=>$otp]);
						}
						else
						{
							$response['success'] = false;
							$response['message'] = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> There is Error in sending SMS  </div>';
						}



						session(['otp'=>$otp]);


					}

					
					//session(['otp'=>$otp]);

					// if($email){
					// 	$to_email = $email;
					// 	$subject = 'Your OTP to varify on Johnpride';

					// 	$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

					// 	if(empty($ADMIN_EMAIL)){
					// 		$ADMIN_EMAIL = config('custom.admin_email');
					// 	}

					// 	$from_email = $ADMIN_EMAIL;				

					// 	$email_data = [];
					// 	$email_data['smsMessage'] = $smsMessage;

					// 	$is_mail = CustomHelper::sendEmail('emails.verify_otp', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

					// 	session(['otp'=>$otp]);

					// }

					$response['success'] = true;
					$response['message'] = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> OTP has been sent to your phone. </div>';

					//prd($otpResult);
				}
				elseif(isset($request->register) && $request->register == '1')
				{
					/*echo '$request->register=';
					prd($request->toArray());*/
					session()->forget('otp');

					$password = bcrypt($request->password);
					$otp = $request->otp;

					$role_id = 2;

					$name = isset($request->name) ? $request->name : '';

					$user->name = $name;
					$user->role_id = $role_id;
					$user->email = isset($request->email) ? $request->email : '';
					$user->phone = $request->phone;
					$user->password = $password;
					$user->gender = $request->gender;
					$user->verify_token = $verify_token;
					$user->is_verified = 1;
					$user->referer = $referer;
					$user->otp = $otp;
					$user->status = 1;

				//prd($user->toArray());

					$is_saved = 0;

					$is_saved = $user->save();

					if($is_saved){

					Auth::login($user);

						$response['success'] = true;
						$response['is_register'] = 1;
					//$response['message'] = "you have registered successfully, please login.";
						$response['message'] = '<div class="alert alert-success"> You have registered successfully, please login. </div>';
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

				$subject = 'Reset password - Johnpride';
				
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

					$subject = 'Reset password - Johnpride';

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

		$applicationName = 'Johnpride';
	
		$clientId = '717646013544-1iumsdc7m99f6urmmfam2bjvb23cc9re.apps.googleusercontent.com';
		$clientSecret = 'u53dyrKMuACpyQa92PIIr7EX';
		$developerKey = 'AIzaSyDd1lOXlwEO9w_KEaf6qekKcjddQ_83BgU';

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

				$sessionToken = csrf_token();
				$userId = $user->id;
				$this->updateCartToUser($userId, $sessionToken);


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

			$graphUser = '';

			if(!empty($response)){
				$graphUser = $response->getGraphUser();
			}

			/*pr($graphUser->getId());
			pr($graphUser->getName());
			pr($graphUser->getEmail());

			prd($graphUser);*/
			if(!empty($graphUser)) {

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
		}
			//echo 'Logged in as ' . $me->getName();

			//return redirect(url('account/login'))->with('alert-danger', 'something went wrong please try again.');	
			return redirect(url('/'))->with('alert-danger', 'something went wrong please try again.');	

	}




/* end of controller */
}
