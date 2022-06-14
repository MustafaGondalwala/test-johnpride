<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use App\State;
use App\Pincode;

use Validator;
use Session;
use DB;


class CommonController extends Controller {


	//get_pincode_city_state
	public function getPincodeCityState(Request $request){

		//prd($request->toArray());

		$response = [];

		$citiesOptions = '';

		$pincode = (isset($request->pincode))?$request->pincode:'';

		if(!empty($pincode)){
			$pincodeData = Pincode::where('pin', $pincode)->first();

			if(isset($pincodeData->pin) && $pincodeData->pin == $pincode){

				$cityId = $pincodeData->city_id;
				$stateId = $pincodeData->state_id;

				$response['city_id'] = $cityId;
				$response['state_id'] = $stateId;

				$citiesOptions = $this->getCitiesOptions($stateId, $cityId);

				//prd($citiesOptions);

				$response['success'] = true;
			}
		}

		
		$response['citiesOptions'] = $citiesOptions;

		return response()->json($response);

	}

	
	//ajax_load_cities
	public function ajaxLoadCities(Request $request){

		$response = [];
		$cities = [];

		//$options = '<option value="">--Select--</option>';

		$stateId = (isset($request->state_id))?$request->state_id:0;
		$cityId = (isset($request->city_id))?$request->city_id:0;

		/*$selectArr = ['id','name','state','state_id','gst_code','status','created_at','updated_at'];

		if(is_numeric($state_id) && $state_id > 0){
			$cities = DB::table('cities')->select($selectArr)->where('state_id', $state_id)->orderBy('name')->get();
		}

		if(!empty($cities) && count($cities) > 0){
			foreach($cities as $city){
				$selected = '';
				if($city->id == $city_id){
					$selected = 'selected';
				}
				$options .= '<option value="'.$city->id.'" '.$selected.'>'.$city->name.'</option>';
			}
		}*/

		$options = $this->getCitiesOptions($stateId, $cityId);

		$response['success'] = true;
		$response['options'] = $options;

		return response()->json($response);

	}


	private function getCitiesOptions($stateId=0, $cityId=0){

		$cities = '';

		$options = '<option value="">--Select--</option>';

		$selectArr = ['id','name','state','state_id','gst_code','status','created_at','updated_at'];

		if(is_numeric($stateId) && $stateId > 0){
			$cities = DB::table('cities')->select($selectArr)->where('state_id', $stateId)->orderBy('name')->get();
		}

		if(!empty($cities) && count($cities) > 0){
			foreach($cities as $city){
				$selected = '';
				if($city->id == $cityId){
					$selected = 'selected';
				}
				$options .= '<option value="'.$city->id.'" '.$selected.'>'.$city->name.'</option>';
			}
		}

		return $options;

	}

	
	//ajax_regenerate_captcha
	public function ajaxRegenerateCaptcha(Request $request){

		$response = [];

		$response['success'] = true;

		$captcha_src = captcha_src('custom');

		$response['captcha_src'] = $captcha_src;

		return response()->json($response);

	}




	public function ajax_changeLanguage(Request $request){

		//prd($request->toArray());

		$response = [];

		$response['success'] = false;

		if($request->method() == 'POST'){
			$locale_lang = (isset($request->locale_lang))?$request->locale_lang:'';

			if(!empty($locale_lang)){
				session(['locale_lang'=>$locale_lang]);
				$response['success'] = true;
			}

		}

		return response()->json($response);

	}


	// ajax_set_currency
	public function ajaxSetCurrency(Request $request){

		//prd($request->toArray());

		$response = [];

		$response['success'] = false;

		if($request->method() == 'POST'){
			$currency = (isset($request->currency))?$request->currency:'';

			if(!empty($currency)){
				session(['to_currency'=>$currency]);
				$response['success'] = true;
			}

		}

		return response()->json($response);

	}


	// ajax_check_pincode
	public function ajaxCheckPincode(Request $request){
		//prd($request->toArray());

		$response = [];

		$response['success'] = false;

		$message = '';

		$pincode = (isset($request->pincode))?$request->pincode:'';

		if(!empty($pincode)){

			$selectArr = ['id','state_id','city_id','pin','status','created_at','updated_at'];

			$Pincode = Pincode::select($selectArr)->where('pin', $pincode)->first();

			if(!empty($Pincode) && count($Pincode) > 0){
				//prd($Pincode->toArray());

				$response['success'] = true;
			}
		}

		return response()->json($response);
	}


	public function newsletterSubscribe(Request $request){

		$response = [];
		$response['success'] = false;
		$message = '';

		if($request->method() == 'POST' || $request->method() == 'post'){

			$email = $request->email;

			$existEmail =  DB::table('newsletter_subscribers')->select('id')->where(['email'=>$email])->first();
			
			if(!empty($existEmail)){

				$existEmail=true;
			}
			if(!$existEmail) {
				$id = DB::table('newsletter_subscribers')->insert(['email'=>$email, 'status'=>1]);
				$response['success'] = true; 
				$response['message'] = 'Subscribed Successfully.';  
			}
			elseif($existEmail){
				$response['message'] = 'You are already subscribed our newsletter.'; 
			}
			
		} // close post
		echo json_encode($response); exit;
	}



	/* End of Controller */
}
