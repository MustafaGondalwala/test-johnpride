<?php
namespace App\Http\Controllers;
use Stripe\Stripe;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Helpers\PayPalHelper;
use App\Helpers\CategoryHelper;
use App\Helpers\SparkPostHelper;
use Stripe\Charge as StripeCharge;
use Stripe\Customer as StripeCustomer;
use Illuminate\Support\Facades\Validator;

use DB;
use App\Customer;
use App\Order;
use App\Coupon;
use App\Address;
use App\Product;
use App\Category;
use App\Inventory;
use App\PaymentSource;
use App\Country;
use App\State;
use App\City;
use App\Common;
use App\UsersWallet;
use Cart;
class OrderController extends Controller {

    
    public function index(Request $request){
        return redirect(url('/'));

    }

    public function confirmation(Request $request){
        
        $user_wallet_amount= 0;
        $Common_model=new Common;

        $UsersWallet_model= new UsersWallet; 
        $user_id=0;
        if(auth()->check())
        {
            $auth_user = auth()->user();
            $user_id=$auth_user->id; 
            if($auth_user->is_wallet==1)
            {

              $user_wallet_amount= $UsersWallet_model->getuser_total_balance($user_id); 

            }
            

        }        


       // checking if cart is empty
        $data= [];
        if(Cart::isEmpty())
        {

            return redirect(url('cart'));

        }
        // checking if user address in session
        if (!session()->has('user_address')) 
        {

            return redirect(url('cart/checkout'));
           
   
        }

        $user_address= session('user_address'); 





        
        $dat['meta_title']= 'Texindia | Order Confirmation';
        $user_address =session('user_address');
        $method=$request->method();


        // saving the order here
        if($method=='POST' && $request->order_confirm)
        {
             
             //echo 'hi'; exit; 
             
             $order= new Order; // craeting order model

             $order->user_id=$user_id; 

             $order->billing_first_name=$user_address->billing_first_name; 
             $order->billing_last_name=$user_address->billing_last_name;
             $order->billing_email=$user_address->billing_email;
             $order->billing_phone=$user_address->billing_phone;
             $order->billing_address1=$user_address->billing_address1;

             $order->billing_address2=(!empty($user_address->billing_address2))?$user_address->billing_address2: '';

             
             $order->billing_pincode=$user_address->billing_pincode;
             $order->billing_city=$user_address->billing_city;
             $order->billing_state =$user_address->billing_state;
             $order->billing_country=$user_address->billing_country;



             $order->shipping_first_name=$user_address->shipping_first_name; 
             $order->shipping_last_name=$user_address->shipping_last_name;
             $order->shipping_email=$user_address->shipping_email;
             $order->shipping_phone=$user_address->shipping_phone;
             $order->shipping_address1=$user_address->shipping_address1;

             $order->shipping_address2=(!empty($user_address->shipping_address2))?$user_address->shipping_address2: '';

             $order->shipping_pincode=$user_address->shipping_pincode;
             $order->shipping_city=$user_address->shipping_city;
             $order->shipping_state =$user_address->shipping_state;
             $order->shipping_country=$user_address->shipping_country;



             $used_wallet_amount=0;






             $discount=$Common_model->get_cart_coupon_discount(Cart::getTotal());

             $coupon_data=''; 
             $coupon_id=0;

             if (session()->has('coupon_sess_data')) 
             {
              
                $coupon_sess_data= session('coupon_sess_data');
                $coupon_id=$coupon_sess_data['coupon_id'];
                $coupon_data= json_encode($coupon_sess_data);

             }

            




             $shipping_charge=0;
             $tax=0;
             

             $sub_total=Cart::getSubTotal();
             $total=Cart::getTotal();
             if($discount < $total)
             {

                 $total=  $total-$discount; 

             }
             
             $total= $total+$tax+$shipping_charge;
             

             if (session()->has('is_wallet_use')) 
                {
                    if($user_wallet_amount > 0 )
                    {

                        if($user_wallet_amount > $total )
                        {

                            $used_wallet_amount=$total;


                        }
                        else
                        {
                            $used_wallet_amount=$user_wallet_amount;

                        }

                        
                        $total=$total-$used_wallet_amount;



                    }

                }





            
             
             $order->discount=$discount; 
             $order->coupon_data=$coupon_data;
             $order->coupon_id=$coupon_id;
             $order->shipping_charge=$shipping_charge; 
             $order->tax=$tax; 
             $order->used_wallet_amount=$used_wallet_amount; 

             $order->sub_total=$sub_total;
             $order->total=$total; 
             
            

             $order->payment_method=$request->payment_method; 
             $order->order_status=0;
             //pr($order);exit; 
             $order->save();

             $order_id=$order->order_id;


             if(!empty($order_id))
             { 

                $order_history_data= []; 
                $order_history_data['old_status_id']=0;
                $order_history_data['status_id']=0; 
                $order_history_data['order_id']=$order_id;
                $order_history_data['comment']='Order Placed';
                // saving order history
                
                Order::save_order_history($order_history_data); 
                

                if(!Cart::isEmpty())
                {
                    
                    $cart_items=$cart_items=Cart::getContent();
                    foreach($cart_items as $items) 
                    { 
                         
                          $item_data= [];

                          $design_id= 0;
                          $designer_id= 0;
                          $designer_commission=0; 
                          $size= ''; 
                          $fabric_generator= ''; 
                          $products_images= ''; 
                          $length=''; 
                          $attributes= $items->attributes;

                          $product_data=Product::where(['id'=>$items->id])->get();

                          Product::find($items->id)->increment('total_sale_counter', 1);



            

                          if(!empty($attributes))
                          {

                              if(!empty($attributes['fabric_generator']) )
                              {

                                $fabric_generator= json_encode($attributes['fabric_generator']);
                                if(!empty($attributes['fabric_generator']['design_id']))
                                {

                                   Product::find($attributes['fabric_generator']['design_id'])->increment('total_sale_counter', 1);



                                }


                              }
                             

                              if(isset($attributes['size']) && !empty($attributes['size']))
                              {

                                  $size=$attributes['size'];

                              }

                              if(isset($attributes['length']) && !empty($attributes['length']))
                              {

                                  if($product_data->type='design' || $product_data->type='fabric')
                                  {
                                     $length=$attributes['length'];

                                  }
                                  

                              }


                              



                              if(isset($attributes['products_images']) && !empty($attributes['products_images']))
                              {

                                  $products_images=$attributes['products_images'];

                              }

                              if(isset($attributes['design_id']) )
                              {

                                  $design_id=$attributes['design_id'];

                              }

                              if(isset($attributes['designer_id']) )
                              {

                                  $designer_id=$attributes['designer_id'];

                              }

                              if(isset($attributes['designer_commission']) )
                              {

                                  $designer_commission=$attributes['designer_commission'];

                              }





                          }






                          

                          $item_data['order_id']= $order_id;
                          $item_data['product_id']= $items->id;
                          $item_data['product_name']= $items->name;

                          $item_data['design_id']= $design_id;

                          $item_data['designer_id']= $designer_id;
                          $item_data['designer_comission_amount']= $designer_commission;





                          $item_data['price']= $items->price;
                          $item_data['discount']= 0;
                          $item_data['qty']= $items->quantity;

                          $item_data['size']= $size;
                          $item_data['length']= $length;

                          $item_data['fabric_generator']= $fabric_generator;
                          $item_data['products_images']= $products_images;

                          //pr($item_data);exit; 






                          DB::table('order_products')->insert($item_data);
                        
                         
                         
                    }

                    Cart::clear();

                     // setting order id in session
                    session(['order_id' =>$order_id]);
                    if (session()->has('user_address')) 
                    {
               
                       session()->forget('user_address');
                    }
                     

                    if (session()->has('fabric_generator_id')) 
                    {
                         session()->forget('fabric_generator_id');
                    }
                    


                   

                    //unset

                    if (session()->has('coupon_sess_data')) 
                    {
               
                        session()->forget('coupon_sess_data');

                    }

                    if (session()->has('is_wallet_use')) 
                    {
               
                        session()->forget('is_wallet_use');


                    }


                    // coupon is set decrease the use_limit 
                    if(!empty($coupon_id))
                    {
                       $coupon_data= Coupon::where(['id'=>$coupon_id])->first();
                       if($coupon_data->use_limit > 0)
                       {
                          Coupon::find($coupon_id)->decrement('use_limit', 1);

                       }


                    }
                    if($request->payment_method=='cod' && in_array($request->payment_method, array('cod')))
                    {

                        // add wallet transaction

                        if($used_wallet_amount > 0 && $user_id)
                        {
                             
                             $wallet_tranaction_data= []; 
                             $wallet_tranaction_data['user_id']=$user_id;
                             $wallet_tranaction_data['order_id']=$order_id;
                             $wallet_tranaction_data['transaction_type']='order_placed';
                             $wallet_tranaction_data['debit_amount']=$used_wallet_amount; 
                             $wallet_description= 'Amount deducted, placing order-'.$order_id;
                             
                             $wallet_tranaction_data['description']=$wallet_description;
                             $wallet_tranaction_data['created']=date('Y-m-d H:i:s');
                             $wallet_tranaction_data['updated']=date('Y-m-d H:i:s');

                             $wallet_transcation_id= $UsersWallet_model->do_wallet_transaction($wallet_tranaction_data); 
                        }

                        return redirect(url('order/success'));

                    }



                    



                }
                else
                {
                      echo 'Can not place order, '; 
                      exit;
                }

                


             }

             


             //pr($order); exit; 
             
             



    





        }
        $data['billing_country']= Country::where(['id'=>$user_address->billing_country])->first();
        $data['billing_state']=State::where(['id'=>$user_address->billing_state])->first();
        $data['billing_city']= City::where(['id'=>$user_address->billing_city])->first();

        $data['shipping_country']= Country::where(['id'=>$user_address->billing_country])->first();

        $data['shipping_state']=State::where(['id'=>$user_address->billing_state])->first();

        $data['shipping_city']= City::where(['id'=>$user_address->billing_city])->first();
        $data['user_address']= $user_address;



        $data['coupon_discount']= $Common_model->get_cart_coupon_discount(Cart::getTotal());

        $data['user_wallet_amount']= $user_wallet_amount;


        return view('order/confirmation', $data); 
    }

   

   

   


