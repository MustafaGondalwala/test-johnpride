<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Helpers\CustomHelper;

use App\UserAddress;
use App\Product;
use App\Country;
use App\State;
use App\City;
use App\Size;
use App\Coupon;

use DB;
use Validator;
use Cart;


class AddressController extends Controller {

  // for cart listing
  public function index() {
    //prd(Cart::getContent());
    $data = [];

    $cartContent = Cart::getContent();

    $productModel = new Product;

    $data['meta_title'] = 'Cart | SlumberJill'; 
    $data['cartContent'] = $cartContent; 
    $data['productModel'] = $productModel;

    return view('cart.index', $data);
  }


  public function empty(){
    $data = [];

    $data['meta_title'] = 'Cart empty | SlumberJill'; 

    return view('cart.index', $data);
  }


  public function add(Request $request){

    $response = [];
    $response['success'] = false;

    //pr($request->toArray());

    $slug = (isset($request->slug))?$request->slug:'';
    $size_id = (isset($request->size))?$request->size:1;
    $qty = (isset($request->qty))?$request->qty:1;

    if(!empty($slug) && is_numeric($size_id) && $size_id > 0){
      $product = Product::where('slug', $slug)->first();
      $size = Size::find($size_id);

      if(!empty($product) && count($product) > 0){
        //prd($product->toArray());

        $cartId = $product->id.'_'.$size->id;

        $price = $product->price;
        $sale_price = $product->sale_price;

        $productPrice = $price;

        if(is_numeric($sale_price) && $sale_price > 0 && is_numeric($price) && $price > 0){
          if($sale_price < $price){
            $productPrice = $sale_price;
          }
        }

        $cartData = [];
        $attributes = [];

        $attributes['id'] = $product->id;
        $attributes['slug'] = $product->slug;
        $attributes['gender'] = $product->gender;
        $attributes['price'] = $product->price;
        $attributes['sale_price'] = $product->sale_price;
        $attributes['size_id'] = $size_id;
        $attributes['size_name'] = $size->name;
        $attributes['color_id'] = $product->color_id;
        $attributes['color_name'] = $product->color_name;
        $attributes['gst'] = $product->gst;
        $attributes['weight'] = $product->weight;

        $cartData['id'] = $cartId;
        $cartData['name'] = $product->name;
        $cartData['price'] = $productPrice;
        $cartData['quantity'] = $qty;
        $cartData['attributes'] = $attributes;

        $attributes = [];

        Cart::add($cartData);

        $cart = Cart::get($cartId);

        if(!empty($cart) && count($cart) > 0){
          //prd($cart->toArray());

          $cartCollection = Cart::getContent();
          $cartCount = $cartCollection->count();
          $response['cartCount'] = $cartCount;
          $response['success'] = true;
          //$response['message'] = ;
        }

        /*$cartCollection = Cart::getContent();

        if($cartCollection && $cartCollection->count() > 0){
          pr($cartCollection->toArray());
        }*/
      }
    }

    return response()->json($response); 

  }

  public function delete(Request $request){
    //prd($request->toArray());

    $response = [];
    $response['success'] = false;

    $cartId = (isset($request->cartId))?$request->cartId:0;

    if(!empty($cartId)){
      Cart::remove($cartId);

      $cart = Cart::get($cartId);

      if(empty($cart) || count($cart) == 0){
        $response['success'] = true;
      }
    }


    return response()->json($response); 
  }

