<?php

namespace App\Http\Controllers\Admin;   

use Validator;

use App\UsersFeedback;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Foo;

use DB;

class FeedbackController extends Controller {

    protected $foo;

    public function __construct(Foo $foo)
    {       
       //$this->middleware('auth');
       $this->foo = $foo;
       $this->middleware('permission:cities');
    }
    
    public function index() {
        
        $feedbacks = UsersFeedback::orderBy('id', 'desc')->get();

        //prd($feedbacks);

        return view('admin.feedback.index', compact('feedbacks'));
    }

    public function ajax_view(Request $request) {
        
        //prd($request->all());

        $result['success'] = false;

        $feedback_id = (isset($request->feedback_id))?$request->feedback_id:0;

        if(is_numeric($feedback_id) && $feedback_id > 0){

            $feedback = UsersFeedback::find($feedback_id);

            if(!empty($feedback) && count($feedback) > 0){

                $data['feedback'] = $feedback;
                $view = view('admin.feedback._view', $data)->render();

                if(!empty($view)){
                    $result['view'] = $view;
                    $result['success'] = true;
                }
            }
        }
        return response()->json($result);
    }

    public function ajax_delete(Request $request) {

        $result['success'] = false;

        $feedback_id = (isset($request->feedback_id))?$request->feedback_id:0;

        if(is_numeric($feedback_id) && $feedback_id > 0){

            $is_deleted = UsersFeedback::where('id', $feedback_id)->delete();

            if($is_deleted){
                $created_at = date('Y-m-d H:i:s');

                $admin_id = auth()->user()->id;

                $users_activity = [
                'user_id' => $admin_id,
                'module_name' => 'Delete Customer Feedback',
                'module_action' => 'Delete',
                'tbl_id' => $feedback_id,
                'tbl_name' => 'users_feedback',
                'id_name' => 'id',
                'description' => 'Delete Customer Feedback',
                //'tbl_data' => serialize($brand->toArray()),
                'created_at' => $created_at,
                'updated_at' => $created_at,
                ];

                $foo = new Foo;

                $foo->users_activity($users_activity);
                
                $result['success'] = true;

                $result['message'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Feedback has been deleted.</div>';
            }
        }
        return response()->json($result);

    }

    /* End of controller */
}