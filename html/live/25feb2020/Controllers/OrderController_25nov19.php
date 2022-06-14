<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CustomHelper;

use App\Libraries\Ccavenue\Crypto;

use Illuminate\Support\Facades\Validator;

use DB;
use App\Customer;
use App\Order;
use App\OrderItem;
use App\Coupon;
use App\Address;
use App\Product;
use App\Category;
use App\Country;
use App\State;
use App\City;
use App\UserWallet;
use App\UserAddress;

use Cart;

use LaravelMsg91;

class OrderController extends Controller {

	private $restrictedArr = array('#','&',';','$','%',"'",'"',"\'",'\"','<>','()','+','CR','LF','< ?','? >','//< ?..? >','@@','<=','<>','!<','!=','!>','--','/*...*/','AND','OR','BETWEEN','IN','NOT','UNION','DESC','FROM','ALTER','INSERT','DELETE','DROP','LOCK','CONVERT','CAUSE','COMMIT','COUNT','CREATE','EXEC','EXECUTE','IF','ELSE','KILL','IS','ISNULL','LTRIM','LIKE','OBJECT','RETURN','SET','SHUTDOWN','TRUNCATE','WHILE','UPDATE');

	private $ccMerchantId = '3758';
	private $ccWorkingKey = '0B02C88FE461EE1E77C81E23624E3887';
	private $ccAccessCode = 'AVPB02GG79AI70BPIA';

	
	public function index(Request $request){
		return redirect(url('/'));

	}