    public function process(Request $request){
        $method = $request->method();        
        //pr($method);
        //pr($request->toArray());

        $shipping_info = $request->toArray();

        if($method == 'POST' || $method == 'post'){

            $customer_id = auth()->user()->id;

            $data = [];

            $cartItems = Cart::where('customer_id', $customer_id)->get();

            $ProductModel = new Product;

            if(session()->has('shipping_info')){
                session()->forget('shipping_info');
            }

            session(['shipping_info' => $shipping_info]);

            $data['shipping_info'] = $shipping_info;
            $data['ProductModel'] = $ProductModel;
            $data['cartItems'] = $cartItems;

            return view('order.process', $data);
        }
        else{
            return redirect('cart');
        }
    }


    


    public function payment(Request $request){
        $method = $request->method();        
        //pr($method);
        //prd($request->toArray());

        $payment_method = ($request->has('payment_method'))?$request->payment_method:'';
        $shipping_info = session('shipping_info');

        if($method == 'POST' || $method == 'post'){

            $data = $request->all();

            //prd($shipping_info);

            if($payment_method == 'card'){

                $cart_ids = $data['cart_id'];
                $product_ids = $data['product_id'];

                if(!empty($cart_ids) && count($cart_ids) > 0){

                    $customer_id = auth()->user()->id;

                    $Cart = Cart::whereIn('id', $cart_ids)->where('customer_id', $customer_id)->get();

                    if(!empty($Cart) && count($Cart) > 0){

                        $customer = Customer::find($customer_id);

                        $customer_name = trim($customer->first_name.' '.$customer->last_name);

                        $customer_email = $customer->email;

                    //pr($shipping_info);
                    //prd($customer->toArray());

                        $delivery_firstname = $shipping_info['ship_first_name'];
                        $delivery_lastname = $shipping_info['ship_last_name'];
                        $delivery_company = $shipping_info['ship_company'];
                        $delivery_email = $shipping_info['ship_email'];
                        $delivery_address_1 = $shipping_info['ship_address_1'];
                        $delivery_address_2 = $shipping_info['ship_address_2'];
                        $delivery_city = $shipping_info['ship_city'];
                        $delivery_state = $shipping_info['ship_state'];
                        $delivery_country = $shipping_info['ship_country'];
                        $delivery_zipcode = $shipping_info['ship_zipcode'];
                        $delivery_phone = $shipping_info['ship_phone'];
                        $delivery_fax = $shipping_info['ship_fax'];

                        $billing_firstname = $customer->first_name;
                        $billing_lastname = $customer->last_name;
                        $billing_company = $customer->company;
                        $billing_email = $customer_email;
                        $billing_address_1 = $customer->address_1;
                        $billing_address_2 = $customer->address_2;
                        $billing_city = $customer->city;
                        $billing_state = $customer->state;
                        $billing_country = $customer->country;
                        $billing_zipcode = $customer->zipcode;
                        $billing_phone = $customer->phone;
                        $billing_fax = $customer->fax;

                        $payment_status = 'paid';
                        $status = '0';
                        $comments = ($request->has('comments'))?$request->comments:'';

                        $OrderData['customer_id'] = $customer_id;

                        $OrderData['delivery_firstname'] = $delivery_firstname;
                        $OrderData['delivery_lastname'] = $delivery_lastname;
                        $OrderData['delivery_company'] = $delivery_company;
                        $OrderData['delivery_email'] = $delivery_email;
                        $OrderData['delivery_address_1'] = $delivery_address_1;
                        $OrderData['delivery_address_2'] = $delivery_address_2;
                        $OrderData['delivery_city'] = $delivery_city;
                        $OrderData['delivery_state'] = $delivery_state;
                        $OrderData['delivery_country'] = $delivery_country;
                        $OrderData['delivery_zipcode'] = $delivery_zipcode;
                        $OrderData['delivery_phone'] = $delivery_phone;
                        $OrderData['delivery_fax'] = $delivery_fax;

                        $OrderData['billing_firstname'] = $billing_firstname;
                        $OrderData['billing_lastname'] = $billing_lastname;
                        $OrderData['billing_company'] = $billing_company;
                        $OrderData['billing_email'] = $billing_email;
                        $OrderData['billing_address_1'] = $billing_address_1;
                        $OrderData['billing_address_2'] = $billing_address_2;
                        $OrderData['billing_city'] = $billing_city;
                        $OrderData['billing_state'] = $billing_state;
                        $OrderData['billing_country'] = $billing_country;
                        $OrderData['billing_zipcode'] = $billing_zipcode;
                        $OrderData['billing_phone'] = $billing_phone;
                        $OrderData['billing_fax'] = $billing_fax;

                        $OrderData['payment_method'] = $payment_method;
                        $OrderData['payment_status'] = $payment_status;
                        $OrderData['status'] = $status;
                        $OrderData['comments'] = $comments;

                        $sub_total = 0;
                        $grand_total = 0;

                        $order_id = Order::insertGetId($OrderData);

                        $ordersProducts = [];

                        if($order_id > 0){

                            $product_tax = 0;
                            $shipping_charges = 0;

                            foreach($Cart as $item){
                                $cart_id = (isset($item['id']))?$item['id']:0;

                                $qty = $item['quantity'];
                                $product = Product::where('id', $item['product_id'])->first();

                                $product_price = $product->price;

                                $RangePrice = CustomHelper::GetRangePrice($product_price, $qty);

                                $price = $RangePrice*$qty;

                                $sub_total = $sub_total + $price;
                                $grand_total = $grand_total + $price;

                                $ordersProducts[] = array(
                                    'order_id' => $order_id,
                                    'product_id' => $product->id,
                                    'product_code' => $product->code,
                                    'product_name' => $product->name,
                                    'product_price' => $RangePrice,
                                    'final_price' => $price,
                                    'product_tax' => $product_tax,
                                    'product_qty' => $qty,
                                );
                            }

                            Cart::whereIn('id', $cart_ids)->where('customer_id', $customer_id)->delete();

                            if(session()->has('order_id')){
                                session()->forget('order_id');
                            }
                            session(['order_id' => $order_id]);

                            $orderUpdateData['subtotal'] = $sub_total;
                            $orderUpdateData['total'] = $grand_total;
                            $orderUpdateData['shipping_charges'] = $shipping_charges;

                            if(!empty($ordersProducts) && count($ordersProducts) > 0){
                                Order::where('id', $order_id)->update($orderUpdateData);

                                DB::table('orders_products')->insert($ordersProducts);
                            }

                            return redirect('order/success');
                        }

                        //echo_die($is_mail_sent);
                    }
                    else{
                        return redirect('cart');
                    }
                    //$insertedOrder = Order::insertGetId($OrderData);

                }

            }
            else{
                return redirect('cart');
            }
        }
        else{
            return redirect('cart');
        }
    }


