<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Coupon;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Helpers\CustomHelper;

use Validator;


class CouponController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }


    public function index(Request $request){
        $limit = $this->limit;

        //name=june&code=&start=&expiry=&status=

        $name = (isset($request->name))?$request->name:'';
        $code = (isset($request->email))?$request->code:'';

        $status = (isset($request->status))?$request->status:'';

        $start = (isset($request->start))?$request->start:'';
        $expiry = (isset($request->expiry))?$request->expiry:'';

        $start_date = CustomHelper::DateFormat($start, 'Y-m-d', 'd M Y');
        $expiry_date = CustomHelper::DateFormat($expiry, 'Y-m-d', 'd M Y');

        $coupon_query = Coupon::orderBy('created_at', 'desc');

        if(!empty($name)){
            $coupon_query->where('name','like', '%'.$name.'%');
        }
        if(!empty($code)){
            $coupon_query->where('code','like', '%'.$code.'%');
        }
        if( strlen($status) > 0 ){
            $coupon_query->where('status', $status);
        }
        if(!empty($start_date)){
            $coupon_query->whereRaw('DATE(start_date) >= "'.$start_date.'"');
        }
        if(!empty($expiry_date)){
            $coupon_query->whereRaw('DATE(expiry_date) <= "'.$expiry_date.'"');
        }

        $coupons = $coupon_query->paginate($limit);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Admin - Create Coupon
     * URL: /admin/coupons/create
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request){
        $data = array();

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $is_saved = $this->save($request);

            if($is_saved['status'] > 0){
                return redirect(route('admin.coupons.index'))->with('alert-success', $is_saved['msg']);
            }
            else{
                return back()->with('alert-danger', 'The coupon cannot be added, please try again or contact the administrator.');
            }

        }
        $data['coupon_id'] = 0;
        $data['title'] = "Add Coupon";
        $data['heading'] = "Add Coupon";
        return view('admin.coupons.form',$data);
    }


    public function edit(Request $request){

        $data = array();
        $coupon_id = ($request->id) ? $request->id : 0;

        $CouponModel = new Coupon;
        $coupons = array();
        if(is_numeric($coupon_id) && $coupon_id > 0){
            $coupons = $CouponModel->where('id', $coupon_id)->first();
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $is_saved = $this->save($request);

            if($is_saved['status'] > 0){
                return redirect(route('admin.coupons.index'))->with('alert-success', $is_saved['msg']);
            }
            else{
                return back()->with('alert-danger', 'The coupon cannot be added, please try again or contact the administrator.');
            }

        }

        $data['coupon_id'] = 0;
        $data['title'] = "Update Coupon";
        $data['coupons'] = $coupons;
        $data['heading'] = "Update Coupon (".$coupons['name'].")";
        return view('admin.coupons.form',$data);
    }
    

    function save($req){

        $data = [];

        $coupon_id = (int)$req->coupon_id;
        $type = $req->type;


        $rules = [];
        $rules['name'] = 'required|min:3';

        if(is_numeric($coupon_id) && $coupon_id > 0){
            $rules['code'] = ['required', Rule::unique('coupons')->ignore($coupon_id)];
        }
        else{
            $rules['code'] = 'required|min:3|unique:coupons';
        }

        $rules['discount'] = 'required|numeric';
        $rules['min_amount'] = 'required|numeric';

        if($type == 'value'){
            $rules['max_discount_amt'] = 'required|numeric|max:discount';
        }
        else{
            $rules['max_discount_amt'] = 'required|numeric';
        }

        $rules['use_limit'] = 'nullable|numeric';
        $rules['start_date'] = 'required';
        $rules['expiry_date'] = 'required';

        $this->validate($req, $rules);

        $msg_data = array();
        $data = $req->except(['_token', 'coupon_id', 'back_url','start_date','expiry']);

        $start_from_date = CustomHelper::DateFormat($req->start_date, 'Y-m-d', 'd/m/Y');
        $expire_date_formated = CustomHelper::DateFormat($req->expiry_date, 'Y-m-d', 'd/m/Y');

        $data['start_date'] = $start_from_date;
        $data['expiry_date'] = $expire_date_formated;

        //prd($data);

        if(is_numeric($coupon_id) && $coupon_id > 0){
            $savedata = Coupon::where('id', $coupon_id)->update($data);
            $savedata = 1;
            $insertedId = $coupon_id;
            $sccMsg = "Coupon updated successfully.";
        }
        else{
            $savedata = Coupon::create($data);
            $insertedId = $savedata->id;
            $sccMsg = "Coupon added successfully.";
        }

        if($savedata){
            $msg_data['status'] = 1;
            $msg_data['msg'] = $sccMsg;
        }
        else{
            $msg_data['status'] = 0;
            $msg_data['msg'] = "Something went wrong, please try again or contact the administrator.";
        }
        
        return $msg_data;
    }

    
    public function delete(Request $request)
    {
        $method = $request->method();
        //prd($method);
        $id = $request->id;

        if($method == 'POST'){
            $is_deleted = Coupon::where('id', $id)->delete();
        }

        if($is_deleted)
        {
            return redirect(route('admin.coupons.index'))->with('alert-success', "Coupon deleted successfully.");
        }else
        {
            return redirect(route('admin.coupons.index'))->with('alert-danger', "Coupon can n't delete. please try again or contact the administrator.");
        }
    }
}