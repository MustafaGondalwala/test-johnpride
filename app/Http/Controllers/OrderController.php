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

use App\TempOrder;

use App\TempOrderItem;

use App\User;

use Storage;

use Cart;

use PaytmWallet;



use LaravelMsg91;



class OrderController extends Controller {



	private $restrictedArr = array('#','&',';','$','%',"'",'"',"\'",'\"','<>','()','+','CR','LF','< ?','? >','//< ?..? >','@@','<=','<>','!<','!=','!>','--','/*...*/','AND','OR','BETWEEN','IN','NOT','UNION','DESC','FROM','ALTER','INSERT','DELETE','DROP','LOCK','CONVERT','CAUSE','COMMIT','COUNT','CREATE','EXEC','EXECUTE','IF','ELSE','KILL','IS','ISNULL','LTRIM','LIKE','OBJECT','RETURN','SET','SHUTDOWN','TRUNCATE','WHILE','UPDATE');



	//private $ccMerchantId = '3758';

	//private $ccAccessCode = 'AVPB02GG79AI70BPIA';

	//private $ccWorkingKey = '0B02C88FE461EE1E77C81E23624E3887';



	private $ccMerchantId = '663962';

	private $ccAccessCode = 'AVJM49II34CG84MJGC';

	private $ccWorkingKey = '8E38F43612C8E5370CF2F833C042C832';



	// private $ccAccessCode = 'AVYN03HK53AD01NYDA';

 //    private $ccWorkingKey = '352EF2C3246483E15E822FC2C7B011A9';



    private $cOrderId;



	

	public function index(Request $request){

		return redirect(url('/'));



	}





	public function process(Request $request){
		$method = $request->method();



		$shipping_info = $request->toArray();



		if($method == 'POST' || $method == 'post'){



			//$rules = [];

			//$rules['paymentMethod'] = 'required';

			

			//$this->validate($request, $rules);

			$validator = Validator::make($request->all(), [

				'paymentMethod' => 'required',

			]);



			if ($validator->fails()) {

				return redirect(url('cart/payment-method'))

				->withErrors($validator);

			}



			//prd($request->toArray());



			$shppingAddrId = (isset($request->shppingAddrId))?$request->shppingAddrId:'';

			$wallet_request = (isset($request->isWallet))?$request->isWallet:'';

			$paymentMethod = (isset($request->paymentMethod))?$request->paymentMethod:'';

			// $ccavenuepm = (isset($request->hdfc_paymentMethod))?$request->hdfc_paymentMethod:'';



			$isWallet = '';

			//if($paymentMethod == 'is_wallet')

			if($wallet_request == '1')

			{

				$isWallet = 1;

			}



			//prd($paymentMethod);

			

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

					$existOrder = TempOrder::find($orderId);

				}



				//prd($cartContent);

				//UserAddress::find($shppingAddrId);



				$authCheck = auth()->check();



				$user = auth()->user();



				$shippingAddress = $user->userAddresses->where('id', $shppingAddrId)->first();



					/*pr(session()->all());



					pr($user->toArray());

					prd($shippingAddress->toArray());*/



					$couponId = 0;



					$FREE_DELIVERY_AMOUNT = CustomHelper::WebsiteSettings('FREE_DELIVERY_AMOUNT');

					$SHIPPING_CHARGE = CustomHelper::WebsiteSettings('SHIPPING_CHARGE');

					$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');

					/*$DISCOUNT_AMOUNT = CustomHelper::WebsiteSettings('DISCOUNT_AMOUNT');

					$DISCOUNT_PERCENTAGE = CustomHelper::WebsiteSettings('DISCOUNT_PERCENTAGE');*/



					$totalTaxByLoyaltyPer = 0;

					$totalTaxwithLoyalty = 0;



					$totalTax = 0;

					$offerDiscount = 0;

					$amountForFreeDelivery = 0;



					$totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;



					

					$totalMrp = Cart::getTotalPrice($cartContent);

					$cartTotal = Cart::getTotal($cartContent);

					$totalTax = Cart::getTax($cartContent);



					$productDiscount = $totalMrp - $cartTotal;



					//$totalWithTax = $cartTotal + $totalTax;

					$totalWithTax = $cartTotal;



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



					$loyaltyDiscount = 0;

					$loyaltyDiscountAmt = 0;



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



					//$totalWithCouponDiscount = $cartTotal + $totalTax - $couponDiscountAmt;

					$totalWithCouponDiscount = $cartTotal - $couponDiscountAmt;



					// if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithCouponDiscount >= $FREE_DELIVERY_AMOUNT ){

					// 	$totalShipping = 0;

					// }

					// else{

					// 	$amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $cartTotal;

					// }



					$getshpping_data = CustomHelper::findShippingChargeFromDB($totalWithCouponDiscount);

					if($getshpping_data > 0)
					{
						$totalShipping = $getshpping_data;
					}


					if($authCheck){

						//Loyalyi Points Facilities script goes here

						//$findLoyaltyPonitsCriteria = CustomHelper::findLoyaltyPonitsCriteria($user->id, $totalWithCouponDiscount);


						$findLoyaltyPonitsCriteria = CustomHelper::findLoyaltyPonitsCriteriaForName($user->id, $totalWithCouponDiscount);


						//prd($findLoyaltyPonitsCriteria);


						if(!empty($findLoyaltyPonitsCriteria) && $findLoyaltyPonitsCriteria['freeShipping'] && $findLoyaltyPonitsCriteria['shipping_free_min_order'] <= $totalWithCouponDiscount)
						{
							$totalShipping = 0;
							$amountForFreeDelivery = 0;
						}

						if($findLoyaltyPonitsCriteria && $findLoyaltyPonitsCriteria['shipping_free_min_order'] > 0 && $findLoyaltyPonitsCriteria['shipping_free_min_order'] <= $totalWithCouponDiscount)
							{	
								
								$totalShipping = 0;
								$amountForFreeDelivery = 0;

							}


						if(!empty($findLoyaltyPonitsCriteria) && is_numeric($findLoyaltyPonitsCriteria['discount']) && $findLoyaltyPonitsCriteria['discount'] > 0)

						{	
								$loyaltyDiscount = $findLoyaltyPonitsCriteria['discount'];

								$loyaltyDiscountAmt = $loyaltyDiscount;


									if($findLoyaltyPonitsCriteria['discount_type'] == 'percentage'){

										$loyaltyDiscountAmt = ( $totalWithCouponDiscount * ($loyaltyDiscount/100) );

										

										//$totalTaxByLoyaltyPer = ($totalTax * $loyaltyDiscount)/100;



										//$totalTaxwithLoyalty = ($totalTaxByLoyaltyPer * $totalTax) / 100;

									}



									else{

										

										//$totalTaxByLoyaltyPer = ($loyaltyDiscount * 100)/ $totalWithTax;



										//$totalTaxwithLoyalty = ($totalTaxByLoyaltyPer * $totalTax) / 100;

									}


						}



						//





					}


					//prd($totalShipping);
					$total = $totalWithCouponDiscount - $loyaltyDiscountAmt + $totalShipping;



					//prd($total);



					//$totalBagDiscount = $productDiscount + $offerDiscount;

					$totalBagDiscount = $offerDiscount;



					$paybleAmount = $total;



					$walletAmount = 0;



					$newWalletBalance = 0;

					$walletBalance = 0;



					$paymentStatus = 'pending';



					$isWalletUsed = 0;



					if(isset($isWallet) && ($isWallet == 1 || $isWallet == '1') )

					{

						//if($paymentMethod == "cod"){
							//echo "ok";die;
							 //return back()->with('alert-danger', 'COD is not avaliable');
						//}

						$isWalletUsed = 1;



						$userWallet = $user->userWallet;



						$walletCredit = $userWallet->sum('credit_amount');

						$walletDebit = $userWallet->sum('debit_amount');



						$walletBalance = $walletCredit - $walletDebit;



						if($walletBalance >= $paybleAmount)

						{

							$paybleAmount = 0;

							$walletAmount = $total;

							$paymentMethod = 'Wallet';



							$paymentStatus = 'success';



							$newWalletBalance = $walletBalance - $total;

						}

						else

						{

							$paybleAmount = $total - $walletBalance;

							$walletAmount = $walletBalance;



							$newWalletBalance = 0;

						}



					}





				//	prd("Payble Amount:".$paybleAmount);



				



					$userId = $user->id;



					$order = new TempOrder;



					//$orderData = [];


					if($paybleAmount > 0 && $paymentMethod != 'paytm')
					{
						if(!empty($existOrder) && count($existOrder) > 0){

							$order = $existOrder;

						}
					}


					$order->user_id = $userId;

					$order->shipping_name = $shippingAddress->name;

					$order->shipping_email = (isset($shippingAddress->email))?$shippingAddress->email:$user->email;

					$order->shipping_phone = $shippingAddress->phone;

					$order->shipping_address = $shippingAddress->address;

					$order->shipping_locality = $shippingAddress->locality;

					$order->shipping_pincode = $shippingAddress->pincode;

					$order->shipping_city = $shippingAddress->city;

					$order->shipping_state = $shippingAddress->state;

					$order->shipping_country = $shippingAddress->country;





					$order->billing_name = (!empty($user->name))?$user->name:$order->shipping_name;

					$order->billing_email = (!empty($user->email))?$user->email:$order->shipping_email;

					$order->billing_phone = (!empty($user->phone))?$user->phone:$order->shipping_phone;

					$order->billing_address = (!empty($user->address))?$user->address:$order->shipping_address;

					$order->billing_locality = (!empty($user->locality))?$user->locality:$order->shipping_locality;

					$order->billing_pincode = (!empty($user->pincode))?$user->pincode:$order->shipping_pincode;

					$order->billing_city = (!empty($user->city))?$user->city:$order->shipping_city;

					$order->billing_state = (!empty($user->state))?$user->state:$order->shipping_state;

					$order->billing_country = (!empty($user->country))?$user->country:$order->shipping_country;



					$order->coupon_id = $couponId;

					$order->coupon_data = serialize($couponData);

					$order->coupon_discount = $couponDiscountAmt;

					$order->loyalty_discount = $loyaltyDiscountAmt;

					$order->sub_total = $cartTotal;

					$order->total = $total;

					//$orderData['discount'] = $totalBagDiscount;

					$order->used_wallet_amount = $walletAmount;

					$order->shipping_charge = $totalShipping;

					$order->tax = $totalTax;

					$order->payment_method = $paymentMethod;

					$order->payment_status = $paymentStatus;

					//$order->order_status = 'pending';

					$order->order_status = 'placed';

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



						// session(['orderId' => $orderId]);

						// session()->forget('couponData');



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

							$orderPrefix = 'JP';

						}