  public function add_to_cart(Request $request)
  {
    //prd($request->toArray());
    $output = array();
    $output['cart_total_items']=0;
    $output['message']='Some error occured';
    //  for getting price
    $common_model= new Common; 
    $product_price_params= array();

    if(!empty($request->products_id) && !empty($request->qty))
    {
      $products_id = $request->products_id;
      $products_quantity = $request->qty; 
      $length=1;      

      $size= ''; 
      $size_value=''; 
      if(!empty($request->size))
      {
        $size= $request->size;
        $product_price_params['fabric_size']= $size;
      }
      if($request->length)
      {
        $length=$request->length;
      }
      $product_price_params['length']= $length;
      $product_price_params['qty']= $products_quantity;

      $product_data = Product::where(['id'=>$products_id])->first();

      //prd($products_images);
      if(!empty($product_data))
      {
        $cart_product_data = array();
        $cart_product_data['id'] = $product_data->id;
        $cart_product_data['name'] = $product_data->name;        
        $cart_product_data['quantity'] = $products_quantity;
        $cart_product_data['attributes'] = array();
        //pr($cart_product_data);  exit;
        $fc = $product_data->price;

        if($product_data->type=='swatch_book')
        {
          $product_price_params['swtach_book_id']= $product_data->id;
        }

        if($product_data->type=='fabric')
        {
          $product_price_params['fabric_id']= $product_data->id;
        }       

        if(!empty($size))
        {
          $swatch_size='20*20cm'; 
          $fat_size='50*50cm';

          if(!empty($product_data->swatch_size))
          {
            $swatch_size=$product_data->swatch_size;
          }
          if(!empty($product_data->fat_size))
          {
            $fat_size=$product_data->fat_size;
          }

          if($size=='linear_metre')
          {
            $fc = $product_data->price;
            $size_value='Linear Meter '.$product_data->size;
          }
          if($size=='swatch')
          {
            $fc = $product_data->swatch_price;
            $size_value='Test Swatch '.$swatch_size;
          }

          if($size=='fat_size')
          {
            $fc = $product_data->fat_price;
            $size_value='Big Swatch '.$swatch_size;
          }
          $cart_product_data['attributes']['size']=$size_value;
        }

        $cart_product_data['attributes']['length']=$length;
        $fc= $fc*$length;
        $dc = 0;
        $products_images = '';

        //if fabric generator used
        if($request->fgid)
        {
          $fabric_generator['design_id']=$request->fgid;

           $product_price_params['design_id']= $request->fgid;

          $fg_data = FabricGenerator::whereRaw("MD5(CONCAT(id,'',file_extension)) = '$request->fgid'")->first();
          if(!empty($fg_data))
          {
            $data_update = array(
                'products_id' => $products_id,
                'layout' => $request->layout,
                'rotate' => $request->rotate,
                'scale' => $request->scale,
                'size' => $size,
                'length' => $length
              );
            if(FabricGenerator::where('id', $fg_data->id)->update($data_update))
            {
              $fabric_generator = $data_update;
              $fabric_generator['fgid'] = $request->fgid;

              $fabric_generator['preview_design'] = url('fabrics/preview_design/'.$request->fgid);
              $cart_product_data['attributes']['fabric_generator'] = $fabric_generator;
              $products_images = url('public/storage/fabric_generator/thumb/'.$fg_data->file_name.'-center.'.$fg_data->file_extension);
            }
            $dc = fabric_generator_price($fg_data->id);
          }
        }

        

        if(empty($products_images))
        {
          $defaultImage = $product_data->defaultImage;
          if(isset($defaultImage->name))
          {
             $products_images = url('public/storage/fabrics/'.$defaultImage->name);
             
             if(in_array($product_data->type, array('swatch_book', 'design')))
             {
                $products_images = url('public/storage/designs/'.$defaultImage->name);

             }
          }
          else
          {
            $Images = $product_data->Images;
            if(!empty($Images))
            {
             
             $products_images = url('public/storage/fabrics/'.$Images[0]->name);
             if(in_array($product_data->type, array('swatch_book', 'design')))
             {
            
                $products_images = url('public/storage/designs/'.$Images[0]->name);

             }
            }
          }
        }
        

        



        //$cart_product_data['price'] = $fc + $dc;
        //
        $price_data=$common_model->get_cart_product_price($product_price_params);


        $cart_product_data['price']= $price_data['total_cost']; 



        $cart_product_data['attributes']['products_images'] = $products_images;
        $cart_product_data['attributes']['products_quantity'] = $products_quantity;


        // adding some attributes to 
        $cart_product_data['attributes']['design_id'] = $price_data['design_id'];
        $cart_product_data['attributes']['designer_id'] = $price_data['designer_id'];
        $cart_product_data['attributes']['designer_commission'] = $price_data['designer_commission'];


        //pr($cart_product_data);  exit;

        $rowid = '';
        if(!Cart::isEmpty())
        {
          $cart_items = Cart::getContent();
          foreach ($cart_items as $item) 
          {
            if($products_id == $item->id)
            {
              $rowid = $item->id;  
            }            
          }
        }
        
        if(!empty($rowid))
        {
          Cart::update($rowid, array(
            'quantity' => array(
              'relative' => false,
              'value' =>$products_quantity
              ),
           ));
          unset($cart_product_data['quantity']);
          //pr($cart_product_data);
          Cart::update($rowid, $cart_product_data);
          //echo 'Done'; die;
        }
        else
        {
          Cart::add($cart_product_data);
        }        
      }

      if(!Cart::isEmpty())
      {
        $cartCollection = Cart::getContent();
        $cart_count=$cartCollection->count();
        if(!empty($cart_count))
        {
          $output['status']='success';
          $output['cart_total_items'] = $cart_count;
          $output['cart_list_html'] = $this->load_cart_list();
          $output['message'] = 'Product successfully added in your cart';
        }
      }
    }
    return response()->json($output);
  }