    public function success(){

        $data= [];
        $order_id = 0;

        if(session()->has('order_id')){
            $order_id = session('order_id');
        }

        //$order_id = 61;

        if(is_numeric($order_id) && $order_id > 0){
            
            $data=[];
            $order_model = new Order;
            $res = Order::where(['order_id'=>$order_id])->first();

            $order_products = $res->order_products;

            $designers_arr = [];

            $designs_arr = [];

            //prd($order_products->toArray());

            if(!empty($order_products) && count($order_products) > 0){
              foreach($order_products as $op){

                $product_design = $op->Design;

                if(!empty($product_design) && count($product_design) > 0){

                  $Designer = $product_design->Designer;

                  if(!empty($Designer) && count($Designer) > 0){                    

                    $designs_arr[$Designer->id][$product_design->id] = array(
                      'design_id' => $product_design->id,
                      'design_name' => $product_design->name,
                      'design_slug' => $product_design->slug
                    );

                    $designers_arr[$Designer->id] = array(
                      'first_name' => $Designer->first_name,
                      'last_name' => $Designer->last_name,
                      'email' => $Designer->email,
                      'designs' => $designs_arr[$Designer->id]
                    );
                  }
                }
              }
            }
            
            $data['res'] = $res;

            session()->forget('order_id');
            $order_success= true;
            $tagline = 'Your order is placed successfully with the order id:'.$order_id;

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
            if(empty($ADMIN_EMAIL)){
              $ADMIN_EMAIL = config('custom.admin_email');
            }
            $from_email = $ADMIN_EMAIL;

            $email_data =$data;
            $user_name= $res->billing_first_name." ".$res->billing_last_name;
            $email_data['user_name'] = $user_name;

            $tag_line= "Hi $user_name, Your order is placed successfully with the order id:".$order_id;
            $email_data['tag_line'] = $tag_line;

            /*$email_view = view('emails.orders.customer.order_success_failed', $email_data)->render();
            echo "customer-email_view";
            pr($email_view);*/
            
            $is_mail = CustomHelper::SendMail('emails.orders.customer.order_success_failed', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);
            
            // Sending Email to Admin
           
            $to_email = 'ramji@indiaint.com';
            $subject = 'Orer Success -'.$order_id;
            $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

            if(empty($ADMIN_EMAIL)){
              $ADMIN_EMAIL = config('custom.admin_email');
            }

            $from_email = $ADMIN_EMAIL;

            $email_data = $data;
            $user_name='Admin';
            $email_data['user_name'] = $user_name;
            $tag_line= "Hi $user_name, New order is placed successfully with the order id:".$order_id;
            $email_data['tag_line'] = $tag_line;            

            /*$email_view = view('emails.orders.admin.order_success_failed', $email_data)->render();
            echo "admin-email_view";
            prd($email_view);*/

            $is_mail = CustomHelper::SendMail('emails.orders.admin.order_success_failed', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);


            $this->send_email_to_designers($designers_arr);


            return view('order.success_failed', $data);

        }
        
        return redirect(url('/'));
        
    }

