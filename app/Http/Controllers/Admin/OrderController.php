<?php

namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\User;

use Validator;
use DB;

/*use Excel;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;*/

use App\Order;
use App\OrderItem;
use App\Product;

use App\Country;
use App\State;
use App\City;
use App\LoyaltyPoints;

use LaravelMsg91;
use App\Exports\OrderExport;

use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller{

		/**
		 * Admin - Orders
		 * URL: /admin/orders
		 *
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */

		// orders listing
		public function index(request $request){

			$data= []; 
			$order_model= new Order;

			$dateType = (isset($request->date_type))?$request->date_type:'';

			$dateType = ($dateType == 'updated_at')?$dateType:'created_at';

			$export_xls = (isset($request->export_xls))?$request->export_xls:'';

			$name = (isset($request->name))?$request->name:'';
			$email = (isset($request->email))?$request->email:'';
			$phone = (isset($request->phone))?$request->phone:'';
			$order_status = (isset($request->order_status))?$request->order_status:'';

			$order_no = (isset($request->order_no))?$request->order_no:'';


			$from = (isset($request->from))?$request->from:'';


			$to = (isset($request->to))?$request->to:'';

			$from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
			$to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');

			//$orderQuery = Order::orderBy('id', 'desc');
			$orderQuery = Order::orderBy('created_at', 'desc');

			if(!empty($name)){
				//$orderQuery->whereRaw("CONCAT(orders.billing_first_name,' ',COALESCE(orders.billing_last_name,'')) LIKE '%".$name."%'" );
				$orderQuery->whereRaw("orders.billing_name LIKE '%".$name."%' OR orders.shipping_name LIKE '%".$name."%'" );
			}

			if(!empty($email)){
				$orderQuery->whereRaw("orders.billing_email LIKE '%".$email."%' or orders.shipping_email LIKE '%".$email."%'    ");
			}

			if(!empty($order_no)){
				$orderQuery->whereRaw("orders.order_no LIKE '%".$order_no."%'");
			}

			if(!empty($phone)){
				$orderQuery->whereRaw("orders.billing_phone LIKE '%".$phone."%' or orders.billing_phone LIKE '%".$phone."%'    ");
			}

			if(!empty($order_status)){
				$orderQuery->where('order_status', $order_status);
			}

			if(!empty($from_date)){
				$orderQuery->whereRaw('DATE('.$dateType.') >= "'.$from_date.'"');
			}

			if(!empty($to_date)){
				$orderQuery->whereRaw('DATE('.$dateType.') <= "'.$to_date.'"');
			}

			if(!empty($export_xls) && ($export_xls == 1 || $export_xls == '1') ){
				return $this->exportXls($orderQuery);
			}

			//DB::enableQueryLog();
			$orders = $orderQuery->paginate(20);
			//prd(DB::getQueryLog());



			$data['orders'] = $orders;


			return view('admin.orders.index', $data);

		}

		private function exportXls($query){

			$orders = $query->get();
			$exportArr = [];

			if(!empty($orders) && $orders->count() > 0){
				foreach($orders as $order){

					$orderStatusDetails = $order->orderStatusDetails;
					$orderStatus = (isset($orderStatusDetails->name))?$orderStatusDetails->name:'';
					$added_on = CustomHelper::DateFormat($order->created_at, 'd F y');
					$orderItems = $order->orderItems;

					$billingCityName = '';
					$billingStateName = '';
					$billingCountryName = '';

					$billingCity = $order->billingCity;
					$billingState = $order->billingState;
					$billingCountry = $order->billingCountry;

					if(isset($billingCity->name) && !empty($billingCity->name)){
						$billingCityName = $billingCity->name;
					}
					if(isset($billingState->name) && !empty($billingState->name)){
						$billingStateName = $billingState->name;
					}
					if(isset($billingCountry->name) && !empty($billingCountry->name)){
						$billingCountryName = $billingCountry->name;
					}

					$shippingCityName = '';
					$shippingStateName = '';
					$shippingCountryName = '';

					$shippingCity = $order->shippingCity;
					$shippingState = $order->shippingState;
					$shippingCountry = $order->shippingCountry;


					if(isset($shippingCity->name) && !empty($shippingCity->name)){
						$shippingCityName = $shippingCity->name;
					}
					if(isset($shippingState->name) && !empty($shippingState->name)){
						$shippingStateName = $shippingState->name;
					}
					if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
						$shippingCountryName = $shippingCountry->name;
					}

					$orderStatusArr = config('custom.order_status_arr');
					$billingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=true, $isPhone=true, $isEmail=true);
					$shippingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=false, $isPhone=true, $isEmail=true);

					if(!empty($orderItems) && count($orderItems) > 0){
						foreach($orderItems as $item){

							$item_price = $item->item_price;
							$gst = $item->gst;

							$discount = 0;
							$priceWithoutGst = 0;
							$withOutGstP = 0;

							$priceWithoutGst = CustomHelper::priceWithoutGst($item_price, $gst);
							$withOutGstP = $item_price - $priceWithoutGst;

							
							$orderArr = [];
							$billing_address = implode(', ', $billingAddrArr);
							$shipping_address = implode(', ', $billingAddrArr);
							$orderArr['id'] = $order->id;
							$orderArr['order_no'] = $order->order_no;
							$orderArr['billing_address'] = strip_tags($billing_address);
							// $orderArr['billing_address'] = implode('<br/>', $billingAddrArr);;
							$orderArr['shipping_address'] = strip_tags($shipping_address);
							// $orderArr['shipping_address'] = implode('<br/>', $shippingAddrArr);;
							$orderArr['added_on'] = $added_on;
							$orderArr['order_status'] = $orderStatus;
							$orderArr['payment_status'] = ucfirst($order->payment_status);
							
							$orderArr['sub_total'] = $order->sub_total;
							$orderArr['discount'] = ($order->discount > 0)?$order->discount:'';
							$orderArr['coupon_discount'] = ($order->coupon_discount > 0)?$order->coupon_discount:'';
							$orderArr['sub_total'] = $order->sub_total;
							$orderArr['tax'] = ($order->tax > 0)?$order->tax:'';
							$orderArr['shipping_charge'] = ($order->shipping_charge > 0)?$order->shipping_charge:'';
							$orderArr['used_wallet_amount'] = ($order->used_wallet_amount > 0)?$order->used_wallet_amount:'';

							$orderArr['online_payment'] = $order->total - $order->used_wallet_amount;
							$orderArr['payment_method'] = $order->payment_method;
							$orderArr['order_total'] = $order->total;



							$orderArr['product_name'] = $item->product_name;
							$orderArr['product_sku'] = $item->product_sku;
							$orderArr['price'] = $item->price;
							$orderArr['sale_price'] = number_format($priceWithoutGst);
							$orderArr['tax'] = number_format($withOutGstP);
							$orderArr['quantity'] = $item->qty;
							$orderArr['total'] = $item->item_price*$item->qty;


							$exportArr[] = $orderArr;

						}
					}
				}
			}

			$fieldNames = array_keys($exportArr[0]);
	       // prd($exportArr);

			$fileName = 'orders_'.date('Y-m-d-H-i-s').'.xlsx';
			return Excel::download(new OrderExport($exportArr, $fieldNames), $fileName);

		}


		


		


		public function view(request $request){
			//$createExportJobUnicommerce = $this->createExportJobUnicommerce();
			//$statusExportJobUnicommerce = $this->statusExportJobUnicommerce();
			//prd($statusExportJobUnicommerce);
			$data= [];			

			$orderId = (isset($request->id))?$request->id:0;

			if(is_numeric($orderId) && $orderId > 0){

				//$order = Order::find($orderId);
				$orderItem = orderItem::find($orderId);
				$order = $orderItem->order;
				

				$sub_order_no = $orderItem->sub_order_no;
				$order_no = $order->order_no;
				$customerPhone = $order->billing_phone;

				$method= $request->method();

				if($method == 'POST'){

					//prd($request->toArray());

					$rules = [];
					$rules['comment'] = 'required';
					$actionCancelUnicommerce = false;

					$this->validate($request, $rules);

					$old_order_status = $orderItem->order_status;

					$order_history_data= []; 
					$order_history_data['order_id'] = $orderItem->order_id;
					$order_history_data['order_item_id'] = $orderId;
					$order_history_data['old_order_status'] = $orderItem->order_status;
					$order_history_data['order_status'] = $request->order_status;
					$order_history_data['comment'] = $request->comment;

					


					

					if(!empty($request->payment_status)){
						$order->payment_status  = $request->payment_status;

						//if($request->order_status=='cancelled' && $order->order_status!=$request->order_status)
						if($request->order_status=='cancelled' && $orderItem->order_status!=$request->order_status)
						{
							$actionCancelUnicommerce = true;
						}
					}

					$order_status = $request->order_status;
					//$order->order_status = $order_status;
					$orderItem->order_status = $order_status;


					// For Gernerate the Invoice Number

					if( $order_status == 'shipped' && empty($orderItem->invoice_no)){

						$websiteSettingsNamesArr = ['INVOICE_NUMBER_PREFIX', 'INVOICE_NUMBER_POSTFIX'];

						$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

						$INVOICE_NUMBER_PREFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_PREFIX']))?$websiteSettingsArr['INVOICE_NUMBER_PREFIX']->value:'';
						$INVOICE_NUMBER_POSTFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_POSTFIX']))?$websiteSettingsArr['INVOICE_NUMBER_POSTFIX']->value:'';

						$postfixNo = (int)$INVOICE_NUMBER_POSTFIX+1;
						$unqPostfixNo = sprintf('%06d',$postfixNo);
						//prd($INVOICE_NUMBER_POSTFIX);
						$invoiceNo = $INVOICE_NUMBER_PREFIX.'/'.$unqPostfixNo;
						//prd($invoiceNo);
						$orderItem->invoice_no = $invoiceNo;
						$orderItem->invoice_date = date('Y-m-d H:i:s');

						DB::table('website_settings')->where('name','INVOICE_NUMBER_POSTFIX')->update(['value'=>$unqPostfixNo]);
					}

					if($orderItem->loyalty_points > 0)
					{
						//$loyalty_points = $orderItem->loyalty_points * $orderItem->qty;
						$loyalty_points = $orderItem->loyalty_points;
						
						//$orderItemPointsQuery = LoyaltyPoints::where(array('order_id' => $orderItem->order_id, 'order_item_id' => $orderId, 'status' => 1));

						//$countOrderItemPoints = $orderItemPointsQuery->count();

						
						$user = User::where('id', $order->user_id)->where('deleted', '!=', 1)->first();

						//if($old_order_status!='delivered' && $order_status == 'delivered')
						if($old_order_status!='delivered' && $order_status == 'delivered')
						{	

							
							$credit_amount = $loyalty_points;
							$debit_amount = 0;

							//if($countOrderItemPoints == 0){


								$user_lpd['customer_id'] = $order->user_id;
								$user_lpd['order_id'] = $orderItem->order_id;
								$user_lpd['order_item_id'] = $orderId;
								$user_lpd['credit_amount'] = $credit_amount;
								$user_lpd['debit_amount'] = $debit_amount;
								$user_lpd['description'] = 'Order Delivered';
								$user_lpd['created'] = date('Y-m-d H:i:s');
								$user_lpd['updated'] = date('Y-m-d H:i:s');

								$inserted = LoyaltyPoints::create($user_lpd);

								$loyalty_points = CustomHelper::loyaltyPonitsBalance($order->user_id);

                 				// Sending Email to Customer

                 				$sendMail = $this->sendLoyaltyPointsTransactionMail($user, $loyaltyPoints=$credit_amount, $type='credit', $inserted, $balance=$loyalty_points);


								/*$to_email = $user->email;
								$user_name= $user->first_name." ".$user->last_name;

								$subject = ''.$credit_amount.' Loyalty Points is credited in your account';

								$tag_line=  ''.$credit_amount.' Loyalty Points is credited in your account';



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
								$is_mail = CustomHelper::sendEmail('emails.loyalty_points.loyalty_points_transaction', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);*/								

							//}

						}

						

						if($old_order_status=='delivered' && $request->order_status == 'return')
						{
							//echo 'EE';die;

							$credit_amount = 0;
							$debit_amount = $orderItem->loyalty_points;

							$user_lpd['order_id'] = $orderItem->order_id;
							$user_lpd['order_item_id'] = $orderId;
							$user_lpd['customer_id'] = $order->user_id;
							$user_lpd['credit_amount'] =0;
							$user_lpd['debit_amount'] = $debit_amount;
							$user_lpd['description'] = 'Order Return';
							$user_lpd['created_at'] = date('Y-m-d H:i:s');
							$user_lpd['updated_at'] = date('Y-m-d H:i:s');
							
							//$result = DB::table('loyalty_points_to_customer')->insert($user_lpd);

							$inserted = LoyaltyPoints::create($user_lpd);
							$loyalty_points = CustomHelper::loyaltyPonitsBalance($order->user_id);

							$sendMail = $this->sendLoyaltyPointsTransactionMail($user, $loyaltyPoints=$debit_amount, $type='debit', $inserted, $balance=$loyalty_points);

						}

					}

					$isSavedOrder = $order->save();
					$isSavedSubOrder = $orderItem->save();

					$isSavedMainOrder = $this->updateMainOrderStatus($order);

					//if($actionCancelUnicommerce && $isSavedMainOrder && $orderItem->order_status == 'cancelled')
					if($actionCancelUnicommerce && $orderItem->order_status == 'cancelled')
					{
						$responseUnicommerce = $this->cancelOrderUnicommerce($orderItem);

					}

					


					if($isSavedSubOrder){
						DB::table('order_history')->insert($order_history_data);

						if( $order_status == 'shipped' && !empty($customerPhone)){

							//$orderLink = url('uo/'.$orderId);
							$orderLink = url('uo/'.$orderItem->sub_order_no);
							//$smsMessage = "Your Order#$order_no status has been changed, check details on: $orderLink";
							
							//$smsMessage = "Your Order#$order_no has been shipped, check details on: $orderLink";
							
							$smsMessage = "Your Order#$sub_order_no has been shipped, check details on: $orderLink";

							$urlencodeMessage = urlencode($smsMessage);

							/*$smsOpts = [];
							$smsOpts['unicode'] = 1;

							if(CustomHelper::isSmsGateway() ){
								LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);
							}*/


							//$isSendSMS = CustomHelper::sendSMS($customerPhone,$urlencodeMessage,);
						}

						
						// Sending Email to Customer
						$toEmail = $order->billing_email;
						//$subject = 'Order Status Changed - Order No: '.$sub_order_no;
						$subject = 'Johnpride Order Information #'.date('dmy').'-'.$sub_order_no;

						$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
						if(empty($ADMIN_EMAIL)){
							$ADMIN_EMAIL = config('custom.admin_email');
						}

						$fromEmail = $ADMIN_EMAIL;

						$emailData = [];
						$emailData['orderId'] = $orderId;
						$emailData['order'] = $order;
						$emailData['subOrder'] = $orderItem;
						//prd($order->toArray());
						//$viewHtml = view('emails.orders.sub_order_status', $emailData)->render();

						//echo $viewHtml; die;

						$isMailCustomer = '';

						if(!empty($toEmail)){
							$isMailCustomer = CustomHelper::sendEmail('emails.orders.sub_order_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);
						}

						return back()->with('alert-success', 'Order Status has been changed successfully.');
					}


				}

				

				if(!empty($orderItem) && count($orderItem) > 0){

					$orderHistory = DB::table('order_history')->where('order_item_id', $orderId)->get();

					$data['order'] = $order;
					$data['subOrder'] = $orderItem;
					$data['orderHistory'] = $orderHistory;

					return view('admin.orders.sub_order_view', $data);
				}

			}

			

			return back();

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

				$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';

			}


			if(!empty($orderItem) && count($orderItem) > 0 && $orderItem->order_status=='cancelled'){
				$order = $orderItem->order;
				$cancelorderData = [];
				//$cancelorderData['saleOrderCode'] = $orderItem->sub_order_id;
				$cancelorderData['saleOrderCode'] = $order->order_no;
				$cancelorderData['saleOrderItemCodes'] = array($orderItem->sub_order_no);
				$cancelorderData['cancellationReason'] = "Cancel order by admin user";


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

		private function cancelOrderUnicommerceOldOld($orderItem){
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

				$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';

			}


			if(!empty($orderItem) && count($orderItem) > 0 && $orderItem->order_status=='cancelled'){
				$cancelorderData = [];
				$cancelorderData['saleOrderCode'] = $orderItem->sub_order_id;
				//$cancelorderData['saleOrderItemCodes'] = array();
				$cancelorderData['cancellationReason'] = "Cancel order by admin user";


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

		private function cancelOrderUnicommerceOld($order){
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

				$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/oms/saleOrder/cancel';

			}


			if(!empty($order) && count($order) > 0 && $order->order_status=='cancelled'){
				$cancelorderData = [];
				$cancelorderData['saleOrderCode'] = $order->id;
				//$cancelorderData['saleOrderItemCodes'] = array();
				$cancelorderData['cancellationReason'] = "Cancel order by admin user";


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
					echo "cURL Error #:" . $err;
				} else {
					echo $response;
				}

				curl_close($curl);

				return $response;


			}


			return json_encode($response);


		}

		/*private function statusExportJobUnicommerce(){
			$response = [];

			$accessToken = CustomHelper::getUnicommerceAccessToken();
			$unicommerce_api_mode = config('custom.unicommerce_api_mode');

			if($unicommerce_api_mode == 'DEMO')
			{
				$unicommerce_api_url = config('custom.unicommerce_demo_api_url');
				$unicommerce_facility = config('custom.unicommerce_demo_facility');

				$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/export/job/status';
			}
			else
			{
				$unicommerce_api_url = config('custom.unicommerce_api_url');
				$unicommerce_facility = config('custom.unicommerce_facility');

				$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/export/job/status';

			}


			
				$reqData = [];
				$reqData['jobCode'] = "5f27ded2e4b064d069c87bbb-a37fc9598f38d88df2439544daed9480";		


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
					echo "cURL Error #:" . $err;
				} else {
					echo $response;
				}

				curl_close($curl);

				return $response;


			


		}


		private function createExportJobUnicommerce(){
			$response = [];

			$accessToken = CustomHelper::getUnicommerceAccessToken();
			$unicommerce_api_mode = config('custom.unicommerce_api_mode');

			if($unicommerce_api_mode == 'DEMO')
			{
				$unicommerce_api_url = config('custom.unicommerce_demo_api_url');
				$unicommerce_facility = config('custom.unicommerce_demo_facility');

				$curl_url = 'https://demostaging.unicommerce.com/services/rest/v1/export/job/create';
			}
			else
			{
				$unicommerce_api_url = config('custom.unicommerce_api_url');
				$unicommerce_facility = config('custom.unicommerce_facility');

				$curl_url = 'https://fwfpl989898.unicommerce.com/services/rest/v1/export/job/create';

			}


			
				$reqData = [];
				$exportFilters = [];
				$exportColums = [];
				$exportFilters[0]['id']="addedOn";
				$exportFilters[0]['dateRange']=array('start'=>"1593628200000",'end'=>'1596220200000');
				$exportColums=array("code","displayorderCode","status","trackingStatus","courierStatus");
				$reqData['exportJobTypeName'] = "Sale Orders";
				$reqData['exportFilters'] = $exportFilters;
				$reqData['exportColums'] = $exportColums;
				$reqData['frequency'] = "ONETIME";


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
					echo "cURL Error #:" . $err;
				} else {
					echo $response;
				}

				curl_close($curl);

				return $response;


			


			}*/

			public function update_order_status(Request $request){
				//prd($request->toArray());

				$result['success'] = false;

				$post_data = $request->all();

				$rules = [];

				$rules['amount'] = 'required|numeric';

				$validator = Validator::make($post_data, $rules);
				 //$validator->setAttributeNames($attributes);

				if($validator->fails()){
					$result['errors'] = $validator->errors();
				}
				else{
					$order_id = $post_data['order_id'];

					if(is_numeric($order_id) && $order_id > 0){

						$find_order = Order::find($order_id);

						if(!empty($find_order) && count($find_order) > 0){

							$updateData['status'] = $post_data['order_status'];
							$updateData['comments'] = $post_data['customer_comments'];
							$updateData['admin_comments'] = $post_data['sales_comments'];

							$is_updated = Order::where('id', $order_id)->update($updateData);

							if($is_updated){
								$result['success'] = true;
								$result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>Order status has been updated successfully.</div>';
							}
						}
						else{
							$result['msg'] = '<div class="alert alert-danger alert-dismissable"><a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>Invalid Order details!</div>';
						}

					}
					else{
						$result['msg'] = '<div class="alert alert-danger alert-dismissable"><a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>Invalid Order details!</div>';
					}

				}

				return response()->json($result);
			}

			public function detail($order_id)
			{
				if(is_numeric($order_id) && $order_id>0)
				{
					$order = Order::find($order_id);
						//prd($order->products()->toArray());

					$order_products = $order->products();

					$ProductModel = new Product;

					$data['order']= $order;
					$data['order_products']= $order_products;
					$data['ProductModel']= $ProductModel;
					return view('.admin.orders.detail',$data);
				}
			}
			public function export($orders){



				$filename = 'orders_'.date('Y-m-d-H-i-s').'.xls';

				//echo view('admin.buyers_orders._export', $data)->render(); die;

				$sheetHeaderArr = array('Order ID', 'Order Date', 'Name', 'Email', 'Country', 'Status', 'IN Status', 'Product Code', 'Product Name', 'Qty', 'Price', 'Total Price', 'Sub Total', 'Total');

				//prd($sheetHeaderArr);

				$objPHPExcel = new PHPExcel();

				$objPHPExcel->getProperties()->setCreator("Mushkis");
				$objPHPExcel->getProperties()->setLastModifiedBy("Mushkis");
				$objPHPExcel->getProperties()->setTitle("Mushkis");
				$objPHPExcel->getProperties()->setSubject("Mushkis");
				$objPHPExcel->getProperties()->setDescription("Mushkis");

				foreach($sheetHeaderArr as $col=>$header){
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '1', "$header");
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, '1')->getFont()->setBold(true);
				}

				$i = 3;

				$viewData=[];

				if(!empty($orders) && count($orders) > 0){

					foreach($orders as $key=>$order){

						//pr($order->toArray());
						//prd($costing->CostingPricing->toArray());

						$order_date = CustomHelper::DateFormat($order->created_at, 'd/m/Y');


						$col = 0;

						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $order->id);
						$col++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $order_date);
						$col++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $order->billing_firstname);
						$col++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $order->billing_email);

						$country_name = CustomHelper::GetCountry($order->billing_country, 'name');
						$col++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $country_name);

						$statusCode = CustomHelper::OrdersStatusCode();
						$status = (isset($statusCode[$order->status]->name))?$statusCode[$order->status]->name:'';
						$col++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $status);


								/*$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $order->total);*/
								$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, '');
								
								$col++;

								$product_code = '';
								$product_name = '';
								$quantity = '';
								$price = '';
								$amount = '';

								$order_products = $order->products();

								$sub_total = 0;

								if(!empty($order_products) && count($order_products) > 0){

									foreach ($order_products as $product) {
										$sub_total  += number_format($product->product_price * $product->product_qty, 2);
										$product_code .= $product->product_code."\n";
										$product_name .= $product->product_name."\n";
										$quantity .= $product->product_qty."\n";
										$price .= number_format($product->product_price,2)."\n";
										$amount .= number_format($product->product_price * $product->product_qty, 2)."\n";
									}
								}


								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $product_code);
								$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $product_name);
								$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $quantity);
								$col++;

								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $price);
								$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $amount);
								$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, number_format($sub_total, 2));
								$col++;
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $order->total);

								$i++;

							}

						}

						$file_name = 'ordersSheet_'.date('YmdHis').'.xls';

						header('Content-Type: application/vnd.ms-excel');
						//tell browser what's the file name
						header('Content-Disposition: attachment;filename="'.$file_name.'"');
						//no cache
						header('Cache-Control: max-age=0');

						//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
						//if you want to save it as .XLSX Excel 2007 format
						$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

						$objWriter->save('php://output');

					}


					/*ajax_print_invoice*/
					public function printInvoiceOld(request $request){

						$orderId = (isset($request->order_id))?$request->order_id:0;

						$response = [];

						if(is_numeric($orderId) && $orderId>0){

							$order = Order::find($orderId);
		//prd($order->products()->toArray());

							if(!empty($order)){
								$orderHistory = DB::table('order_history')->where('order_id', $orderId)->get();

								$response['order'] = $order;
								$response['orderHistory'] = $orderHistory;

								$response['order']= $order;
								/*"http://johnpride.ii71.com/public/assets/img/logo.png"*/
								$response['logoPath']= asset('public/assets/img/logo.png');


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
					/*ajax_print_invoice*/
					public function printInvoice(request $request){

						$orderId = (isset($request->order_id))?$request->order_id:0;

						$response = [];

						if(is_numeric($orderId) && $orderId>0){

		//$order = Order::find($orderId);
							$order = Order::find($orderId);
							$subOrder = OrderItem::find($orderId);
		//prd($order->products()->toArray());

							if(!empty($subOrder) && count($subOrder) > 0){
								$order = $subOrder->order;
								$orderHistory = DB::table('order_history')->where('order_item_id', $orderId)->get();


								$response['subOrder'] = $subOrder;
								$response['orderHistory'] = $orderHistory;

								$response['order']= $order;
								/*"http://johnpride.ii71.com/public/assets/img/logo.png"*/
								$response['logoPath']= asset('public/assets/img/logo.png');


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


					/* End of controller */
				}