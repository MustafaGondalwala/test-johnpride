<?php

namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;

use Validator;
use DB;

/*use Excel;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;*/

use App\Order;
use App\Product;

use App\Country;
use App\State;
use App\City;

use LaravelMsg91;

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


			$from = (isset($request->from))?$request->from:'';


			$to = (isset($request->to))?$request->to:'';

			$from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
			$to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');

			$orderQuery = Order::orderBy('id', 'desc');

			if(!empty($name)){
				//$orderQuery->whereRaw("CONCAT(orders.billing_first_name,' ',COALESCE(orders.billing_last_name,'')) LIKE '%".$name."%'" );
				$orderQuery->whereRaw("orders.billing_name LIKE '%".$name."%' OR orders.shipping_name LIKE '%".$name."%'" );
			}

			if(!empty($email)){
				$orderQuery->whereRaw("orders.billing_email LIKE '%".$email."%' or orders.shipping_email LIKE '%".$email."%'    ");
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

			$fileName = 'orders_'.date('Y-m-d-H-i-s').'.xlsx';

			header('Content-Type: application/vnd.ms-excel');
			//tell browser what's the file name
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			//no cache
			header('Cache-Control: max-age=0');

			$viewData = [];
			$viewData['orders'] = $orders;
			return view('admin.orders._xls_view', $viewData);

			//return Excel::download(new OrderExport($orders), $fileName, 'Html');
		}



		public function view(request $request){
			$data= [];			

			$orderId = (isset($request->id))?$request->id:0;

			if(is_numeric($orderId) && $orderId > 0){

				$order = Order::find($orderId);

				$order_no = $order->order_no;
				$customerPhone = $order->billing_phone;

				$method= $request->method();

				if($method == 'POST'){

					//prd($request->toArray());

					$rules = [];
					$rules['comment'] = 'required';

					$this->validate($request, $rules);

					$order_history_data= []; 
					$order_history_data['order_id'] = $orderId;
					$order_history_data['old_order_status'] = $order->order_status;
					$order_history_data['order_status'] = $request->order_status;
					$order_history_data['comment'] = $request->comment;

					//prd($order_history_data);

					$order_status = $request->order_status;

					$order->order_status = $order_status;

					if(!empty($request->payment_status)){
						$order->payment_status  = $request->payment_status;
					}


					// For Gernerate the Invoice Number

					if( $order_status == 'shipped' && empty($order->invoice_no)){

						$websiteSettingsNamesArr = ['INVOICE_NUMBER_PREFIX', 'INVOICE_NUMBER_POSTFIX'];

						$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

						$INVOICE_NUMBER_PREFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_PREFIX']))?$websiteSettingsArr['INVOICE_NUMBER_PREFIX']->value:'';
						$INVOICE_NUMBER_POSTFIX = (isset($websiteSettingsArr['INVOICE_NUMBER_POSTFIX']))?$websiteSettingsArr['INVOICE_NUMBER_POSTFIX']->value:'';

						$postfixNo = (int)$INVOICE_NUMBER_POSTFIX+1;
						$unqPostfixNo = sprintf('%06d',$postfixNo);
						//prd($INVOICE_NUMBER_POSTFIX);
						$invoiceNo = $INVOICE_NUMBER_PREFIX.'/'.$unqPostfixNo;
						//prd($invoiceNo);
						$order->invoice_no = $invoiceNo;
						$order->invoice_date = date('Y-m-d H:i:s');

						DB::table('website_settings')->where('name','INVOICE_NUMBER_POSTFIX')->update(['value'=>$unqPostfixNo]);
					}

					$isSavedOrder = $order->save();

					if($isSavedOrder){
						DB::table('order_history')->insert($order_history_data);

						if( $order_status == 'shipped' && !empty($customerPhone)){

							$orderLink = url('uo/'.$orderId);
							$smsMessage = "Your Order#$order_no status has been changed, check details on: $orderLink";

							$smsOpts = [];
							$smsOpts['unicode'] = 1;

							if(CustomHelper::isSmsGateway() ){
								LaravelMsg91::message($customerPhone, $smsMessage, $smsOpts);
							}
						}

						
						// Sending Email to Customer
						$toEmail = $order->billing_email;
						$subject = 'Order Status Changed - Order No: '.$orderId;

						$ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
						if(empty($ADMIN_EMAIL)){
							$ADMIN_EMAIL = config('custom.admin_email');
						}

						$fromEmail = $ADMIN_EMAIL;

						$emailData = [];
						$emailData['orderId'] = $orderId;
						$emailData['order'] = $order;
						//prd($order->toArray());
						$viewHtml = view('emails.orders.order_status', $emailData)->render();

						//echo $viewHtml; die;

						$isMailCustomer = '';

						if(!empty($toEmail)){
							$isMailCustomer = CustomHelper::sendEmail('emails.orders.order_status', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);
						}

						return back()->with('alert-success', 'Order Status has been changed successfully.');
					}


				}

				

				if(!empty($order) && count($order) > 0){

					$orderHistory = DB::table('order_history')->where('order_id', $orderId)->get();

					$data['order'] = $order;
					$data['orderHistory'] = $orderHistory;

					return view('admin.orders.view', $data);
				}

			}

			

			return back();

		}
		

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
public function printInvoice(request $request){

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
			/*"http://slumberjill.ii71.com/public/assets/img/logo.png"*/
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


/* End of controller */
}