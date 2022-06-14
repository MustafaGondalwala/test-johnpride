<?php

namespace App\Http\Controllers\Admin;

use App\Pincode;
use App\City;
use App\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PincodeController extends Controller{

     private $limit;
     public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){
        
        $limit = $this->limit;
        $pincodeRow = '';
        $pin_data = [];
        $method = $request->method();
        $id = $request->id;
        if(is_numeric($id) && $id > 0){
            $pincodeRow = Pincode::find($id);
        }

        if($method == 'POST' || $method == 'post'){
            //prd($request->all());            
                $rules = array(
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'pin' => 'required|numeric',
                    'status' => 'required'
                    );

                $validator = $this->validate($request, $rules);    
 
                $pin_data['state_id'] = isset($request->state_id)?$request->state_id:0;
                $pin_data['city_id'] = isset($request->city_id)?$request->city_id:0;
                $pin_data['pin'] = isset($request->pin)?$request->pin:0;
                $pin_data['status'] = isset($request->status)?$request->status:0;

                if(is_numeric($id) && $id > 0){
                    $saved_data = Pincode::where('id', $id)->update($pin_data);
                    $success_msg = 'Pincode has been updated';
                    $activity_description = 'Update Pincode';
                    $module_name = 'Update Pincode';
                }
                else{
                    $saved_data = Pincode::create($pin_data);
                    $id = (isset($saved_data->id))?$saved_data->id:'';
                    $success_msg = 'Size has been added successfully';
                    $activity_description = 'Add Pincode';
                    $module_name = 'Add Pincode';
                }

                if(!empty($saved_data)){

                    session()->flash('alert-success', $success_msg);
                    
                    return redirect('admin/pincodes');
                }
        }

        $data = [];
        $pincodes = Pincode::orderBy('id', 'asc')->paginate($limit);
        $state = State::orderBy('name', 'asc')->get();
        $city = City::orderBy('name', 'asc')->get();
        $data['pincodes'] = $pincodes;
        $data['pincodeRow'] = $pincodeRow;
        $data['city'] = $city;
        $data['state'] = $state;

        return view('admin.pincodes.index', $data);
    }

    public function delete(Request $request){
        $method = $request->method();
        
        $is_deleted = 0;

        if($method == 'POST'){
            $id = (isset($request->id))?$request->id:0;

            if(is_numeric($id) && $id > 0){
                $pincode = Pincode::find($id);
                if(!empty($pincode) && count($pincode) > 0){
                    $is_deleted = $pincode->delete();
                }
            }
        }

        if($is_deleted){
            return redirect(url('admin/pincodes'))->with('alert-success', 'The Pincode has been deleted successfully.');
        }
        else{
            return redirect(url('admin.pincodes'))->with('alert-danger', 'The Pincode cannot be deleted, please try again or contact the administrator.');
        }

    }


    
}