<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Validator;


class CouponController extends Controller
{
    private $limit;

    public function __construct(){
        $this->limit = 20;
    }


    public function index()
    {
        $limit = $this->limit;
        $coupon_query = Coupon::orderBy('created_at', 'desc');
        $coupons = $coupon_query->paginate($limit);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Admin - Create Coupon
     * URL: /admin/coupons/create
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        $data = array();

        if($request->method() == 'POST' || $request->method() == 'post')
        {
            $rules = [];
            $rules['name'] = 'required|min:3';
            $rules['code'] = 'required|min:3|unique:coupons';
            $rules['discount'] = 'required';
            $rules['start_date'] = 'required';
            $rules['expiry_date'] = 'required';

            $this->validate($request, $rules);

            $is_saved = $this->save($request);
            if($is_saved['status'] > 0)
            {
                return redirect(route('admin.coupons.index'))->with('alert-success', $is_saved['msg']);
            }else
            {
                return back()->with('alert-danger', 'The coupon cannot be added, please try again or contact the administrator.');
            }

        }
        $data['coupon_id'] = 0;
        $data['title'] = "Add Coupon";
        $data['heading'] = "Add Coupon";
        return view('admin.coupons.form',$data);
    }


    public function edit(Request $request)
    {
        $data = array();
        $coupon_id = ($request->id) ? $request->id : 0;

        $CouponModel = new Coupon;
        $coupons = array();
        if(is_numeric($coupon_id) && $coupon_id > 0){
            $coupons = $CouponModel->where('id', $coupon_id)->first();
        }

        if($request->method() == 'POST' || $request->method() == 'post')
        {
            $rules = [];
            $rules['name'] = 'required|min:3';
            $rules['code'] = ['required', Rule::unique('coupons')->ignore($coupon_id)];

            $rules['discount'] = 'required';
            $rules['start_date'] = 'required';
            $rules['expiry_date'] = 'required';
            $this->validate($request, $rules);

            $is_saved = $this->save($request);
            if($is_saved['status'] > 0)
            {
                return redirect(route('admin.coupons.index'))->with('alert-success', $is_saved['msg']);
            }else
            {
                return back()->with('alert-danger', 'The coupon cannot be added, please try again or contact the administrator.');
            }

        }

        $data['coupon_id'] = 0;
        $data['title'] = "Add Coupon";
        $data['coupons'] = $coupons;
        $data['heading'] = "Update Coupon (".$coupons['name'].")";
        return view('admin.coupons.form',$data);
    }
    

    function save($req)
    {
        $data = [];
        $msg_data = array();
        $data = $req->except(['_token', 'coupon_id', 'back_url','use_limit','min_amount','max_discount','start_date','expiry']);
        $data['use_limit'] = ($req->use_limit) ? $req->use_limit : 0.00;
        $data['max_discount'] = ($req->max_discount) ? $req->max_discount : 0.00;
        $data['min_amount'] = ($req->min_amount) ? $req->min_amount : 0.00;

        $start_from_date = explode('/', $req->start_date);
        $start_from_date_formated = $start_from_date[2] . "-" . $start_from_date[1] . "-" . $start_from_date[0];
        $data['start_date'] = $start_from_date_formated;


        $expire_date = explode('/', $req->expiry_date);
        $expire_date_formated = $expire_date[2] . "-" . $expire_date[1] . "-" . $expire_date[0];
        $data['expiry_date'] = $expire_date_formated;

        //prd($data);

        $coupon_id = (int)$req->coupon_id;
        if(is_numeric($coupon_id) && $coupon_id > 0)
        {
            $savedata = Coupon::where('id', $coupon_id)->update($data);
            $savedata = 1;
            $insertedId = $coupon_id;
            $sccMsg = "Coupon updated successfully.";
        }else
        {
            $savedata = Coupon::create($data);
            $insertedId = $savedata->id;
            $sccMsg = "Coupon added successfully.";
        }
        if($savedata)
        {
            $msg_data['status'] = 1;
            $msg_data['msg'] = $sccMsg;
        }else
        {
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