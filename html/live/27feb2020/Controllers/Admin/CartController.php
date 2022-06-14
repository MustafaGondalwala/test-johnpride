<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\CustomHelper;

use App\UserCartItem;

use Validator;
use Storage;


use Image;
use DB;

class CartController extends Controller{


    private $limit;

    public function __construct(){
        $this->limit = 100;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $customer = (isset($request->customer))?$request->customer:'';
        $product = (isset($request->product))?$request->product:'';

        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');


        $cartQuery = UserCartItem::select('*', DB::raw("COUNT(user_id) as count_items"))->where('user_id','>',0)->groupBy('user_id')->orderBy('updated_at', 'desc');

        

        if(!empty($customer)){
            $cartQuery->whereHas('user', function($query) use ($customer){
                $query->where('name', 'like', '%'.$customer.'%');
            });
        }

        if(!empty($product)){
            $cartQuery->whereHas('product', function($query) use ($product){
                $query->where('name', 'like', '%'.$product.'%');
            });
        }

        if(!empty($from_date)){
            $cartQuery->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $cartQuery->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }

        $cartQuery->paginate($limit);
        
        $cart = $cartQuery->paginate($limit);
        


        $data['cart'] = $cart;
        
        $data['limit'] = $limit;

        return view('admin.cart.index', $data);

    }

    /* ajax_get_items */
    public function ajaxGetItems(Request $request){
        //prd($request->toArray());
        $response = [];

        $response['success'] = false;

        $userId = (isset($request->userId))?$request->userId:0;

        if(is_numeric($userId) && $userId > 0){
            $userCart = UserCartItem::where('user_id', $userId)->orderBy('updated_at', 'desc')->get();

            if(!empty($userCart) && count($userCart) > 0){
                //prd($userCart->toArray());

                $viewData = [];
                $viewData['userCart'] = $userCart;

                $rowsHtml = view('admin.cart._details', $viewData)->render();

                $response['rowsHtml'] = $rowsHtml;

                $response['success'] = true;
            }
        }

        return response()->json($response);
    }


    /* end of controller */
}