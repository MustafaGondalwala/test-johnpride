<?php



namespace App\Http\Controllers;



use App\User;

use App\UserCartItem;

use App\Order;

use App\OrderItem;

use App\Product;

use App\ProductInventory;

use App\Setting;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\LoyaltyPoints;

use App\Helpers\CustomHelper;

use App\Libraries\Cart;

use DB;

use Validator;

//use LaravelMsg91;



class ProcessJobsController extends Controller {



	public function __construct(){



	}



	public function index(){

		echo "unauthorized"; die;

	}





	//http://slumberjill.ii71.com/processjobs/getInventory

	//getInventory

	//get_inventory

	public function getInventory(Request $request){



		//echo bcrypt('astra@2019'); die;

		

		//prd($currentDataTime);

		$result = [];



		$result['success'] = false;



		$isUpdated = '';



		$method = $request->method();



		//$accessToken = $this->getUnicommerceAccessToken();

		$accessToken = CustomHelper::getUnicommerceAccessToken();



		//prd($accessToken);



		if(!empty($accessToken)){



			$inv_select = ['id','sku','product_id','size_id','size_name','price','stock','created_at','updated_at'];



			$productInventories = DB::table('product_inventory')->select($inv_select)->get();



			$invSkuArr = [];

			if(!empty($productInventories)){

				//prd($productInventories);



				foreach($productInventories as $inv){

					if(!empty($inv->sku)){

						$invSkuArr[] = $inv->sku;

					}

				}

				//prd($invSkuArr);

				if(!empty($invSkuArr)){

					//pr($invSkuArr);

					//prd(json_encode($invSkuArr));



					$invSkuArrJson = json_encode($invSkuArr);



					$response = $this->getUnicommerceSkuInventory($invSkuArr, $accessToken);



					$resp = json_decode($response);



					//prd($response);



					if(isset($resp->inventorySnapshots) && count($resp->inventorySnapshots) > 0){

						foreach($resp->inventorySnapshots as $skuData){

							//prd($skuData);



							$sku = $skuData->itemTypeSKU;

							$inventory = $skuData->inventory;



							if(!empty($sku) && is_numeric($inventory)){

								$isInvUpdated = DB::table('product_inventory')->where('sku', $sku)->update(['stock'=>$inventory]);



								if($isInvUpdated){

									$isUpdated = $isInvUpdated;

								}

							}



						}

					}

				}

			}



		}



		if($isUpdated && !empty($isUpdated)){

			$result['success'] = true;



			$currentDataTime = date('Y-m-d H:i:s');

			

			$settingData = Setting::where('name','LAST_UPDATED_TIME_UNICOMMERCE_INVENTORY')->first();

			$old_value = (isset($settingData->value)) ? $settingData->value:'';



			$settingData->value = $currentDataTime;

			$settingData->old_value = $old_value;

			$settingData->save();

		}



		return response()->json($result);



	}







	private function getUnicommerceSkuInventory($invSkuArr, $accessToken){



		//prd($invSkuArrJson);



		if(!empty($invSkuArr)){



			$unicommerce_api_url = config('custom.unicommerce_api_url');

			$unicommerce_facility = config('custom.unicommerce_facility');



			$curl_url = $unicommerce_api_url.'services/rest/v1/inventory/inventorySnapshot/get';



			$postData = [];

			$postData['itemTypeSKUs'] = $invSkuArr;

			//$postData['updatedSinceInMinutes'] = 20;



			//prd(json_encode($postData));



			$jsonData = json_encode($postData);



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



			curl_close($curl);



			if ($err) {

				//echo "cURL Error #:" . $err;

			} else {

				//echo $response;



				/*$resp = json_decode($response);



				echo 'getUnicommerceSkuInventory=';



				prd($resp);



				if(isset($resp->error) && $resp->error == 'invalid_token'){

					//$this->

				}*/

			}



			return $response;



		}



	}



