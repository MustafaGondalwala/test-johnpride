<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use DB;
use Validator;
use App\Http\Requests;
use App\Repositories\Role\EloquentRole;
use View;

use URL;

class PermissionsController extends Controller
{
	public function __construct()
	{
       //$this->middleware('auth');
	   //$this->middleware(['permission:permissions.manage']);
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$this->middleware(['permission:permissions.listing', 'permission:permissions.edit']);
		$roles = Role::all();
        //$permission = Permission::all();
		$permission = Permission::where('parent_id', '')->orWhere('parent_id', NULL)->get();
        //echo "<pre>"; print_r($permissions); die;
        return view('admin.permissions.index', compact('roles', 'permission'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        //prd($input);
	   $name = $request->input('name');
		 if(!empty($name)){
			 $validator = Validator::make(['name' => $name],['name' => "required|unique:permissions,name"]);
		  if ($validator->fails()){			
			    return Redirect::back()->with('msg_delete', "Permission already created!");
		  }else{			 
			 $inserted = Permission::create($input);
             //prd($inserted);
			 /*for user activity */
			//$description = array('description'=>'Permission Updated');
			//$this->foo->users_activity($description);
			 return redirect('admin/permissions')->with('msg_success', 'Permission created successfully.');
		 }
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    public function add()
    {
       return view('admin.permissions.add');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		 $permissiondata = Permission::findOrFail($id);
       return view('admin.permissions.add',compact('permissiondata'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input =  $request->all();
		$name =  $request->input('name');
	    if(!empty($name)){
		   $validator = Validator::make(['name' => $name],['name' => "required|unique:permissions,name,{$id}"]);
		  if ($validator->fails()){
		    return Redirect::back()->with('msg_delete', "Permission already exists!");
		  }else{
		   $data = Permission::findOrFail($id);
		     $data->update($input);
			 /*for user activity */
			//$description = array('description'=>'Permission Updated');
			//$this->foo->users_activity($description);
			
            //return redirect('admin/permissions')->with('msg_update', trans('app.update_success_message'));
            return redirect('admin/permissions')->with('msg_update', 'Permission Updated');
	   }
	 }
    }
	
	
	 /**
     * Update permissions for each role.
     *
     * @param Request $request
     * @return mixed
     */

    public function saveRolePermissions(Request $request)
    {
        //prd($request->all());
        $roles = $request->get('roles');
        $role_ids = $request->get('role_ids');

        $allRoles = DB::table('roles')->pluck('id');  

        foreach ($role_ids as $role_id){
            $insertData = [];

            //prd($role_v);

            DB::table('permission_role')->where('role_id', $role_id)->delete();

            if(isset($roles[$role_id])){
                foreach($roles[$role_id] as $permission_id){
                    $insertData[] = ['role_id'=>$role_id, 'permission_id'=>$permission_id];
                }
            }

            if(!empty($insertData) && count($insertData) > 0){                
                DB::table('permission_role')->insert($insertData);
            }
        }

        $description = array('description'=>'Roles wise permission Update');
		
        //return redirect('admin/permissions')->with('msg_update',trans('app.update_success_message'));
		return redirect('admin/permissions')->with('msg_update','Roles wise permission Update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //echo $id; die;
      $data = Permission::findOrFail($id);		
	  if ($data){ 
		 $data->delete();
		 /*for user activity */
        //$description = array('description'=>'Permission Delete.');
	    //$this->foo->users_activity($description);
		return redirect('admin/permissions')->with('msg_delete','Permission deleted successfully.');
		}
    }

    function ajax_delete_permission(Request $request){

        dd($request);
        $response = array();

        $response['success'] = false;


        $id = $request->input('id');
        //echo $id; die;
        $response['id'] = $id;

        /*if(is_numeric($id) && $id > 0){
            $data = Permission::findOrFail($id);
            if ($data){ 
               $data->delete();
               $response['success'] = true;
           }
       }*/

        
       return json_encode($response);
    }

    /* End of Controller */
}
