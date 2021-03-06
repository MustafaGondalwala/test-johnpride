<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\CustomHelper;

use App\UserCartItem;
use App\User;

use Validator;
use Storage;




use Image;
use DB;

use App\Exports\CartExport;
use Maatwebsite\Excel\Facades\Excel;

class CartController extends Controller{


    private $limit;

    public function __construct(){
        $this->limit = 100;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $export_xls = (isset($request->export_xls))?$request->export_xls:'';


        $customer = (isset($request->customer))?$request->customer:'';
        $product = (isset($request->product))?$request->product:'';
        $customer = (isset($request->customer))?$request->customer:'';
        $phone= (isset($request->phone))?$request->phone:'';
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

          if(!empty($phone)){
            $cartQuery->whereHas('user', function($query) use ($phone){
                $query->where('phone',$phone);
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
        


          if(!empty($export_xls) && ($export_xls == 1 || $export_xls == '1') )
          {
            $cart_items = UserCartItem::where('user_id', '>', 0)->orderBy('created_at')->get();
            return $this->exportXls($cart_items);

          }


        $data['cart'] = $cart;
        
        $data['limit'] = $limit;

        return view('admin.cart.index', $data);

    }



     private function exportXls($cart_items){

        $exportArr = [];



        if(!empty($cart_items) && $cart_items->count() > 0){

            foreach($cart_items as $cart_item){


                $user_id = $cart_item->user_id;

                $user_data = User::select('name','email','phone')->where('id',$user_id)->first();


              //  $dob = CustomHelper::DateFormat($customer->dob, $toFormat='d/m/Y', $fromFormat='Y-m-d');

                $cartArr = [];

              //  $cartArr['id'] = $cart_item->id;

                $cartArr['name'] = isset($user_data->name) ? $user_data->name : '';
                $cartArr['email'] = isset($user_data->email) ? $user_data->email : '';
                $cartArr['phone'] = isset($user_data->phone) ? $user_data->phone : '';
                $cartArr['Product Name'] = isset($cart_item->product_name) ? $cart_item->product_name : '';
                $cartArr['Size Name'] = isset($cart_item->size_name) ? $cart_item->size_name : '';
                $cartArr['qty'] = isset($cart_item->qty) ? $cart_item->qty : '';
                $cartArr['Price'] = isset($cart_item->price) ? $cart_item->price : '';
                $cartArr['Cart Price'] = isset($cart_item->cart_price) ? $cart_item->cart_price : '';
                $cartArr['Created_At'] = isset($cart_item->created_at) ? $cart_item->created_at : '';

                $exportArr[] = $cartArr;

            }

        }



        $fieldNames = array_keys($exportArr[0]);
        $sheetHeading = 'Cart';
        $fileName = 'cart_'.date('Y-m-d-H-i-s').'.xlsx';

     //   return Excel::download(new CartExport($exportArr, $fieldNames), $fileName);
         return Excel::download(new CartExport($exportArr, $fieldNames, $sheetHeading), $fileName);

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