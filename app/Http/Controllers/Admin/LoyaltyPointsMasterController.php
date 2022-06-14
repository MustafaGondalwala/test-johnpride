<?php

namespace App\Http\Controllers\Admin;

use App\LoyaltyPointsMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Storage;

use App\Helpers\CustomHelper;
//use Maatwebsite\Excel\Facades\Excel;


class LoyaltyPointsMasterController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;      
    }

    public function index(Request $request){

        //echo "LoyaltyPointsMaster-index"; die;

        $data = [];
        $limit = $this->limit;
        
        $query = LoyaltyPointsMaster::query();
        $query->orderBy('id', 'desc');
        
        $loyaltyPointsMaster = $query->paginate($limit);
        //prd($colors->toArray());
        
        $data['loyaltyPointsMaster'] = $loyaltyPointsMaster;    
        return view('admin.loyalty_points_master.index', $data);

    }

    public function add(Request $request){

       // prd($request->toArray());
        $data = [];
        $type = (isset($request->type))?$request->type:'';
        $id = (isset($request->id))?$request->id:'';

        $loyaltyPointsMaster = '';
        if(is_numeric($id) && $id > 0){
            $loyaltyPointsMaster = LoyaltyPointsMaster::where('id', $id)->first();
            if(!isset($loyaltyPointsMaster->id) || $loyaltyPointsMaster->id != $id){
                return redirect('admin/loyaltypoints');
            }
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/loyaltypoints?type='.$type;
            }

            $facilities = (isset($request->facilities))?implode(',',$request->facilities):'';
            $id = (isset($request->id))?$request->id:0;

            $rules = [];
            $rules['name'] = 'required';
            $rules['min_order_amount'] = 'required';
            $rules['value_of_points'] = 'required';
            $rules['points_needed'] = 'required';
            //$rules['points_needed_max'] = 'required';

            $this->validate($request, $rules);

            $req_data = [];

            $req_data = $request->except(['_token', 'id', 'back_url']);
            $req_data['facilities'] = $facilities;
            $req_data['points_needed_max'] = ($request->points_needed_max > 0 )?$request->points_needed_max:0;

           // prd($req_data);




            if(!empty($loyaltyPointsMaster->id) && $loyaltyPointsMaster->id == $id){
                $isSaved = LoyaltyPointsMaster::where('id', $loyaltyPointsMaster->id)->update($req_data);
            }
            else{
                $isSaved = LoyaltyPointsMaster::create($req_data);

                $loyaltyPoints_id = $isSaved->id;
            }


            if ($isSaved) {

                return redirect(url($back_url))->with('alert-success', 'The Loyalty Points Master has been saved successfully.');
            } else {
                return back()->with('alert-danger', 'The Loyalty Points Master cannot be added, please try again or contact the administrator.');
            }
        }
    
        $page_heading = 'Add Loyalty Points Master';

        if(isset($loyaltyPointsMaster->name)){
             $page_heading = 'Update Color - '.$loyaltyPointsMaster->name;
        }

        $data['page_heading'] = $page_heading;
        $data['type'] = $type;
        $data['loyaltyPointsMaster'] = $loyaltyPointsMaster;
        $data['id'] = $id;

        return view('admin.loyalty_points_master.form', $data);

    }

    public function delete($id){
        //prd($request->toArray());
        $is_deleted = 0;

            if(is_numeric($id) && $id > 0){
                $loyaltyPointsMaster = LoyaltyPointsMaster::find($id);

                if(!empty($loyaltyPointsMaster) && count($loyaltyPointsMaster) > 0){
                    $is_deleted = $loyaltyPointsMaster->delete();

                }
        }
   
        if($is_deleted){
            return redirect(url('admin/colors'))->with('alert-success', 'The Loyalty Points Master has been removed successfully..');
        }else
        {
            return redirect(url('admin/colors'))->with('alert-danger', 'The Loyalty Points Master cannot be deleted, please try again or contact the administrator.');
        }
    }




    /* end of controller */
}