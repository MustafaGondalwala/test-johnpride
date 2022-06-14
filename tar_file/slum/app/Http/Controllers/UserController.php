<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAddress;
use App\UserWishlist;
use App\Product;
use App\State;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Helpers\CustomHelper;

use DB;
use Validator;
use Hash;


class UserController extends Controller {

	private $limit;
	/**
	 * Homepage
	 * URL: /
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */

	public function __construct(){
		$this->limit = 20;		
	}

	public function index(){

	}

	public function profile(Request $request){

		$data = [];

		$user = auth()->user();

		$method = $request->method();

		if($method == 'POST'){

			//prd($request->toArray());

			$rules = [];
			$validation_msg = [];

			$rules['current_password'] = 'required';
			$rules['new_password'] = 'required|min:6';
			$rules['confirm_password'] = 'required|same:new_password';

			$validator = Validator::make($request->all(), $rules, $validation_msg);

			$validator->after(function($validator) use ($user){
				if (!Hash::check(request('current_password'), $user->password)){
					$validator->errors()->add('current_password', 'Invalid password!');
				}
				else{
					session(['verify_password'=>TRUE, 'verify_time'=>date('Y-m-d H:i:s')]);
				}
			});

			if ($validator->fails()){
				return back()->withErrors($validator);
			}
			else{
				$password = bcrypt($request->new_password);

				$user->password = $password;

				$isSaved = $user->save();

				if($isSaved){
					return back()->with('alert-success', 'Password has been changed successfullly.');
				}
				else{
					return back()->with('alert-danger', 'something went wrong, please try again...');
				}
			}

		}


		$data['user'] = $user;

		//prd($user->userState);

		return view('users.profile', $data);
	}

	// update
	public function update(Request $request){

		$data = [];

		$user = auth()->user();

		$method = $request->method();

		if($method == 'POST'){

			//prd($request->toArray());

			$rules = [];
			$validation_msg = [];

			$rules['first_name'] = 'required';
			$rules['company_name'] = 'required';
			$rules['phone'] = 'required|numeric|digits:10';
			$rules['address'] = 'required';
			$rules['state'] = 'required|numeric';
			$rules['city'] = 'required|numeric';
			$rules['pincode'] = 'required|numeric';
			$rules['country'] = 'required|numeric';

			$validation_msg['company_name.required'] = 'The business name field is required.';

			$this->validate($request, $rules, $validation_msg);

			$userData = $request->except(['_token']);

			//prd($userData);

			if(!empty($userData) && count($userData) > 0){
				foreach($userData as $uKey=>$uVal){
					$user->$uKey = $uVal;
				}
			}

			//prd($user->toArray());

			$isSaved = $user->save();

			if($isSaved){
				return back()->with('alert-success', 'Your profile has been updated successfullly.');
			}
			else{
				return back()->with('alert-danger', 'something went wrong, please try again...');
			}

		}

		$states = State::where('status', 1)->orderBy('name')->get();

		$data['user'] = $user;
		$data['states'] = $states;

		return view('users.update', $data);
	}

	public function details(){

		$data = [];

		return view('users.details', $data);
	}



	// get_address_form
	function getAddressForm(Request $request){

	  //prd($request->toArray());

		$response['success'] = false;

		if($request->method() == 'POST'){
			$addressId = (isset($request->addressId))?$request->addressId:'';

			$userAddress = '';
			$title = 'Add Address';

			$stateId = 0;
			$cityId = 0;
			$countryId = 0;

			if(is_numeric($addressId) && $addressId > 0){
				$userAddress = UserAddress::find($addressId);

				if(!empty($userAddress) && count($userAddress) > 0){
			//prd($userAddress->toArray());
					$title = 'Update Address';

					$stateId = $userAddress->state;
					$cityId = $userAddress->city;
					$countryId = $userAddress->country;
				}
			}

			$states = State::select('id', 'name')->where('status', 1)->orderBy('name')->get();

			$viewData = [];

			$viewData['userAddress'] = $userAddress;
			$viewData['states'] = $states;

			$htmlData = view('common._address_form', $viewData)->render();

			if($htmlData){
				$response['success'] = true;
				$response['title'] = $title;
				$response['stateId'] = $stateId;
				$response['cityId'] = $cityId;
				$response['countryId'] = $countryId;
				$response['htmlData'] = $htmlData;
			}

		}

		return response()->json($response);

	}