	private function getUnicommerceAccessToken(){



		$token_select = ['id','access_token','token_type','refresh_token','expires_in','created_at','updated_at'];



		$curr_date = date('Y-m-d');

		//DB::enableQueryLog();

		$token_data = DB::table('unicommerce_api')->select($token_select)->orderBy('updated_at', 'desc')->first();

		//prd(DB::getQueryLog());



		$accessToken = '';



		$access_token = (isset($token_data->access_token))?$token_data->access_token:'';

		$token_updated_at = (isset($token_data->updated_at))?$token_data->updated_at:'';



		$token_updated_date = CustomHelper::DateFormat($token_updated_at, 'Y-m-d');



		//pr($curr_date);

		//prd($token_updated_date);



		/*pr($curr_date);

		prd($token_updated_date);*/



		if($curr_date == $token_updated_date ){

			$accessToken = $access_token;

		}



		//echo 'getUnicommerceAccessToken==';



		//prd($accessToken);



		if(empty($accessToken)){



			$api_token_data = $this->generateUnicommerceAccessToken();



			if(isset($api_token_data->access_token) && !empty($api_token_data->access_token) ){

				$dbData = [];

				$dbData['access_token'] = $api_token_data->access_token;

				$dbData['token_type'] = $api_token_data->token_type;

				$dbData['refresh_token'] = $api_token_data->refresh_token;

				$dbData['expires_in'] = $api_token_data->expires_in;

				$dbData['created_at'] = date('Y-m-d H:i:s');

				$dbData['updated_at'] = date('Y-m-d H:i:s');



				DB::table('unicommerce_api')->delete();

				DB::table('unicommerce_api')->insert($dbData);



				$accessToken = $api_token_data->access_token;



			}

		}



		return $accessToken;

		

	}



	private function generateUnicommerceAccessToken(){

		$curl = curl_init();



		$unicommerce_api_url = config('custom.unicommerce_api_url');

		$unicommerce_username = config('custom.unicommerce_username');

		$unicommerce_password = config('custom.unicommerce_password');



		$username = urlencode($unicommerce_username);



		$curl_url = $unicommerce_api_url.'oauth/token?grant_type=password&client_id=my-trusted-client&username='.$unicommerce_username.'&password='.$unicommerce_password;



		//echo $curl_url; die;



		curl_setopt_array($curl, array(

			CURLOPT_URL => $curl_url,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => "",

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 30,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => "GET",

			CURLOPT_HTTPHEADER => array(

				"cache-control: no-cache",

			),

		));



		$response = curl_exec($curl);

		$err = curl_error($curl);



		curl_close($curl);



		if ($err) {

			//echo "cURL Error #:" . $err;

		} else {

			//echo $response;

		}



		return json_decode($response);

	}



	/* cart_abondon */

	public function cartAbondon(Request $request){



		$type = (isset($request->type))?$request->type:'';

		$time = (isset($request->time))?$request->time:'';



		if(strtoupper($type) == 'DAY' || strtoupper($type) == 'HOUR'){

			$type = strtoupper($type);

		}

		else{

			$type = 'HOUR';

		}



		$CART_ABONDON_TIME = CustomHelper::WebsiteSettings('CART_ABONDON_TIME');



		//$time = (int)$CART_ABONDON_TIME;

		//$CART_ABONDON_TIME = 1;



		if(is_numeric($time) && $time > 0){

			

		}

		elseif(is_numeric($CART_ABONDON_TIME) && $CART_ABONDON_TIME > 0){

			$time = $CART_ABONDON_TIME;

		}



		if(is_numeric($time) && $time > 0){

			

		}

		else{

			$time = 1;

		}



		if(is_numeric($CART_ABONDON_TIME) && $CART_ABONDON_TIME > 0){

			$cartQuery = UserCartItem::orderBy('created_at', 'desc');



			$cartQuery->where('user_id', '>', 0);

			$cartQuery->whereRaw("TIMESTAMPDIFF($type,DATE(`created_at`),CURDATE()) >= $time");



			$cartQuery->groupBy('user_id');



			//DB::enableQueryLog();

			$cart = $cartQuery->get();

			//prd(DB::getQueryLog());



			//prd($cart->toArray());



			if(!empty($cart) && count($cart) > 0){



				$productModel = new Product;



				//$cartItems = $cart->groupBy('user_id');



				//prd($cartItems->toArray());



				$FREE_DELIVERY_AMOUNT = (isset($websiteSettingsArr['FREE_DELIVERY_AMOUNT']))?$websiteSettingsArr['FREE_DELIVERY_AMOUNT']->value:0;



				$SHIPPING_CHARGE = (isset($websiteSettingsArr['SHIPPING_CHARGE']))?$websiteSettingsArr['SHIPPING_CHARGE']->value:0;



				//foreach($cartItems as $user_id=>$cartContent){

				foreach($cart as $cartItem){



					$user_id = $cartItem->user_id;

					$cartContent = UserCartItem::where('user_id', $user_id)->get();

					

					if(!empty($cartContent) && count($cartContent) > 0){



						//prd($cartContent->toArray());



						$totalTax = Cart::getTax($cartContent);



						$user = User::select('name', 'email')->where('id', $user_id)->first();



						/*foreach($cartContent as $item){

							pr($item->toArray());

						}*/



						$email = $user->email;

						$name = $user->name;



						$to_email = $email;



						$subject = 'Cart pending - SlumberJill';



						$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');



						if(empty($ADMIN_EMAIL)){

							$ADMIN_EMAIL = config('custom.admin_email');

						}



						$from_email = $ADMIN_EMAIL;



						$email_data = [];

						$email_data['name'] = $name;

						$email_data['productModel'] = $productModel;

						$email_data['cartContent'] = $cartContent;

						$email_data['totalTax'] = $totalTax;

						$email_data['FREE_DELIVERY_AMOUNT'] = $FREE_DELIVERY_AMOUNT;

						$email_data['SHIPPING_CHARGE'] = $SHIPPING_CHARGE;



						$emailContent = view('emails.cart_abondon', $email_data)->render();



						//echo $emailContent; die;



						$is_mail = CustomHelper::sendEmail('emails.cart_abondon', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);



					}

				}

				

			}

		}



		//prd($CART_ABONDON_TIME);



	}







//updateOrderStatus by Unicommerce

