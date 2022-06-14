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

use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;

use LaravelMsg91;

class TestController extends Controller {

	
	public function index(Request $request){

		//prd($request->toArray());

		$param = Input::get('param');

		if(!empty($param) && method_exists($this, $param) ){
			return $this->$param($request);
		}
		else{
			abort(404);
		}
	}

	private function checkProductDiscount(){
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

	private function test_currency_convert(){
		//prd('test_currency_convert');
		$CurrencyConverter = new CurrencyConverter();

		$converted = $CurrencyConverter->convert(69.28, 'INR', 'USD');

		prd($converted);

	 //$CurrencyConverter->CurrencyConverter();
	}

	private function exportProducts($request){

		//prd($request->toArray());

		$category = (isset($request->category))?$request->category:'';

		$products = Product::query();
		$productQuery = Product::query();

		$fileName = time().'-products.xlsx';

		if(is_numeric($category) && $category > 0){
            $productQuery->whereHas('productCategories', function ($query) use ($category) {
                $query->where('category_id', $category);
            });
        }

		//prd($products->toArray());
		return Excel::download(new ProductExport($productQuery), $fileName);

		//return (new ProductExport($request))->download($fileName);
	}

	private function testMsg91(){
		//echo 'testMsg91'; die;

		$result = [];

		$result['message'] = '396871715a78313437363439';
		$result['type'] = 'success';

		$result = (object)$result;

		//$result = LaravelMsg91::message(880205260, 'This is a first test message');

		prd($result);
	}

	private function php(){
		phpinfo();
		die;		
	}

	private function getServerIp(){
		echo CustomHelper::isSmsGateway();
	}


	/*end of cotroller*/
}
