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

use App\LoyaltyPoints;

use App\LoyaltyPointsMaster;

use App\Size;

use App\Order;

use App\OrderItem;

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



						$subject = 'Password changed - Johnpride';



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
    		$rules['email'] = 'required|email';

    		$rules['phone'] = 'required|numeric|digits:10';

    		$rules['address'] = 'required';

			//$rules['locality'] = 'required';

    		$rules['state'] = 'required|numeric';

    		$rules['city'] = 'required|numeric';

    		$rules['pincode'] = 'required|numeric|digits:6';

    		$rules['country'] = 'required|numeric';



    		$validation_msg['company_name.required'] = 'The business name field is required.';



    		$this->validate($request, $rules, $validation_msg);



    		$userData = $request->except(['_token', 'dob', 'wedding_anniversary']);



    		$dob = CustomHelper::DateFormat($request->dob, $toFormat='Y-m-d', $fromFormat='d/m/Y');

    		$wedding_anniversary = CustomHelper::DateFormat($request->wedding_anniversary, $toFormat='Y-m-d', $fromFormat='d/m/Y');



    		if(!empty($dob)){

    			$userData['dob'] = $dob;

    		}



    		if(!empty($wedding_anniversary)){

    			$userData['wedding_anniversary'] = $wedding_anniversary;

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
    		$rules['email'] = 'required|email';

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




    			$isSaved = $userAddress->save();



    			if($isSaved){

    				// CHECK in USER MAIN PROFILE, IF EMAIL NOT  FOUND THEN UPDATE

    				$user_data = auth()->user();

    				//prd($user_data);

    				if(!empty($user_data))
    				{

    					$user_id = $user_data->id;

    					$userAdd = UserAddress::where('user_id',$user_id)->first();
    					//prd($userAddress);

    					$user_email = $user_data->email;

    					if(empty($user_email))
    					{
    						// $user_data->email = $user_email;
    						// $isuser_email_save = $user_data->save();	
    						$user_phone = $user_data->phone;

    						$userAdressEmail = isset($userAdd->email) ? $userAdd->email : '';
    						$userAdressName = isset($userAdd->name) ? $userAdd->name : '';
								$userAddress = isset($userAdd->address) ? $userAdd->address : '';
								$userCity = isset($userAdd->city) ? $userAdd->city : '';
								$userState = isset($userAdd->state) ? $userAdd->state : '';
								$userCountry = isset($userAdd->country) ? $userAdd->country : '';
								$userPincode = isset($userAdd->pincode) ? $userAdd->pincode : '';
								$userlocality = isset($userAdd->locality) ? $userAdd->locality : '';
    						
    						$update_user_data = array(
    							"name"=>$userAdressName,
    							"email"=>$userAdressEmail,
    							"address"=>$userAddress,
    							"state"=>$userState,
    							"city"=>$userCity,
    							"country"=>$userCountry,
    							"pincode"=>$userPincode,
    							"locality"=>$userlocality
    						);
    					//	prd($update_user_data);
    		
    						$user_update = User::where('phone',$user_phone)->update($update_user_data);


    					}
    				}





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



		//echo 'hi'; die;

    	$data = [];



    	$user = auth()->user();



    	$userWishlist = $user->userWishlist;



    	/*$product_query->whereHas('productInventorySize', function($stocks) {

            $stocks->havingRaw('SUM(stock) > 0');

        });



        $userWishlist = $product_query;*/



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



	







	public function ordersOld(Request $request){



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



	public function orders(Request $request){



		$orderNo = (isset($request->order_no))?$request->order_no:'';



		//prd($orderNo);



		if(!empty($orderNo)){

			$orderQuery = OrderItem::where('sub_order_no', $orderNo);

			$orderQuery->orWhere('id', $orderNo);

			$orderItem = $orderQuery->first();



			if(isset($orderItem->sub_order_no) && ($orderItem->sub_order_no == $orderNo || $orderItem->id == $orderNo) ){

				//prd($order->toArray());



				$subOrderNo = $orderItem->sub_order_no;

				$order = $orderItem->order;

				$orderNo = $order->order_no;



				$data = [];

				$data['orderNo'] = $orderNo;

				$data['subOrderNo'] = $subOrderNo;

				$data['order'] = $order;

				$data['subOrder'] = $orderItem;

				$data['order'] = $order;



				



				return view('users.orders.detail', $data);

			}

		}



		$data = [];



		$userId = auth()->user()->id;



		$orders = Order::where('user_id',$userId)->orderBy('id','desc')->get();

		

		//prd($orders);



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



	public function loyaltyPoints(){



		$data = [];

		$userId = auth()->user()->id;

		$loyaltyPoints = LoyaltyPoints::where(['customer_id'=>$userId,'status'=>1])->get();

		

		$loyaltyPointsDetails = CustomHelper::findLoyaltyPonitsCriteria($userId,$orderAmount=0,$show=true);
      //  prd($loyaltyPointsDetails);

        $loyaltyPointsDetailsForName = CustomHelper::findLoyaltyPonitsCriteriaForName($userId,$orderAmount=0,$show=true);


        $loyality_master = LoyaltyPointsMaster::all();
   //  prd($loyaltyPointsDetailsForName);

		//$haveOrder = Order::where('user_id',$userId)->orderBy('id','desc')->count();

		$data['loyaltyPoints'] = $loyaltyPoints;
		$data['loyaltyPointsDetails'] = $loyaltyPointsDetails;
        $data['loyality_master'] = $loyality_master;
        $data['loyaltyPointsDetailsForName'] = $loyaltyPointsDetailsForName;



		return view('users.loyalty_points', $data);

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



		$data['meta_title'] = 'User Address | Johnpride';

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

				$subOrder = OrderItem::find($order_id);

				$order = $subOrder->order;

				$viewData['order_id'] = $order_id;

				$viewData['order'] = $order;

				$viewData['subOrder'] = $subOrder;



				$htmlData = view('common._sub_order_cancel_form', $viewData)->render();

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

				//$rules['reason_comment'] = 'required';

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

					//$order = Order::find($orderId);

					$subOrder = OrderItem::find($orderId);



					if(!empty($subOrder) && count($subOrder) > 0){



                        $order_old_status = $subOrder->order_status;



						$order = $subOrder->order;

						$subOrder->order_status = 'cancelled';

						$subOrder->refund_mode = $refund_mode;

						$subOrder->reason = $reason;

						$subOrder->reason_comment = $reason_comment;

						$subOrder->bank_details = $bank_details;

					//$isSavedOrder = $order->save();

						$isSavedSubOrder = $subOrder->save();



                        // Added on 4march 2021

                        $order_history_data= []; 

                        $order_history_data['order_id'] = $subOrder->order_id;

                        $order_history_data['order_item_id'] = $orderId;

                        $order_history_data['old_order_status'] = $order_old_status;

                        $order_history_data['order_status'] = 'cancelled';

                        $order_history_data['comment'] = $reason;

                        DB::table('order_history')->insert($order_history_data);





					//$isSavedMainOrder = $this->updateMainOrderStatus($order);



						$isSavedMainOrder = $this->updateMainOrderStatus($order);

					//if($isSavedMainOrder && $subOrder->order_status == 'cancelled')

						if( $subOrder->order_status == 'cancelled')

						{

						//echo 'hi'; die;

							$responseUnicommerce = $this->cancelOrderUnicommerce($subOrder);



						}







					// Sending Email to Customer

						$toEmail = $order->billing_email;

					//$subject = 'Order Cancelled - Order No: '.$subOrder->sub_order_no;

						$subject = 'Johnpride Order Information #'.date('dmy').'-'.$subOrder->sub_order_no;



						$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

						if(empty($ADMIN_EMAIL)){

							$ADMIN_EMAIL = config('custom.admin_email');

						}



						$fromEmail = $ADMIN_EMAIL;



						$emailData = [];

						$emailData['orderId'] = $orderId;

						$emailData['order'] = $order;

						$emailData['subOrder'] = $subOrder;

					//$emailData['subOrderNo'] = $subOrder->sub_order_no;

						$emailData['reason'] = $reason;

						$emailData['reason_comment'] = $reason_comment;

					//prd($order->toArray());

					//$viewHtml = view('emails.orders.order_cancel_status', $emailData)->render();

                        //pr($fromEmail);

                        //prd($toEmail);

					//echo $viewHtml; die;



						$isMailCustomer = '';



						if(!empty($toEmail)){

							$isMailCustomer = CustomHelper::sendEmail('emails.orders.sub_order_cancel_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);



							$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin_sub_order_cancel_status', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);

						}



						if($isMailCustomer) {

							$response['success'] = true;

							session()->flash('alert-success', 'Order Cancelled Successfullly');

						}

						else{

							$message = 'Error in Submitting Form.'; 

						}







					}

					else{

						$message = 'Error in Submitting Form.'; 

					}











				}



				

			}

			

		}



		$response['message'] = $message;



		return response()->json($response);

	}





	// Cancel order on unicommerce



	private function cancelOrderUnicommerce($orderItem){

		$response = [];



		$accessToken = CustomHelper::getUnicommerceAccessToken();

		$unicommerce_api_mode = config('custom.unicommerce_api_mode');



		if($unicommerce_api_mode == 'DEMO')

		{

			$unicommerce_api_url = config('custom.unicommerce_demo_api_url');

			$unicommerce_facility = config('custom.unicommerce_demo_facility');



			$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';

		}

		else

		{

			$unicommerce_api_url = config('custom.unicommerce_api_url');

			$unicommerce_facility = config('custom.unicommerce_facility');



			//$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';



            $curl_url = $unicommerce_api_url.'services/rest/v1/oms/saleOrder/cancel';



		}





		if(!empty($orderItem) && count($orderItem) > 0 && $orderItem->order_status=='cancelled'){

			$order = $orderItem->order;

			$cancelorderData = [];

				//prd($orderItem->sub_order_no);



				//$cancelorderData['saleOrderCode'] = $orderItem->sub_order_id;

			$cancelorderData['saleOrderCode'] = $order->order_no;

			$cancelorderData['saleOrderItemCodes'] = array($orderItem->sub_order_no);

			$cancelorderData['cancellationReason'] = "Cancel order by customer. Reason: ".$orderItem->reason." Reason comment: ".$orderItem->reason_comment;





			$jsonData = json_encode($cancelorderData);



				//echo $jsonData."Anand";



			$curl = curl_init();



			curl_setopt_array($curl, array(

				CURLOPT_URL => $curl_url,

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => "",

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 30,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => "POST",

				CURLOPT_POSTFIELDS => "$jsonData",

				CURLOPT_HTTPHEADER => array(

					"authorization: bearer $accessToken",

					"cache-control: no-cache",

					"content-type: application/json",

					"facility: $unicommerce_facility",

				),

			));



			$response = curl_exec($curl);

			$err = curl_error($curl);

			if ($err) {

					//echo "cURL Error #:" . $err;

			} else {

					//echo $response;

			}



			curl_close($curl);



				//die;



			return $response;





		}





		return json_encode($response);





	}



	private function cancelOrderUnicommerceOld($orderItem){

		$response = [];



		$accessToken = CustomHelper::getUnicommerceAccessToken();

		$unicommerce_api_mode = config('custom.unicommerce_api_mode');



		if($unicommerce_api_mode == 'DEMO')

		{

			$unicommerce_api_url = config('custom.unicommerce_demo_api_url');

			$unicommerce_facility = config('custom.unicommerce_demo_facility');



			$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';

		}

		else

		{

			$unicommerce_api_url = config('custom.unicommerce_api_url');

			$unicommerce_facility = config('custom.unicommerce_facility');



			//$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';



            $curl_url = $unicommerce_api_url.'services/rest/v1/oms/saleOrder/cancel';



		}





		if(!empty($orderItem) && count($orderItem) > 0 && $orderItem->order_status=='cancelled'){

			$cancelorderData = [];

			$cancelorderData['saleOrderCode'] = $orderItem->sub_order_id;

				//$cancelorderData['saleOrderItemCodes'] = array();

			$cancelorderData['cancellationReason'] = "Cancel order by customer. Reason: ".$orderItem->reason." Reason comment: ".$orderItem->reason_comment;





			$jsonData = json_encode($cancelorderData);



				//echo $jsonData."Anand";



			$curl = curl_init();



			curl_setopt_array($curl, array(

				CURLOPT_URL => $curl_url,

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => "",

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 30,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => "POST",

				CURLOPT_POSTFIELDS => "$jsonData",

				CURLOPT_HTTPHEADER => array(

					"authorization: bearer $accessToken",

					"cache-control: no-cache",

					"content-type: application/json",

					"facility: $unicommerce_facility",

				),

			));



			$response = curl_exec($curl);

			$err = curl_error($curl);

			if ($err) {

					//echo "cURL Error #:" . $err;

			} else {

					//echo $response;

			}



			curl_close($curl);



			return $response;





		}





		return json_encode($response);





	}





	/*get_order_return_form*/

	function getOrderReturnFormOld(Request $request){



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



	/*get_order_return_form*/

	function getOrderReturnForm(Request $request){



	  //prd($request->toArray());

		$response['success'] = false;

		$order_id = ($request->has('order_id'))?$request->order_id:0;



		if(is_numeric($order_id) && $order_id > 0){

			if($request->method() == 'POST'){

				$viewData = [];

				$subOrder = OrderItem::find($order_id);

				$order = $subOrder->order;

				$viewData['order_id'] = $order_id;

				$viewData['subOrders'] = $subOrder;

				$viewData['order'] = $order;



				$htmlData = view('common._sub_order_return_form', $viewData)->render();

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

				//$rules['reason_comment'] = 'required';

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



				$subOrderId = isset($request->order_id)?$request->order_id:'';

				$refund_mode = isset($request->refund_mode)?$request->refund_mode:'';

				$reason = isset($request->reason)?$request->reason:'';

				$reason_comment = isset($request->reason_comment)?$request->reason_comment:'';

				$bank_details = isset($request->bank_details)?$request->bank_details:'';





				if(is_numeric($subOrderId) && $subOrderId > 0){

					//$order = Order::find($orderId);

					$subOrder = OrderItem::find($subOrderId);



					if(!empty($subOrder) && count($subOrder) > 0 ){

						$order_old_status = $subOrder->order_status;



						$order = $subOrder->order;

						$subOrder->order_status = 'return';

						$subOrder->refund_mode = $refund_mode;

						$subOrder->reason = $reason;

						$subOrder->reason_comment = $reason_comment;

						$subOrder->bank_details = $bank_details;

						// $isSavedOrder = $order->save();











						$isSavedSubOrder = $subOrder->save();

                        // Added on 4march 2021

                        $order_history_data= []; 

                        $order_history_data['order_id'] = $subOrder->order_id;

                        $order_history_data['order_item_id'] = $subOrderId;

                        $order_history_data['old_order_status'] = $order_old_status;

                        $order_history_data['order_status'] = 'return';

                        $order_history_data['comment'] = $reason;

                        DB::table('order_history')->insert($order_history_data);



                        //End





						$isSavedMainOrder = $this->updateMainOrderStatus($order);





						// Sending Email to Customer

						$toEmail = $order->billing_email;

						//$subject = 'Order Returned - Order No: '.$subOrder->sub_order_no;



						$subject = 'Johnpride Order Information #'.date('dmy').'-'.$subOrder->sub_order_no;



						$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

						if(empty($ADMIN_EMAIL)){

							$ADMIN_EMAIL = config('custom.admin_email');

						}



						$fromEmail = $ADMIN_EMAIL;



						



						$emailData = [];

						$emailData['subOrderId'] = $subOrderId;

						$emailData['subOrderNo'] = $subOrder->sub_order_no;

						$emailData['order'] = $order;

						$emailData['subOrder'] = $subOrder;

						$emailData['reason'] = $reason;

						//prd($order->toArray());

						//$viewHtml = view('emails.orders.admin_order_return_status', $emailData)->render();

						//$viewHtml = view('emails.orders.sub_order_return_status', $emailData)->render();



						//echo $viewHtml; die;



						$isMailCustomer = '';



						if(!empty($toEmail)){



							$isMailCustomer = CustomHelper::sendEmail('emails.orders.sub_order_return_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);



							// $isMailAdmin = CustomHelper::sendEmail('emails.orders.admin_order_return_status', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $toEmail, $subject);

                            

                            $isMailAdmin = CustomHelper::sendEmail('emails.orders.admin_sub_order_return_status', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $toEmail, $subject);





						}



						if($isMailCustomer) {

							$response['success'] = true;

							session()->flash('alert-success', 'Order Return Successfullly');

						}

						else{

							$message = 'Error in Submitting Form.'; 

						}





						$user = User::where('id', $order->user_id)->where('deleted', '!=', 1)->first();

						if($order_old_status =='delivered' && $subOrder->order_status == 'return')

						{	

							

							$credit_amount = 0;

							$debit_amount = $order->loyalty_points;



							$user_lpd['order_id'] = $order->id;

							$user_lpd['order_item_id'] = $subOrder->id;	

							$user_lpd['customer_id'] = $order->user_id;

							$user_lpd['credit_amount'] =0;

							$user_lpd['debit_amount'] = $debit_amount;

							$user_lpd['description'] = 'Order Return';

							$user_lpd['created_at'] = date('Y-m-d H:i:s');

							$user_lpd['updated_at'] = date('Y-m-d H:i:s');

							$inserted = LoyaltyPoints::create($user_lpd);



							$loyalty_points = CustomHelper::loyaltyPonitsBalance($order->user_id);





								$to_email = $user->email;

								$user_name= $user->first_name." ".$user->last_name;



								$subject = ''.$debit_amount.' Loyalty Points is debited in your account';



								$tag_line=  ''.$debit_amount.' Loyalty Points is debited in your account';







								$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

								if(empty($ADMIN_EMAIL)){

									$ADMIN_EMAIL = config('custom.admin_email');

								}

								$from_email = $ADMIN_EMAIL;



								$email_data =[];



								$email_data['user_name'] = $user_name;

								$email_data['tag_line'] = $tag_line; 

								$email_data['loyalty_points_data'] = $inserted; 

								$email_data['av_loyalty_points'] = $loyalty_points; 

								$is_mail = CustomHelper::sendEmail('emails.loyalty_points.loyalty_points_transaction', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

							

						}	





					}

					else

					{

						$message = 'Error in Submitting Form.';

					}

					

				}

				else

				{

					$message = 'Error in Submitting Form.';

				}	

			}

			

		}



		$response['message'] = $message;



		return response()->json($response);

	}





	// 12-aug-2020

	private function updateMainOrderStatus($order)

	{

		if(!empty($order) && count($order) > 0 ){

			$order_old_status = $order->order_status;

			$orderItems = $order->orderItems;

			$subOrderStatus = array();



			//$orderStatusArr = config('custom.order_status_arr');



			if(!empty($orderItems) && count($orderItems) > 0){

				$endSubOrderStatus = '';

				foreach($orderItems as $subOrder){

					$subOrderStatus[] = $subOrder->order_status;

					$endSubOrderStatus = $subOrder->order_status;

				}





				if ((count(array_unique($subOrderStatus)) === 1 && end($subOrderStatus) === $endSubOrderStatus) && $order_old_status!=$endSubOrderStatus) 

				{

					$order->order_status = $endSubOrderStatus;

					$isSavedOrder = $order->save();

					//echo 'Update order status=>'.$endSubOrderStatus;

					return true;

				}

				else if(in_array('cancelled', $subOrderStatus))

				{

					$order->order_status = 'partially_cancelled';

					$isSavedOrder = $order->save();

					return true;



				}





			}





		}



		return false;



	}







	/*ajax_print_invoice*/

	public function printInvoiceOld(request $request){



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


    public function redeemLoyalityPoints(request $request){

       // prd($request->toArray());

        $userId = (isset($request->user_id))?$request->user_id:0;

        $response = [];
        $response['success'] = false;

       if(is_numeric($userId) && $userId>0){


        $loyaltyPoints = LoyaltyPoints::where(['customer_id'=>$userId,'status'=>1])->get();

        

        $loyaltyPointsDetails = CustomHelper::findLoyaltyPonitsCriteria($userId,$orderAmount=0,$show=true);



        $credit_total = 0;

        $debit_total = 0;

        $balance = 0;

        if(!empty($loyaltyPoints) && count($loyaltyPoints) > 0){

          foreach($loyaltyPoints as $lp){

            $credit_total = $credit_total + $lp->credit_amount;

             $debit_total = $debit_total + $lp->debit_amount;


          }
      }

        $balance = $credit_total - $debit_total;


        $total_redeem_balance = 0; 
        if(!empty($loyaltyPointsDetails) && $loyaltyPointsDetails['haveCriteria'])
        {
         $total_redeem_balance = $balance * $loyaltyPointsDetails['value_of_points'];
         $total_redeem_balance = floor($total_redeem_balance);
        }  

          // Debit in Loyality Points

            $credit_amount = 0;

            $debit_amount = $balance ;

            $user_lpd = array();

            $user_lpd['order_id'] =0;
            $user_lpd['order_item_id'] = 0;
            $user_lpd['customer_id'] = $userId;
            $user_lpd['credit_amount'] =0;
            $user_lpd['debit_amount'] = $debit_amount;
            $user_lpd['description'] = 'Redeem Points at '.date('Y-m-d H:i:s');
            $user_lpd['created_at'] = date('Y-m-d H:i:s');
            $user_lpd['updated_at'] = date('Y-m-d H:i:s');


            $inserted = LoyaltyPoints::create($user_lpd);

            if($inserted)
            {
                 $loyalty_points = CustomHelper::loyaltyPonitsBalance($userId);


                // CREDIT IN WALLET

                $user    = User::find($userId);
                $userWallet = $user->userWallet;

                $walletCredit = $userWallet->sum('credit_amount');
                $walletDebit = $userWallet->sum('debit_amount');
                $walletBalance = $walletCredit - $walletDebit;

                if(is_numeric($total_redeem_balance) && $total_redeem_balance > 0){

                    $newWalletBalance = $walletBalance + $total_redeem_balance;


                    $walletData = [];

                    $walletData['user_id'] = $userId;
                    $walletData['order_id'] = 0;
                    $walletData['transaction_type'] = 'Redeem Points';
                    $walletData['credit_amount'] = $total_redeem_balance;
                    $walletData['balance'] = $newWalletBalance;
                    $walletData['description'] = 'Credit Amount for Redeem Loyality Points at '.date('Y-m-d H:i:s');

                    UserWallet::insert($walletData);

                  }  

                $response['success'] = true;
               

             }


           
            return response()->json($response);
             

           

       }





    }


	public function printInvoice(request $request){



		$orderId = (isset($request->order_id))?$request->order_id:0;

		//prd($request->all());



		$response = [];



		if(is_numeric($orderId) && $orderId>0){



			//$order = Order::find($orderId);

			$subOrder = OrderItem::where('id', $orderId)->first();

			//prd($subOrder);

			



			if(!empty($subOrder) && count($subOrder) > 0){

				$order = $subOrder->order;

				$orderHistory = DB::table('order_history')->where('order_item_id', $orderId)->get();



				$response['subOrder'] = $subOrder;

				$response['orderHistory'] = $orderHistory;



				$response['order']= $order;

				$response['logoPath']= asset('public/images/logo.png');



				$viewHtml = view('admin.orders.sub_order_invoice', $response)->render();



				//prd($viewHtml);



				$response['viewHtml'] = $viewHtml;

				$response['success'] = true;



				return response()->json($response);

			}

			else{



			}



		}

	}



    public function impersonate($id)
    {
        $user = User::find($id);

       // prd($user->id);

         $user->setImpersonating($user->id);

         return redirect('/users/profile');

      //   prd( Auth::user());

    }

    public function stopImpersonate()
    {
        Auth::user()->stopImpersonating();

        //echo "Welcome back!";

       // flash()->success('Welcome back!');

         return redirect(url(''))->with('alert-success', 'You have successfully logged out!');
    }




	/* end of controller */

}

