<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Helpers\CustomHelper;
use App\Libraries\Cart;

use App\UserAddress;
use App\UserWishlist;
use App\UserCartItem;
use App\Product;
use App\Country;
use App\State;
use App\City;
use App\Size;
use App\Coupon;
use App\Order;
use App\Pincode;

use DB;
use Validator;


class CartController extends Controller {

// for cart listing
	public function index() {

		$data = [];

		$data['meta_title'] = 'Cart | SlumberJill';

		$cartContent = Cart::getContent();

		if($cartContent->count() > 0){

//prd($cartContent->toArray());
			$cartCount = $cartContent->count();
			$cartContent = $cartContent->sortBy('attributes.sort_order')->sortBy('attributes.id');

			$productModel = new Product;

			$currDate = date('Y-m-d');

			$couponsQeury = Coupon::where('status', 1);

			$couponsQeury->whereRaw('DATE(start_date) <= "'.$currDate.'"');
			$couponsQeury->whereRaw('DATE(expiry_date) >= "'.$currDate.'"');

//DB::enableQueryLog();
			$Coupons = $couponsQeury->get();
//prd(DB::getQueryLog());


			$data['cartContent'] = $cartContent; 
			$data['cartCount'] = $cartCount;
			$data['Coupons'] = $Coupons;
			$data['productModel'] = $productModel;

			return view('cart.index', $data);

		}
		else{
			return view('cart.empty', $data);
		}
	}


	public function empty(){
		$data = [];

		$data['meta_title'] = 'Cart empty | SlumberJill'; 

		return view('cart.empty', $data);
	}


	public function add(Request $request){

		$response = [];
		$response['success'] = false;

		$error = '';

//prd($request->toArray());

		$method = $request->method();

		if($method == 'POST'){

			$slug = (isset($request->slug))?$request->slug:'';
			$size_id = (isset($request->size))?$request->size:1;
			$qty = (isset($request->qty))?$request->qty:1;

			if(!empty($slug) && is_numeric($size_id) && $size_id > 0){
				$product = Product::where('slug', $slug)->first();
				$size = Size::find($size_id);

				if(!empty($product) && count($product) > 0){

					$isStockAvailable = $this->checkProductStock($product, $size_id, $qty);

					if($isStockAvailable){

						$userId = 0;

						if(auth()->check()){
							$userId = auth()->user()->id;
						}

						$productId = $product->id;
						$sizeId = $size->id;

						$cartCollectionCount = Cart::getContent()->count();

//prd($cartCollectionCount);

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

						if(is_numeric($userId) && $userId > 0){

						}
						else{
							$cartData['session_token'] = csrf_token();
						}

						$cartData['user_id'] = $userId;
						$cartData['size_id'] = $size_id;
						$cartData['product_id'] = $productId;
						$cartData['product_name'] = $product->name;
						$cartData['size_name'] = $size->name;
						$cartData['product_slug'] = $product->slug;
						$cartData['product_sku'] = $product->sku;
						$cartData['product_gender'] = $product->gender;
						$cartData['qty'] = $qty;
						$cartData['price'] = $product->price;
						$cartData['sale_price'] = $product->sale_price;
						$cartData['cart_price'] = $productPrice;
						$cartData['gst'] = $product->gst;
						$cartData['weight'] = $product->weight;
						$cartData['color_id'] = $product->color_id;
						$cartData['color_name'] = $product->color_name;


						$cartId = Cart::add($cartData);
//prd($cartId);

//$cart = Cart::get($cartId);

//if(!empty($cart) && count($cart) > 0){
						if(is_numeric($cartId) && $cartId > 0){
//prd($cart->toArray());

							$cartCollection = Cart::getContent();
//prd($cartCollection->toArray());
							$cartCount = $cartCollection->count();
							$response['cartCount'] = $cartCount;
							$response['success'] = true;
//$response['message'] = ;

							$this->checkWishlist($productId, $sizeId);
						}
					}
					else{
						$error = '<div style="color:#a94442;"> You have exceeded available stock. </div>';
					}
				}
			}
		}

		$response['error'] = $error;

		return response()->json($response); 

	}


	private function checkProductStock($product, $size_id, $qty){
		$user = auth()->user();

		if(!empty($product) && count($product) > 0){

			$productId = $product->id;

			$productInventorySize = $product->productInventorySize->where('id', $size_id);
			$totalStock = $productInventorySize->sum('pivot.stock');

			$totalStock = (int)$totalStock;

			$where = [];

			$sessionToken = csrf_token();

            if(auth()->check()){
                $user = auth()->user();
                $userId = $user->id;

            }
            else{
                $where['session_token'] = $sessionToken;
            }

            $where['size_id'] = $size_id;

            $where['product_id'] = $productId;

            $userCartItem = UserCartItem::where($where)->first();

            $cartQty = (isset($userCartItem->qty))?$userCartItem->qty:0;

            $newQty = $qty + (int)$cartQty;

            if($totalStock >= $newQty){
            	return true;
            }
		}

		return false;
	}

