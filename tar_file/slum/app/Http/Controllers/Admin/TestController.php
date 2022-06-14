<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Order;
use App\Coupon;
use App\Product;
use App\Category;
use App\Attribute;
use App\Inventory;
use Carbon\Carbon;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

use App\Helpers\CustomHelper;

use App\Libraries\CurrencyConverter;

use DB;

class TestController extends Controller {

    
    public function index(Request $request){

        //prd($request->toArray());

        $param = Input::get('param');

        if(!empty($param) && method_exists($this, $param) ){
            $this->$param($request);
        }
        else{
            abort(404);
        }
    }

    public function checkProductDiscount(){
        $products = Product::where('discount', 0)->orWhere('discount', 0.00)->get();
        if(!empty($products) && count($products)){
            //prd($products->toArray());

            foreach($products as $product){
                /*$price = $product->price;
                $salePrice = $product->sale_price;

                $discount = CustomHelper::calculateProductDiscount($price, $salePrice);

                if(is_numeric($discount) && $discount > 0){
                    $product->discount = $discount;
                    $product->save();
                }*/
            }
        }
    }

    public function test_currency_convert(){
        //prd('test_currency_convert');
     $CurrencyConverter = new CurrencyConverter();

     $converted = $CurrencyConverter->convert(69.28, 'INR', 'USD');

     prd($converted);

     //$CurrencyConverter->CurrencyConverter();
    }

    public function php(){
        phpinfo();
        die;

        
    }


/*end of cotroller*/
}