    public function send_email_to_designers($designers_arr){

      //echo 'send_email_to_designers';

      //prd($designers_arr);

      if(!empty($designers_arr) && count($designers_arr) > 0){
        $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

        if(empty($ADMIN_EMAIL)){
          $ADMIN_EMAIL = config('custom.admin_email');
        }

        $from_email = $ADMIN_EMAIL;

        foreach($designers_arr as $designer){

          //prd($designer);

          if(!empty($designer['email'])){

            $to_email = $designer['email'];
            $designs = $designer['designs'];

            //$to_email = 'ramji@indiaint.com';

            $name = trim($designer['first_name'].' '.$designer['last_name']);

            $subject = 'Desings purchased on '.date('d M Y');

            $email_data = [];
            $email_data['name'] = $name;
            $email_data['designs'] = $designs;

            /*$email_view = view('emails.design_buy_notification', $email_data)->render();
            echo "admin-email_view";
            prd($email_view);*/

            $is_mail = CustomHelper::SendMail('emails.design_buy_notification', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);
          }
        }
      }

    }

    public function failed(){

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
            
            
            $is_mail = CustomHelper::SendMail('emails.orders.customer.order_success_failed', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);


            
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
            $is_mail = CustomHelper::SendMail('emails.orders.admin.order_success_failed', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);


            

            return view('order.success_failed', $data);

        }
        

        
    }



    /**
     * Return URL from PayPal
     * URL: /shop/checkout/paypal(?success=<1/0>&PayerID=&token=)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkoutPayPal(Request $request) 
    {
        $orderId = session('order.id');

        $order = Order::findOrFail($orderId);

        // If Payment is not successful (i.e. canceled)
        if (!$request->get('success')) {
            $order->update([
                'status' => 'canceled'
            ]);

            session()->forget('order.paypal');

            return redirect(route('shop'))->with('alert-warning', 'PayPal transaction has been canceled or failed.');
        }
        else {
            $paypal = new PayPalHelper();

            $result = $paypal->getPaymentStatus($request);

            // If Payment is made
            if ($result->getState() == 'approved') {
                $paymentId = session('order.paypal.payment_id');

                session()->forget('order');

                $order->update([
                    'payment_status' => 'paid',
                    'paypal_payment_id' => $paymentId
                ]);

                session()->forget('cart');

                // Send Order Confirmation email to customer
                $this->__sendEmail('order_confirmation', $order);

                // Send New Order notification email to Admin
                $this->__sendEmail('new_order', $order);

                return redirect(route('shop.checkout.confirmation', [$order['order_number'], $order['confirmation_code']]));

            }

            return redirect(route('shop'))->with('alert-danger', 'Unexpected error occurred & payment has failed.');
        }
    }

    /**
     * Shop - Submit Checkout
     * URL: /shop/checkout (POST)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkoutSubmit(Request $request) {
        $data = $request->all();

        $orderData = [];

        // Retrieve cart info from session
        $cart = session('cart');

        // Whether Pay Now or Pay Later
        $payNow = !(app()->global['settings']['pay_later']['state'] && isset($data['pay_later']));

        // Compute Fees
        $fees = CustomHelper::computeFees();

        // If customer is logged in
        if (auth()->check()) {
            // Validation
            $validation = [
                'delivery.name'      => 'sometimes|required|max:255',
                'delivery.address_1' => 'sometimes|required|max:255',
                'delivery.address_2' => 'sometimes|max:255',
                'delivery.city'      => 'sometimes|required|max:255',
                'delivery.state'     => 'sometimes|required|size:2',
                'delivery.zipcode'   => 'sometimes|required|max:20',
                'delivery.phone'     => 'sometimes|required|max:20',
                'notes'              => 'sometimes|max:255',
            ];

            // Extra validation for Pay Now: If total is not $0, then also validate billing and stripe_token
            if ($payNow && $data['payment']['method'] == 'credit_card' && $fees['total'] > 0) {
                $validation['billing.name'] = 'sometimes|required|max:255';
                $validation['billing.address_1'] = 'sometimes|required|max:255';
                $validation['billing.address_2'] = 'sometimes|max:255';
                $validation['billing.city'] = 'sometimes|required|max:255';
                $validation['billing.state'] = 'sometimes|required|size:2';
                $validation['billing.zipcode'] = 'sometimes|required|max:20';
                $validation['billing.phone'] = 'sometimes|required|max:20';
                $validation['stripe_token'] = 'required_without:card_id'; // required stripe_token if card_id is NOT present
            }

            $this->validate($request, $validation);

            $user = auth()->user();

            $orderData['user_id'] = $user['id'];

            // Contact
            $orderData['contact_email'] = $user['email'];
        }
        // If customer is a guest
        else {
            // Validation
            $validation =  [
                'contact_email'      => 'required|max:255|email',
                'delivery.name'      => 'required|max:255',
                'delivery.address_1' => 'required|max:255',
                'delivery.address_2' => 'sometimes|max:255',
                'delivery.city'      => 'required|max:255',
                'delivery.state'     => 'required|size:2',
                'delivery.zipcode'   => 'required|max:20',
                'delivery.phone'     => 'required|max:20',
                'notes'              => 'sometimes|max:255',
            ];

            // Extra validation for Pay Now: // If total is not $0, then also validate billing and stripe_token
            if ($payNow && $data['payment']['method'] == 'credit_card' && $fees['total'] > 0) {
                $validation['billing.name'] = 'sometimes|required|max:255';
                $validation['billing.address_1'] = 'sometimes|required|max:255';
                $validation['billing.address_2'] = 'sometimes|max:255';
                $validation['billing.city'] = 'sometimes|required|max:255';
                $validation['billing.state'] = 'sometimes|required|size:2';
                $validation['billing.zipcode'] = 'sometimes|required|max:20';
                $validation['billing.phone'] = 'sometimes|required|max:20';
                $validation['stripe_token'] = 'required';
            }

            $this->validate($request, $validation);

            // Contact
            $orderData['contact_email'] = $data['contact_email'];
        }

        // Notes
        if (!empty($data['notes'])) {
            $orderData['notes'] = $data['notes'];
        }

        // Generate a random & unique order number for this order
        $orderData['order_number'] = $this->__generateOrderNumber();

        // Generate a random confirmation code for this order
        $orderData['confirmation_code'] = strtoupper(str_random(6));

        $orderData['subtotal'] = $fees['subtotal'];
        $orderData['tax'] = $fees['tax'];
        $orderData['shipping_fee'] = $fees['shipping'];
        $orderData['discount'] = $fees['discount'];
        $orderData['total'] = $fees['total'];

        // Stripe
        if ($payNow && $data['payment']['method'] == 'credit_card' && !empty($data['stripe_token'])) {
            $orderData['token'] = $data['stripe_token'];
        }

        // Shipping
        if (isset($data['cash_on_delivery'])) {
            $orderData['cash_on_delivery'] = true;
        } else {
            foreach ($data['shipping'] as $k => $v) {
                $orderData['shipping_carrier'] = $k;
                $orderData['shipping_plan'] = $v;

                break;
            }
        }

        // Addresses - Delivery
        // If customer is logged in, and selected an existing delivery address (or selected a newly created delivery address from the modal)
        if (!empty($data['delivery']['address_id'])) {
            $deliveryAddress = Address::find($data['delivery']['address_id']);

            // Set up delivery address for the order
            $orderData['delivery_name'] = $deliveryAddress['name'];
            $orderData['delivery_phone'] = $deliveryAddress['phone'];
            $orderData['delivery_address_1'] = $deliveryAddress['address_1'];
            $orderData['delivery_address_2'] = $deliveryAddress['address_2'];
            $orderData['delivery_city'] = $deliveryAddress['city'];
            $orderData['delivery_state'] = $deliveryAddress['state'];
            $orderData['delivery_zipcode'] = $deliveryAddress['zipcode'];
        }
        // Else, if this is a guest, or a customer w/ no saved delivery addresses, a new delivery address is submitted
        else {
            // Set up delivery address for the order
            $orderData['delivery_name'] = $data['delivery']['name'];
            $orderData['delivery_phone'] = $data['delivery']['phone'];
            $orderData['delivery_address_1'] = $data['delivery']['address_1'];
            $orderData['delivery_address_2'] = $data['delivery']['address_2'];
            $orderData['delivery_city'] = $data['delivery']['city'];
            $orderData['delivery_state'] = $data['delivery']['state'];
            $orderData['delivery_zipcode'] = $data['delivery']['zipcode'];

            // If customer is logged in, and has no saved delivery address, add this new one in
            if (auth()->check()) {
                $data['delivery']['is_delivery'] = true;

                // If customer doesn't have a default delivery address yet, set this as default delivery address
                if ($user->deliveryAddress()->count() == 0) {
                    $data['delivery']['default_delivery'] = true;
                }

                $user->addresses()->create($data['delivery']);
            }
        }

        // Addresses - Billing (Only for Credit Card payment method)
        if ($payNow && $data['payment']['method'] == 'credit_card') {
            // If customer is logged in, and selected an existing billing address (or selected a newly created billing address from the modal)
            if (!empty($data['billing']['address_id'])) {
                $billingAddress = Address::find($data['billing']['address_id']);

                // Set up billing address for the order
                $orderData['billing_name'] = $billingAddress['name'];
                $orderData['billing_phone'] = $billingAddress['phone'];
                $orderData['billing_address_1'] = $billingAddress['address_1'];
                $orderData['billing_address_2'] = $billingAddress['address_2'];
                $orderData['billing_city'] = $billingAddress['city'];
                $orderData['billing_state'] = $billingAddress['state'];
                $orderData['billing_zipcode'] = $billingAddress['zipcode'];
            }
            // Else, if this is a guest, or a customer w/ no saved billing addresses
            else if ($payNow) {
                // If customer would like to use the same selected/input delivery address as billing address
                if (isset($data['copy_address']) && $data['copy_address']) {
                    // Set up billing address for the order
                    $orderData['billing_name'] = $orderData['delivery_name'];
                    $orderData['billing_phone'] = $orderData['delivery_phone'];
                    $orderData['billing_address_1'] = $orderData['delivery_address_1'];
                    $orderData['billing_address_2'] = $orderData['delivery_address_2'];
                    $orderData['billing_city'] = $orderData['delivery_city'];
                    $orderData['billing_state'] = $orderData['delivery_state'];
                    $orderData['billing_zipcode'] = $orderData['delivery_zipcode'];
                }
                // Else, a new billing address is submitted
                else {
                    // Set up billing address for the order ("billing" will not be set if total is $0)
                    if (isset($data['billing'])) {
                        $orderData['billing_name'] = $data['billing']['name'];
                        $orderData['billing_phone'] = $data['billing']['phone'];
                        $orderData['billing_address_1'] = $data['billing']['address_1'];
                        $orderData['billing_address_2'] = $data['billing']['address_2'];
                        $orderData['billing_city'] = $data['billing']['city'];
                        $orderData['billing_state'] = $data['billing']['state'];
                        $orderData['billing_zipcode'] = $data['billing']['zipcode'];
                    }
                }

                // If customer is logged in, and has no saved billing address, add this new one in (no billing info if total is $0)
                if (auth()->check() && $fees['total'] > 0) {
                    $billingAddress = [
                        'name' => $orderData['billing_name'],
                        'phone' => $orderData['billing_phone'],
                        'address_1' => $orderData['billing_address_1'],
                        'address_2' => $orderData['billing_address_2'],
                        'city' => $orderData['billing_city'],
                        'state' => $orderData['billing_state'],
                        'zipcode' => $orderData['billing_zipcode'],
                        'is_billing' => true
                    ];

                    // If customer doesn't have a default billing address yet, set this as default billing address
                    if ($user->billingAddress()->count() == 0) {
                        $billingAddress['default_billing'] = true;
                    }

                    $user->addresses()->create($billingAddress);
                }
            }
        }

        // Payment
        if ($fees['total'] > 0) {
            if ($payNow) {
                // Pay with Credit Card
                if ($data['payment']['method'] == 'credit_card') {
                    Stripe::setApiKey(env('STRIPE_SECRET'));

                    // If customer is logged in
                    if (auth()->check()) {
                        // If customer doesn't have a Stripe customer account yet
                        if (empty($user['stripe_customer_id'])) {
                            // Create a Stripe Customer account
                            $stripeCustomer = StripeCustomer::create([
                                'description' => $user['first_name'] . ' ' . $user['last_name'],
                                'email' => $user['email'],
                                'metadata' => [
                                    'user_id' => $user['id']
                                ]
                            ]);

                            // Update 'stripe_customer_id' of the local customer
                            $user->stripe_customer_id = $stripeCustomer->id;
                            $user->save();
                        } else {
                            // Retrieve customer from Stripe
                            $stripeCustomer =  StripeCustomer::retrieve($user['stripe_customer_id']);
                        }

                        // If Stripe Card is selected
                        if (!empty($data['card_id'])) {
                            $card = PaymentSource::find($data['card_id']);

                            $stripeCard = $stripeCustomer->sources->retrieve($card['vendor_card_id']);

                            // Charge this Stripe customer w/ this Stripe card
                            $charge = StripeCharge::create([
                                'amount' => $fees['total'] * 100, // in cents
                                'currency' => 'usd',
                                'customer' => $stripeCustomer->id,
                                'source' => $stripeCard->id
                            ]);
                        }
                        // Else, a token must have been submitted
                        else if (!empty($data['stripe_token'])) {
                            // If customer wants to save this new payment method
                            if (isset($data['save_payment_method']) && $data['save_payment_method']) {
                                $stripeCard = $stripeCustomer->sources->create(['source' => $data['stripe_token']]);

                                // Charge this Stripe customer w/ this Stripe card
                                $charge = StripeCharge::create([
                                    'amount' => $fees['total'] * 100, // in cents
                                    'currency' => 'usd',
                                    'customer' => $stripeCustomer->id,
                                    'source' => $stripeCard->id
                                ]);

                                // Insert this Stripe card to DB
                                PaymentSource::create([
                                    'vendor' => 'stripe',
                                    'name_on_card' => $stripeCard->name,
                                    'last4' => $stripeCard->last4,
                                    'brand' => $stripeCard->brand,
                                    'type' => 'card',
                                    'user_id' => $user['id'],
                                    'default' => true, // Since this is the first card
                                    'vendor_card_id' => $stripeCard->id
                                ]);
                            }
                            // Else, just charge the given card (by token)
                            else {
                                // Charge w/ this Stripe token
                                $charge = StripeCharge::create([
                                    'amount' => $fees['total'] * 100, // in cents
                                    'currency' => 'usd',
                                    'customer' => $stripeCustomer->id,
                                    'source' => $data['stripe_token']
                                ]);
                            }
                        }
                    }
                    // Else, charge w/ the Stripe token
                    else {
                        $charge = StripeCharge::create([
                            'amount' => $fees['total'] * 100, // in cents
                            'currency' => 'usd',
                            'source' => $data['stripe_token'] // obtained with Stripe.js
                        ]);
                    }

                    $orderData['payment_status'] = 'paid'; // Set Payment Status of this Order to 'paid'
                    $orderData['payment_method'] = 'card'; // Set Payment Method of this Order to 'card'
                    $orderData['stripe_charge_id'] = $charge->id; // Save Stripe Charge ID to this Order
                }
                // Pay with PayPal
                else {
                    $orderData['payment_method'] = 'paypal'; // Set Payment Method of this Order to 'paypal'
                }
            } else {
                $orderData['pay_later'] = true;
            }
        }
        else {
            $orderData['payment_status'] = 'free'; // Set Payment Status of this Order to 'free' if total is $0
        }

        $createdOrder = Order::create($orderData);

        // If Order is created
        if ($createdOrder) {
            $orderItems = collect($cart['items'])->reduce(function($carry, $item) {
                // Update inventory
                $inventoryItem = Inventory::find($item['inventory_id']);

                if ($inventoryItem['stock'] > $item['quantity']) {
                    $inventoryItem->decrement('stock', $item['quantity']);
                } else {
                    $inventoryItem->stock = 0;
                    $inventoryItem->save();
                }

                unset($item['product_id'], $item['stock']);

                $carry[$item['inventory_id']] = $item;

                return $carry;
            });

            $createdOrder->inventoryItems()->attach($orderItems);

            // Update Coupon usage, if used, and associate the Coupon with the Order
            if (session()->has('cart.coupon')) {
                $coupon = Coupon::find(session('cart.coupon.id'));

                $coupon->usage = $coupon['usage'] + 1;

                $coupon->save();

                $createdOrder->coupon()->associate($coupon);

                $createdOrder->save();
            }

            // Pay Now
            if ($payNow) {
                // Pay with PayPal
                if ($data['payment']['method'] == 'paypal') {
                    // Save the Order ID to session
                    session(['order.id' => $createdOrder['id']]);

                    $paypal = new PayPalHelper();

                    return $paypal->pay($createdOrder, $orderItems);
                }
                // Pay with Credit Card
                else {
                    // Update Stripe Charge's metadata w/ the Order ID
                    if ($fees['total'] > 0) {
                        $charge->metadata = ['order_id' => $createdOrder['id']];
                        $charge->save();
                    }
                }
            }

            // Empty the Cart session
            session()->forget('cart');

            // Send Order Confirmation email to customer
            $this->__sendEmail('order_confirmation', $createdOrder);

            // Send New Order notification email to Admin
            $this->__sendEmail('new_order', $createdOrder);

            return redirect(route('shop.checkout.confirmation', [$orderData['order_number'], $orderData['confirmation_code']]));
        }

    }

    /**
     * Shop - Checkout - Login
     * URL: /shop/checkout/login (POST)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request) {
        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            return redirect()->intended(route('shop.checkout'));
        } else {
            return back()->with('alert-warning', 'The email and/or password you entered is not correct, please try again.');
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

    /**
     * Send transactional email
     *
     * @param $type
     * @param $data
     */
    private function __sendEmail($type, $data) {
        switch ($type) {
            case 'order_confirmation':
            case 'new_order':
                // If customer is logged in, retrieve name and email from user info
                if (!empty($data['user_id'])) {
                    $data['name'] = auth()->user()->first_name;
                    $data['email'] = auth()->user()->email;
                }
                // Else if customer is a guest, use given billing name for name, and contact email for email
                else {
                    $data['name'] = $data['billing_name'];
                    $data['email'] = $data['contact_email'];
                }

                $sub = [
                    'customer_name' => $data['name'],
                    'customer_email' => $data['email'],
                    'order_number' => (string) $data['order_number'],
                    'confirmation_code' => $data['confirmation_code'],
                    'purchase_date' => $data['created_at']->timezone(config('custom.timezone'))->toDayDateTimeString(),
                    'shipping' => [
                        'carrier' => config('custom.checkout.shipping..carriers.' . $data['shipping_carrier'] . '.name'),
                        'plan' => config('custom.checkout.shipping..carriers.' . $data['shipping_carrier'] . '.plans.' . $data['shipping_plan'] . '.name')
                    ],
                    'billing_address' => [
                        'name'       => $data['billing_name'],
                        'phone'      => $data['billing_phone'],
                        'address_1'  => $data['billing_address_1'],
                        'address_2'  => $data['billing_address_2'],
                        'city'       => $data['billing_city'],
                        'state'      => $data['billing_state'],
                        'zipcode'    => $data['billing_zipcode']
                    ],
                    'delivery_address' => [
                        'name'       => $data['delivery_name'],
                        'phone'      => $data['delivery_phone'],
                        'address_1'  => $data['delivery_address_1'],
                        'address_2'  => $data['delivery_address_2'],
                        'city'       => $data['delivery_city'],
                        'state'      => $data['delivery_state'],
                        'zipcode'    => $data['delivery_zipcode']
                    ],
                    'items' => [],
                    'subtotal' => number_format($data['subtotal'], 2),
                    'tax' => number_format($data['tax'], 2),
                    'shipping_fee' => number_format($data['shipping_fee'], 2),
                    'total' => number_format($data['total'], 2),
                ];

                foreach ($data->inventoryItems as $item) {
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

                if ($type == 'order_confirmation') {
                    $templateId = config('custom.emails.templates.order_confirmation');

                    $recipients = [
                        [
                            'address' => $data['email'],
                            'name' => $data['name'],
                            'substitution_data' => $sub
                        ]
                    ];
                }
                else if ($type == 'new_order') {
                    $templateId = config('custom.emails.templates.new_order');

                    $recipients = [
                        [
                            'address' => env('OWNER_EMAIL'),
                            'name' => env('OWNER_NAME'),
                            'substitution_data' => $sub
                        ],
                        [
                            'address' => env('ADMIN_EMAIL'),
                            'name' => env('ADMIN_NAME'),
                            'substitution_data' => $sub
                        ]
                    ];
                }

                SparkPostHelper::sendTemplate($templateId, $recipients);

                break;
        }
    }

}