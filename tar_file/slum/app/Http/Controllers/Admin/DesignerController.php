<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Role;
use App\Design;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use DB;
use Auth;

use Validator;

use App\Helpers\CustomHelper;

class DesignerController extends Controller{

    private $limit;

    public function __construct(){
        //$this->middleware(['auth:admin','permission:designers.add']);
        $this->limit=20;
    }

    public function index(Request $request){
        $data = [];
        $limit = $this->limit;

        $name = (isset($request->name))?$request->name:'';
        $email = (isset($request->email))?$request->email:'';
        $phone = (isset($request->phone))?$request->phone:'';
        $reff_code = (isset($request->reff_code))?$request->reff_code:'';

        $print_comm_scope = (isset($request->print_comm_scope))?$request->print_comm_scope:'=';
        $print_comm = (isset($request->print_comm))?$request->print_comm:'';

        $reff_comm_scope = (isset($request->reff_comm_scope))?$request->reff_comm_scope:'=';
        $reff_comm = (isset($request->reff_comm))?$request->reff_comm:'';

        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');

        $designer_query = User::query();

        $designer_query->where('type', 'designer');
        $designer_query->orderBy('id', 'desc');

        if(!empty($name)){
            $designer_query->whereRaw("CONCAT(users.first_name,' ',COALESCE(users.last_name,'')) LIKE '%".$name."%'" );
        }
        if(!empty($email)){
            $designer_query->where('users.email','like', '%'.$email.'%');
        }
        if(!empty($phone)){
            $designer_query->where('users.phone','like', '%'.$phone.'%');
        }
        if(!empty($reff_code)){
            $designer_query->where('users.referral_code','like', $reff_code.'%');
        }
        if(is_numeric($print_comm) && $print_comm > 0){
            $designer_query->where('printing_commission', $print_comm_scope, $print_comm);
        }
        if(is_numeric($reff_comm) && $reff_comm > 0){
            $designer_query->where('referral_commission', $reff_comm_scope, $reff_comm);
        }
        if( strlen($status) > 0 ){
            $designer_query->where('status', $status);
        }
        if(!empty($from_date)){
            $designer_query->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }
        if(!empty($to_date)){
            $designer_query->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }

        $designers = $designer_query->paginate($limit);

        $data['designers'] = $designers;

        return view('admin.designers.index', $data);

    }

    public function add(Request $request){
        $data = [];

        $designer_id = (isset($request->designer_id))?$request->designer_id:0;

        $designer = '';
        if(is_numeric($designer_id) && $designer_id > 0){
            $designer = User::where('type', 'designer')->where('id',$designer_id)->first();
            if(empty($designer)){
                return redirect('admin/designers');
            }
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/designers';
            }

            $email = (isset($request->email))?$request->email:'';
            

            $rules = [];

            $rules['first_name'] = 'required|min:2';
            //$rules['password'] = 'required|min:5';
            //$rules['address'] = 'required|min:5';

            if(!empty($email)){
                $rules['email'] = ['required','email',Rule::unique('users')->ignore($designer_id)];                
            }

            $this->validate($request, $rules);

            $createdDesigner = $this->save($request, $designer_id);

            if ($createdDesigner) {
                $alert_msg = 'The designer has been added successfully.';
                if(is_numeric($designer_id) && $designer_id > 0){
                    $alert_msg = 'The designer has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'The designers cannot be added, please try again or contact the administrator.');
            }
        }

        
        $designer_name = '';

        $page_heading = 'Add Designer';

        if(isset($designer->first_name)){
            $designer_name = trim($designer->first_name.' '.$designer->last_name);
            $page_heading = 'Update Designer - '.$designer_name;
        }        

        $states = DB::table('states')->get()->keyBy('id');

        $data['page_heading'] = $page_heading;
        $data['states'] = $states;
        $data['designer_id'] = $designer_id;
        $data['designer'] = $designer;
        $data['designer_name'] = $designer_name;

        return view('admin.designers.form', $data);

    }