	// save_address
	function saveAddress(Request $request){

	  //prd($request->toArray());

		$response['success'] = false;

		$method = $request->method();

		if($method == 'POST'){

			$user_id = auth()->user()->id;
			$address_id = (isset($request->address_id))?$request->address_id:'';

			//prd($request->toArray());

			$rules = [];
			$validation_msg = [];

			$rules['type'] = ['required', Rule::in(['home', 'office'])];
			$rules['first_name'] = 'required';
			$rules['company_name'] = 'required';
			$rules['phone'] = 'required|numeric|digits:10';
			$rules['address'] = 'required';
			$rules['state'] = 'required|numeric';
			$rules['city'] = 'required|numeric';
			$rules['pincode'] = 'required|numeric';
			$rules['country'] = 'required|numeric';

			$validation_msg['company_name.required'] = 'The business name field is required.';

			//$this->validate($request, $rules, $validation_msg);

			$validator = Validator::make($request->all(), $rules, $validation_msg);

			if($validator->fails()){
				$response['errors'] = $validator->errors();
			}
			else{

				$addrData = $request->except(['_token', 'address_id']);

			  //prd($addrData);

				$userAddress = new UserAddress;

				if(is_numeric($address_id) && $address_id > 0){
					$exist = UserAddress::find($address_id);

					if(!empty($exist) && count($exist) > 0){
				//prd($exist->toArray());

						$userAddress = $exist;
					}
				}

				if(!empty($addrData) && count($addrData) > 0){
					foreach($addrData as $key=>$val){
						$userAddress->$key = $val;
					}
				}

				$userAddress->user_id = $user_id;

			  //prd($userAddress);

				$isSaved = $userAddress->save();

				if($isSaved){
					$this->setDefaultAddress($user_id);
					session()->flash('alert-success', 'Address has been saved successfullly.');
					$response['success'] = true;
				}
				else{
					session()->flash('alert-danger', 'something went wrong, please try again...');
				}

			}

		}

		return response()->json($response);

	}

	public function setDefaultAddress($user_id){

		if(is_numeric($user_id) && $user_id > 0){

			$userAddresses = UserAddress::where(['user_id'=>$user_id])->get();

			if(!empty($userAddresses) && count($userAddresses) > 0){

				$defaultAddress = $userAddresses->where('is_default', 1);

				if(empty($defaultAddress) || count($defaultAddress) == 0){
					$firstAddress = $userAddresses->first();

					if(!empty($firstAddress) && count($firstAddress) > 0){
						$firstAddress->is_default = 1;
						$firstAddress->save();
					}
				}
			}
		}
	}

	// add_to_wishlist
	public function addToWishlist(Request $request){
		//prd($request->toArray());

		$response['success'] = false;

		$method = $request->method();

		if($method == 'POST'){

			$slug = (isset($request->slug))?$request->slug:'';

			if(!empty($slug)){
				$product = Product::where('slug', $slug)->first();

				if(!empty($product) && count($product) > 0){
					
					$isSaved = '';

					$user_id = auth()->user()->id;
					$product_id = $product->id;

					$exist = UserWishlist::where(['user_id'=>$user_id, 'product_id'=>$product_id])->first();

					if(!empty($exist) && count($exist) > 0){
						$isSaved = 1;
					}
					else{
						$wishlistData = [];
						$wishlistData['user_id'] = $user_id;
						$wishlistData['product_id'] = $product_id;

						$isSaved = UserWishlist::create($wishlistData);
					}

					if($isSaved){
						$response['success'] = true;
					}
				}
			}
		}


		return response()->json($response);
	}

	public function wishlist(){

		$data = [];

		$user = auth()->user();

		$wishlistProducts = $user->wishlistProducts;

		/*if(!empty($wishlistProducts) && count($wishlistProducts) > 0){
			pr($wishlistProducts->toArray());
		}*/

		$data['user'] = $user;
		$data['wishlistProducts'] = $wishlistProducts;

		return view('users.wishlist', $data);
	}


	// delete_from_wishlist
	public function deleteFromWishlist(Request $request){
		//prd($request->toArray());

		$response['success'] = false;

		$method = $request->method();

		if($method == 'POST'){

			$productId = (isset($request->productId))?$request->productId:'';

			if(is_numeric($productId) && $productId > 0 ){

				$isDeleted = '';

				$user_id = auth()->user()->id;

				$isDeleted = UserWishlist::where(['user_id'=>$user_id, 'product_id'=>$productId])->delete();

				if($isDeleted){
					session()->flash('alert-success', 'One item has been removed.');
					$response['success'] = true;
				}
				
			}
		}


		return response()->json($response);
	}



	public function orders(){

		$data = [];

		return view('users.orders', $data);
	}

	public function wallet(){

		$data = [];


		return view('users.wallet', $data);
	}


	/* end of controller */
}
