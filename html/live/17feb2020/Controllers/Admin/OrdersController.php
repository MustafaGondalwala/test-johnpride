<?php

namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;

use Validator;
use DB;

use Excel;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;



use App\Order;
use App\Product;

use App\Country;
use App\State;
use App\City;

use App\Orderstatus;

class OrdersController extends Controller 
{

    /**
     * Admin - Orders
     * URL: /admin/orders
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    // orders listing
      public function index(request $request)
      { 
          $data= []; 
          $order_model= new Order;

          $name = (isset($request->name))?$request->name:'';
          $email = (isset($request->email))?$request->email:'';
          $phone = (isset($request->phone))?$request->phone:'';
          $order_status = (isset($request->order_status))?$request->order_status:'';


          $from = (isset($request->from))?$request->from:'';


          $to = (isset($request->to))?$request->to:'';

          $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
          $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');

          $res_query = Order::orderBy('order_id', 'desc');

           if(!empty($name))
           {
                $res_query->whereRaw("CONCAT(orders.billing_first_name,' ',COALESCE(orders.billing_last_name,'')) LIKE '%".$name."%'" );
           }

           if(!empty($email))
           {
               $res_query->whereRaw("orders.billing_email LIKE '%".$email."%' or orders.shipping_email LIKE '%".$email."%'    ");
           }

           if(!empty($phone))
           {
               $res_query->whereRaw("orders.billing_phone LIKE '%".$phone."%' or orders.billing_phone LIKE '%".$phone."%'    ");
           }

           if(!empty($from_date))
           {
            $res_query->whereRaw('DATE(created_at) >= "'.$from_date.'"');
           }

           if(!empty($to_date)) 
           {
               $res_query->whereRaw('DATE(created_at) <= "'.$to_date.'"');
           }

           if($order_status!='')
           {

               $res_query->where(['order_status'=>$order_status]);

           }

           $res = $res_query->paginate(20);


           $data['res']= $res;
           $data['order_model']= new Order;
           $data['order_status_arr']= DB::table('order_status')->where('status_id', '<=', 5)->get();


           $data['country_model']=new Country;
           $data['state_model']=new State;
           $data['city_model']=new City;
           

           return view('admin.orders.list', $data);

      }

      // View Order
      public function view_order(request $request, $order_id='')
      { 
          $data= []; 
          $order_model= new Order;

          $res=$order_model->where(['order_id'=>$order_id])->first();
          
          

          $method= $request->method(); 
          if($method=='POST')
          { 
               if(!empty($order_id))
               {

                           $rules = [];
                           $rules['comment'] = 'required';
                           $this->validate($request, $rules);

                          $order_history_data= []; 
                          $order_history_data['old_status_id']=$res->order_status;
                          $order_history_data['status_id']=$request->order_status;
                          $order_history_data['order_id']=$order_id;
                          $order_history_data['comment']=$request->comment;
                            // saving order history
                            
                          

                          //saving order 

                          $order_update_data['order_status']= $request->order_status;

                          if($request->payment_status!='')
                          {

                              $order_update_data['payment_status']= $request->payment_status;
                          }

                         
                          $is_saveed_order=Order::where('order_id',$order_id)->update($order_update_data);
                          if($is_saveed_order)
                          { 
                                Order::save_order_history($order_history_data);
                          }

                          
                    }









          }

          $res=$order_model->where(['order_id'=>$order_id])->first();
              
          $data['res']= $res;

          $data['billing_country']= Country::where(['id'=>$res->billing_country])->first();
          $data['billing_state']=State::where(['id'=>$res->billing_state])->first();
          $data['billing_city']= City::where(['id'=>$res->billing_city])->first();

          $data['shipping_country']= Country::where(['id'=>$res->billing_country])->first();

          $data['shipping_state']=State::where(['id'=>$res->billing_state])->first();

          $data['shipping_city']= City::where(['id'=>$res->billing_city])->first();

          $data['order_model']=$order_model;
          $order_status_list=  DB::table('order_status')->where('status_id', '<=', 5)->get();
          $data['order_status_list']=  $order_status_list;

          $data['order_history']=  DB::table('order_history')->where('order_id', '=', $order_id)->orderBy('id','desc')->get();
          
         


          
          return view('admin.orders.view', $data);

      }
    
    



    public function update(Request $request, $order)
    {
        $data = $request->all();

        // Refund requested
        if ($request->has('refund')) {
            switch ($order['payment_method']) {
                case 'card':
                    if (!empty($order['stripe_charge_id'])) {
                        Stripe::setApiKey(env('STRIPE_SECRET'));

                        try {
                            $refund = StripeRefund::create(array(
                                'charge' => $order['stripe_charge_id']
                            ));
                        } catch (StripeInvalidRequest $exception) {
                            return back()->with('alert-danger', $exception->getMessage());
                        }

                        if (!empty($refund['status']) && $refund['status'] == 'succeeded' && !empty($refund['id'])) {
                            $order->status = 'refunded';
                            $order->payment_status = 'refunded';
                            $order->stripe_refund_id = $refund['id'];
                            $order->save();

                            return back()->with('alert-success', 'You have successfully refunded this order.');
                        } else {
                            return back()->with('alert-danger', 'The refund was not processed successfully, please try again or contact the administrator.');
                        }
                    }
                    break;

                case 'paypal':
                    if (env('PAYPAL_MODE') && !empty($order['paypal_payment_id'])) {
                        $paypal = new PayPalHelper();

                        $payment = $paypal->getPayment($order['paypal_payment_id']);

                        $sale = $paypal->getSale($payment);
                        $saleId = $sale->getId();

                        $refundedSale = $paypal->refundSale($saleId, $order);
                        $refundedSaleId = $refundedSale->getId();

                        if ($refundedSale) {
                            $order->status = 'refunded';
                            $order->payment_status = 'refunded';
                            $order->paypal_refund_id = $refundedSaleId;
                            $order->save();

                            return back()->with('alert-success', 'You have successfully refunded this order.');
                        } else {
                            return back()->with('alert-danger', 'The refund was not processed successfully, please try again or contact the administrator.');
                        }
                    }
                    break;
            }
        }

        // Validation
        $this->validate($request, [
            'order_number'       => 'required|digits:10',
            'confirmation_code'  => 'required|alpha_num|size:6',
            'contact_email'      => 'required|max:255|email',
            'delivery_name'      => 'required|max:255',
            'delivery_address_1' => 'required|max:255',
            'delivery_address_2' => 'sometimes|max:255',
            'delivery_city'      => 'required|max:255',
            'delivery_state'     => 'required|size:2',
            'delivery_zipcode'   => 'required|max:20',
            'delivery_phone'     => 'required|max:20',
            'billing_name'       => 'required_if:payment_method,card|max:255',
            'billing_address_1'  => 'required_if:payment_method,card|max:255',
            'billing_address_2'  => 'max:255',
            'billing_city'       => 'required_if:payment_method,card|max:255',
            'billing_state'      => 'required_if:payment_method,card|size:2',
            'billing_zipcode'    => 'required_if:payment_method,card|max:20',
            'billing_phone'      => 'required_if:payment_method,card|max:20',
            'payment_status'     => 'in:not_paid,paid,refunded',
            'payment_method'     => 'in:card,paypal,cash',
        ]);

        foreach ([
            'order_number',
            'confirmation_code',
            'contact_email',
            'shipping_carrier',
            'shipping_plan',
            'shipping_tracking_number',
            'delivery_name',
            'delivery_address_1',
            'delivery_address_2',
            'delivery_city',
            'delivery_state',
            'delivery_zipcode',
            'delivery_phone',
            'billing_name',
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_zipcode',
            'billing_phone',
            'status',
            'payment_status',
            'payment_method',
        ] as $field) {
            if ($data[$field] != $order->{$field}) {
                $order->{$field} = $data[$field];
            }
        }

        $order->save();

        return back()->with('alert-success', 'The order has been updated successfully.');
    }

    /**
     * Send email to customer regarding the order (i.e. shipping confirmation)
     *
     * @param $order
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function email($order, $type) {

        switch ($type) {
            case 'shipping_confirmation':
                if ($order['status'] == 'shipped' && !empty($order['shipping_tracking_number'])) {
                    // Send Shipping Confirmation email to customer
                    $templateId = config('custom.emails.templates.shipping_confirmation');

                    if (!empty($order['user_id'])) {
                        $user = $order->user;
                    }

                    $sub = [
                        'customer_name' => !empty($order['user_id']) ? $user['first_name'] : $order['billing_name'],
                        'order_number' => $order['order_number'],
                        'shipping_carrier' => !empty($order['shipping_carrier']) ? config('custom.checkout.shipping.carriers.' . $order['shipping_carrier'] . '.name') : '',
                        'shipping_plan' => !empty($order['shipping_plan']) ? config('custom.checkout.shipping.carriers.' . $order['shipping_carrier'] . '.plans.' . $order['shipping_plan'] . '.plan') : '',
                        'tracking_url' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . $order['shipping_tracking_number'],
                        'tracking_number' => $order['shipping_tracking_number'],
                        'delivery_address' => [
                            'name'       => $order['delivery_name'],
                            'phone'      => $order['delivery_phone'],
                            'address_1'  => $order['delivery_address_1'],
                            'address_2'  => $order['delivery_address_2'],
                            'city'       => $order['delivery_city'],
                            'state'      => $order['delivery_state'],
                            'zipcode'    => $order['delivery_zipcode']
                        ]
                    ];

                    foreach ($order->inventoryItems as $item) {
                        $tmp = [
                            'name' => $item->product['name'],
                            'url' => route('shop.product', [$item->product['uri'], $item->product['id']]),
                            'unit_price' => number_format($item->pivot['price'], 2),
                            'quantity' => $item->pivot['quantity'],
                            'price' => number_format($item->pivot['price'] * $item->pivot['quantity'], 2)
                        ];

                        if ($item->options()->count() > 0) {
                            $tmp['options'] = [];

                            foreach ($item->options()->get() as $option) {
                                $tmp['options'][] = [
                                    'attribute' => $option->attribute['name'],
                                    'value' => $option['name']
                                ];
                            }
                        }

                        if ($item->product->defaultPhoto()->count() > 0) {
                            $tmp['image'] = CustomHelper::image($item->product->defaultPhoto['name'], true);
                        }

                        $sub['items'][] = $tmp;
                    }

                    $recipients = [
                        [
                            'address' => !empty($order['contact_email']) ? $order['contact_email'] : $user['email'],
                            'name' => $sub['customer_name'],
                            'substitution_data' => $sub
                        ]
                    ];

                    SparkPostHelper::sendTemplate($templateId, $recipients);

                    return back()->with('alert-success', 'You have successfully sent a Shipping Confirmation email to the customer.');
                } else {
                    return back()->with('alert-danger', 'You need to change the status to Shipped and add a Tracking Number first in order to send this Shipping Confirmation email to the customer.');
                }

                break;
        }
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

/* End of controller */
}