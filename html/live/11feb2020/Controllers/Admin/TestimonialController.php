<?php

namespace App\Http\Controllers\Admin;

use App\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use DB;
use Auth;

use Validator;

use App\UsersWallet;
use App\Helpers\CustomHelper;

class TestimonialController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){
        $data = [];
        $limit = $this->limit;

        $testimonial_query = Testimonial::orderBy('id', 'desc');

        $testimonials = $testimonial_query->paginate($limit);

        $data['testimonials'] = $testimonials;

        return view('admin.testimonials.index', $data);

    }

    public function add(Request $request){
        $data = [];

        $id = (isset($request->id))?$request->id:0;

        $testimonial = '';
        if(is_numeric($id) && $id > 0){
            $testimonial = Testimonial::find($id);
            if(empty($testimonial)){
                return redirect('admin/testimonials');
            }
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/testimonials';
            }

            $name = (isset($request->name))?$request->name:'';
            

            $rules = [];

            $rules['name'] = 'required';
            $rules['description'] = 'required';

            $this->validate($request, $rules);

            $createdTestimonial = $this->save($request, $id);

            if ($createdTestimonial) {
                $alert_msg = 'Testimonial has been added successfully.';
                if(is_numeric($id) && $id > 0){
                    $alert_msg = 'Testimonial has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }

       
        $testimonial_name = '';

        $page_heading = 'Add Testimonial';

        if(isset($testimonial->name)){
            $testimonial_name = $testimonial->name;
            $page_heading = 'Update Testimonial - '.$testimonial_name;
        }  

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['testimonial'] = $testimonial;
        $data['testimonial_name'] = $testimonial_name;

        return view('admin.testimonials.form', $data);

    }


    public function save(Request $request, $id=0){

        $data = $request->except(['_token', 'back_url']);

        $date_on = (isset($request->date_on))?$request->date_on:'';

        $date_on = CustomHelper::DateFormat($date_on, 'Y-m-d', 'd/m/Y');

        $data['date_on'] = $date_on;

        //prd($data);

        $is_saved = '';

        if(is_numeric($id) && $id > 0){
            $is_saved = Testimonial::where('id', $id)->update($data);
        }
        else{
            $is_saved = Testimonial::insert($data);
        }        
        return $is_saved;
    }


    public function delete(Request $request){

        //prd($request->toArray());

        $id = (isset($request->id))?$request->id:0;

        $is_delete = '';

        if(is_numeric($id) && $id > 0){
            $is_delete = Testimonial::where('id', $id)->delete();
        }

        if(!empty($is_delete)){
            return back()->with('alert-success', 'Testimonial has been deleted successfully.');
        }
        else{
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    
/* end of controller */
}