						$rno = 9;

						$randomOrderNo = CustomHelper::randomNumberOrder($rno);

						//pr($order_id);

						$orderNo = $orderPrefix.$randomOrderNo;



						$order->order_no = $orderNo;



						if($paybleAmount == 0){

							//$order->order_status = 'confirmed';

							$order->order_status = 'placed';

						}



						//prd($paybleAmount);



						$order->save();



						

						$discount_percentage = ($couponDiscountAmt > 0 )?($couponDiscountAmt / $cartTotal) * 100 : 0;

						

						$loyalty_discount_percentage = ($loyaltyDiscountAmt > 0 )?($loyaltyDiscountAmt / ($cartTotal-$couponDiscountAmt)) * 100 : 0;

						

						$shipping_percentage = ($totalShipping > 0 )?($totalShipping / ($totalWithCouponDiscount- $loyaltyDiscountAmt)) * 100 : 0;



						if(!empty($cartContent) && count($cartContent) > 0){



							$orderItemsData = [];

							$i=1;



							$temp_del = TempOrderItem::where(['order_id'=>$orderId])->delete();



							$total_loyalty_points = 0;

							foreach($cartContent as $item){

								//$total_loyalty_points = $total_loyalty_points + ($item->loyalty_points * $item->qty);





								//prd($item);



								$total = 0;

								$amountForGst = $item->price;





		                        if(is_numeric($item->sale_price) && $item->sale_price > 0 && $item->sale_price < $item->price){

		                            $amountForGst = $item->sale_price;

		                        }



		                        $tax = $this->sub_order_tax($amountForGst,$item->gst,$item->qty);



		                        $sub_total = $amountForGst * $item->qty;

		                        $coupon_discount = 0;

		                        $loyalty_discount = 0;



		                        if($discount_percentage > 0)

		                        {

		                        	$coupon_discount = ($discount_percentage / 100) * $sub_total;

		                        }



		                        if($loyalty_discount_percentage > 0)

		                        {

		                        	$loyalty_discount = ($loyalty_discount_percentage / 100) * ($sub_total - $coupon_discount);

		                        }



		                        $totalWithCouponDiscount = $sub_total - $coupon_discount - $loyalty_discount;



		                        $shipping_charge = 0;



		                        if($shipping_percentage > 0)

		                        {

		                        	$shipping_charge = ($shipping_percentage / 100) * $totalWithCouponDiscount;

		                        }





		                        $loyalty_points = 0;

								$total = $totalWithCouponDiscount + $shipping_charge;



								$LOYALTY_POINT_PERCENT = CustomHelper::WebsiteSettings('LOYALTY_POINT_PERCENT');

								$loyalty_point_percent = ($LOYALTY_POINT_PERCENT > 0)?$LOYALTY_POINT_PERCENT:10;



								$loyalty_points = ($total * 10)/100;

								$total_loyalty_points = $total_loyalty_points + $loyalty_points;





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

								//$itemsData['loyalty_points'] = $item->loyalty_points;

								$itemsData['loyalty_points'] = $loyalty_points;







								//Extra fields for sub order

								$itemsData['sub_order_id'] = $orderId."_".$i;

								$itemsData['sub_order_no'] = $orderNo."_".$i;

								$itemsData['coupon_data'] = '';

								$itemsData['coupon_discount'] = round($coupon_discount,2);

								$itemsData['loyalty_discount'] = round($loyalty_discount,2);

								$itemsData['sub_total'] = round($sub_total,2);

								$itemsData['total'] = round($total,2);

								$itemsData['shipping_charge'] = round($shipping_charge,2);

								$itemsData['tax'] = round($tax,2);

								$itemsData['order_status'] = $order->order_status;

								



								$existCount = TempOrderItem::where(['order_id'=>$orderId, 'product_id'=>$item->product_id, 'size_id'=>$item->size_id])->count();



								if($existCount > 0){

									//OrderItem::where(['order_id'=>$orderId, 'product_id'=>$item->product_id])->update($itemsData);



									$orderItem = TempOrderItem::where(['order_id'=>$orderId, 'product_id'=>$item->product_id])->updateOrCreate($itemsData);

								}

								else{

									//OrderItem::insert($itemsData);

									$orderItem = TempOrderItem::updateOrCreate($itemsData);



									$i++;

								}						



							}



							$order->loyalty_points = $total_loyalty_points;



							$order->save();



							if(!empty($orderItemsData) && count($orderItemsData) > 0){

								

							}

						}





