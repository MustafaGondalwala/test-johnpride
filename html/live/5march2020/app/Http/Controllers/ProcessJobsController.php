<?php

namespace App\Http\Controllers;

use App\User;
use App\UserCartItem;
use App\Product;
use App\ProductInventory;
use App\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Helpers\CustomHelper;
use App\Libraries\Cart;
use DB;
use Validator;


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

		$accessToken = $this->getUnicommerceAccessToken();

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
				if(!empty($invSkuArr)){
					//pr($invSkuArr);
					//prd(json_encode($invSkuArr));

					$invSkuArrJson = json_encode($invSkuArr);

					$response = $this->getUnicommerceSkuInventory($invSkuArr, $accessToken);

					$resp = json_decode($response);

					//prd($resp);

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


/* end of controller */
}
