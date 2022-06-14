<?php

namespace App\Http\Controllers\Admin;   

use Validator;

use App\ShippingZone;

use App\City;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use DB;

use Illuminate\Support\Facades\Input;

class ShippingZoneController extends Controller {
   
    private $limit;

    public function __construct(){
        $this->limit = 20;
    }


    public function index($id=0) {

        $data = array();
        $limit = $this->limit;
        $ShippingZone = array();

        $ShippingZoneModel = new ShippingZone;


        if(is_numeric($id) && $id > 0){
            $ShippingZone = $ShippingZoneModel->where('id', $id)->first();
        }
        $ShippingZone_list = $ShippingZoneModel->paginate($limit);

        $data['ShippingZoneModel'] = $ShippingZoneModel;
        $data['ShippingZone_list'] = $ShippingZone_list;
        return view('admin.shippingzones.index', $data);
    }

    public function add(Request $request){
        $data = array();

        $ShippingZoneModel = new ShippingZone;
        
        $data['ShippingZoneModel'] = $ShippingZoneModel;

        if($request->method() == 'POST' || $request->method() == 'post'){
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
            else{
                $is_saved = $this->save($request);
                if($is_saved['status'] > 0){
                    return redirect(route('admin.shippingzones.index'))->with('alert-success', $is_saved['msg']);
                }
                else{
                    return back()->with('alert-danger', 'something went wrong, please try again...');
                }
            }
        }

        $ShippingZoneCities_all = DB::table('shipping_zones_city')->get();

        $resrvCityId = [];

        if(!empty($ShippingZoneCities_all) && count($ShippingZoneCities_all) > 0){
            $resrvCityId = $ShippingZoneCities_all->pluck('city_id')->all();
        }

        $cities = City::orderBy('name')->whereNotIn('id', $resrvCityId)->get();

        $data['ShippingZoneCities_all'] = $ShippingZoneCities_all;
        
        $data['cities'] = $cities;
        $data['title'] = 'Add Shipping Zone';
        $data['heading'] = 'Add Shipping Zone';

        return view('admin.shippingzones.form', $data);
    }

    public function edit(Request $request){
        $data = []
        ;
        $id = (isset($request->id))?$request->id:0;

        $shippingzones = [];
        $shippingZoneCities = [];
        $shippingzones_name = '';

        $params['not_shipping_zones_id'] = $id;

        $zoneCityIds = [];

        if($request->method() == 'POST' || $request->method() == 'post'){
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect('admin/shippingzones')
                ->withErrors($validator);
            }
            else{
                $is_saved = $this->save($request,$id);
                if($is_saved['status'] > 0){
                    return redirect(route('admin.shippingzones.index'))->with('alert-success', $is_saved['msg']);
                }
                else{
                    return redirect(route('admin.shippingzones.index'))->with('alert-danger', $is_saved['msg']);
                }
            }
        }


        if(is_numeric($id) && $id > 0){
            $shippingzones = ShippingZone::where('id', $id)->first();

            $shippingzones_name = (isset($shippingzones->name))?$shippingzones->name:'';

            if(isset($shippingzones->id) && $shippingzones->id == $id){

                $shippingZoneCities = $shippingzones->shippingZoneCities;

                $zoneCityIds = $shippingZoneCities->pluck('id')->all();
            }
        }

        $ShippingZonesCities_all = DB::table('shipping_zones_city')->where('shipping_zones_id', '!=', $id)->get();

        $resrvCityId = [];

        if(!empty($ShippingZonesCities_all) && count($ShippingZonesCities_all) > 0){
            $resrvCityId = $ShippingZonesCities_all->pluck('city_id')->all();
        }

        //prd($resrvCityId);

        $cities = [];

