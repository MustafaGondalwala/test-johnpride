<?php

namespace App\Http\Controllers\Admin;   

use File;
use Image;
use Storage;
use Validator;

use App\User;
use App\UsersWallet;
use App\Order;
use App\PaymentReceived;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

use App\Foo;

use DB;

use App\Helpers\CustomHelper;

use Excel;

class PaymentsController extends Controller {

    private $limit = 20;

    public function __construct()
    {       
       //$this->middleware('auth');
       
       //$this->middleware('permission:brands');
    }

    /**
     * Users
     * URL: /admin/users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $data = [];
        //echo "index"; die;
        $users = User::orderBy('first_name')->get();

        $user_id = Input::get('user');
        $type = Input::get('type');
        $record_id = Input::get('record_id');
        $bank_account = Input::get('bank_account');
        $amt_from = Input::get('amt_from');
        $amt_to = Input::get('amt_to');
        $from_date = Input::get('from');
        $to_date = Input::get('to');
        $status = Input::get('status');

        $select = PaymentReceived::orderBy('created', 'desc');

        if(is_numeric($user_id) && $user_id > 0){
            $select->where('user_id', $user_id);
        }
        if(!empty($type)){
            $select->where('type', $type);
        }
        if(is_numeric($record_id) && $record_id > 0){
            $select->where('record_id', $record_id);
        }
        if(is_numeric($bank_account) && $bank_account > 0){
            $select->where('bank_accounts_id', $bank_account);
        }
        if(!empty($amt_from)){
            $select->where('amount', '>=', $amt_from);
        }
        if(!empty($amt_to)){
            $select->where('amount', '<=', $amt_to);
        }
        if(!empty($from_date)){
            $from_date = date_format2($from_date,$to_format='Y-m-d',$from_format='d/m/Y');
            //pr($from_date);
            $select->whereDate('created', '>=', $from_date);
        }
        if(!empty($to_date)){
             $to_date = date_format2($to_date,$to_format='Y-m-d',$from_format='d/m/Y');
            $select->whereDate('created', '<=', $to_date);
        }
        if(!empty($status)){
            $select->where('status', $status);
        }

        $limit = $this->limit;

        if($request->method() == 'POST' && isset($request->export_xls) && $request->export_xls === '1' ){

            // /prd(auth()->user()->can('export_xls'));

            if(auth()->user()->can('export_xls')){
                //echo "export"; die;
                //prd($request->all());

                $exportData = [];
                //$exportData = $data;
                $exportData['payments'] = $select->get();

                //return view('admin.payments.export', $exportData); die;

                $excel_name = 'payments_'.'_'.date('Y_m_d_H_i_s');

                Excel::create($excel_name, function($excel) use ($excel_name, $exportData){

                    $excel->sheet($excel_name, function($sheet) use ($excel_name, $exportData) {

                        $sheet->loadView('admin.payments.export', $exportData);

                    });

                })->download('xls');
            }
            else{
                abort(404);
            }
        }
        else{

            $payments = $select->paginate($limit);

            $data['users'] = $users;
            $data['payments'] = $payments;

//prd($brands);

            return view('admin.payments.index', $data);
        }
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

        $back_url = ($request->has('back_url'))?$request->back_url:'';

        $data['back_url'] = $back_url;

        $record_id = ($request->has('record_id'))?$request->record_id:0;
        $user_id = ($request->has('user'))?$request->user:0;

        //prd($user_id);

        if(!is_numeric($record_id) || $record_id <= 0 || !is_numeric($user_id) || $user_id <= 0){
            if(!empty($back_url)){
                return redirect($back_url);
            }
            else{
                return redirect('admin/payments');
            }
            
        }

        $admin_id = auth()->user()->id;

        $users = User::orderBy('first_name')->get();

        if($method == 'POST' || $method == 'post'){
           $errors = array();

        $req_data = $request->all();

        //prd($req_data);

        $rules = [
            //'username' => 'required|min:6|unique:users',
            'user' => 'required',
            'type' => 'required',
            'record_id' => 'required|numeric',
            'transaction_type' => 'required',
            'transaction_id' => 'required',
            'bank_account' => 'required',
            'transaction_amount' => 'required|numeric',
            'transaction_date' => 'required',
        ];


        //$this->validate($request, $rules);

        $transaction_type = $req_data['transaction_type'];
        $transaction_amount = $req_data['transaction_amount'];

        $validator = Validator::make($req_data, $rules);

        if($transaction_type == 'wallet'){

            $check_wallet_bal = CustomHelper::checkUserWalletBal($user_id, $transaction_amount);

            $validator->after(function ($validator) use ($check_wallet_bal) {
                if (!$check_wallet_bal) {
                    $validator->errors()->add('transaction_amount', 'Insufficient balance in wallet!');
                }
            });
        }

        //$this->validate($request, $rules, $messages);

        if ($validator->fails()) {

            //prd($validator->errors());

            return back()->withErrors($validator)->withInput($req_data);
        }
        else{

            //echo "here"; die;

           //$insertedId = 3;

            $transaction_date = $req_data['transaction_date'];

            $transaction_date = date_format2($transaction_date, $to_format='Y-m-d H:i:s', $from_format='d/m/Y');

            $updated = date('Y-m-d H:i:s');

            $insert_data = [];

            $insert_data['user_id'] = $user_id;
            $insert_data['type'] = $req_data['type'];
            $insert_data['record_id'] = $record_id;
            $insert_data['transaction_type'] = $req_data['transaction_type'];
            $insert_data['bank_accounts_id'] = $req_data['bank_account'];
            $insert_data['amount'] = $req_data['transaction_amount'];
            $insert_data['transaction_id'] = $req_data['transaction_id'];
            //$insert_data['sender_bank'] = $req_data['sender_bank'];
            //$insert_data['sender_acc_name'] = $req_data['sender_ac_name'];
            //$insert_data['sender_acc_num'] = $req_data['sender_ac_num'];
            $insert_data['date_on'] = $transaction_date;
            $insert_data['remarks'] = $req_data['payment_remarks'];
            $insert_data['status'] = 'details_entered';

            $insert_data['updated'] = $updated;

            $is_inserted = PaymentReceived::create($insert_data);           

           if(isset($is_inserted->id) && $is_inserted->id > 0){

            if($req_data['transaction_type'] == 'wallet'){

                $wallet_params = [];

                if($req_data['type'] == 'orders'){
                    $order = Order::where('id', $record_id)->select('order_number')->first();
                    if(!empty($order) && count($order) > 0){
                        $orderNumber = $order->order_number;

                        $wallet_params['description'] = 'Debited against Order No.- '.$orderNumber;
                        $wallet_params['orderNumber'] = $orderNumber;
                    }                    
                }

                CustomHelper::AddUserWallet($user_id, $admin_id, $credit_amount=0, $debit_amount=$req_data['transaction_amount'], $wallet_params);

            }

            $created_at = date('Y-m-d H:i:s');

            $users_activity = [
                'user_id' => $admin_id,
                'module_name' => "Add Payment",
                'module_action' => 'Add',
                'tbl_id' => $is_inserted->id,
                'tbl_name' => 'payment_received',
                'id_name' => 'id',
                'description' => 'Add Payment',
                'created_at' => $updated,
                'updated_at' => $updated,
            ];

            $foo = new Foo;

            $foo->users_activity($users_activity);

            if(!empty($back_url)){
                return redirect($back_url)->with($errors);
            }
            else{
                return redirect('admin/payments')->with($errors);
            }

            //return redirect('admin/payments')->with($errors);

           }        
        }          
        }
        else{

            $user_id = Input::get('user');
            $type = Input::get('type');
            $record_id = Input::get('record_id');

            $order = '';

            if($type == 'orders'){
                $order = Order::find($record_id);
                //prd($order);
            }

            $data['order'] = $order;

            $data['user_id'] = $user_id;
            $data['type'] = $type;
            $data['record_id'] = $record_id;

            $title = 'Add Payment';
            $heading = 'Add Payment';

            $data['title'] = $title;
            $data['heading'] = $heading;
            $data['users'] = $users;
            
            return view('admin.payments.add', $data);
        }        
       
    }


    public function ajax_change_status(Request $request){
        //prd($request->all());

        $result['success'] = false;

        $alert_type = 'danger';
        $message = 'Something went wrong, please try again!';

        $id = $request->input('id');
        $status = $request->input('status');

        if(is_numeric($id) && $id > 0 && !empty($status)){

            $PaymentReceived = PaymentReceived::find($id);
            //prd($PaymentReceived->toArray());

            $type = (isset($PaymentReceived->type))?$PaymentReceived->type:'';

            $updated = date('Y-m-d H:i:s');

            $update_data['status'] = $status;
            $update_data['updated'] = $updated;

            $is_updated = PaymentReceived::where('id', $id)->update($update_data);

            if(isset($is_updated)){

                if($type == 'orders'){
                    $order_id = (isset($PaymentReceived->record_id))?$PaymentReceived->record_id:'';

                    if(is_numeric($order_id) && $order_id > 0){
                        $order = Order::find($order_id);

                        if(!empty($order) && $order->count() >0 ){

                            $updateOrderData = [];
                            $updateOrderData['payment_status'] = $status;

                            $order->update($updateOrderData);
                        }
                    }
                }

                $admin_id = auth()->user()->id;

                $users_activity = array(
                    'user_id' => $admin_id,
                    'module_name' => "Update Payment Satus",
                    'module_action' => 'Update',
                    'tbl_id' => $id,
                    'tbl_name' => 'payment_received',
                    'id_name' => 'id',
                    'description' => 'Payment Satus updated',
                    'created_at' => $updated,
                    'updated_at' => $updated,
                );

                $foo = new Foo;

                $foo->users_activity($users_activity);

                $alert_type = 'success';
                $message = 'Status has been updated successfully.';
                
                $result['success'] = true;

            }
        }

        $result['scc_msg'] = '<div class="alert alert-'.$alert_type.' alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.$message.'</div>';

        return response()->json($result);
    }


    public function ajax_view_payment(Request $request){
        // prd($request->all());

        $result['success'] = false;

        $id = $request->input('id');

        if(is_numeric($id) && $id > 0){

            $payment = PaymentReceived::find($id);

            //prd($payment);

            $data['payment'] = $payment;
            $view = view('admin.payments.view', $data)->render();

            if(!empty($view)){
                $result['view'] = $view;
                $result['success'] = true;
            }
        }
        return response()->json($result);
    }

    /* End of controller */
}