  public function address(Request $request){
    $data = [];

    $cartContent = Cart::getContent();

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

    $data['meta_title'] = 'Cart Address | SlumberJill';
    $data['cartContent'] = $cartContent; 
    $data['productModel'] = $productModel;
    $data['states'] = $states;

    return view('cart.address', $data);
  }


    // for checkout
  public function checkout(Request $request){

    $data = [];

    $meta_title = 'Cart Checkout';

    $data['meta_title'] = $meta_title;

    return view('cart.checkout', $data);

  }

  
    //other functions
  public function load_cart_list()
  { 
   $data=[];
   $Common_model=new Common;

         $data['coupon_discount']= $Common_model->get_cart_coupon_discount(Cart::getTotal());
         return view('cart.include.cart_list', $data)->render(); 
    }


    
    

    // for applying coupon
    public function apply_coupon(Request $request)
    {
         $res['status']=0;
         $res['cart_total_items']=0;
         $res['message']='Invalid Coupon.';
         $res['cart_list_html']= $this->load_cart_list();
         $method=$request->method();
         if($method=='POST')
         {
             
             if(!empty($request->coupon_code))
             {
                 $coupon_code= trim(strtolower($request->coupon_code));

                 $coupon_data= Coupon::where(['code'=>$coupon_code, 'status'=>1])
                 ->whereDate('start_date', '<=', date("Y-m-d"))
                 ->whereDate('expiry_date', '>=', date("Y-m-d"))->first();
                 //pr($coupon_data);
                 if(!empty($coupon_data))
                 {
                    $order_total= Cart::getTotal();

                    $discount_type=$coupon_data->type;
                    $discount_val=$coupon_data->discount;

                    $order_amount=$coupon_data->order_amount;
                    $use_limit=$coupon_data->use_limit;
                    $max_discount=$coupon_data->max_discount;

                    $coupon_apply_status= true;

                    if($discount_type=='value' &&  $discount_val > $order_total)
                    {

                        $res['status']=0;
                        $res['message']='Coupon can not be applied.';
                        $coupon_apply_status=false;

                     }
                    else if($use_limit==0)
                    {

                        $res['status']=0;
                        $res['message']='Coupon can not be applied.';
                        $coupon_apply_status=false;

                     }
                    else if($order_amount > 0 &&  $order_amount > $order_total )
                    {
                        $res['status']=0;
                        $res['message']='Order total must be greater than or equal to Rs '.$order_amount. 'to use this coupn code.';
                        $coupon_apply_status=false;

                    }

                    if($coupon_apply_status)
                    {
                        $coupon_sess_data= [];
                        $coupon_sess_data['coupon_id']= $coupon_data->id;

                        $coupon_sess_data['coupon_code']=$coupon_data->code;
                        $coupon_sess_data['coupon_name']=$coupon_data->name;
                        $coupon_sess_data['discount_type']= $discount_type;
                        $coupon_sess_data['discount_val']= $discount_val; 
                        $coupon_sess_data['order_amount']= $order_amount; 
                        $coupon_sess_data['use_limit']= $use_limit; 
                        $coupon_sess_data['max_discount']= $max_discount; 

                        session(['coupon_sess_data' => $coupon_sess_data]);
                        $res['status']=1;
                        $res['message']='Coupon applied successfully.';
                     }


                    //pr($coupon_data);


                 }
                




             }
             

              

            





            if(!Cart::isEmpty())
            {
                $cartCollection = Cart::getContent();
                $cart_count=$cartCollection->count();
                if(!empty($cart_count))
                {

                    
                    $res['cart_total_items']=$cart_count;
                    $res['cart_list_html']= $this->load_cart_list();
                    
                 }

            }  
        

         }
         return response()->json($res);
      

      

    }


