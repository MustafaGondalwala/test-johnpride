<?php

namespace App\Http\Controllers\Admin;   

use File;
use Image;
use Storage;
use Validator;
use App\DeliveryTerms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Foo;

use DB;

class DeliveryTermsController extends Controller {

    protected $foo;

    public function __construct(Foo $foo)
    {       
       //$this->middleware('auth');
       $this->foo = $foo;
       $this->middleware('permission:deliveryterms');
    }

    /**     
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        //echo "index"; die;
        $deliveryterms = DeliveryTerms::all();

        //pr($deliveryterms);

        return view('admin.deliveryterms.index', compact('deliveryterms'));
    }

    /**
     * Update User
     * URL: /admin/users/{user} (POST)
     *
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */

    public function add(Request $request){

        $method = $request->method();

        $deliveryterms_id = (isset($request->id))?$request->id:0;

        if($method == 'POST'){
            $errors = array();

            $data = $request->all();

            $validator = Validator::make($data, [
            //'username' => 'required|min:6|unique:users',
                'title' => 'required',
            //'address' => 'required'
                'contents' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect('admin/deliveryterms/add')
                ->withErrors($validator)->withInput($data);
            }
            else{

                $queryData = [
                    'title' => $data['title'],
                    'contents' => $data['contents'],
                    'status' => $data['status'],
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s')
                ];

                if(is_numeric($deliveryterms_id) && $deliveryterms_id > 0){

                    unset($queryData['created']);

                    $savedata = DeliveryTerms::where('id', $deliveryterms_id)->update($queryData);

                    if($savedata){
                        $insertedId = $deliveryterms_id;
                        $errors['sccMsg'] = "Delivery Term updated successfully.";
                    }

                }
                else{
                    $savedata = DeliveryTerms::create($queryData);
                    $insertedId = $savedata->id;
                    $errors['sccMsg'] = "Delivery Term added successfully.";
                }

                if($insertedId){

                    $created_at = date('Y-m-d H:i:s');

                    $admin_id = auth()->user()->id;

                    $users_activity = [
                        'user_id' => $admin_id,
                        'module_name' => 'Add Delivery Term',
                        'module_action' => 'Add',
                        'tbl_id' => $insertedId,
                        'tbl_name' => 'delivery_terms',
                        'id_name' => 'id',
                        'description' => 'Add Delivery Term',
                        //'tbl_data' => serialize($brand->toArray()),
                        'created_at' => $created_at,
                        'updated_at' => $created_at,
                    ];

                    $foo = new Foo;

                    $foo->users_activity($users_activity);

                    return redirect('admin/deliveryterms')->with($errors);
                }
            }

        }
        else{

            $title = 'Add Delivery Term';
            $heading = 'Add Delivery Term';

            if(is_numeric($deliveryterms_id) && $deliveryterms_id > 0){
                $title = 'Edit Delivery Term';
                $heading = 'Edit Delivery Term';

                $deliveryterms = DeliveryTerms::find($deliveryterms_id);
            }

            return view('admin.deliveryterms.add', compact('title', 'heading', 'deliveryterms'));
        }

    }
    

    public function delete(Request $request) {

        $method = $request->method();
        //prd($request->id);
        $id = $request->id;

        if($method != 'DELETE'){
            return redirect('admin/deliveryterms');
        }
        else{
            $is_deleted = DeliveryTerms::where('id', $id)->delete();

            if($is_deleted){
                $created_at = date('Y-m-d H:i:s');

                $admin_id = auth()->user()->id;

                $users_activity = [
                'user_id' => $admin_id,
                'module_name' => 'Delete Delivery Term',
                'module_action' => 'Delete',
                'tbl_id' => $id,
                'tbl_name' => 'delivery_terms',
                'id_name' => 'id',
                'description' => 'Delete Delivery Term',
                //'tbl_data' => serialize($brand->toArray()),
                'created_at' => $created_at,
                'updated_at' => $created_at,
                ];

                $foo = new Foo;

                $foo->users_activity($users_activity);
            }
        }

        return redirect('admin/deliveryterms');

    }

    /* End of controller */
}