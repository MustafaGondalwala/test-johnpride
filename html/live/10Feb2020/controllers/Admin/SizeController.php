<?php

namespace App\Http\Controllers\Admin;

use App\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SizeController extends Controller{

     private $limit;
     public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){
        
        $limit = $this->limit;
        $sizeRow = '';
        $method = $request->method();
        //prd($method);
        $id = $request->id;
        //prd($admin_id);

        if(is_numeric($id) && $id > 0){
            $sizeRow = Size::find($id);
        }

        if($method == 'POST' || $method == 'post'){
            //prd($request->all());            

                $rules = array(
                    'name' => 'required',
                    'status' => 'required'
                    );

                $validator = $this->validate($request, $rules);    
 
                $size_data['name'] = $request->name;
                $size_data['sort_order'] = (isset($request->sort_order) && is_numeric($request->sort_order))?$request->sort_order:0;
                $size_data['status'] = (isset($request->status))?$request->status:0;

                if(is_numeric($id) && $id > 0){
                    $saved_data = Size::where('id', $id)->update($size_data);
                    $success_msg = 'Size has been updated';
                    $activity_description = 'Update Size';
                    $module_name = 'Update Size';
                }
                else{
                    $saved_data = Size::create($size_data);
                    $id = (isset($saved_data->id))?$saved_data->id:'';
                    $success_msg = 'Size has been added successfully';
                    $activity_description = 'Add Size';
                    $module_name = 'Add Size';
                }

                if(!empty($saved_data)){

                    session()->flash('alert-success', $success_msg);
                    
                    return redirect('admin/sizes');
                }
        }

        $data = [];
        $sizes = Size::orderBy('sort_order', 'asc')->paginate($limit);       
        $data['sizes'] = $sizes;
        $data['sizeRow'] = $sizeRow;

        return view('admin.sizes.index', $data);
    }

    public function delete(Request $request){
        $id=$request->id;
        $method=$request->method();
        $is_deleted = 0;

        if($method=="POST"){
            if(is_numeric($id) && $id > 0)
            {
                $select_size = Size::find($id);

                if(!empty($select_size) && count($select_size) > 0){
                    $is_deleted = $select_size->delete();
                }
            }
        }

        if($is_deleted){
            return redirect(url('admin/sizes'))->with('alert-success', 'The Size has been deleted successfully.');
        }else
        {
            return redirect(url('admin.sizes'))->with('alert-danger', 'The Size cannot be deleted, please try again or contact the administrator.');
        }

    }


    
}