						if($paybleAmount > 0 && $paymentMethod == 'payumoney')

						{	

							?>

							<script type="text/javascript">

								fbq('trackCustom', 'Checkout', {currency:"INR", value:<?php echo $paybleAmount; ?>});

							</script>	

							<?php

							return $this->payumoneyRequest($order);

						}


						if($paybleAmount > 0 && $paymentMethod == 'paytm')
						{	
							return $this->paytmRequest($order);
						}


						// if($paybleAmount > 0 && $paymentMethod == 'ccavenue')
						// {
						// 	echo "yesss";die;
						// 	return $this->paytmRequest($order);
						// }
						if($paybleAmount > 0 && $paymentMethod == 'ccavenue'){
							return $this->ccavenueRequest($order);
						}


						else

						{	

							?>

							<script type="text/javascript">

								fbq('trackCustom', 'Checkout', {currency:"INR", value:<?php echo $paybleAmount; ?>});

							</script>	

							<?php					

							return $this->cod_order($orderId);

						}





					}

				}

			}



			return redirect('cart');

		}




		private function sub_order_tax($amountForGst,$gst,$qty) {

			$tax = 0;

			if(is_numeric($gst) && $gst > 0){ 



                if($amountForGst > 0){                            

                    $withoutGstP = CustomHelper::priceWithoutGst($amountForGst, $gst);

                    $gstPrice = $amountForGst - $withoutGstP;

                    $gstPrice = $gstPrice * $qty;

                    $tax = $gstPrice;

                }



            }



            return $tax;

		}



		private function addOrderUnicommerce($order){

			$response = [];



			$accessToken = CustomHelper::getUnicommerceAccessToken();

			$unicommerce_api_mode = config('custom.unicommerce_api_mode');



			if($unicommerce_api_mode == 'DEMO')
			{

				$unicommerce_api_url = config('custom.unicommerce_demo_api_url');

				$unicommerce_facility = config('custom.unicommerce_demo_facility');



				$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/oms/saleOrder/create';
				//$curl_url = $unicommerce_api_url.'services/rest/v1/oms/saleOrder/create';

			}

			else

			{

				//echo 'hi';

				$unicommerce_api_url = config('custom.unicommerce_api_url');

				$unicommerce_facility = config('custom.unicommerce_facility');



				$curl_url = $unicommerce_api_url.'services/rest/v1/oms/saleOrder/create';



				//pr($curl_url)."Url";

			}

			

			//$unicommerce_facility = 'Warehouse';
			//prd($order);


			if(!empty($order) && count($order) > 0){

				$orderData = [];



				$totalPrepaidAmount = $order->total;



				if($order->payment_method=='cod'){

					$used_wallet_amount = isset($order->used_wallet_amount) ? $order->used_wallet_amount:0;

					if(!empty($used_wallet_amount) && $used_wallet_amount > 0){

						$totalPrepaidAmount = $used_wallet_amount;
					}
					else{
						$totalPrepaidAmount = 0;
					}
					
					//

				}



				$orderData['code'] = "$order->order_no";

				$orderData['displayOrderCode'] = "$order->order_no";

				

				//$orderData['channel'] = "JohnPride";

				$orderData['channel'] = "JohnP";



				//$orderData['channel'] = "John pride";

				$orderData['cashOnDelivery'] = ($order->payment_method=='cod')?true:false;



				$orderData['currencyCode'] = "INR";

				$orderData['customerName'] = "";

				$orderData['notificationEmail'] = "";

				$orderData['notificationMobile'] = "";

				$orderData['totalDiscount'] = $order->coupon_discount;

				$orderData['totalShippingCharges'] = $order->shipping_charge;

				$orderData['totalPrepaidAmount'] = $totalPrepaidAmount;



				$shippingCity = $order->shippingCity;

		        $shippingState = $order->shippingState;

		        $shippingCountry = $order->shippingCountry;



		        $shippinCityName = '';

		        $shippinStateName = '';

		        $shippinCountryName = '';



		        if(isset($shippingCity->name) && !empty($shippingCity->name)){

		          $shippinCityName = $shippingCity->name;

		        }

		        if(isset($shippingState->name) && !empty($shippingState->name)){

		          $shippinStateName = $shippingState->name;

		        }

		        if(isset($shippingCountry->name) && !empty($shippingCountry->name)){

		          $shippinCountryName = $shippingCountry->name;

		        }

				

				$addresses = array(

					 "id"=>"$order->order_no",

					 "name"=>$order->shipping_name,

					 "addressLine1"=>$order->shipping_address,

					 "addressLine2"=>$order->shipping_address,

					 "city"=>$shippinCityName,

					 "state"=>$shippinStateName,

					 "country"=>$shippinCountryName,

					 "pincode"=>$order->shipping_pincode,

					 "phone"=>$order->shipping_phone,

					 "email"=>$order->shipping_email,

				);



				$orderData['addresses'][] = $addresses;



				$orderData['billingAddress'] = array(

					"referenceId"=>"$order->order_no"

				);

				$orderData['shippingAddress'] = array(

					"referenceId"=>"$order->order_no"

				);



				

				$saleOrderItems = array();

				$orderItems = $order->orderItems;



				if(!empty($orderItems) && $orderItems->count()){

					foreach($orderItems as $item){

						$product = $item->productDetail;



						//prd($item->size_name);



						$totalPrice = $product->price*$item->qty;

						//$totalSaleprice = $item->item_price*$item->qty;
						
						$totalSaleprice = $item->sale_price;

						$discountAmt = $totalPrice - $totalSaleprice;

						for($i = 1; $i<=$item->qty;$i++)
						{
							$saleOrderItemsData['itemSku'] = $product->sku.'_'.$item->size_name;

							$saleOrderItemsData['itemName'] = $product->name;

							$saleOrderItemsData['channelProductId'] = $product->sku."-".$item->id;

							$saleOrderItemsData['channelSaleOrderItemCode'] = $product->sku."-".$item->id;

							$saleOrderItemsData['shippingMethodCode']="STD";

							//$saleOrderItemsData['code']=$product->sku."-".$item->id;

							$saleOrderItemsData['code']=$item->sub_order_no;

							if($item->qty > 1)
							{
								$saleOrderItemsData['code']=$item->sub_order_no.'_'.$i;
							}

							$saleOrderItemsData['packetNumber'] = "0";

							$saleOrderItemsData['giftWrap'] = false;

							$saleOrderItemsData['giftMessage'] = "";

							$saleOrderItemsData['totalPrice'] = $totalPrice;

							$saleOrderItemsData['sellingPrice'] = $totalSaleprice;

							$saleOrderItemsData['prepaidAmount'] = 0;

							$saleOrderItemsData['discount'] = 0;

							$saleOrderItemsData['shippingCharges'] = 0;

							$saleOrderItemsData['giftWrapCharges'] = 0;

							$saleOrderItemsData['facilityCode'] = $unicommerce_facility;

							$saleOrderItemsData['shippingAddress'] = array(

													"referenceId"=>"$order->order_no"

												);
							$saleOrderItems[]= $saleOrderItemsData;
	
						}

						

						//prd($saleOrderItemsData);



						

					}

	//echo "<pre>";print_r($saleOrderItems)	;die;			

Storage::disk('local')->put('file.txt', json_encode($saleOrderItems));



				}



				$orderData['saleOrderItems'] = $saleOrderItems;				



				

				$postData['saleOrder']=$orderData;



				$jsonData = json_encode($postData);


				//echo "<pre>";print_r($postData);die;

				$request_arr = array();
				$request_arr['request_url']=$curl_url;
				$request_arr['request']=$jsonData;
				$request_arr['request_time']=date('Y-m-d H:i:s');
				$request_arr['api_type']= 'sale_order_create';


				$api_id = CustomHelper::saveUnicommerceRequest($request_arr);



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

				} 
				else 
				{

					//echo $response;

				 $response_arr = array();

				 $response_arr['response']=$response;
				 $response_arr['response_time']=date('Y-m-d H:i:s');
				 $response_arr['api_type']= 'sale_order_create';
				
				CustomHelper::saveUnicommerceResponse($response_arr,$api_id);	

				}



				curl_close($curl);



				//die;



				//return $response;

			}



			return json_encode($response);



		}



	private function payumoneyRequest($order){



		if(!empty($order) && count($order) > 0){



			$orderId = $order->id;



			$amount = $order->total;



			if(is_numeric($order->used_wallet_amount) && $order->used_wallet_amount > 0)

			{

				$amount = $amount - $order->used_wallet_amount;

			}



			$PAYU_BASE_URL = config('custom.payumoney_base_url');

			$PAYU_KEY = config('custom.payumoney_key');

			$PAYU_SALT = config('custom.payumoney_salt');

			$PAYU_S_URL = config('custom.payumoney_success_url');

			$PAYU_F_URL = config('custom.payumoney_fail_url');

			$PAYU_RESPONSE_URL = config('custom.payumoney_response_url');

			

			$billingName = $order->billing_name;

			$billingAddress = $order->billing_address;

			$billingPhone = $order->billing_phone;

			$billingEmail = $order->billing_email;



			//$paymentData['tid'] = urlencode($tid);

			$paymentData['udf1'] = $orderId;

			$paymentData['key'] = $PAYU_KEY;

			$paymentData['salt'] = $PAYU_SALT;

			$paymentData['payumoney_base_url'] = $PAYU_BASE_URL;

			$paymentData['payumoney_response_url'] = $PAYU_RESPONSE_URL;

			$paymentData['firstname'] = $billingName;

			$paymentData['amount'] = $amount;

			$paymentData['email'] = $billingEmail;

			$paymentData['phone'] = $billingPhone;

			$paymentData['productinfo'] = 'Test';

			$paymentData['surl'] = $PAYU_S_URL;

			$paymentData['furl'] = $PAYU_F_URL;

			$paymentData['service_provider'] = 'payu_paisa';



			//prd($paymentData);



			//return view($this->THEME_NAME.'.order.payumoney_request', $paymentData);

			return view('order.payumoney_request', $paymentData);

			//return view($this->THEME_NAME.'.order.payubiz', $paymentData);



		}

	}



