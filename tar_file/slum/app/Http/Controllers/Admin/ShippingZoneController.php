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

        if($request->method() == 'POST' || $request->method() == 'post')
        {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
            else
            {
                $is_saved = $this->save($request);
                if($is_saved['status'] > 0)
                {
                    return redirect(route('admin.shippingzones.index'))->with('alert-success', $is_saved['msg']);
                }else
                {
                    return back()->with('alert-danger', 'something went wrong, please try again...');
                }
            }
        }

        $ShippingZoneCities_all = $ShippingZoneModel->ShippingZonesCities()->toArray();

        $resrvCityId = [];

        if(!empty($ShippingZoneCities_all) && count($ShippingZoneCities_all) > 0){
            foreach($ShippingZoneCities_all as $resrvCity){
                $resrvCityId[] = $resrvCity->city_id;
            }
        }

        $cities = City::orderBy('name')->whereNotIn('id', $resrvCityId)->get();

        $data['ShippingZoneCities_all'] = $ShippingZoneCities_all;
        
        $data['cities'] = $cities;
        $data['title'] = 'Add Shipping Zone';
        $data['heading'] = 'Add Shipping Zone';

        return view('admin.shippingzones.form', $data);
    }

    public function edit(Request $request){
        $data = array();
        $id = (isset($request->id))?$request->id:0;
        $shippingzones = array();
        $ShippingZonesCities = array();
        $shippingzones_name = '';
        //prd($cities);
        $thisZoneCities = [];

        $ShippingZonesModel = new ShippingZone;

        $params['not_shipping_zones_id'] = $id;

        $zoneCityIds = [];

        if($request->method() == 'POST' || $request->method() == 'post')
        {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect('admin/shippingzones')
                ->withErrors($validator);
            }
            else
            {
                $is_saved = $this->save($request,$id);
                if($is_saved['status'] > 0)
                {
                    return redirect(route('admin.shippingzones.index'))->with('alert-success', $is_saved['msg']);
                }else
                {
                    return redirect(route('admin.shippingzones.index'))->with('alert-danger', $is_saved['msg']);
                }
            }
        }


        if(is_numeric($id) && $id > 0){
            $shippingzones = $ShippingZonesModel->where('id', $id)->first();

            $shippingzones_name = (isset($shippingzones->name))?$shippingzones->name:'';

            $ShippingZonesCities = $ShippingZonesModel->ShippingZonesCities($id);

            $thisZoneCities = $shippingzones->thisZoneCities();
            //prd($thisZoneCities);

            if(!empty($thisZoneCities) && count($thisZoneCities) > 0){
                foreach($thisZoneCities as $zc){
                    $zoneCityIds[] = $zc->city_id;
                }
            }
        }

        $ShippingZonesCities_all = $ShippingZonesModel->ShippingZonesCities(0, $params)->toArray();

        $resrvCityId = [];

        if(!empty($ShippingZonesCities_all) && count($ShippingZonesCities_all) > 0){
            foreach($ShippingZonesCities_all as $resrvCity){
                $resrvCityId[] = $resrvCity->city_id;
            }
        }

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
        
        
        $data['ShippingZonesModel'] = $ShippingZonesModel;        

        $data['title'] = 'Update Shipping Zone';
        $data['heading'] = 'Update Shipping Zone - '.$shippingzones_name;

        $data['cities'] = $cities;
        $data['shippingzones'] = $shippingzones;
        $data['ShippingZonesCities'] = $ShippingZonesCities;

        return view('admin.shippingzones.form', $data);
    }

    

    public function save($request, $shippingzone_id=0){

        $errors = array();
        $data = $request->all();
        $sccMsg = "";

           //$insertedId = 3;

            $created = date('Y-m-d H:i:s');

           $queryData = [
            'name' => $data['name'],
            'created_at' => $created,
            'updated_at' => $created
            ];

            $city_id_arr = (isset($data['city_id']))?$data['city_id']:0;

            $queryData['status'] = (isset($data['status']))?$data['status']:0;

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

                if(!empty($city_id_arr) && count($city_id_arr) > 0){
                    //prd(count($city_id_arr));
                    $shippingZoneCity = array();
                    $exist_zone_cities = $ShippingZoneModel->ShippingZonesCities($insertedId, ['count'=>1]);

                    if($exist_zone_cities > 0){
                        $ShippingZoneModel->DeleteShippingZonesCities($insertedId);
                    }
                    
                    foreach($city_id_arr as $city_id){
                        $shippingZoneCity[] = array(
                            'shipping_zones_id' => $insertedId,
                            'city_id' => $city_id,
                            'created_at' => $created,
                            'updated_at' => $created,
                            );
                    }
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