	private function checkWishlist($productId, $sizeId){
		$user = auth()->user();

		if(auth()->check()){
			$user = auth()->user();

			$userId = $user->id;

			$wishlistWhere = [];
			$wishlistWhere['user_id'] = $userId;
			$wishlistWhere['product_id'] = $productId;
			$wishlistWhere['size_id'] = $sizeId;

			$wishlist = UserWishlist::where(['user_id'=>$userId, 'product_id'=>$productId])->first();

			if(!empty($wishlist) && count($wishlist) > 0){
				$wishlist->delete();
			}
		}


	}

public function update(Request $request){
//prd($request->toArray());

	$updateTypeArr = ['size', 'qty'];

	$response = [];
	$response['success'] = false;

	return $this->updateSizeQty($request);

/*$type = (isset($request->type))?$request->type:'';

if(!empty($type) && in_array($type, $updateTypeArr)){
if($type == 'size'){
return $this->updateSize($request);
}
elseif($type == 'qty'){
return $this->updateQty($request);
}
}*/

return response()->json($response);
}

private function updateSizeQty($request){

//prd($request->toArray());

	$response = [];
	$response['success'] = false;

	$cartId = (isset($request->cartId))?$request->cartId:0;
	$productId = (isset($request->productId))?$request->productId:0;
	$sizeId = (isset($request->sizeId))?$request->sizeId:0;
	$oldSizeId = (isset($request->oldSizeId))?$request->oldSizeId:0;
	$qty = (isset($request->qty))?$request->qty:0;
	$oldQty = (isset($request->oldQty))?$request->oldQty:0;

	if(is_numeric($cartId) && $cartId > 0 && is_numeric($sizeId) && $sizeId > 0){

		$cartData = [];

		$size = Size::find($sizeId);

		if(isset($size->id) && $size->id == $sizeId){

//prd($size->toArray());

			$cartData['size_id'] = $sizeId;
			$cartData['size_name'] = $size->name;

			if(is_numeric($qty) && $qty > 0){
				$cartData['qty'] = $qty;
			}

			$isUpdated = Cart::update($cartId, $cartData);

			if($isUpdated){
				$response['success'] = true;
			}
		}
	}

	return response()->json($response);

}

private function updateSize($request){

//prd($request->toArray());

	$response = [];
	$response['success'] = false;

	$cartId = (isset($request->cartId))?$request->cartId:0;
	$productId = (isset($request->productId))?$request->productId:0;
	$sizeId = (isset($request->sizeId))?$request->sizeId:0;
	$oldSizeId = (isset($request->oldSizeId))?$request->oldSizeId:0;

	if(is_numeric($cartId) && $cartId > 0 && is_numeric($sizeId) && $sizeId > 0){

		$cartData = [];

		$size = Size::find($sizeId);

		$cartData['size_id'] = $sizeId;
		$cartData['size_name'] = $size->name;

		$isUpdated = Cart::update($cartId, $cartData);

		if($isUpdated){
			$response['success'] = true;
		}
	}

	return response()->json($response);

}

private function updateQty($request){
//prd($request->toArray());

	$response = [];
	$response['success'] = false;

	$cartId = (isset($request->cartId))?$request->cartId:0;
	$productId = (isset($request->productId))?$request->productId:0;
	$sizeId = (isset($request->sizeId))?$request->sizeId:0;
	$qty = (isset($request->qty))?$request->qty:0;
	$oldQty = (isset($request->oldQty))?$request->oldQty:0;

	if(is_numeric($cartId) && $cartId > 0 && is_numeric($qty) && $qty > 0){

		$cartUpdateData = [];
		$cartUpdateData['qty'] = $qty;

		$isUpdated = Cart::update($cartId, $cartUpdateData);

		if($isUpdated){
			$response['success'] = true;
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
		$isRemoved = Cart::remove($cartId);

		if($isRemoved){
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

	$method = $request->method();

	if($method == 'POST'){

		$shppingAddrId = (isset($request->shppingAddr))?$request->shppingAddr:0;

		$request->session()->put('shppingAddrId',$shppingAddrId);

		if(is_numeric($shppingAddrId) && $shppingAddrId > 0){
			$shppingAddress = UserAddress::find($shppingAddrId);

			if(!empty($shppingAddress) && count($shppingAddress) > 0){
			//prd($shppingAddress->toArray());

				$cartContent = Cart::getContent();

				$cartCount = $cartContent->count();
				$cartContent = $cartContent->sortBy('attributes.sort_order')->sortBy('attributes.id');

				$productModel = new Product;

				$data['cartContent'] = $cartContent; 
				$data['cartCount'] = $cartCount;
				$data['productModel'] = $productModel;

				$data['meta_title'] = $meta_title;
				$data['shppingAddress'] = $shppingAddress;
				$data['shppingAddrId'] = $shppingAddrId;

				return view('cart.checkout', $data);
			}
		}



	}

	return redirect(url('cart'));

}

//apply_coupon

public function applyCoupon(Request $request){

//prd($request->toArray());

	$response['success'] = false;

	$response['message']='Invalid Coupon.';


	$method = $request->method();

	if($method == 'POST'){

		if(!empty($request->coupon)){

			$couponCode = trim(strtolower($request->coupon));

			$couponData = Coupon::where(['code'=>$couponCode, 'status'=>1])
			->whereDate('start_date', '<=', date("Y-m-d"))
			->whereDate('expiry_date', '>=', date("Y-m-d"))
			->first();

			$response['message'] = 'Invalid coupon code.';


			if(!empty($couponData) && count($couponData) > 0){
//prd($couponData->toArray());

				$userId = auth()->user()->id;

				$couponId = $couponData->id;

//$countOrder = Order::where(['user_id'=>$userId, 'coupon_id'=>$couponId])->count();
				$countOrder = Order::where(['coupon_id'=>$couponId])->count();

				$cartContent = Cart::getContent();

				$cartTotal = Cart::getTotal($cartContent);

				$type = $couponData->type;
				$discount = $couponData->discount;

				$min_amount = $couponData->min_amount;
				$use_limit = $couponData->use_limit;
				$max_discount_amt = $couponData->max_discount_amt;

				$coupon_apply_status = true;


				/*if($countOrder >= $use_limit){
				$response['success'] = false;
				$response['message'] = 'Sorry, you have exceeded use limit of this Coupon.';
				$coupon_apply_status = false;
				}*/



				if($min_amount > 0 && $min_amount > $cartTotal ){
					$response['success'] = false;
					$response['message'] = 'Order total must be greater than or equal to Rs '.number_format($min_amount). ' to use this coupn code.';
					$coupon_apply_status = false;
				}

				if($coupon_apply_status){

					$coupon_sess_data = [];

					$notArr = ['description', 'created_at', 'updated_at'];

					foreach($couponData->toArray() as $cKey=>$cVal){
						if(!in_array($cKey, $notArr)){
							$coupon_sess_data[$cKey] = $cVal;
						}
					}

					//prd($coupon_sess_data);

					session(['couponData' => $coupon_sess_data]);
					$response['success'] = true;
					$response['message'] = 'Coupon applied successfully.';
				}


			}

		}

	}

	return response()->json($response);

}


// remove_coupon
public function removeCoupon(Request $request){

	$response['success'] = false;

	$method = $request->method();

	if($method == 'POST'){

		if (session()->has('couponData')) {
			session()->forget('couponData');
		}

		$response['success'] = true;
	}

	return response()->json($response);
}


// ajax_get_size_qty
public function ajaxGetSizeQty(Request $request){

	//prd($request->toArray());

	$response['success'] = false;

	$method = $request->method();

	if($method == 'POST'){

		$productId = (isset($request->productId))?$request->productId:'';
		$oldSizeId = (isset($request->oldSizeId))?$request->oldSizeId:'';
		$cartId = (isset($request->cartId))?$request->cartId:'';
		$oldQty = (isset($request->oldQty))?$request->oldQty:'';

		$productsSizesJson = (isset($request->productsSizesJson))?$request->productsSizesJson:'';

		if(!empty($productsSizesJson)){
			$productsSizesArr = json_decode($productsSizesJson);

			//prd($productsSizesArr);

			if(is_numeric($productId) && $productId > 0){
				$productSizesArr = (isset($productsSizesArr->$productId))?$productsSizesArr->$productId:[];

				$viewData = [];
				$viewData['productId'] = $productId;
				$viewData['oldSizeId'] = $oldSizeId;
				$viewData['cartId'] = $cartId;
				$viewData['oldQty'] = $oldQty;
				$viewData['productSizesArr'] = $productSizesArr;

				$view_html = view('cart._sizes_qty', $viewData)->render();

				$response['sizeListing'] = $view_html;

				$response['success'] = true;
			}
		}
		
	}

	return response()->json($response);
}

public function payment_method(Request $request){

	//prd($request->all());
	$data = [];
	$meta_title = 'Payment Method';

	$shppingAddrId = $request->session()->get('shppingAddrId');
	$method = $request->method();
	$cod_available = '';

	if(is_numeric($shppingAddrId) && $shppingAddrId > 0){
		$userAddress = UserAddress::find($shppingAddrId);
		$pincode = $userAddress->pincode;

		if(!empty($pincode)){
			$pincodes = Pincode::where(['status'=>1,'pin'=>$pincode])->first();
			$cod_available = $pincodes->cod_available;
		}
	}
	//prd($cod_available);
	//if($method == 'POST'){
	$data['shppingAddrId'] = $shppingAddrId;
	$data['cod_available'] = $cod_available;
	$data['meta_title'] = $meta_title;
	return view('cart.payment_method', $data);
	//}
	
}


/* end of controller */
}