public function payuresponse(Request $request) 

{

		$postdata = $request->toArray();

		//prd($postdata);

		$orderId = $request->udf1;



		$order = TempOrder::find($orderId);



		$salt = config('custom.payumoney_salt');



		if (isset($postdata ['key'])) {

			$key				=   $postdata['key'];

			$txnid 				= 	$postdata['txnid'];

			$amount      		= 	$postdata['amount'];

			$productInfo  		= 	$postdata['productinfo'];

			$firstname    		= 	$postdata['firstname'];

			$email        		=	$postdata['email'];

			$udf5				=   $postdata['udf5'];	

			$status				= 	$postdata['status'];

			$resphash			= 	$postdata['hash'];

			//Calculate response hash to verify	

			$keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';

			$keyArray 	  		= 	explode("|",$keyString);

			$reverseKeyArray 	= 	array_reverse($keyArray);

			$reverseKeyString	=	implode("|",$reverseKeyArray);

	$CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString)); //hash without additionalcharges

	

	//check for presence of additionalcharges parameter in response.

	$additionalCharges  = 	"";

	

	If (isset($postdata["additionalCharges"])) {

		$additionalCharges=$postdata["additionalCharges"];

	   //hash with additionalcharges

		$CalcHashString 	= 	strtolower(hash('sha512', $additionalCharges.'|'.$salt.'|'.$status.'|'.$reverseKeyString));

	}





// 	echo "Response Hash:".$resphash.'<br/>';

// 	echo "CalcHashString:".$CalcHashString.'<br/>';



// die;

	//Comapre status and hash. Hash verification is mandatory.

	//if ($status == 'success'  && $resphash == $CalcHashString) {

if ($status == 'success') {



		//pr($status);

		$msg = "Transaction Successful, Hash Verified...<br />";

		//Do success order processing here...

		//Additional step - Use verify payment api to double check payment.



		$verify_payment = $this->verifyPayment($key,$salt,$txnid,$status);

		//prd($verify_payment);

		



		if($verify_payment)
		{

			/***** MOVE TEMP ORDERS TO MAIN ORDER TABLE *****/

		  $order_no = $this->moveToMainOrder($orderId,$status);			

		  return redirect(url('order/success/'.$order_no));

		}

		



		else

		{



			$order_no = isset($order->order_no) ? $order->order_no :'';

			return redirect(url('order/success/'.$order_no));

		}

			//$msg = "Transaction Successful, Hash Verified...Payment Verification failed...";

			

	}

	else {

		//tampered or failed

		//$msg = "Payment failed for Hash not verified...";

		$order_no = isset($order->order_no) ? $order->order_no :'';

		return redirect(url('order/success/'.$order_no));



		//return redirect(url('order/success/'.$order_no));





	} 

}

else exit(0);



}