	public function updateOrderStatusOld(Request $request){

		//$curr_date = date('Y-m-d');

		//$ss  = date('Y-m-d H:i:s.uZ');

		//echo $ss;die;

		$getUnicommerceOrderStatus = $this->getUnicommerceOrderStatus();

		$resp = json_decode($getUnicommerceOrderStatus);

		$isUpdated = '';

		//prd($resp);



		

		if(isset($resp->elements) && count($resp->elements) > 0){

			foreach($resp->elements as $orderData){

				//pr($orderData);



				

				$orderId = (isset($orderData->code))?$orderData->code:0;



				if(is_numeric($orderId) && $orderId > 0){



					$order = Order::find($orderId);



					if(isset($order) && count($order) > 0)

					{

						$updated = false;

						$old_order_status = $order->order_status;

						if(($orderData->status == 'CREATED' || $orderData->status == 'PENDING_VERIFICATION') && $order->order_status!='pending')

						{

							$order->order_status = 'pending';

							$order->save();



							$updated = true;



						}



						if($orderData->status == 'PROCESSING' && $order->order_status!='confirmed')

						{

							$order->order_status = 'confirmed';

							$order->save();

							$updated = true;



						}



						if($orderData->status == 'CANCELLED' && $order->order_status!='cancelled')

						{

							$order->order_status = 'cancelled';

							$order->save();

							$updated = true;



						}



						if($orderData->status == 'DISPATCHED' && $order->order_status!='shipped')

						{

							$order->order_status = 'shipped';

							$order->save();

							$updated = true;



						}



						if($orderData->status == 'COMPLETE' && $order->order_status!='delivered')

						{

							$order->order_status = 'delivered';

							$order->save();

							$updated = true;



						}



						if($orderData->status == 'RETURN' && $order->order_status!='return')

						{

							$order->order_status = 'return';

							$order->save();

							$updated = true;



						}





						if($updated)

						{

							$order_history_data= []; 

							$order_history_data['order_id'] = $orderId;

							$order_history_data['old_order_status'] = $old_order_status;

							$order_history_data['order_status'] = $order->order_status;

							$order_history_data['comment'] = "updated by unicommerce api";



							DB::table('order_history')->insert($order_history_data);

							$updated = false;



							$isUpdated = true;

						}





						



					}



				}



				/*$sku = $skuData->itemTypeSKU;

				$inventory = $skuData->inventory;



				if(!empty($sku) && is_numeric($inventory)){

					$isInvUpdated = DB::table('product_inventory')->where('sku', $sku)->update(['stock'=>$inventory]);



					if($isInvUpdated){

						$isUpdated = $isInvUpdated;

					}

				}*/



			}

		}



		$result['success'] = false;

		$result['message'] = 'nothing updated';

		if($isUpdated && !empty($isUpdated)){

			$currentDataTime = date('Y-m-d H:i:s');

			$result['success'] = true;

			$result['updated_at'] = $currentDataTime;

			$result['message'] = 'status updated successfully';



		}



		return response()->json($result);



	}



	//get Order Status by Unicommerce

