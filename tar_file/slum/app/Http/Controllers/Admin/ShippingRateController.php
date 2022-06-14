<?php

namespace App\Http\Controllers\Admin;   

use Validator;

use App\ShippingRate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use Illuminate\Support\Facades\Input;

class ShippingRateController extends Controller {

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }


    public function index(Request $request) {
        //prd($request->id);

        $data = array();
        $limit = $this->limit;

        $shippingrates = [];

        $id = (isset($request->id))?$request->id:0;

        if($request->method() == 'POST' || $request->method() == 'post'){

            $back_url = (isset($request->back_url))?$request->back_url:0;

            $rules = [];

            $rules['shipping_zone_id'] = 'required|numeric';
            $rules['min_weight'] = 'required|numeric';
            $rules['max_weight'] = 'required|numeric';
            $rules['rate'] = 'required|numeric';
            $rules['status'] = 'required';

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($request) {
                if ($request->min_weight >= $request->max_weight) {
                    $validator->errors()->add('max_weight', 'Max Weight should be greater than Min Weight.');
                }
            });


            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
            else{
                $isSaved = $this->save($request, $id);

                if($isSaved){
                    return redirect('admin/shippingrates')->with('alert-success', 'Shipping Rate has been saved successfully.');
                }
                else{
                    return back()->with('alert-danger', 'something went wrong, please try again..');
                }
            }
        }

        if(is_numeric($id) && $id > 0){
            $shippingrates = ShippingRate::where('id', $id)->first();
        }

        $ShippingRatesModel = new ShippingRate;

        $shippingrates_list = $ShippingRatesModel->orderBy('min_weight', 'asc')->paginate($limit);
        $ShippingZones = $ShippingRatesModel->ShippingZones();

        $data['ShippingRatesModel'] = $ShippingRatesModel;

        $data['shippingrates_list'] = $shippingrates_list;
        $data['shippingrates'] = $shippingrates;
        $data['ShippingZones'] = $ShippingZones;
        $data['id'] = $id;

        return view('admin.shippingrates.index', $data);
    }



    public function save($data, $id){

        //prd($request->toArray());

        $msg_data = [];

        $queryData = [];

        $queryData['shipping_zone_id'] = $data['shipping_zone_id'];
        $queryData['min_weight'] = $data['min_weight'];
        $queryData['max_weight'] = $data['max_weight'];
        $queryData['rate'] = $data['rate'];
        $queryData['status'] = $data['status'];

        $ShippingRate = new ShippingRate;

        if(is_numeric($id) && $id > 0){
            $isExist = ShippingRate::find($id);

            if(isset($isExist->id) && $isExist->id == $id){
                $ShippingRate = $isExist;
            }
        }

        $ShippingRate->shipping_zone_id = $data['shipping_zone_id'];
        $ShippingRate->min_weight = $data['min_weight'];
        $ShippingRate->max_weight = $data['max_weight'];
        $ShippingRate->rate = $data['rate'];
        $ShippingRate->status = $data['status'];

        //prd($ShippingRate->toArray());

        $is_saved = $ShippingRate->save();

        return $is_saved;
    }


    public function delete(Request $request) {

        $method = $request->method();
        //prd($method);
        $id = $request->id;

        if($method == 'POST'){
            $is_deleted = ShippingRate::where('id', $id)->delete();
        }

        if($is_deleted){
            return redirect(route('admin.shippingrates.index'))->with('alert-success', "Shipping Rate deleted successfully.");
        }
        else{
            return redirect(route('admin.shippingrates.index'))->with('alert-danger', "Shipping Rate can n't delete. please try again or contact the administrator.");
        }

    }

    /* End of controller */
}