    public function save(Request $request, $designer_id=0){

        $data = $request->except(['_token', 'back_url']);

        if(empty($data['password'])){
            unset($data['password']);
        }
        else{
            $data['password'] = bcrypt($data['password']);
        }

       

        $roles = Role::orderBy('name')->get()->keyBy('name');

        $type = 'designer';

        $data['type'] = $type;
        $data['role_id'] = (isset($roles[$type]))?$roles[$type]->id:'';

        //prd($data);

        $is_saved = '';

        if(is_numeric($designer_id) && $designer_id > 0){
            $is_saved = User::where('id', $designer_id)->update($data);
        }
        else{
            $is_saved = User::insert($data);
        }        
        return $is_saved;
    }
    

    public function designs(Request $request){
        //prd($request->designer_id);

        $limit = $this->limit;

        $designer_id = (isset($request->designer_id))?$request->designer_id:0;

        if(is_numeric($designer_id) && $designer_id > 0){

            $CategoryDropDown = CustomHelper::CategoryDropDown('category', 'fabric', '', '', 10);
            pr('CategoryDropDown');
            prd($CategoryDropDown);

            $data = [];

            $design_query = Design::orderBy('name')->where('user_id', $designer_id);

            $designs = $design_query->paginate($limit);

            $data['designs'] = $designs;

            return view('admin.designers.designs.index', $data);
        }

        
    }


     public function view_design(Request $request, $designer_id=0)
     {
        $designer_id = (isset($request->designer_id))?$request->designer_id:0;
        
        $data = [];

        if(is_numeric($designer_id) && $designer_id > 0){

        $designData = Design::orderBy('name','desc')->where('user_id',$designer_id)->paginate($this->limit);

        }

        //pr($designData); die;
        $data['designData'] = $designData;

        return view('admin.designers.view_design', $data);

    }



    public function edit_design(Request $request, $id=0){
        $data = [];

        $id = (isset($request->id))?$request->id:0;

        $designer = '';
        if(is_numeric($id) && $id > 0){
            $design = Design::where('id',$id)->first();
            if(empty($design)){
                return redirect('admin/designers/view_design');
            }
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //pr($request->all()); die;

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/designers/view_design/.$design->user_id';
            }

            $rules = [];

            //$rules['name'] = 'required';
            //$rules['status'] = 'required';

            $this->validate($request, $rules);

            $category_id='';
            $cat_id = isset($request->category_id)?$request->category_id:'';
            if(!empty($cat_id))
            {
                $category_id =implode(',', $cat_id);

            }

            $data['category_id'] = $category_id;
            $data['is_approved'] = $request->is_approved;

            if(is_numeric($id) && $id > 0)
                {
                    $createdDesign = Design::where('id', $id)->update($data);
                }
                else
                {
                    $createdDesign = Design::insert($data);
                }

            if ($createdDesign) {

                $userData = User::where('id', $design->user_id)->first();
                if(!empty($userData))
                {
                   $to_email = $userData->email;
                   $name = $userData->first_name.' '.$userData->last_name;
                }
                
                if($request->is_approved ==0)
                {
                    $is_approved = 'Pending';
                }
               else if($request->is_approved ==1)
                {
                    $is_approved = 'Approved';
                }
                else if($request->is_approved ==2)
                {
                    $is_approved = 'Disapproved';
                }


                
                $subject = 'Your Design Status is Changed - Tex India';
                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                if(empty($ADMIN_EMAIL)){
                    $ADMIN_EMAIL = config('custom.admin_email');
                }

                $from_email = $ADMIN_EMAIL; 

                $email_data = [];
                $email_data['name'] = $name;
                $email_data['is_approved'] = $is_approved;
               
                $is_mail = CustomHelper::SendMail('emails.change_status', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

                $alert_msg = 'The designe has been added successfully.';
                if(is_numeric($id) && $id > 0){
                    $alert_msg = 'The designe has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } 
            else {
                return back()->with('alert-danger', 'The design cannot be added, please try again or contact the administrator.');
            }
        }

        
        $designer_name = '';

        $page_heading = 'Add Design';

        if(isset($design->name)){
            $design_name = trim($design->name);
            $page_heading = 'Update Design - '.$design_name;
        }        

        $categories = DB::table('categories')->get();

        $data['page_heading'] = $page_heading;
        $data['categories'] = $categories;
        $data['id'] = $id;
        $data['design'] = $design;
        $data['design_name'] = $design_name;

        return view('admin.designers.form_design', $data);

    }



    
/* end of controller */
}