	private function getUnicommerceOrderStatusOld(){



		$response = [];



		$accessToken = CustomHelper::getUnicommerceAccessToken();

		$unicommerce_api_mode = config('custom.unicommerce_api_mode');



		if($unicommerce_api_mode == 'DEMO')

		{

			$unicommerce_api_url = config('custom.unicommerce_demo_api_url');

			$unicommerce_facility = config('custom.unicommerce_demo_facility');



			$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/oms/saleOrder/search';

		}

		else

		{

			$unicommerce_api_url = config('custom.unicommerce_api_url');

			$unicommerce_facility = config('custom.unicommerce_facility');



			$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleOrder/search';



		}





			//$curr_date = date('Y-m-d');

			$curr_date = gmdate("Y-m-dT00:00:00Z");

			$reqData = [];			

			$reqData["fromDate"] = $curr_date;

			//$reqData["channel"] = "CUSTOM";

			$reqData["channel"] = "slumberjill_in";





			$jsonData = json_encode($reqData);



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





	//updateOrderStatus by Unicommerce

	public function updateOrderStatusOldOld(Request $request){

		

		$getUnicommerceOrderStatus = $this->getUnicommerceOrderStatus();

		$resp = json_decode($getUnicommerceOrderStatus);

		$isUpdated = '';

		//prd($resp);



		

		if(isset($resp->elements) && count($resp->elements) > 0){

			foreach($resp->elements as $orderData){

				//prd($orderData);



				

				$orderId = (isset($orderData->code))?$orderData->code:0;



				if(!empty($orderId) && $orderId !=''){



					//$order = Order::find($orderId);

					//DB::enableQueryLog(); // Enable query log

					$subOrder = OrderItem::where('sub_order_no', $orderId)->first();





					//dd(DB::getQueryLog());



					if(!empty($subOrder) && count($subOrder) > 0)

					{	

						$updated = false;

						$old_order_status = $subOrder->order_status;

						$order = $subOrder->order;

						$order_status = '';

						if(($orderData->status == 'CREATED' || $orderData->status == 'PENDING_VERIFICATION') && $subOrder->order_status!='pending')

						{

							$order_status = 'pending';

							//$subOrder->order_status = 'pending';

							//$subOrder->save();



							$updated = true;



						}



						if($orderData->status == 'PROCESSING' && $subOrder->order_status!='confirmed')

						{

							$order_status = 'confirmed';

							//$subOrder->order_status = 'confirmed';

							//$subOrder->save();

							$updated = true;



						}



						if($orderData->status == 'CANCELLED')

						{

							



							if($subOrder->order_status!='return')

							{

								$getUnicommerceSigleOrderDetail = $this->getUnicommerceSigleOrderDetail($orderData->code);

								$orderDetail = json_decode($getUnicommerceSigleOrderDetail);



								if(isset($orderDetail) && $orderDetail->successful && isset($orderDetail->saleOrderDTO) && $orderDetail->saleOrderDTO->status == 'CANCELLED' && count($orderDetail->saleOrderDTO->saleOrderItems) > 0)

								{

									if(!empty($orderDetail->saleOrderDTO->saleOrderItems[0]->reversePickupCode))

									{

										$order_status = 'return';

										//$subOrder->order_status = 'return';

										//$subOrder->save();

										$updated = true;



									}

									else if($subOrder->order_status!='cancelled')

									{

										$order_status = 'cancelled';

										//$subOrder->order_status = 'cancelled';

										//$subOrder->save();

										$updated = true;



									}



								}







							}

							elseif ($subOrder->order_status!='cancelled' && $subOrder->order_status!='return') 

							{

								$order_status = 'cancelled';

								//$subOrder->order_status = 'cancelled';

								//$subOrder->save();

								$updated = true;

							}













						}



						/*if($orderData->status == 'DISPATCHED' && $subOrder->order_status!='shipped')

						{

							$order_status = 'shipped';

							$updated = true;



						}



						if($orderData->status == 'COMPLETE' && $subOrder->order_status!='delivered')

						{

							$order_status = 'delivered';

							$updated = true;



						}*/



						if($orderData->status == 'COMPLETE' && $subOrder->order_status!='shipped')

						{

							$order_status = 'shipped';

							$updated = true;



						}



						//if($orderData->status == 'RETURN' && $subOrder->order_status!='return')

						//{

						//	$subOrder->order_status = 'return';

						//	$subOrder->save();

						//	$updated = true;



						//}





						if($updated)

						{



							// For Gernerate the Invoice Number

							$subOrder->order_status = $order_status;

							if( $order_status == 'shipped' && empty($subOrder->invoice_no)){



								$websiteSettingsNamesArr = ['INVOICE_NUMBER_PREFIX', 'INVOICE_NUMBER_POSTFIX'];



								$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);



								$INVOICE_NUMBER_PREFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_PREFIX']))?$websiteSettingsArr['INVOICE_NUMBER_PREFIX']->value:'';

								$INVOICE_NUMBER_POSTFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_POSTFIX']))?$websiteSettingsArr['INVOICE_NUMBER_POSTFIX']->value:'';



								$postfixNo = (int)$INVOICE_NUMBER_POSTFIX+1;

								$unqPostfixNo = sprintf('%06d',$postfixNo);

								//prd($INVOICE_NUMBER_POSTFIX);

								$invoiceNo = $INVOICE_NUMBER_PREFIX.'/'.$unqPostfixNo;

								//prd($invoiceNo);

								$subOrder->invoice_no = $invoiceNo;

								$subOrder->invoice_date = date('Y-m-d H:i:s');



								DB::table('website_settings')->where('name','INVOICE_NUMBER_POSTFIX')->update(['value'=>$unqPostfixNo]);

							}

							

							$customerPhone = $order->billing_phone;

							$billing_email = $order->billing_email;

							$subOrderId = $subOrder->id;

							$subOrder->save();





							$isSavedMainOrder = $this->updateMainOrderStatus($order);



							$order_history_data= []; 

							$order_history_data['order_id'] = $subOrder->order_id;

							$order_history_data['order_item_id'] = $subOrderId;

							$order_history_data['old_order_status'] = $old_order_status;

							$order_history_data['order_status'] = $subOrder->order_status;

							$order_history_data['comment'] = "Updated by unicommerce api";



							DB::table('order_history')->insert($order_history_data);

							$updated = false;



							$isUpdated = true;





							//for message and mail send



							if( $order_status == 'shipped' && !empty($customerPhone)){

									$orderLink = url('uo/'.$subOrder->sub_order_no);

									$smsMessage = "Your Order#$subOrder->sub_order_no has been shipped, check details on: $orderLink";

									$smsOpts = [];

									$smsOpts['unicode'] = 1;

									if(CustomHelper::isSmsGateway() ){

										//LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

									}



								}





								// Sending Email to Customer

								$toEmail = $billing_email;

								//$subject = 'Order Status Changed - Order No: '.$subOrder->sub_order_no;



								$subject = 'Slumber Jill Order Information #'.date('dmy').'-'.$subOrder->sub_order_no;



								$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

								if(empty($ADMIN_EMAIL)){

									$ADMIN_EMAIL = config('custom.admin_email');

								}



								$fromEmail = $ADMIN_EMAIL;



								$emailData = [];

								$emailData['orderId'] = $subOrderId;

								$emailData['order'] = $order;

								$emailData['subOrder'] = $subOrder;



								$isMailCustomer = '';



								if(!empty($toEmail)){

									$isMailCustomer = CustomHelper::sendEmail('emails.orders.sub_order_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

								}



















						}





						



					}



				}



			}

		}



		$result['success'] = false;

		$result['message'] = 'nothing updated';

		if($isUpdated && !empty($isUpdated)){

			$currentDataTime = date('Y-m-d H:i:s');

			$result['success'] = true;

			$result['updated_at'] = $currentDataTime;

			$result['message'] = 'status updated successfully';



		}



		return response()->json($result);



	}



	public function updateOrderStatus(Request $request){

		

		$getUnicommerceOrderStatus = $this->getUnicommerceOrderStatus();

		$resp = json_decode($getUnicommerceOrderStatus);

		$isUpdated = '';

		//echo $getUnicommerceOrderStatus; die;

		//prd($resp->elements);



		

		if(isset($resp->elements) && count($resp->elements) > 0){

			foreach($resp->elements as $orderData){



				$orderId = (isset($orderData->code))?$orderData->code:0;



				if(!empty($orderId) && $orderId !=''){



					//prd($orderId);

					$order = Order::where('order_no',$orderId)->first();

					$orderItem = orderItem::find($orderId);

					//$old_order_status = $orderItem->order_status;

					//DB::enableQueryLog(); // Enable query log

					//$subOrder = OrderItem::where('sub_order_id', $orderId)->first();

					//dd(DB::getQueryLog());



					if(!empty($order) && count($order) > 0)

					{



						$getUnicommerceSigleOrderDetail = $this->getUnicommerceSigleOrderDetail($orderId);

						//echo $getUnicommerceSigleOrderDetail;die;

						$orderDetail = json_decode($getUnicommerceSigleOrderDetail);

						//pr($orderDetail->saleOrderDTO->saleOrderItems);

						if(isset($orderDetail) && $orderDetail->successful && isset($orderDetail->saleOrderDTO) && count($orderDetail->saleOrderDTO->saleOrderItems) > 0)

							{
								//prd($orderDetail->saleOrderDTO->saleOrderItems);


								foreach($orderDetail->saleOrderDTO->saleOrderItems as $item)

								{



									//Single item status updates code goes here					

									$subOrder = OrderItem::where('sub_order_no', $item->code)->first();





									//dd(DB::getQueryLog());



				if(!empty($subOrder) && count($subOrder) > 0)

				{

			

						$updated = false;

						$old_order_status = $subOrder->order_status;

						//$mainOrder = $subOrder->order;//UNFULFILLABLE

						$order_status = '';

						if(($item->statusCode == 'CREATED' || $item->statusCode == 'PENDING_VERIFICATION' || $item->statusCode == 'UNFULFILLABLE') && $subOrder->order_status!='placed')

						{

							$order_status = 'placed';

							$updated = true;



						}



						if(($item->statusCode == 'PROCESSING' || $item->statusCode == 'FULFILLABLE' ) && $subOrder->order_status!='confirmed')

						{

							$order_status = 'confirmed';

							$updated = true;

						}

						if($item->statusCode == 'REPLACED')
						{
							$order_status = 'return';
							$updated = true;
						}



						if($item->statusCode == 'CANCELLED')
						{							



							if($subOrder->order_status!='return')

							{

								

								if(!empty($item->reversePickupCode))
								{

									$order_status = 'return';

									$updated = true;



								}

								else if($subOrder->order_status!='cancelled')

								{

									$order_status = 'cancelled';

									$updated = true;



								}



							}

							elseif ($subOrder->order_status!='cancelled' && $subOrder->order_status!='return') 

							{

								$order_status = 'cancelled';

								$updated = true;

							}



						}



						if(($item->statusCode == 'COMPLETE' || $item->statusCode == 'DISPATCHED') && $subOrder->order_status!='shipped')

						{

							$order_status = 'shipped';

							$updated = true;



						}





						if(($item->statusCode == 'DELIVERED') && $subOrder->order_status!='delivered')

						{

							$order_status = 'delivered';

							$updated = true;



						}


						$user = User::where('id', $order->user_id)->where('deleted', '!=', 1)->first();


						if($updated)

						{



							// For Gernerate the Invoice Number

							$subOrder->order_status = $order_status;

							if( $order_status == 'shipped' && empty($subOrder->invoice_no)){






								$websiteSettingsNamesArr = ['INVOICE_NUMBER_PREFIX', 'INVOICE_NUMBER_POSTFIX'];



								$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);



								$INVOICE_NUMBER_PREFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_PREFIX']))?$websiteSettingsArr['INVOICE_NUMBER_PREFIX']->value:'';

								$INVOICE_NUMBER_POSTFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_POSTFIX']))?$websiteSettingsArr['INVOICE_NUMBER_POSTFIX']->value:'';



								$postfixNo = (int)$INVOICE_NUMBER_POSTFIX+1;

								$unqPostfixNo = sprintf('%06d',$postfixNo);

								//prd($INVOICE_NUMBER_POSTFIX);

								$invoiceNo = $INVOICE_NUMBER_PREFIX.'/'.$unqPostfixNo;

								//prd($invoiceNo);

								$subOrder->invoice_no = $invoiceNo;

								$subOrder->invoice_date = date('Y-m-d H:i:s');



								DB::table('website_settings')->where('name','INVOICE_NUMBER_POSTFIX')->update(['value'=>$unqPostfixNo]);



								if($subOrder->loyalty_points > 0)
								{
									$loyalty_points = $subOrder->loyalty_points;

									


									$credit_amount = $loyalty_points;

									$debit_amount = 0;



									if($old_order_status!='shipped' && $order_status == 'shipped')
									{
										$credit_amount = $loyalty_points;

										$debit_amount = 0;

										$user_lpd['customer_id'] = $order->user_id;

										$user_lpd['order_id'] = $subOrder->order_id;

										$user_lpd['order_item_id'] = $orderId;

										$user_lpd['credit_amount'] = $credit_amount;

										$user_lpd['debit_amount'] = $debit_amount;

										$user_lpd['description'] = 'Order Shipped';

										$user_lpd['created'] = date('Y-m-d H:i:s');

										$user_lpd['updated'] = date('Y-m-d H:i:s');

										$inserted = LoyaltyPoints::create($user_lpd);

										$loyalty_points = CustomHelper::loyaltyPonitsBalance($order->user_id);

									}

								}








							}

							

							$customerPhone = $order->billing_phone;

							$billing_email = $order->billing_email;

							$subOrderId = $subOrder->id;

							$subOrder->save();





							



							$order_history_data= []; 

							$order_history_data['order_id'] = $subOrder->order_id;

							$order_history_data['order_item_id'] = $subOrderId;

							$order_history_data['old_order_status'] = $old_order_status;

							$order_history_data['order_status'] = $subOrder->order_status;

							$order_history_data['comment'] = "Updated by unicommerce api";



							DB::table('order_history')->insert($order_history_data);

							$updated = false;



							$isUpdated = true;



							$isSavedMainOrder = $this->updateMainOrderStatus($subOrder->order);





							//for message and mail send



							if( $order_status == 'shipped' && !empty($customerPhone)){

									//$orderLink = url('uo/'.$subOrder->sub_order_no);

									//$smsMessage = "Your Order#$subOrder->sub_order_no has been shipped, check details on: $orderLink";

									//$smsOpts = [];

									//$smsOpts['unicode'] = 1;



									$totalItems = $subOrder->qty;

									$totalAmount = $subOrder->total;

									//$orderLink = url('uo/'.$subOrder->sub_order_no);

									$smsMessage = "We have shipped your John Pride order $subOrder->sub_order_no for INR $totalAmount with $totalItems item(s). Stay Stylish, Team John Pride";



									$urlencodeMessage = urlencode($smsMessage);



									$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage);



								// if($subOrder->loyalty_points > 0)
								// {
								// 	$loyalty_points = $subOrder->loyalty_points;

								// 	$user = User::where('id', $order->user_id)->where('deleted', '!=', 1)->first();


								// 	$credit_amount = $loyalty_points;

								// 	$debit_amount = 0;



								// 	if($old_order_status!='shipped' && $order_status == 'shipped')
								// 	{
								// 		$credit_amount = $loyalty_points;

								// 		$debit_amount = 0;

								// 		$user_lpd['customer_id'] = $order->user_id;

								// 		$user_lpd['order_id'] = $subOrder->order_id;

								// 		$user_lpd['order_item_id'] = $orderId;

								// 		$user_lpd['credit_amount'] = $credit_amount;

								// 		$user_lpd['debit_amount'] = $debit_amount;

								// 		$user_lpd['description'] = 'Order Shipped';

								// 		$user_lpd['created'] = date('Y-m-d H:i:s');

								// 		$user_lpd['updated'] = date('Y-m-d H:i:s');

								// 		$inserted = LoyaltyPoints::create($user_lpd);

								// 		$loyalty_points = CustomHelper::loyaltyPonitsBalance($order->user_id);

								// 	}

								// }


									//if(CustomHelper::isSmsGateway() ){

										//LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);

									//}



					}



					if($order_status == 'return' || $order_status == 'cancelled')
						{
							$credit_amount = 0;

							$debit_amount = $subOrder->loyalty_points;;



							$user_lpd['order_id'] =$subOrder->order_id;

							$user_lpd['order_item_id'] = $orderId;

							$user_lpd['customer_id'] = $order->user_id;

							$user_lpd['credit_amount'] =0;

							$user_lpd['debit_amount'] = $debit_amount;

							if($order_status == "cancelled")
							{
								$user_lpd['description'] = 'Order Cancelled';
							}
							else if($order_status == "return")
							{
								$user_lpd['description'] = 'Order Return';
							}

							

							$user_lpd['created_at'] = date('Y-m-d H:i:s');

							$user_lpd['updated_at'] = date('Y-m-d H:i:s');


							$inserted = LoyaltyPoints::create($user_lpd);

							$loyalty_points = CustomHelper::loyaltyPonitsBalance($order->user_id);



							$sendMail = $this->sendLoyaltyPointsTransactionMail($user, $loyaltyPoints=$debit_amount, $type='debit', $inserted, $balance=$loyalty_points);



						}














								// Sending Email to Customer

								$toEmail = $billing_email;

								//$subject = 'Order Status Changed - Order No: '.$subOrder->sub_order_no;



								$subject = 'John Pride Order Information #'.date('dmy').'-'.$subOrder->sub_order_no;



								$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

								if(empty($ADMIN_EMAIL)){

									$ADMIN_EMAIL = config('custom.admin_email');

								}



								$fromEmail = $ADMIN_EMAIL;



								$emailData = [];

								$emailData['orderId'] = $subOrderId;

								$emailData['order'] = $order;

								$emailData['subOrder'] = $subOrder;



								$isMailCustomer = '';



								if(!empty($toEmail)){

									$isMailCustomer = CustomHelper::sendEmail('emails.orders.sub_order_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

								}



								}	

							}



				







									//



								}



							}







					}



				}

			}



		}



		$result['success'] = false;

		$result['message'] = 'nothing updated';

		if($isUpdated && !empty($isUpdated)){

			$currentDataTime = date('Y-m-d H:i:s');

			$result['success'] = true;

			$result['updated_at'] = $currentDataTime;

			$result['message'] = 'status updated successfully';



		}



		return response()->json($result);



	}




private function sendLoyaltyPointsTransactionMail($user, $loyaltyPoints=0, $type, $inserted,$balance)

