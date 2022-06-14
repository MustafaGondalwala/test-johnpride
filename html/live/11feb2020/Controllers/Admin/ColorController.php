<?php

namespace App\Http\Controllers\Admin;

use App\ColorMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Storage;

use App\Helpers\CustomHelper;

use Image;

class ColorController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;      
    }

    public function index(Request $request){

        //echo "ColorController-index"; die;

        $data = [];

        $limit = $this->limit;
        
        $colors = ColorMaster::paginate($limit);
        //prd($colors->toArray());

        $parentColor = '';
        
        $data['colors'] = $colors;
    
        return view('admin.colors.index', $data);

    }

    public function add(Request $request){

       // prd($request->toArray());
        $data = [];

        $type = (isset($request->type))?$request->type:'';

        $id = (isset($request->id))?$request->id:'';

        $color = '';

        if(is_numeric($id) && $id > 0){
            $color = ColorMaster::where('id', $id)->first();
            if(!isset($color->id) || $color->id != $id){
                return redirect('admin/colors');
            }
        }

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/colors?type='.$type;
            }

            //$parent_id = (isset($request->parent_id))?$request->parent_id:'';
            $featured = (isset($request->featured))?$request->featured:'0';
            $id = (isset($request->id))?$request->id:0;

            $rules = [];
            $rules['name'] = 'required';

            $this->validate($request, $rules);

            $req_data = [];

            $req_data = $request->except(['_token', 'id', 'back_url']);

            $slug = CustomHelper::GetSlug('colors_master', 'id', $id, $request->name);

            //$req_data['parent_id'] = $parent_id;
            $req_data['slug'] = $slug;

            //prd($req_data);

            if(!empty($color->id) && $color->id == $id){
                $isSaved = ColorMaster::where('id', $color->id)->update($req_data);
            }
            else{
                $isSaved = ColorMaster::create($req_data);

                $color_id = $isSaved->id;
            }


            if ($isSaved) {

                return redirect(url($back_url))->with('alert-success', 'The color has been saved successfully.');
            } else {
                return back()->with('alert-danger', 'The color cannot be added, please try again or contact the administrator.');
            }
        }
    
        $page_heading = 'Add Color';

        if(isset($color->name)){
             $page_heading = 'Update Color - '.$color->name;
        }

        $data['page_heading'] = $page_heading;
        $data['type'] = $type;
        $data['color'] = $color;
        $data['id'] = $id;

        return view('admin.colors.form', $data);

    }

    public function delete($id){
        //prd($request->toArray());
        $is_deleted = 0;

            if(is_numeric($id) && $id > 0){
                $color = ColorMaster::find($id);

                if(!empty($color) && count($color) > 0){
                    $countProducts = $color->countProducts();

                    if($countProducts > 0)
                    {
                        return back()->with('alert-danger', 'This Color cannot be removed because there are currently ' .$countProducts. ' products associated with it. Please remove the products first.');
                    }
                    else{
                        $is_deleted = $color->delete();
                    }

                }
        }
   
        if($is_deleted){
            return redirect(url('admin/colors'))->with('alert-success', 'The color has been removed successfully..');
        }else
        {
            return redirect(url('admin/colors'))->with('alert-danger', 'The Color cannot be deleted, please try again or contact the administrator.');
        }
    }

    /* end of controller */
}