private function verifyPayment($key,$salt,$txnid,$status) {

	$command = "verify_payment"; //mandatory parameter

	

	$hash_str = $key  . '|' . $command . '|' . $txnid . '|' . $salt ;

	$hash = strtolower(hash('sha512', $hash_str)); //generate hash for verify payment request



	$r = array('key' => $key , 'hash' =>$hash , 'var1' => $txnid, 'command' => $command);



	$qs= http_build_query($r);

	//for production

	$wsUrl = "https://info.payu.in/merchant/postservice.php?form=2";



	//for test

	//$wsUrl = "https://test.payu.in/merchant/postservice.php?form=2";

	

	try 

	{		

		$c = curl_init();

		curl_setopt($c, CURLOPT_URL, $wsUrl);

		curl_setopt($c, CURLOPT_POST, 1);

		curl_setopt($c, CURLOPT_POSTFIELDS, $qs);

		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);

		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($c, CURLOPT_SSLVERSION, 6); //TLS 1.2 mandatory

		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

		$o = curl_exec($c);

		if (curl_errno($c)) {

			$sad = curl_error($c);

			throw new Exception($sad);

		}

		curl_close($c);

		

		

		$response = json_decode($o,true);

		

		if(isset($response['status']))

		{

			// response is in Json format. Use the transaction_detailspart for status

			$response = $response['transaction_details'];

			$response = $response[$txnid];

			

			if($response['status'] == $status) //payment response status and verify status matched

			return true;

			else

				return false;

		}

		else {

			return false;

		}

	}

	catch (Exception $e){

		return false;	

	}

}



public function paysuccess(Request $request) {



	//prd($request->toArray());

	$orderId = $request->udf1;



	//$orderId = (session()->has('orderId'))?session('orderId'):0;



	if(is_numeric($orderId) && $orderId > 0){

		$order = Order::find($orderId);



		session()->forget('orderId');



		//$paymentMethod = $order->payment_method;



		$order->payment_status = 'success';

		$order->order_status = 'confirmed';	



		$isSaved = $order->save();



		if(is_numeric($orderId) && $orderId > 0){



			$data = [];



			$order = Order::find($orderId);



			$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';

			$order_no = (isset($order->order_no))?$order->order_no:'';

			$payment_method = (isset($order->payment_method))?$order->payment_method:'';



			$this->updateWallet($order);



			//if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

			//$this->updateStock($order);

				//}



			if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

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



			$FROM_EMAIL = CustomHelper::WebsiteSettings('FROM_EMAIL');



			if(empty($FROM_EMAIL)){

				$FROM_EMAIL = config('custom.admin_email');

			}





			$fromEmail = $FROM_EMAIL;



			$emailData = [];

			$emailData['orderId'] = $orderId;

			$emailData['order'] = $order;



				/*$viewHtml = view('emails.orders.customer', $emailData)->render();



				echo $viewHtml; die;*/



				$isMailCustomer = '';



				if(!empty($toEmail)){

					$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);



					Cart::clear();

				}



				if(!empty($customerPhone)){

					$orderLink = url('uo/'.$orderId);

					$smsMessage = "Your Order # $order_no has been placed, check details: $orderLink";



					$smsOpts = [];

					$smsOpts['unicode'] = 1;



					if(CustomHelper::isSmsGateway() ){

						//LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

					}



				}



				$subject = 'New Order placed - Order No: '.$orderId;



				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);



				$data['order'] = $order;



				return view($this->THEME_NAME.'.order.success_failed', $data);



			}



		return redirect(url('order/success/'.$orderId));



	}



	return redirect(url('cart'));

}