		{

			if($loyaltyPoints > 0)

			{

				// Sending Email to Customer

				$to_email = $user->email;

				$user_name= $user->first_name." ".$user->last_name;

				$subject = '';

				$tag_line = '';



				$sendMail = false;



				if($type == 'credit')

				{

					$subject = ''.$loyaltyPoints.' Loyalty Points is credited in your account';



					$tag_line=  ''.$loyaltyPoints.' Loyalty Points is credited in your account';



					$sendMail = true;



				}

				elseif ($type == 'debit') {

					$subject = ''.$loyaltyPoints.' Loyalty Points is debited from your account';



					$tag_line=  ''.$loyaltyPoints.' Loyalty Points is debited from your account';



					$sendMail = true;

				}



				if($sendMail)

				{

					$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

					if(empty($ADMIN_EMAIL)){

						$ADMIN_EMAIL = config('custom.admin_email');

					}

					$from_email = $ADMIN_EMAIL;



					$email_data =[];



					$email_data['user_name'] = $user_name;

					$email_data['tag_line'] = $tag_line; 

					$email_data['loyalty_points_data'] = $inserted; 

					$email_data['av_loyalty_points'] = $balance; 

					$is_mail = CustomHelper::sendEmail('emails.loyalty_points.loyalty_points_transaction', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);	



					return true;



				}



				







				



			}



			return false;



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



	//get Single Order Status by Unicommerce

	//status is"reversePickupCode" = null, then cancel otherwise return

	private function getUnicommerceSigleOrderDetail($orderCode=''){



		$response = [];



		if(!empty($orderCode))

		{

			$accessToken = CustomHelper::getUnicommerceAccessToken();

			$unicommerce_api_mode = config('custom.unicommerce_api_mode');



			if($unicommerce_api_mode == 'DEMO')

			{

				$unicommerce_api_url = config('custom.unicommerce_demo_api_url');

				$unicommerce_facility = config('custom.unicommerce_demo_facility');



				$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/oms/saleorder/get';

			}

			else

			{

				$unicommerce_api_url = config('custom.unicommerce_api_url');

				$unicommerce_facility = config('custom.unicommerce_facility');



				//$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleorder/get';

				$curl_url = $unicommerce_api_url.'services/rest/v1/oms/saleorder/get';

			}

			

			

			$reqData = [];			

			$reqData["code"] = $orderCode;



			$jsonData = json_encode($reqData);

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





			//$dd = json_decode($response);

			//echo 'Status=>'.$dd->saleOrderDTO->status;

			//echo "  reversePickupCode=>".$dd->saleOrderDTO->saleOrderItems[0]->reversePickupCode;die;



			curl_close($curl);



		}



		



		return $response;



	}





	//get Order Status by Unicommerce

	private function getUnicommerceOrderStatus(){



		$response = [];



		$accessToken = CustomHelper::getUnicommerceAccessToken();

		$unicommerce_api_mode = config('custom.unicommerce_api_mode');



		if($unicommerce_api_mode == 'DEMO')

		{

			$unicommerce_api_url = config('custom.unicommerce_demo_api_url');

			$unicommerce_facility = config('custom.unicommerce_demo_facility');



			$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/oms/saleOrder/search';

		}

		else

		{

			$unicommerce_api_url = config('custom.unicommerce_api_url');

			$unicommerce_facility = config('custom.unicommerce_facility');



			//$curl_url = 'https://stgbloomexim.unicommerce.com/services/rest/v1/oms/saleOrder/search';



			$curl_url = $unicommerce_api_url.'services/rest/v1/oms/saleOrder/search';



		}

		// echo $curl_url;die;

		// https://stgbloomexim.unicommerce.com/services/rest/v1/oms/saleOrder/search



			//$curr_date = date('Y-m-d');

			$curr_date = gmdate("Y-m-d\T00:00:00\Z");

			$reqData = [];			

			$reqData["fromDate"] = $curr_date;

			//$reqData["channel"] = "CUSTOM";

			$reqData["channel"] = "JohnP";





			$jsonData = json_encode($reqData);



			// echo $jsonData."Testing";



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



			curl_close($curl);//die;



			return $response;



	}





/* end of controller */

}

