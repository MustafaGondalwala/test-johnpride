<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAddress;
use App\UserWishlist;
use App\UserCartItem;
use App\Product;
use App\ProductSizeNotification;
use App\State;
use App\UserWallet;
use App\Size;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Helpers\CustomHelper;
use App\Libraries\Cart;

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

			if(!empty($user->password)){
				$rules['current_password'] = 'required';
			}

			$rules['new_password'] = 'required|min:6';
			$rules['confirm_password'] = 'required|same:new_password';

			$validator = Validator::make($request->all(), $rules, $validation_msg);

			if(!empty($user->password)){
				$validator->after(function($validator) use ($user){
					if (!Hash::check(request('current_password'), $user->password)){
						$validator->errors()->add('current_password', 'Invalid password!');
					}
					else{
						session(['verify_password'=>TRUE, 'verify_time'=>date('Y-m-d H:i:s')]);
					}
				});
			}
			

			if ($validator->fails()){
				return back()->withErrors($validator);
			}
			else{
				$password = bcrypt($request->new_password);

				$user->password = $password;

				$isSaved = $user->save();

				if($isSaved){
                    $to_email = $user->email;

                    if(!empty($to_email)){

                    	$subject = 'Password changed - SlumberJill';

                    	$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                    	if(empty($ADMIN_EMAIL)){
                    		$ADMIN_EMAIL = config('custom.admin_email');
                    	}

                    	$from_email = $ADMIN_EMAIL;

                    	$emailData = [];
                    	$emailData['name'] = $user->name;

                    	/*$viewHtml = view('emails.password_change', $emailData)->render();

                    	prd($viewHtml);*/

                    	$is_mail = CustomHelper::sendEmail('emails.password_change', $emailData, $to=$to_email, $from_email, $replyTo = $from_email, $subject);
                    }

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

			$rules['name'] = 'required';
			//$rules['company_name'] = 'required';
			$rules['phone'] = 'required|numeric|digits:10';
			$rules['address'] = 'required';
			//$rules['locality'] = 'required';
			$rules['state'] = 'required|numeric';
			$rules['city'] = 'required|numeric';
			$rules['pincode'] = 'required|numeric|digits:6';
			$rules['country'] = 'required|numeric';

			$validation_msg['company_name.required'] = 'The business name field is required.';

			$this->validate($request, $rules, $validation_msg);

			$userData = $request->except(['_token', 'dob']);

			$dob = CustomHelper::DateFormat($request->dob, $toFormat='Y-m-d', $fromFormat='d/m/Y');

			if(!empty($dob)){
				$userData['dob'] = $dob;
			}

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
			$title = 'Add New Address';

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
			$rules['name'] = 'required';
			//$rules['company_name'] = 'required';
			$rules['phone'] = 'required|numeric|digits:10';
			$rules['address'] = 'required';
			//$rules['locality'] = 'required';
			$rules['state'] = 'required|numeric';
			$rules['city'] = 'required|numeric';
			$rules['pincode'] = 'required|numeric|digits:6';
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

				//prd($userAddress->toArray());

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

			$userId = auth()->user()->id;

			$cartId = (isset($request->cartId))?$request->cartId:'';

			if(is_numeric($cartId) && $cartId > 0){

				$userCart = UserCartItem::find($cartId);

				if(!empty($userCart) && count($userCart) > 0){
					
					$isSaved = '';

					$productId = $userCart->product_id;
					$sizeId = $userCart->size_id;

					$userWishlist = new UserWishlist;

					$userWishlist->user_id = $userId;
					$userWishlist->product_id = $productId;
					$userWishlist->size_id = $sizeId;

					$exist = UserWishlist::where(['user_id'=>$userId, 'product_id'=>$productId])->first();

					if(!empty($exist) && count($exist) > 0){
						$userWishlist = $exist;
					}

					$isSaved = $userWishlist->save();

					if($isSaved){

						$isRemoved = Cart::remove($cartId);

						$cartCollection = Cart::getContent();
						$cartCount = $cartCollection->count();
						$response['cartCount'] = $cartCount;

						$response['success'] = true;
					}
				}
			}
			else{
				$slug = (isset($request->slug))?$request->slug:'';

				if(!empty($slug)){
					$product = Product::where('slug', $slug)->first();

					if(!empty($product) && count($product) > 0){

						$productId = $product->id;

						$userWishlist = new UserWishlist;

						$userWishlist->user_id = $userId;
						$userWishlist->product_id = $productId;
						//$userWishlist->size_id = $sizeId;

						$exist = UserWishlist::where(['user_id'=>$userId, 'product_id'=>$productId])->first();

						$isSaved = 0;

						if(!empty($exist) && count($exist) > 0){
							$userWishlist = $exist;
						}

						$isSaved = $userWishlist->save();

						if($isSaved){
							$response['success'] = true;
						}
					}
				}
			}
		}


		return response()->json($response);
	}

	public function wishlist(){

		$data = [];

		$user = auth()->user();

		$userWishlist = $user->userWishlist;

		/*if(!empty($wishlistProducts) && count($wishlistProducts) > 0){
			pr($wishlistProducts->toArray());
		}*/

		$data['user'] = $user;
		$data['userWishlist'] = $userWishlist;

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



	public function orders(Request $request){

		$orderNo = (isset($request->order_no))?$request->order_no:'';

		//prd($orderNo);

		if(!empty($orderNo)){
			$orderQuery = Order::where('order_no', $orderNo);
			$orderQuery->orWhere('id', $orderNo);
			$order = $orderQuery->first();

			if(isset($order->order_no) && ($order->order_no == $orderNo || $order->id == $orderNo) ){
				//prd($order->toArray());

				$orderNo = $order->order_no;

				$data = [];
				$data['orderNo'] = $orderNo;
				$data['order'] = $order;

				return view('users.orders.detail', $data);
			}
		}

		$data = [];

		$userId = auth()->user()->id;

		$orders = Order::where('user_id',$userId)->orderBy('id','desc')->get();
		
		$data['orders'] = $orders;
		return view('users.orders.list', $data);
	}

	public function wallet(){

		$data = [];
		$userId = auth()->user()->id;
		$wallet = UserWallet::where(['user_id'=>$userId,'status'=>1])->get();

		$data['wallet'] = $wallet;

		return view('users.wallet', $data);
	}


	public function addresses(Request $request){
		$data = [];

		//$cartContent = Cart::getContent();

		$productModel = new Product;

		$method = $request->method();

	    if($method == 'POST'){

			//prd($request->toArray());

			$user_id = auth()->user()->id;
			$address_id = (isset($request->address_id))?$request->address_id:'';

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

			$this->validate($request, $rules, $validation_msg);

			$addrData = $request->except(['_token', 'address_id']);

			//prd($userData);

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
				return redirect(url('cart/address'))->with('alert-success', 'Address has been saved successfullly.');
			}
			else{
				return back()->with('alert-danger', 'something went wrong, please try again...');
			}

		}


		$states = State::where('status', 1)->orderBy('name')->get();

		$data['meta_title'] = 'User Address | SlumberJill';
		//$data['cartContent'] = $cartContent; 
		$data['productModel'] = $productModel;
		$data['states'] = $states;

		return view('users.address', $data);
	}


	// notify_product_size
	public function notifyProductSize(Request $request){
		//prd($request->toArray());

		$response['success'] = false;

		$message = '';

		$method = $request->method();

		if($method == 'POST'){

			$user = auth()->user();
			$userId = $user->id;

			$productSlug = (isset($request->slug))?$request->slug:'';
			$sizeId = (isset($request->size))?$request->size:0;

			if(!empty($productSlug)){
				$product = Product::where('slug', $productSlug)->first();

				$sizeName = '';

				if(is_numeric($sizeId) && $sizeId > 0){
					$size = DB::table('sizes')->where('id', $sizeId)->first();

					$sizeName = (isset($size->name))?$size->name:'';

				}


				if(!empty($product) && count($product) > 0 ){

					$notificationData = [];
					$notificationData['user_id'] = $userId;
					$notificationData['product_id'] = $product->id;
					$notificationData['size_id'] = $sizeId;

					$isExist = ProductSizeNotification::where($notificationData)->count();

					if($isExist){
						$message = 'You have already requested for this Product.';
					}
					else{

						$isSaved = ProductSizeNotification::create($notificationData);

						if($isSaved){

							$subject = 'Size availablity for Product: '.$product->name;

							$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
							if(empty($ADMIN_EMAIL)){
								$ADMIN_EMAIL = config('custom.admin_email');
							}

							$toEmail = $ADMIN_EMAIL;

							$fromEmail = $ADMIN_EMAIL;

							$emailData = [];

							$emailData['productName'] = $product->name;
							$emailData['sizeName'] = $sizeName;
							$emailData['customerName'] = $user->name;
							$emailData['customerEmail'] = $user->email;
							$emailData['customerPhone'] = $user->phone;

							// $viewHtml = view('emails.product_size_notification', $emailData)->render();

							// echo $viewHtml; die;

							if(!empty($toEmail)){
								$isMail = CustomHelper::sendEmail('emails.product_size_notification', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

								if($toEmail){
									$response['success'] = true;
								}
							}
						}

					}

				}
			}
		}

		$response['message'] = $message;


		return response()->json($response);
	}

	/*get_order_cancel_form*/
	function getOrderCancelForm(Request $request){

	  //prd($request->toArray());
		$response['success'] = false;
		$order_id = ($request->has('order_id'))?$request->order_id:0;

		if(is_numeric($order_id) && $order_id > 0){

			if($request->method() == 'POST'){
				$viewData = [];
				$orders = Order::find($order_id);
				$viewData['order_id'] = $order_id;
				$viewData['orders'] = $orders;

				$htmlData = view('common._order_cancel_form', $viewData)->render();
				if($htmlData){
					$response['success'] = true;
					$response['htmlData'] = $htmlData;
				}
			}

		}

		return response()->json($response);
	}


	/* ajax_cancel_order */
	public function cancelOrder(Request $request){

		$response = [];
		$response['success'] = false;
		$message = '';

		if($request->method() == 'POST' || $request->method() == 'post'){

			//prd($request->toArray());

			$rules = [];

			$reason = isset($request->reason)?$request->reason:'';
			$refund_mode = isset($request->refund_mode)?$request->refund_mode:'';
			
			$rules['refund_mode'] = 'required';
			$rules['reason'] = 'required';

			if($reason == 'remark'){
				$rules['reason_comment'] = 'required';
			}

			if($refund_mode == 'Bank Account'){
				$rules['bank_details'] = 'required';
			}

			//$this->validate($request, $rules);

			$validator = Validator::make($request->all(), $rules);

			if($validator->fails()){
				$response['errors'] = $validator->errors();
			}
			else{

			//prd($request->name);
				$req_data = [];
				$emailData = [];

				$orderId = isset($request->order_id)?$request->order_id:'';
				$refund_mode = isset($request->refund_mode)?$request->refund_mode:'';
				$reason = isset($request->reason)?$request->reason:'';
				$reason_comment = isset($request->reason_comment)?$request->reason_comment:'';
				$bank_details = isset($request->bank_details)?$request->bank_details:'';

				if(is_numeric($orderId) && $orderId > 0){
					$order = Order::find($orderId);
					$order->order_status = 'cancelled';
					$order->refund_mode = $refund_mode;
					$order->reason = $reason;
					$order->reason_comment = $reason_comment;
					$order->bank_details = $bank_details;
					$isSavedOrder = $order->save();
				}

				// Sending Email to Customer
				$toEmail = $order->billing_email;
				$subject = 'Order Cancelled - Order No: '.$orderId;

				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
				if(empty($ADMIN_EMAIL)){
					$ADMIN_EMAIL = config('custom.admin_email');
				}

				$fromEmail = $ADMIN_EMAIL;

				$emailData = [];
				$emailData['orderId'] = $orderId;
				$emailData['order'] = $order;
				$emailData['reason'] = $reason;
				$emailData['reason_comment'] = $reason_comment;
				//prd($order->toArray());
				$viewHtml = view('emails.orders.order_cancel_status', $emailData)->render();

				//echo $viewHtml; die;

				$isMailCustomer = '';

				if(!empty($toEmail)){
					$isMailCustomer = CustomHelper::sendEmail('emails.orders.order_cancel_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

					$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin_order_cancel_status', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);
				}

				if($isMailCustomer) {
					$response['success'] = true;
					session()->flash('alert-success', 'Order Cancelled Successfullly');
				}
				else{
					$message = 'Error in Submitting Form.'; 
				}
			}
			
		}

		$response['message'] = $message;

		return response()->json($response);
	}


		/*get_order_return_form*/
	function getOrderReturnForm(Request $request){

	  //prd($request->toArray());
		$response['success'] = false;
		$order_id = ($request->has('order_id'))?$request->order_id:0;

		if(is_numeric($order_id) && $order_id > 0){
			if($request->method() == 'POST'){
				$viewData = [];
				$orders = Order::find($order_id);
				$viewData['order_id'] = $order_id;
				$viewData['orders'] = $orders;

				$htmlData = view('common._order_return_form', $viewData)->render();
				if($htmlData){
					$response['success'] = true;
					$response['htmlData'] = $htmlData;
				}
			}
		}

		return response()->json($response);
	}


	/* ajax_return_order */
	public function returnOrder(Request $request){

		$response = [];
		$response['success'] = false;
		$message = '';

		if($request->method() == 'POST' || $request->method() == 'post'){

			//prd($request->toArray());

			$rules = [];
			
			$reason = isset($request->reason)?$request->reason:'';
			$refund_mode = isset($request->refund_mode)?$request->refund_mode:'';
			
			$rules['refund_mode'] = 'required';
			$rules['reason'] = 'required';

			if($reason == 'remark'){
				$rules['reason_comment'] = 'required';
			}

			if($refund_mode == 'Bank Account'){
				$rules['bank_details'] = 'required';
			}
			//$this->validate($request, $rules);

			$validator = Validator::make($request->all(), $rules);

			if($validator->fails()){
				$response['errors'] = $validator->errors();
			}
			else{

			//prd($request->name);
				$req_data = [];
				$emailData = [];

				$orderId = isset($request->order_id)?$request->order_id:'';
				$refund_mode = isset($request->refund_mode)?$request->refund_mode:'';
				$reason = isset($request->reason)?$request->reason:'';
				$reason_comment = isset($request->reason_comment)?$request->reason_comment:'';
				$bank_details = isset($request->bank_details)?$request->bank_details:'';


				if(is_numeric($orderId) && $orderId > 0){
					$order = Order::find($orderId);
					$order->order_status = 'return';
					$order->refund_mode = $refund_mode;
					$order->reason = $reason;
					$order->reason_comment = $reason_comment;
					$order->bank_details = $bank_details;
					$isSavedOrder = $order->save();
				}

				// Sending Email to Customer
				$toEmail = $order->billing_email;
				$subject = 'Order Returned - Order No: '.$orderId;

				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
				if(empty($ADMIN_EMAIL)){
					$ADMIN_EMAIL = config('custom.admin_email');
				}

				$fromEmail = $ADMIN_EMAIL;

				$emailData = [];
				$emailData['orderId'] = $orderId;
				$emailData['order'] = $order;
				$emailData['reason'] = $reason;
				//prd($order->toArray());
				//$viewHtml = view('emails.orders.admin_order_return_status', $emailData)->render();

				//echo $viewHtml; die;

				$isMailCustomer = '';

				if(!empty($toEmail)){
					$isMailCustomer = CustomHelper::sendEmail('emails.orders.order_return_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

					$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin_order_return_status', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);
				}

				if($isMailCustomer) {
					$response['success'] = true;
					session()->flash('alert-success', 'Order Return Successfullly');
				}
				else{
					$message = 'Error in Submitting Form.'; 
				}
			}
			
		}

		$response['message'] = $message;

		return response()->json($response);
	}



	/*ajax_print_invoice*/
	public function printInvoice(request $request){

		$orderId = (isset($request->order_id))?$request->order_id:0;
		//prd($request->all());

		$response = [];

		if(is_numeric($orderId) && $orderId>0){

			$order = Order::find($orderId);

			if(!empty($order)){
				$orderHistory = DB::table('order_history')->where('order_id', $orderId)->get();

				$response['order'] = $order;
				$response['orderHistory'] = $orderHistory;

				$response['order']= $order;
				$response['logoPath']= asset('public/images/logo.png');

				$viewHtml = view('admin.orders.order_invoice', $response)->render();

				//prd($viewHtml);

				$response['viewHtml'] = $viewHtml;
				$response['success'] = true;

				return response()->json($response);
			}
			else{

			}

		}
	}



	/* end of controller */
}