public function payfail(Request $request) {

	//$orderId = (session()->has('orderId'))?session('orderId'):0;



	$orderId = $request->udf1;



	$user = auth()->user();

	$userId = isset($user->id) ? $user->id:0;



	if(is_numeric($orderId) && $orderId > 0){

		$order = Order::find($orderId);



		session()->forget('orderId');



		$order->payment_status = 'fail';

		$order->order_status = 'pending';	





		$used_wallet_amount = $order->used_wallet_amount;



		$orderId = $order->id;



		//$user = auth()->user();

		$userId = $order->user_id;



		$user = User::find($userId);





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



		$isSaved = $order->save();



		return redirect(url('order/success/'.$order->id));



		//return redirect(url('order/success'))->with('orderId', $order->id);

	}



	return redirect(url('cart'));

}





	private function moveToMainOrder($orderId,$payment_status)
	{

		$order_no = '';

		if(isset($orderId) && !empty($orderId))

		{

			$order = TempOrder::find($orderId);



			//prd($order);





			if(!empty($order))

			{

				//$order->order_status = 'confirmed';

				$order->order_status = 'placed';

				$order->payment_status = $payment_status;



				$orderItems = $order->orderItems;



				if(!empty($orderItems) && $orderItems->count()){

					foreach($orderItems as $item){



						$item->order_status = 'placed';

						$isSavedSubOrder = $item->save();



					}



				}



				$isSaved = $order->save();





				$data = [];



				$temp_order = TempOrder::find($orderId);

				$orderPrefix = config('custom.order_prefix');



				if(empty($orderPrefix)){

					$orderPrefix = 'JP';

				}





				if(!empty($temp_order)){



					$insertOrder = $temp_order->toArray();



					$insertOrder['temp_order_id'] = $temp_order->id;

					$order_db = Order::create($insertOrder);



					$order_db->save();



					$temp_order_item = $temp_order->orderItems;



					$temp_order_item = $temp_order_item->toArray();







					$i = 1;



					foreach ($temp_order_item as $val){



						$item = new OrderItem;



						foreach ($val as $k => $v){



							$orderNo = $orderPrefix.$order_db->id;

								

							if($k == 'order_id') {

								$item->{$k} = $order_db->id;

							}



							elseif($k == 'sub_order_id') {

								$item->{$k} = $order_db->id."_".$i;

							}



							/*else if($k == 'sub_order_no'){

								$item->{$k} = $orderNo."_".$i;

							}*/

							else{

								$item->{$k} = $v;

							}



						}

						$item->save();



						$i++;

					}







					



					$responseUnicommerce = $this->addOrderUnicommerce($order_db);



					

				}



				$order_db->unicommerce_resp = $responseUnicommerce;

				$order_db->save();



				$paymentStatus = (isset($order_db->payment_status))?$order_db->payment_status:'';

				$order_no = (isset($order_db->order_no))?$order_db->order_no:'';

				$payment_method = (isset($order_db->payment_method))?$order_db->payment_method:'';



				$this->updateWallet($order);



				if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

					Cart::clear();
					session()->forget('couponData');

				}



				// Sending Email to Customer

				$toEmail = $order_db->billing_email;

				$customerPhone = $order_db->billing_phone;

				$subject = 'Order Details - Order No: '.$orderId;



				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

				if(empty($ADMIN_EMAIL)){

					$ADMIN_EMAIL = config('custom.admin_email');

				}



				$fromEmail = $ADMIN_EMAIL;



				$emailData = [];

				$emailData['orderId'] = $orderId;

				$emailData['order'] = $order_db;





				$isMailCustomer = '';



				if(!empty($toEmail)){

					$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

				}





			  if(!empty($customerPhone)){

					$orderLink = url('uo/'.$orderId);

					$smsMessage = "Your Order # $order_no has been placed, check details: $orderLink";



					$urlencodeMessage = urlencode($smsMessage);



					

					//$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage);



				}



				$subject = 'New Order placed - Order No: '.$orderId;





				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);



			}



		}



		return sha1($order_no);

	}



	private function paytmRequest($order)
	{
		if(!empty($order) && count($order) > 0)
		{
			$orderId = $order->id;
			// $orderId = $order->order_no;
			$amount = $order->total;
			$userId = $order->user_id;
			//dd($amount);

			if(is_numeric($order->used_wallet_amount) && $order->used_wallet_amount > 0)
			{
				$amount = $amount - $order->used_wallet_amount;
			}


		    $input['order_id'] = $orderId;

		    $input['fee'] = $amount;
		   

        	
        

        	$user_data = User::find($userId);

        	$phone = isset($user_data->phone) ? $user_data->phone : $order->billing_phone;
        	$email = isset($user_data->email) ? $user_data->email : '';

            $payment = PaytmWallet::with('receive');
	        $payment->prepare([
	          'order' => $input['order_id'],
	          'user' => $userId,
	          'mobile_number' => $phone,
	          'email' => $email,
	          'amount' => $input['fee'],
	          'callback_url' => url('order/paytmCallBack')
	        ]);
        return $payment->receive();


		}
	}

	public function paytmCallBack()
    {
    	//dd("ok");
        $transaction = PaytmWallet::with('receive');
        $response = $transaction->response();
        $orderId = $transaction->getOrderId();

        if($transaction->isSuccessful()){

         // prd($response);	
        //	$status = $response['STATUS'];

          // EventRegistration::where('order_id',$order_id)->update(['status'=>2, 'transaction_id'=>$transaction->getTransactionId()]);
        $status="success";
		
       	  $order_no = $this->moveToMainOrder($orderId,$status);	

		  return redirect(url('order/success/'.$order_no));

          // dd('Payment Successfully Paid.');
        }else if($transaction->isFailed()){
          // EventRegistration::where('order_id',$order_id)->update(['status'=>1, 'transaction_id'=>$transaction->getTransactionId()]);
          // dd('Payment Failed.');
          // return $this->payfail();
      	
       
        if($orderId){

        	$order = TempOrder::find($orderId);

        	$order_no = $order->order_no ;

        }

     

         $new_order_no = sha1($order_no);


		return redirect(url('order/success/'.$new_order_no));

        }

    }    





		private function ccavenueRequest($order){
			
			if(!empty($order) && count($order) > 0){

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

				//$redirectUrl = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';



				$redirectUrl = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

				//$redirectUrl = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

				

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







		public function response_old(Request $request){			



			//$orderId = 4;



			$orderId = (session()->has('orderId'))?session('orderId'):0;



			if(is_numeric($orderId) && $orderId > 0){

				$order = TempOrder::find($orderId);



				session()->forget('orderId');



				$paymentMethod = $order->payment_method;



				if($paymentMethod == 'ccavenue'){

					return $this->ccavenueResponse($order, $request);

				}

			}

			

			return redirect(url('cart'));



		}



		public function cod_order($orderId)

		{

			//$order = TempOrder::find($orderId);



			if(is_numeric($orderId) && $orderId > 0)

			{

				$data= [];





				$temp_order = TempOrder::find($orderId);



				$orderPrefix = config('custom.order_prefix');



				if(empty($orderPrefix)){

					$orderPrefix = 'SJ';

				}

				//pr($temp_order);

				if(!empty($temp_order)){



					$insertOrder = $temp_order->toArray();

					$insertOrder['temp_order_id'] = $temp_order->id;

					//pr($insertOrder);

					$order = Order::create($insertOrder);



					//$order->order_no = $orderPrefix.$order->id;



					$order->save();



					$temp_order_item = $temp_order->orderItems;



					//prd($temp_order_item);

					$temp_order_item = $temp_order_item->toArray();

					



					$i = 1;



					foreach ($temp_order_item as $val){



						$item = new OrderItem;



						foreach ($val as $k => $v){



							$orderNo = $orderPrefix.$order->id;

								

							if($k == 'order_id') {

								$item->{$k} = $order->id;

							}



							elseif($k == 'sub_order_id') {

								$item->{$k} = $order->id."_".$i;

							}



							/*else if($k == 'sub_order_no'){

								$item->{$k} = $orderNo."_".$i;

							}*/

							else{

								$item->{$k} = $v;

							}



						}

						$item->save();



						$i++;

					}



					//$insertSubItem = $temp_order_item->toArray();



					//OrderItem::insert($insertSubItem);



					$responseUnicommerce = $this->addOrderUnicommerce($order);



					//die;

				}

				//prd($order);

				$order->unicommerce_resp = $responseUnicommerce;

				$order->save();





				$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';

				$order_no = (isset($order->order_no))?$order->order_no:'';

				$payment_method = (isset($order->payment_method))?$order->payment_method:'';



				$this->updateWallet($order,'cod');



				if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

					Cart::clear();
					session()->forget('couponData');

				}



				// Sending Email to Customer

				$orderId = $order->id;



				$toEmail = $order->billing_email;

				$customerPhone = $order->billing_phone;

				$subject = 'Order Details - Order No: '.$order_no;



				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

				if(empty($ADMIN_EMAIL)){

					$ADMIN_EMAIL = config('custom.admin_email');

				}



				$fromEmail = $ADMIN_EMAIL;



				$emailData = [];

				$emailData['orderId'] = $order_no;

				$emailData['order'] = $order;



				/*$viewHtml = view('emails.orders.customer', $emailData)->render();



				echo $viewHtml; die;*/



				$isMailCustomer = '';



				if(!empty($toEmail)){

					$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

				}



				if(!empty($customerPhone)){

					$orderLink = url('users/orders');

					$smsMessage = "Your Order # $order_no has been placed, check details: $orderLink";



					$urlencodeMessage = urlencode($smsMessage);



					/*$smsOpts = [];

					$smsOpts['unicode'] = 1;



					if(CustomHelper::isSmsGateway() ){

						LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

					}*/



					//$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage);



				}



				//prd($isMailCustomer);



				// Sending Email to Admin



				//$fromEmail = 'vikas@ehostinguk.com';

				$subject = 'New Order placed - Order No: '.$order_no;





				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);



				$data['order'] = $order;





				$new_order_no = sha1($order_no);



				return redirect(url('order/success/'.$new_order_no));



				//session()->flash('alert-success', 'Order has been placed successfullly.');



				//return view('order.success_failed', $data);



			}



			



		}





		public function response(Request $request){	



			return $this->ccavenueResponse($request);

		}



		private function ccavenueResponse($request){


			//prd($request->toArray());


			$workingKey = $this->ccWorkingKey;

			$encResponse = $request->encResp;

			$orderId = $request->orderNo;

			//echo "order id 1:". $orderId



			$crypto = new Crypto;

			$rcvdString = $crypto->decrypt($encResponse,$workingKey);

			parse_str($rcvdString, $output);

			$paymentResponse = serialize($request->toArray());			

			$paymentOrderStatus = strtolower($output['order_status']);



			//echo "<pre>";print_r($output);die;



		if(!empty($orderId) && $orderId == $output['order_id'])

		//if(!empty($orderId))

		{



			//echo "Order ID 2: ".$orderId;

			$order = TempOrder::find($orderId);



			if(!empty($order))

			{

				if(isset($output['order_status'])){

				$order->payment_status = $paymentOrderStatus;

				$order->payment_txn_id = $output['tracking_id'];

				$order->payment_response = $paymentResponse;





				if($output['order_status'] !== 'Success'){

					$used_wallet_amount = $order->used_wallet_amount;



					//$orderId = $order->id;



					//$user = auth()->user();



					$order_data = DB::table('temp_orders')->where('id', $orderId)->first();



					$userId = $order_data->user_id;

					$user	 = User::find($userId);

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





					$isSaved = $order->save();





				}

				else{

					$order->order_status = 'confirmed';





					//Update sub order status



					$orderItems = $order->orderItems;



					if(!empty($orderItems) && $orderItems->count()){

						foreach($orderItems as $item){



							$item->order_status = 'confirmed';

							$isSavedSubOrder = $item->save();



						}



					}



				$isSaved = $order->save();



				//**********  Insert Order ***************//

			



				$data = [];



				$temp_order = TempOrder::find($orderId);

				$orderPrefix = config('custom.order_prefix');



				if(empty($orderPrefix)){

					$orderPrefix = 'SJ';

				}





				if(!empty($temp_order)){



					$insertOrder = $temp_order->toArray();



				// $temp_order_exisit = DB::table('orders')->where('temp_order_id', $orderId)->first();

				// if(!empty($temp_order_exisit))

				// {

				// 	dd("User orders");

				// 	//return redirect(url('users/orders'));

				// }



					$insertOrder['temp_order_id'] = $temp_order->id;

					$order_db = Order::create($insertOrder);



					$order_db->save();



					$temp_order_item = $temp_order->orderItems;



					$temp_order_item = $temp_order_item->toArray();







					$i = 1;



					foreach ($temp_order_item as $val){



						$item = new OrderItem;



						foreach ($val as $k => $v){



							$orderNo = $orderPrefix.$order_db->id;

								

							if($k == 'order_id') {

								$item->{$k} = $order_db->id;

							}



							elseif($k == 'sub_order_id') {

								$item->{$k} = $order_db->id."_".$i;

							}



							/*else if($k == 'sub_order_no'){

								$item->{$k} = $orderNo."_".$i;

							}*/

							else{

								$item->{$k} = $v;

							}



						}

						$item->save();



						$i++;

					}







					//OrderItem::insert($insertSubItem);



					$responseUnicommerce = $this->addOrderUnicommerce($order_db);



					//die;

				}



				$order_db->unicommerce_resp = $responseUnicommerce;

				$order_db->save();



				



				//prd($order);

				$paymentStatus = (isset($order_db->payment_status))?$order_db->payment_status:'';

				$order_no = (isset($order_db->order_no))?$order_db->order_no:'';

				$payment_method = (isset($order_db->payment_method))?$order_db->payment_method:'';



				$this->updateWallet($order);



				if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

					Cart::clear();

				}



				// Sending Email to Customer

				$toEmail = $order_db->billing_email;

				$customerPhone = $order_db->billing_phone;

				$subject = 'Order Details - Order No: '.$orderId;



				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

				if(empty($ADMIN_EMAIL)){

					$ADMIN_EMAIL = config('custom.admin_email');

				}



				$fromEmail = $ADMIN_EMAIL;



				$emailData = [];

				$emailData['orderId'] = $orderId;

				$emailData['order'] = $order_db;



				/*$viewHtml = view('emails.orders.customer', $emailData)->render();



				echo $viewHtml; die;*/



				$isMailCustomer = '';



				if(!empty($toEmail)){

					$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

				}



				if(!empty($customerPhone)){

					$orderLink = url('uo/'.$orderId);

					$smsMessage = "Your Order # $order_no has been placed, check details: $orderLink";



					$urlencodeMessage = urlencode($smsMessage);



					/*$smsOpts = [];

					$smsOpts['unicode'] = 1;



					if(CustomHelper::isSmsGateway() ){

						LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

					}*/



					//$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage);



				}



				//prd($isMailCustomer);



				// Sending Email to Admin



				//$fromEmail = 'vikas@ehostinguk.com';

				$subject = 'New Order placed - Order No: '.$orderId;





				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);



				//$data['order'] = $order_db;



				//$orderId = 0;



				//return view('order.success_failed', $data);

					//return redirect(url('order/success/'.$orderId));





				}

			}



			$new_order_no = sha1($order->order_no);



			return redirect(url('order/success/'.$new_order_no));



				//return redirect(url('order/success'))->with('orderId', $order->id);

			

			}

			else

			{

				// echo "order 1";

				// die;

				echo "No data with this Order ID";die;

				return redirect(url('/'));

			}

		}

		else{

			echo "You put wrong Order ID";die;

			return redirect(url('/'));

		}	







	}







		private function ccavenueResponse_old($order, $request){

			//prd($request->toArray());



			$workingKey = $this->ccWorkingKey;



			$encResponse = $request->encResp;



			$crypto = new Crypto;



			$rcvdString = $crypto->decrypt($encResponse,$workingKey);



			parse_str($rcvdString, $output);



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

					$order->order_status = 'confirmed';





					//Update sub order status



					$orderItems = $order->orderItems;



					if(!empty($orderItems) && $orderItems->count()){

						foreach($orderItems as $item){



							$item->order_status = 'confirmed';

							$isSavedSubOrder = $item->save();



						}



					}



				}

			}



			$isSaved = $order->save();





			return redirect(url('order/success'))->with('orderId', $order->id);

		}



		public function success(Request $request)

		{

			$data = [];

			$order_no = ($request->order_id) ? $request->order_id : 0;



		    $temp_order = DB::table('temp_orders')->where(DB::raw('sha1(order_no)'), $order_no)->first();



			if(!empty($temp_order))

			{

				$order_id = $temp_order->id;

				$order = TempOrder::find($order_id);

				if(!empty($order))

				{

					$data['order'] = $order;

					return view('order.success_failed', $data);

				}

				else

				{

					return redirect(url('/'));

				}

			}

			else

			{

				return redirect(url('/'));

			}

		}



