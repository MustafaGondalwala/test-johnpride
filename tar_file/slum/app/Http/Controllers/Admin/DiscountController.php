<?php

namespace App\Http\Controllers\Admin;

use App\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\CustomHelper;

use Validator;
use Storage;
use Image;

class DiscountController extends Controller{
    

    private $limit;
    private $typeArr;

    public function __construct(){
        $this->limit = 20;
        $this->typeArr = ['fabric', 'printing', 'designer_commission'];       
    }

    public function checkType(){
        $type_arr = $this->typeArr;
        $type = (isset(request()->type))?request()->type:'';
        if(!in_array($type, $type_arr)){
            return false;
        }
        return true;
    }

    public function index(Request $request){
        if(!$this->checkType()){ return back(); }

        $data = [];

        $type = (isset($request->type))?$request->type:'';

        $limit = $this->limit;

        $discounts = Discount::where(['type'=>$type])->paginate($limit);

        $parentCategory = '';

        
        $data['type'] = $type;
        $data['discounts'] = $discounts;

        return view('admin.discounts.index', $data);

    }

    public function add(Request $request){
        if(!$this->checkType()){ return back(); }

       // prd($request->toArray());
        $data = [];

        $type = (isset($request->type))?$request->type:'';

        $discount_id = (isset($request->discount_id))?$request->discount_id:'';

        $discount = '';

        if(is_numeric($discount_id) && $discount_id > 0){
            $discount = Discount::find($discount_id);
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/discounts?type='.$type;
            }

            $discount_id = (isset($request->discount_id))?$request->discount_id:0;

            $rules = [];
            $attributes = [];

            $rules['min_len'] = 'required|numeric';
            $rules['max_len'] = 'required|numeric';
            $rules['value'] = 'required|numeric';

            $attributes = [];
            $attributes['min_len'] = 'Min Length';
            $attributes['max_len'] = 'Max Length';
            $attributes['value'] = 'Discount value';

            $validator = Validator::make($request->all(), $rules);

            $validator->setAttributeNames($attributes);

            $validator->after(function ($validator) use ($request) {
                if ($request->min_len >= $request->max_len) {
                    $validator->errors()->add('max_len', 'Max length should be greater than Min length.');
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $req_data = [];

            $req_data = $request->except(['_token', 'discount_id', 'back_url']);

            //prd($req_data);

            if(isset($discount->id) && $discount->id == $discount_id){
                $isSaved = Discount::where('id', $discount->id)->update($req_data);
            }
            else{
                $isSaved = Discount::create($req_data);

                $discount_id = $isSaved->id;
            }


            if ($isSaved) {

                return redirect(url($back_url))->with('alert-success', 'The Discount has been saved successfully.');
            } else {
                return back()->with('alert-danger', 'The Discount cannot be added, please try again or contact the administrator.');
            }
        }

        $page_heading = 'Add Discount Slab - ';

        if(isset($discount->id)){
             $page_heading = 'Update Discount Slab - ';
        }

        $data['page_heading'] = $page_heading.ucwords($type);
        $data['type'] = $type;
        $data['discount'] = $discount;
        $data['discount_id'] = $discount_id;

        return view('admin.discounts.form', $data);

    }

    public function delete($discount_id){

        if(is_numeric($discount_id) && $discount_id > 0){

            $discount = Discount::find($discount_id);

            if(isset($discount->id) && $discount->id == $discount_id){
                $is_deleted = $discount->delete();

                if($is_deleted){
                    return back()->with('alert-success', 'Discount has been deleted successfully.');
                }
            }
        }

        return back()->with('alert-danger', 'something went wrong, please try again...');
    }



    /* end of controller */
}