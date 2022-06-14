<?php

namespace App\Http\Controllers\Admin;

use App\ActionLog;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Helpers\CustomHelper;

use Storage;
use DB;

class ActionLogController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $users = User::where('type', 'admin')->orderBy('first_name')->orderBy('email')->get();

        $user_id = (isset($request->user_id))?$request->user_id:0;
        $module_name = (isset($request->module_name))?$request->module_name:'';
        $action = (isset($request->action))?$request->action:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';
 
        $action_logs_query = ActionLog::orderBy('created_at', 'desc');

        if(is_numeric($user_id) && $user_id > 0){
            $action_logs_query->where('user_id', $user_id);
        }
        if(!empty($module_name)){
            $action_logs_query->where('module_name', 'like', $module_name.'%');
        }
        if(!empty($action)){
            $action_logs_query->where('action', 'like', $action.'%');
        }
        if(!empty($from)){
            $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
            if(!empty($from_date)){
                $action_logs_query->whereRaw('DATE(created_at) >= "'.$from_date.'"');
            }
        }
        if(!empty($to)){
            $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');
            if(!empty($to_date)){
                 $action_logs_query->whereRaw('DATE(created_at) <= "'.$to_date.'"');
            }
        }

        //DB::enableQueryLog();
        $action_logs = $action_logs_query->get();
        //prd(DB::getQueryLog());
        //prd($users->toArray());

        $data['action_logs'] = $action_logs;
        $data['users'] = $users;
        $data['limit'] = $limit;

        return view('admin.action_logs.index', $data);

    }

    public function view(Request $request){
        //prd($request->toArray());

        $data = [];

        $id = (isset($request->id))?$request->id:0;
        $type = (isset($request->type))?$request->type:'old';

        if(is_numeric($id) && $id > 0){
            $log = ActionLog::find($id);

            if(!empty($log) && count($log) > 0){
                $data['log'] = $log;
                $data['id'] = $id;
                $data['type'] = $type;

                return view('admin.action_logs.view', $data);
            }
        }

        return back();
    }




/* end of controller */
}