public function success_2_11_20(Request $request){



			$data= [];

			$orderId = 0;

			$order_id = ($request->order_id) ? $request->order_id : 0;



			//echo "Order id:".$order_id ;die;



			//$order_no = 'SJ'.$order_id;



			$order = DB::table('temp_orders')->where('id', $order_id)->first();



			//$order = Order::find($order_id);



			if(!empty($order))

			{

				//dd('order_empty');

				// if(session()->has('orderId')){

			// 	$orderId = session('orderId');



			// 	session()->forget('orderId');

			// }



				if(!empty($order_id))

				{

					$orderId = $order_id;

				}





		



			if(is_numeric($orderId) && $orderId > 0){



				$data = [];



				$temp_order = TempOrder::find($orderId);

				$orderPrefix = config('custom.order_prefix');



				if(empty($orderPrefix)){

					$orderPrefix = 'SJ';

				}





				if(!empty($temp_order)){



					$insertOrder = $temp_order->toArray();



				$temp_order_exisit = DB::table('orders')->where('temp_order_id', $orderId)->first();

				if(!empty($temp_order_exisit))

				{

					dd("User orders");

					//return redirect(url('users/orders'));

				}



					$insertOrder['temp_order_id'] = $temp_order->id;

					$order = Order::create($insertOrder);



					$order->save();



					$temp_order_item = $temp_order->orderItems;



					$insertSubItem = $temp_order_item->toArray();







					$i = 1;



					foreach ($temp_order_item as $val){



						$item = new OrderItem;



						foreach ($val as $k => $v){



							$orderNo = $orderPrefix.$order->id;

								

							if($k == 'order_id') {

								$item->{$k} = $order->id;

							}



							elseif($k == 'sub_order_id') {

								$item->{$k} = $order->id."_".$i;

							}



							/*else if($k == 'sub_order_no'){

								$item->{$k} = $orderNo."_".$i;

							}*/

							else{

								$item->{$k} = $v;

							}



						}

						$item->save();



						$i++;

					}







					//OrderItem::insert($insertSubItem);



					//$responseUnicommerce = $this->addOrderUnicommerce($order);



					//die;

				}



				//$order->unicommerce_resp = $responseUnicommerce;

				//$order->save();



				//echo "Unicommerce";

				//die;





				//prd($order);

				$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';

				$order_no = (isset($order->order_no))?$order->order_no:'';

				$payment_method = (isset($order->payment_method))?$order->payment_method:'';



				$this->updateWallet($order);



				if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

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



					$urlencodeMessage = urlencode($smsMessage);



					/*$smsOpts = [];

					$smsOpts['unicode'] = 1;



					if(CustomHelper::isSmsGateway() ){

						LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

					}*/



					//$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage);



				}



				//prd($isMailCustomer);



				// Sending Email to Admin



				//$fromEmail = 'vikas@ehostinguk.com';

				$subject = 'New Order placed - Order No: '.$orderId;





				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);



				$data['order'] = $order;



				//$orderId = 0;



				return view('order.success_failed', $data);



				}

			}

			else

			{

				dd("order not empty");

				//return redirect(url('users/orders'));

			}



			



			//return redirect(url('users/orders'));



		}









		public function success_old(){



			



			$data= [];

			$orderId = 0;



			//$orderId = session('orderId');

			if(session()->has('orderId')){

				$orderId = session('orderId');



				session()->forget('orderId');

			}



			//echo $orderId;



			if(is_numeric($orderId) && $orderId > 0){



				$data = [];



				$temp_order = TempOrder::find($orderId);



				$orderPrefix = config('custom.order_prefix');



				if(empty($orderPrefix)){

					$orderPrefix = 'SJ';

				}

				//pr($temp_order);

				if(!empty($temp_order)){



					$insertOrder = $temp_order->toArray();

					$insertOrder['temp_order_id'] = $temp_order->id;

					//pr($insertOrder);

					$order = Order::create($insertOrder);



					//$order->order_no = $orderPrefix.$order->id;



					$order->save();



					$temp_order_item = $temp_order->orderItems;



					//prd($temp_order_item);

					$temp_order_item = $temp_order_item->toArray();

					



					$i = 1;



					foreach ($temp_order_item as $val){



						$item = new OrderItem;



						foreach ($val as $k => $v){



							$orderNo = $orderPrefix.$order->id;

								

							if($k == 'order_id') {

								$item->{$k} = $order->id;

							}



							elseif($k == 'sub_order_id') {

								$item->{$k} = $order->id."_".$i;

							}



							/*else if($k == 'sub_order_no'){

								$item->{$k} = $orderNo."_".$i;

							}*/

							else{

								$item->{$k} = $v;

							}



						}

						$item->save();



						$i++;

					}



					//$insertSubItem = $temp_order_item->toArray();



					//OrderItem::insert($insertSubItem);



					$responseUnicommerce = $this->addOrderUnicommerce($order);



					//die;

				}

				//prd($order);

				$order->unicommerce_resp = $responseUnicommerce;

				$order->save();





				$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';

				$order_no = (isset($order->order_no))?$order->order_no:'';

				$payment_method = (isset($order->payment_method))?$order->payment_method:'';



				$this->updateWallet($order);



				if( strtolower($paymentStatus) == 'success' || strtoupper($payment_method) == 'COD'){

					Cart::clear();

				}



				// Sending Email to Customer

				$orderId = $order->id;



				$toEmail = $order->billing_email;

				$customerPhone = $order->billing_phone;

				$subject = 'Order Details - Order No: '.$order_no;



				$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

				if(empty($ADMIN_EMAIL)){

					$ADMIN_EMAIL = config('custom.admin_email');

				}



				$fromEmail = $ADMIN_EMAIL;



				$emailData = [];

				$emailData['orderId'] = $order_no;

				$emailData['order'] = $order;



				/*$viewHtml = view('emails.orders.customer', $emailData)->render();



				echo $viewHtml; die;*/



				$isMailCustomer = '';



				if(!empty($toEmail)){

					$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

				}



				if(!empty($customerPhone)){

					$orderLink = url('users/orders');

					$smsMessage = "Your Order # $order_no has been placed, check details: $orderLink";



					$urlencodeMessage = urlencode($smsMessage);



					/*$smsOpts = [];

					$smsOpts['unicode'] = 1;



					if(CustomHelper::isSmsGateway() ){

						LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

					}*/



					//$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage);



				}



				//prd($isMailCustomer);



				// Sending Email to Admin



				//$fromEmail = 'vikas@ehostinguk.com';

				$subject = 'New Order placed - Order No: '.$order_no;





				$isMailAdmin = CustomHelper::sendEmail('emails.orders.admin', $emailData, $to=$fromEmail, $fromEmail, $replyTo = $fromEmail, $subject);



				$data['order'] = $order;



				session()->flash('alert-success', 'Order has been placed successfullly.');



				return view('order.success_failed', $data);



			}



			return redirect(url('users/orders'));



		}



		private function updateWallet($order, $pay_type="")

		{



			$orderId = (isset($order->id))?$order->id:'';

			$paymentStatus = (isset($order->payment_status))?$order->payment_status:'';

			$usedWalletAmount = (isset($order->used_wallet_amount))?$order->used_wallet_amount:0;



			if( is_numeric($orderId) && $orderId > 0 && strtolower($paymentStatus) == 'success' && is_numeric($usedWalletAmount) && $usedWalletAmount > 0 )

			{



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



			else if(isset($pay_type) && !empty($pay_type) && $pay_type == 'cod')

			{	

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

				$to_email = 'ramji@indiaint.com';

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



?>

