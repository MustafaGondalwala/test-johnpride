<?php

namespace App\Http\Controllers\Admin;   

use File;
use Image;
use Storage;
use Validator;
use App\BankAccounts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Foo;

use DB;

class PaymentOptionsController extends Controller {

    protected $foo;

    public function __construct(Foo $foo)
    {       
       //$this->middleware('auth');
       $this->foo = $foo;
       $this->middleware('permission:paymentoptions');
    }

    /**
     * Users
     * URL: /admin/users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        //echo "index"; die;
        $payment_options = BankAccounts::orderBy('id', 'desc')->get();

        //prd($bank_accounts->toArray());

        return view('admin.paymentoptions.index', compact('payment_options'));
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

        $paymentoption_id = $request->id;

        //prd($paymentoption_id);

        $message = array();

        if($method == 'POST'){

            $req_data = $request->all();

            //prd($req_data);

            $sort_order = $req_data['sort_order'];

            $rules['account_name'] = 'required';
            $rules['account_number'] = 'required';
            $rules['bank_name'] = 'required';
            $rules['brance_address'] = 'required';
            $rules['ifsc_code'] = 'required';
            $rules['status'] = 'required|numeric';

            if(!empty($sort_order)){
                $rules['sort_order'] = 'required|numeric';
            }            
        
            $validator = Validator::make($req_data, $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            else{
                $sort_order = (is_numeric($req_data['sort_order']))?$req_data['sort_order']:0;

                $queryData['account_name'] = $req_data['account_name'];
                $queryData['account_number'] = $req_data['account_number'];
                $queryData['bank_name'] = $req_data['bank_name'];
                $queryData['brance_address'] = $req_data['brance_address'];
                $queryData['ifsc_code'] = $req_data['ifsc_code'];
                $queryData['sort_order'] = $sort_order;
                $queryData['status'] = $req_data['status'];

                if(is_numeric($paymentoption_id) && $paymentoption_id > 0){
                    

                // /prd($queryData);

                    $savedata = BankAccounts::where('id', $paymentoption_id)->update($queryData);
                    $insertedId = $paymentoption_id;
                    $message['sccMsg'] = "Payment Option updated successfully.";

                    $module_name = 'Update Payment Option';
                    $module_action = 'update';
                }
                else{
                    
                    $savedata = BankAccounts::create($queryData);
                    $insertedId = $savedata->id;
                    $module_name = 'Add Payment Option';
                    $message['sccMsg'] = "Payment Option added successfully.";
                    $module_action = 'add';
                }

                if($insertedId){

                    $created_at = date('Y-m-d H:i:s');

                    $admin_id = auth()->user()->id;

                    $users_activity = [
                        'user_id' => $admin_id,
                        'module_name' => $module_name,
                        'module_action' => $module_action,
                        'tbl_id' => $insertedId,
                        'tbl_name' => 'bank_accounts',
                        'id_name' => 'id',
                        'description' => $module_name,
                        'created_at' => $created_at,
                        'updated_at' => $created_at,
                    ];

                    $foo = new Foo;

                    $foo->users_activity($users_activity);

                    return redirect('admin/paymentoptions')->with($message);

                }
            }
        }
        else{

            $data = [];
            
            $paymentoption = [];

            if(is_numeric($paymentoption_id) && $paymentoption_id > 0){
                $paymentoption = BankAccounts::where('id', $paymentoption_id)->first();
            }

            $title = 'Add Payment Option';
            $heading = 'Add Payment Option';

            $data['title'] = $title;
            $data['heading'] = $heading;
            $data['paymentoption_id'] = $paymentoption_id;
            $data['paymentoption'] = $paymentoption;
            
            return view('admin.paymentoptions.add', $data);
        }

    }

    public function delete(Request $request) {
        //prd($request->all());

        $method = $request->method();
        $id = $request->id;
        $paymentoption_id = $request->paymentoption_id;
        //pr($paymentoption_id);
        //prd($id);

        if($method != 'DELETE' || empty($id) || !is_numeric($id) || $id != $paymentoption_id){
            return redirect('admin/paymentoptions');
        }else{

            $is_deleted = BankAccounts::where('id', $id)->delete();

            if($is_deleted){

                $created_at = date('Y-m-d H:i:s');

                $admin_id = auth()->user()->id;

                $users_activity = [
                'user_id' => $admin_id,
                'module_name' => 'Delete Payment Option',
                'module_action' => 'delete',
                'tbl_id' => $id,
                'tbl_name' => 'bank_accounts',
                'id_name' => 'id',
                'description' => 'Delete Payment Option',
                //'tbl_data' => serialize($brand->toArray()),
                'created_at' => $created_at,
                'updated_at' => $created_at,
                ];

                $foo = new Foo;

                $foo->users_activity($users_activity);
            }
        }

        return redirect('admin/paymentoptions');

    }

    /* End of controller */
}