	public function process(Request $request){
		$method = $request->method();

		$shipping_info = $request->toArray();

		if($method == 'POST' || $method == 'post'){

				//prd($request->toArray());

			$shppingAddrId = (isset($request->shppingAddrId))?$request->shppingAddrId:'';
			$isWallet = (isset($request->isWallet))?$request->isWallet:'';

			$cartContent = Cart::getContent();

			if(is_numeric($shppingAddrId) && $shppingAddrId > 0 && count($cartContent) > 0){

				$orderId = 0;

				if(!empty($cartContent) && count($cartContent) > 0){

					$orderItemsData = [];

					foreach($cartContent as $item){
						if(is_numeric($item->order_id) && $item->order_id > 0){
							$orderId = $item->order_id;
							break;
						}
					}
				}

				$existOrder = '';

				if($orderId > 0){
					$existOrder = Order::find($orderId);
				}

				//UserAddress::find($shppingAddrId);

				$authCheck = auth()->check();

				$user = auth()->user();

				$shippingAddress = $user->userAddresses->where('id', $shppingAddrId)->first();

					/*pr(session()->all());

					pr($user->toArray());
					prd($shippingAddress->toArray());*/

					$couponId = 0;

					$paymentMethod = 'ccavenue';

					$FREE_DELIVERY_AMOUNT = CustomHelper::WebsiteSettings('FREE_DELIVERY_AMOUNT');
					$SHIPPING_CHARGE = CustomHelper::WebsiteSettings('SHIPPING_CHARGE');
					$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');
					/*$DISCOUNT_AMOUNT = CustomHelper::WebsiteSettings('DISCOUNT_AMOUNT');
					$DISCOUNT_PERCENTAGE = CustomHelper::WebsiteSettings('DISCOUNT_PERCENTAGE');*/

					$totalTax = 0;
					$offerDiscount = 0;
					$amountForFreeDelivery = 0;

					$totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;

					
					$totalMrp = Cart::getTotalPrice($cartContent);
					$cartTotal = Cart::getTotal($cartContent);
					$totalTax = Cart::getTax($cartContent);

					$productDiscount = $totalMrp - $cartTotal;

					$totalWithTax = $cartTotal + $totalTax;

					/*if(is_numeric($DISCOUNT_AMOUNT) && $cartTotal >= $DISCOUNT_AMOUNT){
						if(is_numeric($DISCOUNT_PERCENTAGE) && $DISCOUNT_PERCENTAGE > 0){
							$offerDiscount = ( $cartTotal * ($DISCOUNT_PERCENTAGE / 100) );
						}
					}*/

					$subTotal = $cartTotal - $offerDiscount;

					$minAmountForCouponTxt = '';

					$isCoupon = false;

					$couponDiscountAmt = 0;	

					$couponData = '';

					if($authCheck){

						if(session()->has('couponData')){
							$couponData = session('couponData');

							if(isset($couponData['id']) && is_numeric($couponData['id']) && $couponData['id'] > 0){
								$couponId = $couponData['id'];

								$isCoupon = true;

								$minAmountForCoupon = (isset($couponData['min_amount']))?$couponData['min_amount']:0;

								if(is_numeric($minAmountForCoupon) && $minAmountForCoupon > 0 && $minAmountForCoupon > $cartTotal){
									$couponData['discount'] = 0;

									$minAmountForCouponTxt = 'To use this Coupon Total should be greater or equal to '.number_format($minAmountForCoupon);
								}

								if(is_numeric($couponData['discount']) && $couponData['discount'] > 0){

									$couponDiscount = $couponData['discount'];
									$couponDiscountAmt = $couponDiscount;

									if($couponData['type'] == 'percentage'){
										$couponDiscountAmt = ( $cartTotal * ($couponDiscount/100) );
									}

									if(is_numeric($couponData['max_discount_amt']) && $couponData['max_discount_amt'] > 0){
										if($couponDiscountAmt > $couponData['max_discount_amt']){
											$couponDiscountAmt = $couponData['max_discount_amt'];
										}

									}
								}
							}
						}
					}

					$totalWithCouponDiscount = $cartTotal + $totalTax - $couponDiscountAmt;

					if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithCouponDiscount >= $FREE_DELIVERY_AMOUNT ){
						$totalShipping = 0;
					}
					else{
						$amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $cartTotal;
					}

					$total = $totalWithCouponDiscount + $totalShipping;

					//$totalBagDiscount = $productDiscount + $offerDiscount;
					$totalBagDiscount = $offerDiscount;

					$paybleAmount = $total;

					$walletAmount = 0;

					$newWalletBalance = 0;

					$paymentStatus = 'pending';

					$isWalletUsed = 0;

					if(isset($isWallet) && ($isWallet == 1 || $isWallet == '1') ){

						$isWalletUsed = 1;

						$userWallet = $user->userWallet;

						$walletCredit = $userWallet->sum('credit_amount');
						$walletDebit = $userWallet->sum('debit_amount');

						$walletBalance = $walletCredit - $walletDebit;

						if($walletBalance >= $total){
							$paybleAmount = 0;
							$walletAmount = $total;
							$paymentMethod = 'Wallet';

							$paymentStatus = 'success';

							$newWalletBalance = $walletBalance - $total;
						}
						else{
							$paybleAmount = $total - $walletBalance;
							$walletAmount = $walletBalance;

							$newWalletBalance = 0;
						}
					}

					/*pr($request->toArray());

					prd($paybleAmount);*/

					$userId = $user->id;

					$order = new Order;

					//$orderData = [];

					if(!empty($existOrder) && count($existOrder) > 0){
						$order = $existOrder;
					}

					$order->user_id = $userId;
					$order->billing_name = $user->name;
					$order->billing_email = $user->email;
					$order->billing_phone = $user->phone;
					$order->billing_address = $user->address;
					$order->billing_locality = $user->locality;
					$order->billing_pincode = $user->pincode;
					$order->billing_city = $user->city;
					$order->billing_state = $user->state;
					$order->billing_country = $user->country;
					$order->shipping_name = $shippingAddress->name;
					$order->shipping_email = (isset($shippingAddress->email))?$shippingAddress->email:$user->email;
					$order->shipping_phone = $shippingAddress->phone;
					$order->shipping_address = $shippingAddress->address;
					$order->shipping_locality = $shippingAddress->locality;
					$order->shipping_pincode = $shippingAddress->pincode;
					$order->shipping_city = $shippingAddress->city;
					$order->shipping_state = $shippingAddress->state;
					$order->shipping_country = $shippingAddress->country;
					$order->coupon_id = $couponId;
					$order->coupon_data = serialize($couponData);
					$order->coupon_discount = $couponDiscountAmt;
					$order->sub_total = $cartTotal;
					$order->total = $total;
					//$orderData['discount'] = $totalBagDiscount;
					$order->used_wallet_amount = $walletAmount;
					$order->shipping_charge = $totalShipping;
					$order->tax = $totalTax;
					$order->payment_method = $paymentMethod;
					$order->payment_status = $paymentStatus;
					$order->order_status = 'pending';
					$order->ip_address = $request->ip();

					//pr($orderData);

					$cartIds = $cartContent->pluck('id')->toArray();

					//prd($cartIds);

					//$order = Order::create($orderData);
					$order->save();
					//$order = Order::find(1);

					//prd($order->toArray());

					if(isset($order->id) && $order->id > 0){

						$orderId = $order->id;

						session(['orderId' => $orderId]);
						session()->forget('couponData');

						/*if(is_numeric($walletAmount) && $walletAmount > 0){
							$walletData = [];
							$walletData['user_id'] = $userId;
							$walletData['order_id'] = $orderId;
							$walletData['transaction_type'] = 'Order No-'.$orderId;
							$walletData['debit_amount'] = $walletAmount;
							$walletData['balance'] = $newWalletBalance;
							$walletData['description'] = 'Amount debited for Order No-'.$orderId;
							UserWallet::insert($walletData);
						}*/

						/*$cartOrderData = [];
						$cartOrderData['user_id'] = $userId;
						$cartOrderData['order_id'] = $orderId;
						$cartOrderData['cart_ids'] = serialize($cartIds);
						$cartOrderData['is_wallet_used'] = $isWalletUsed;
						$cartOrderData['used_wallet_amount'] = $walletAmount;
						$cartOrderData['payment_status'] = $paymentStatus;

						DB::table('user_cart_order')->insert($cartOrderData);*/

						//prd($cartOrderData);

						$orderPrefix = config('custom.order_prefix');

						if(empty($orderPrefix)){
							$orderPrefix = 'SJ';
						}

						$orderNo = $orderPrefix.$order->id;

						$order->order_no = $orderNo;

						if($paybleAmount == 0){
							$order->order_status = 'success';
						}

						$order->save();

						if(!empty($cartContent) && count($cartContent) > 0){

							$orderItemsData = [];

							foreach($cartContent as $item){

								$item->order_id = $orderId;
								$item->save();

								$itemsData['order_id'] = $orderId;
								$itemsData['product_id'] = $item->product_id;
								$itemsData['size_id'] = $item->size_id;
								$itemsData['product_name'] = $item->product_name;
								$itemsData['size_name'] = $item->size_name;
								$itemsData['product_slug'] = $item->product_slug;
								$itemsData['product_sku'] = $item->product_sku;
								$itemsData['product_gender'] = $item->product_gender;
								$itemsData['qty'] = $item->qty;
								$itemsData['price'] = $item->price;
								$itemsData['sale_price'] = $item->sale_price;
								$itemsData['item_price'] = $item->cart_price;
								$itemsData['gst'] = $item->gst;
								$itemsData['weight'] = $item->weight;
								$itemsData['color_id'] = $item->color_id;
								$itemsData['color_name'] = $item->color_name;

								//$orderItemsData[] = $itemsData;

								$existCount = OrderItem::where(['order_id'=>$orderId, 'product_id'=>$item->product_id, 'size_id'=>$item->size_id])->count();

								if($existCount > 0){
									OrderItem::where(['order_id'=>$orderId, 'product_id'=>$item->product_id])->update($itemsData);
								}
								else{
									OrderItem::insert($itemsData);
								}

							}

							if(!empty($orderItemsData) && count($orderItemsData) > 0){
								//$isSavedOrderItems = OrderItem::insert($orderItemsData);

								/*if($isSavedOrderItems){
									Cart::clear();
								}*/
							}
						}

						if($paybleAmount > 0 && $paymentMethod == 'ccavenue'){
							return $this->ccavenueRequest($order);
						}
						else{
							return redirect(url('order/success'));
						}
					}
				}
			}

