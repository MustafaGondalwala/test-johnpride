<?php

namespace App\Http\Controllers\Admin;

use App\ColorMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Storage;

use App\Helpers\CustomHelper;
use App\Exports\ColorsExport;
use Maatwebsite\Excel\Facades\Excel;
use Image;

class ColorController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;      
    }

    public function index(Request $request){

        //echo "ColorController-index"; die;

        $data = [];
        $export_xls = (isset($request->export_xls))?$request->export_xls:'';

        $limit = $this->limit;

        
        $color_query = ColorMaster::query();
        $color_query->orderBy('id', 'desc');

        if(!empty($export_xls) && ($export_xls == 1 || $export_xls == '1') ){
            return $this->exportXls($color_query);
        }

        
        $colors = $color_query->paginate($limit);
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



    private function exportXls($colors_query){

        $fieldNames = ['id','name'];

        $colors = $colors_query->get();

        $exportArr = [];

        if(!empty($colors) && $colors->count() > 0){
            foreach($colors as $color){
                //prd($color->toArray());

               
                $colorArr = [];
                $colorArr['id'] = $color->id;
                $colorArr['name'] = $color->name;
                

                $exportArr[] = $colorArr;
            }
        }

        $fieldNames = array_keys($exportArr[0]);

       // prd($exportArr);

        $fileName = 'colors_'.date('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new ColorsExport($exportArr, $fieldNames), $fileName);
    }

    /* end of controller */
}