        if(count($zoneCityIds) > 0){
            
            $selArr = ['id', 'name', 'state_id'];

            //$select_cities1 = Cities::orderBy('name');
            //$select_cities1 = DB::table('cities');//->orderBy('name');
            $select_cities1 = City::whereIn('id', $zoneCityIds);
            //$select_cities1->whereIn('id', $zoneCityIds);
            $select_cities1->select($selArr);

            //$cities = $select_cities1->get();

            $resrvCityIds_merge = array_merge($resrvCityId, $zoneCityIds);
            //prd($resrvCityIds_merge);

            //$select_cities2 = Cities::orderBy('name');
            $select_cities2 = City::whereNotIn('id', $resrvCityIds_merge);
            //$select_cities2 = DB::table('cities');//->orderBy('name');
            //$select_cities2->whereNotIn('id', $resrvCityIds_merge);
            $select_cities2->select($selArr);

            //$cities = $select_cities2->get();


            $cities = $select_cities1->orderBy('name')->union($select_cities2)->get();

            //prd($cities);

        }
        else{
            $select_cities = City::orderBy('name');

            $cities = $select_cities->whereNotIn('id', $resrvCityId)->get();
        }

        //pr($ShippingZonesCities_all);

        //prd(count($cities));

        $data['title'] = 'Update Shipping Zone';
        $data['heading'] = 'Update Shipping Zone - '.$shippingzones_name;

        $data['cities'] = $cities;
        $data['shippingzones'] = $shippingzones;
        $data['shippingZoneCities'] = $shippingZoneCities;
        $data['zoneCityIds'] = $zoneCityIds;

        return view('admin.shippingzones.form', $data);
    }

    

    public function save($request, $shippingzone_id=0){

        $errors = array();
        $data = $request->all();
        $sccMsg = "";

           //prd($request->toArray());

            $created = date('Y-m-d H:i:s');

            $queryData = $request->except(['_token', 'shippingzone_id', 'city_id']);

            $city_id_arr = (isset($request->city_id))?$request->city_id:[];

            $queryData['status'] = (isset($request->status))?$request->status:0;

            //prd($city_id_arr);
            //prd(count($city_id_arr));

            $ShippingZoneModel = new ShippingZone;

            if(is_numeric($shippingzone_id) && $shippingzone_id > 0){
                unset($queryData['created_at']);

                $savedata = ShippingZone::where('id', $shippingzone_id)->update($queryData);
                $insertedId = $shippingzone_id;
                $sccMsg = "Shipping Zone updated successfully.";
            }
            else{
                $savedata = ShippingZone::create($queryData);
                $insertedId = $savedata->id;

                $sccMsg = "Shipping Zone added successfully.";    
            }


            if($insertedId){

                DB::table('shipping_zones_city')->where('shipping_zones_id', $insertedId)->delete();

                if(!empty($city_id_arr) && count($city_id_arr) > 0){

                    $shippingZoneCity = [];
                    
                    foreach($city_id_arr as $city_id){
                        $shippingZoneCity[] = array(
                            'shipping_zones_id' => $insertedId,
                            'city_id' => $city_id,
                            'created_at' => $created,
                            'updated_at' => $created,
                            );
                    }

                    //prd($shippingZoneCity);

                    DB::table('shipping_zones_city')->insert($shippingZoneCity);
                }
                if($sccMsg!=="")
                {
                    $errors['status'] = 1;
                    $errors['msg'] = $sccMsg;
                    return $errors;
                }else
                {
                    $errors['status'] = 0;
                    $errors['msg'] = "Something went wrong, please try again or contact the administrator.";
                    return $errors;
                }
            }
       
    }


    public function delete(Request $request) {

        $method = $request->method();
        //prd($method);
        $id = $request->id;

        if($method == 'POST'){
            $is_deleted = ShippingZone::where('id', $id)->delete();

            if($is_deleted){

                DB::table('shipping_zones_city')->where('shipping_zones_id', $id)->delete();
            }
        }

        if($is_deleted)
        {
            return redirect(route('admin.shippingzones.index'))->with('alert-success', "Shipping Zone deleted successfully.");
        }else
        {
            return redirect(route('admin.shippingzones.index'))->with('alert-danger', "Shipping Zone can n't delete. please try again or contact the administrator.");
        }

    }

    /* End of controller */
}