    // for applying coupon
    public function remove_coupon(Request $request)
    {
         $res['status']=0;
         $res['cart_total_items']=0;
         $res['message']='Invalid Request.';
         $method=$request->method();
         if($method=='POST')
         {

            if (session()->has('coupon_sess_data')) 
            {
               
                session()->forget('coupon_sess_data');

            }

            $res['status']=1;
           
            $res['message']='Coupon removed successfully.';
             
               
        

         }

        if(!Cart::isEmpty())
        {
                $cartCollection = Cart::getContent();
                $cart_count=$cartCollection->count();
                if(!empty($cart_count))
                {

                    
                    $res['cart_total_items']=$cart_count;
                    $res['cart_list_html']= $this->load_cart_list();
                    
                 }

         }  
         return response()->json($res);
      

      

    }

     // for applying coupon
    public function use_wallet_amount(Request $request)
    {
        
         $method=$request->method();
         if($method=='POST')
         {
             $is_checked=$request->is_checked;
             if($is_checked)
             {

                 session(['is_wallet_use'=>1]);


             }
             else
             {

                if (session()->has('is_wallet_use')) 
                {
                   
                    session()->forget('is_wallet_use');

                }



             }

           



         }

         echo '1';
        
      

      

    }

    public function get_product_price(Request $request) {

        $common_model= new Common; 
        $product_price_params= array(); 

        if($request->qty){
            $product_price_params['qty']= $request->qty;
        }
        if($request->length){
            $product_price_params['length']= $request->length;
        }

        if($request->length){
            $product_price_params['fabric_size']= $request->fabric_size;
        }

        if($request->fabric_id){
            $product_price_params['fabric_id']= $request->fabric_id;
        }

        if($request->design_id){
            $product_price_params['design_id']= $request->design_id;
        }


        $price_data = $common_model->get_cart_product_price($product_price_params);

        $total_cost = (isset($price_data['total_cost']))?$price_data['total_cost']:0;

        $from_currency = (session()->has('from_currency'))?session('from_currency'):'INR';
        $to_currency = (session()->has('to_currency'))?session('to_currency'):'INR';

        $currency_price = CustomHelper::ConvertCurrency($total_cost, $from_currency, $to_currency);

        $price_data['currency_price'] = $currency_price;
        //pr($price_data); 
        return response()->json($price_data);
    }


    // for ajaxhit functions

/* end of controller */
}