<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use App\State;

use Validator;
use Session;
use DB;


class CommonController extends Controller {
    

    public function ajax_load_cities(Request $request){

        //prd(Input::all());

        $result = array();
        $cities = array();

        $options = '<option value="">--Select--</option>';

        $state_id = (isset($request->state_id))?$request->state_id:0;
        $city_id = (isset($request->city_id))?$request->city_id:0;

        $selectArr = ['id','name','state','state_id','gst_code','status','created_at','updated_at'];

        if(is_numeric($state_id) && $state_id > 0){
            $cities = DB::table('cities')->select($selectArr)->where('state_id', $state_id)->orderBy('name')->get();
        }

        if(!empty($cities) && count($cities) > 0){
            foreach($cities as $city){
                $selected = '';
                if($city->id == $city_id){
                    $selected = 'selected';
                }
                $options .= '<option value="'.$city->id.'" '.$selected.'>'.$city->name.'</option>';
            }
        }

        $result['success'] = true;
        $result['options'] = $options;

        return response()->json($result);

    }


      public function ajax_load_product(){

        //prd(Input::all()); die;

        $result = array();
        $related_product = array();

        $options = '';

        $category_id = Input::get('category_id');
        $related_product_id = Input::get('related_product_id');

        if(is_numeric($category_id) && $category_id > 0){
            $related_product = DB::table('products')->where(['category_id'=>$category_id,'status'=>1,'type'=>'fabric'])->orderBy('name')->get();
        }

        if(!empty($related_product) && count($related_product) > 0){
            foreach($related_product as $rel_product){
               
                $options .= '<option value="'.$rel_product->id.'">'.$rel_product->name.'</option>';
            }
        }

        $result['success'] = true;
        $result['options'] = $options;

        return response()->json($result);

    }

    public function ajax_regenerate_captcha(Request $request){

      $response = [];

      $response['success'] = true;

      $captcha_src = captcha_src('custom');

      $response['captcha_src'] = $captcha_src;

      return response()->json($response);

    }

    // Get Address
    public function get_address()
    {
      
       $data['status']=0;
       $data['res']='';
       $address_id= Input::get('address_id');
       $user_address = Address::find($address_id);
       $user_address=$user_address->toArray();
       if(!empty($user_address))
       {   
           $data['status']=1;
           $data['res']= $user_address;
            
       }
       echo json_encode($data);

    }

    public function getChildCategories(Request $request){

      $category_id = ($request->has('category_id'))?$request->category_id:0;
        //pr($request->all());
        if(is_numeric($category_id) && $category_id > 0){
          $ChildCategories = getChildCategories($category_id);

          if(!empty($ChildCategories) && count($ChildCategories) > 0){
            return response()->json(['result'=>true, 'data'=>$ChildCategories]);
          }
          else{
            return response()->json(['result'=>false, 'error'=>'No data found']);
          }
        }
        else{
          return response()->json(['result'=>false, 'error'=>'Invalid Category ID!']);
        }
        
    }


    function ajax_getMainMenu(Request $request){

      $result['success'] = false;

      if($request->method() == 'POST'){
        $view_html = view('common.left_menu')->render();

        $result['success'] = true;
        $result['data'] = $view_html;
      }
      
      return $result;
    }


    function ajax_changeLanguage(Request $request){

      //prd($request->toArray());

      $result['success'] = false;

      if($request->method() == 'POST'){
        $locale_lang = (isset($request->locale_lang))?$request->locale_lang:'';

        if(!empty($locale_lang)){
          session(['locale_lang'=>$locale_lang]);
          $result['success'] = true;
        }

      }

     return response()->json($result);
     
    }


    function ajax_set_currency(Request $request){

      //prd($request->toArray());

      $response['success'] = false;

      if($request->method() == 'POST'){
        $currency = (isset($request->currency))?$request->currency:'';

        if(!empty($currency)){
          session(['to_currency'=>$currency]);
          $response['success'] = true;
        }

      }

     return response()->json($response);
     
    }



/* End of Controller */
}