			return redirect('cart');
		}




		private function ccavenueRequest($order){

			if(!empty($order) && count($order) > 0){
				//pr($order);
				$orderId = $order->id;

				$amount = $order->total;

				if(is_numeric($order->used_wallet_amount) && $order->used_wallet_amount > 0){
					$amount = $amount - $order->used_wallet_amount;
				}

				/* Billing */

				$billingName = $order->billing_name;
				$billingAddress = $order->billing_address;
				$billingPhone = $order->billing_phone;
				$billingEmail = $order->billing_email;
				$billingZip = $order->billing_pincode;

				$billingCity = $order->billingCity;
				$billingState = $order->billingState;
				$billingCountry = $order->billingCountry;

				$billingCityName = '';
				$billingStateName = '';
				$billingCountryName = '';


				if(isset($billingCity->name) && !empty($billingCity->name)){
					$billingCityName = $billingCity->name;
				}
				if(isset($billingState->name) && !empty($billingState->name)){
					$billingStateName = $billingState->name;
				}
				if(isset($billingCountry->nicename) && !empty($billingCountry->nicename)){
					$billingCountryName = $billingCountry->nicename;
				}

				/* Shipping */

				$shippingName = $order->shipping_name;
				$shippingAddress = $order->shipping_address;
				$shippingPhone = $order->shipping_phone;
				$shippingEmail = $order->shipping_email;
				$shippingZip = $order->shipping_pincode;

				$shippingCity = $order->shippingCity;
				$shippingState = $order->shippingState;
				$shippingCountry = $order->shippingCountry;

				$shippingCityName = '';
				$shippingStateName = '';
				$shippingCountryName = '';


				if(isset($shippingCity->name) && !empty($shippingCity->name)){
					$shippingCityName = $shippingCity->name;
				}
				if(isset($shippingState->name) && !empty($shippingState->name)){
					$shippingStateName = $shippingState->name;
				}
				if(isset($shippingCountry->nicename) && !empty($shippingCountry->nicename)){
					$shippingCountryName = $shippingCountry->nicename;
				}

				$currency = 'INR';

				$merchant_id = $this->ccMerchantId;

				$tid = $order->order_no;

				$responseUrl = url('order/response');

				$paymentData = [];

				//$paymentData['tid'] = urlencode($tid);
				$paymentData['merchant_id'] = $merchant_id;
				$paymentData['order_id'] = $orderId;
				$paymentData['amount'] = $amount;
				$paymentData['currency'] = $currency;
				$paymentData['redirect_url'] = $responseUrl;
				$paymentData['cancel_url'] = $responseUrl;
				$paymentData['language'] = "EN";

				$paymentData['billing_name'] = $billingName;
				$paymentData['billing_address'] = $billingAddress;
				$paymentData['billing_city'] = $billingCityName;
				$paymentData['billing_state'] = $billingStateName;
				$paymentData['billing_zip'] = $billingZip;
				$paymentData['billing_country'] = $billingCountryName;
				$paymentData['billing_tel'] = $billingPhone;
				$paymentData['billing_email'] = $billingEmail;

				$paymentData['delivery_name'] = $shippingName;
				$paymentData['delivery_address'] = $shippingAddress;
				$paymentData['delivery_city'] = $shippingCityName;
				$paymentData['delivery_state'] = $shippingStateName;
				$paymentData['delivery_zip'] = $shippingZip;
				$paymentData['delivery_country'] = $shippingCountryName;
				$paymentData['delivery_tel'] = $shippingPhone;

				//prd($paymentData);

				$merchant_data ='';
				$working_key = $this->ccWorkingKey;
				$access_code= $this->ccAccessCode;

				foreach ($paymentData as $key => $value){
					$merchant_data.=$key.'='.urlencode($value).'&';
				}

				$crypto = new Crypto;

				// Method for encrypting the data.
				$encrypted_data = $crypto->encrypt($merchant_data, $working_key);

				//pr($merchant_data);
				$redirectUrl = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

				/*$inputData = [];
				$inputData['encRequest'] = $encrypted_data;
				$inputData['access_code'] = $access_code;

				//prd($inputData);

				return redirect()->to($redirectUrl)->withInput($inputData);

				die;*/

				$data = [];

				$data['redirectUrl'] = $redirectUrl;
				$data['encrypted_data'] = $encrypted_data;
				$data['access_code'] = $access_code;

				return view('order.ccavenue_request', $data);

			}
			//die;
		}



		public function response(Request $request){			

			//$orderId = 4;

			$orderId = (session()->has('orderId'))?session('orderId'):0;

			if(is_numeric($orderId) && $orderId > 0){
				$order = Order::find($orderId);

				session()->forget('orderId');

				$paymentMethod = $order->payment_method;

				if($paymentMethod == 'ccavenue'){
					return $this->ccavenueResponse($order, $request);
				}
			}
			
			return redirect(url('cart'));

		}


		private function ccavenueResponse($order, $request){
			//prd($request->toArray());

			$workingKey = $this->ccWorkingKey;

			$encResponse = $request->encResp;

			$crypto = new Crypto;

			$rcvdString = $crypto->decrypt($encResponse,$workingKey);

			parse_str($rcvdString, $output);

			//pr($rcvdString);

			$paymentResponse = serialize($request->toArray());			

			$order->payment_response = $paymentResponse;

			$paymentOrderStatus = strtolower($output['order_status']);

			if(isset($output['order_status'])){
				$order->payment_status = $paymentOrderStatus;
				$order->payment_txn_id = $output['tracking_id'];

				if($output['order_status'] !== 'Success'){
					$used_wallet_amount = $order->used_wallet_amount;

					$orderId = $order->id;

					$user = auth()->user();

					$userWallet = $user->userWallet;

					$walletCredit = $userWallet->sum('credit_amount');
					$walletDebit = $userWallet->sum('debit_amount');

					$walletBalance = $walletCredit - $walletDebit;

					if(is_numeric($used_wallet_amount) && $used_wallet_amount > 0){

						$newWalletBalance = $walletBalance + $used_wallet_amount;

						$walletData = [];
						$walletData['user_id'] = $userId;
						$walletData['order_id'] = $orderId;
						$walletData['transaction_type'] = 'Order No-'.$orderId;
						$walletData['credit_amount'] = $used_wallet_amount;
						$walletData['balance'] = $newWalletBalance;
						$walletData['description'] = 'Amount credited on Online payment failed for Order No-'.$orderId;
						UserWallet::insert($walletData);

						$order->used_wallet_amount = 0;
					}

				}
				else{
					$order->order_status = 'success';
				}
			}

			$isSaved = $order->save();

			/*if($isSaved){

			}*/

			return redirect(url('order/success'))->with('orderId', $order->id);
		}


		public function success(){

			//echo 'success'; die;

			$data= [];
			$orderId = 0;

			if(session()->has('orderId')){
				$orderId = session('orderId');

				session()->forget('orderId');
			}

			//$orderId = 25;

			if(is_numeric($orderId) && $orderId > 0){

				$data = [];

				$order = Order::find($orderId);

				$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';
				$order_no = (isset($order->order_no))?$order->order_no:'';

				$this->updateWallet($order);

				if( strtolower($paymentStatus) == 'success' ){
					Cart::clear();
				}

				// Sending Email to Customer
				$toEmail = $order->billing_email;
				$customerPhone = $order->billing_phone;
				$subject = 'Order Details - Order No: '.$orderId;

				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
				if(empty($ADMIN_EMAIL)){
					$ADMIN_EMAIL = config('custom.admin_email');
				}

				$fromEmail = $ADMIN_EMAIL;

				$emailData = [];
				$emailData['orderId'] = $orderId;
				$emailData['order'] = $order;

				/*$viewHtml = view('emails.orders.customer', $emailData)->render();

				echo $viewHtml; die;*/

				$isMailCustomer = '';

				if(!empty($toEmail)){
					$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);
				}

				if(!empty($customerPhone)){
					$orderLink = url('uo/'.$orderId);
					$smsMessage = "Your Order # $order_no has been placed, check details: $orderLink";

					$smsOpts = [];
					$smsOpts['unicode'] = 1;

					if(CustomHelper::isSmsGateway() ){
						LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);
					}

				}

				//prd($isMailCustomer);

				// Sending Email to Admin

				//$fromEmail = 'vikas@ehostinguk.com';
				$subject = 'New Order placed - Order No: '.$orderId;


				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);

				$data['order'] = $order;

				return view('order.success_failed', $data);

			}

			return redirect(url('users/orders'));

		}

		private function updateWallet($order){

			$orderId = (isset($order->id))?$order->id:'';
			$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';
			$usedWalletAmount = (isset($order->used_wallet_amount))?$order->used_wallet_amount:0;

			if( is_numeric($orderId) && $orderId > 0 && strtolower($paymentStatus) == 'success' && is_numeric($usedWalletAmount) && $usedWalletAmount > 0 ){

				$user = auth()->user();

				$userId = $user->id;

				$userWallet = $user->userWallet;

				$walletCredit = $userWallet->sum('credit_amount');
				$walletDebit = $userWallet->sum('debit_amount');

				$walletBalance = $walletCredit - $walletDebit;

				$newWalletBalance = $walletBalance - $usedWalletAmount;

				if(is_numeric($usedWalletAmount) && $usedWalletAmount > 0){
					$walletData = [];
					$walletData['user_id'] = $userId;
					$walletData['order_id'] = $orderId;
					$walletData['transaction_type'] = 'Order No-'.$orderId;
					$walletData['debit_amount'] = $usedWalletAmount;
					$walletData['balance'] = $newWalletBalance;
					$walletData['description'] = 'Amount debited for Order No-'.$orderId;
					UserWallet::insert($walletData);
				}

			}
			
		}

		/* not in use */
		public function failed_old(){

			$data= [];
			$order_id = 0;

			if(session()->has('order_id'))
			{
				$order_id = session('order_id');
			}

		//echo 'order_id = ';pr($order_id);

		//$order_id= 5; 

			if(empty($order_id))
			{

				return redirect(url('/'));
			}



			if(is_numeric($order_id) && $order_id > 0)
			{


				$data=[];
				$order_model=new Order; 
				$res=Order::where(['order_id'=>$order_id])->first();
				$data['res']= $res;
				session()->forget('order_id');

				$order_success= false;
				$tagline= 'Your order is failed with the order id:'.$order_id;

				$data['tag_line']=$tagline; 
				$data['order_success']=$order_success;
				$data['billing_country']= Country::where(['id'=>$res->billing_country])->first();
				$data['billing_state']=State::where(['id'=>$res->billing_state])->first();
				$data['billing_city']= City::where(['id'=>$res->billing_city])->first();

				$data['shipping_country']= Country::where(['id'=>$res->billing_country])->first();

				$data['shipping_state']=State::where(['id'=>$res->billing_state])->first();

				$data['shipping_city']= City::where(['id'=>$res->billing_city])->first();

				$data['order_model']=$order_model;

			// Sending Email to Customer
				$to_email = $res->billing_email;
				$subject = 'Orer Success -'.$order_id;
				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
				if(empty($ADMIN_EMAIL))
				{
					$ADMIN_EMAIL = config('custom.admin_email');
				}
				$from_email = $ADMIN_EMAIL;

				$email_data =$data;
				$user_name= $res->billing_first_name." ".$res->billing_last_name;
				$email_data['user_name'] = $user_name;

				$tag_line= "Hi $user_name, Your order is failed with the order id:".$order_id;
				$email_data['tag_line'] = $tag_line; 


				$is_mail = CustomHelper::sendEmail('emails.orders.customer.order_success_failed', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);



			// Sending Email to Admin
				$to_email = 'vikas@indiaint.com';
				$subject = 'Orer Success -'.$order_id;
				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
				if(empty($ADMIN_EMAIL))
				{
					$ADMIN_EMAIL = config('custom.admin_email');
				}
				$from_email = $ADMIN_EMAIL;

				$email_data =$data;
				$user_name='Admin';
				$email_data['user_name'] = $user_name;
				$tag_line= "Hi $user_name, New order is failed with the order id:".$order_id;
				$email_data['tag_line'] = $tag_line;
				$is_mail = CustomHelper::sendEmail('emails.orders.admin.order_success_failed', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);




				return view('order.success_failed', $data);

			}



		}

	/**
	 * Generate a random & unique order number
	 *
	 * @return int
	 */


	private function __generateOrderNumber() {
		$number = mt_rand(1000000000, 9999999999);

		// Re-call this function again if this order number already exists
		if (Order::whereOrderNumber($number)->exists()) {
			return $this->__generateOrderNumber();
		}

		// Otherwise, it's valid and can be used
		return $number;
	}

}