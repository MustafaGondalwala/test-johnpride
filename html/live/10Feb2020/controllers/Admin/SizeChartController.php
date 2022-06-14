<?php

namespace App\Http\Controllers\Admin;

use App\SizeChart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Helpers\CustomHelper;

use Validator;
use Storage;
use Image;

class SizeChartController extends Controller
{
    private $page_arr;
    private $limit;
    public function __construct(){
        $this->limit = 20;
    }

    public function index(){

        $data = [];
        $limit = $this->limit;

        $sizeCharts = SizeChart::orderBy('created_at','desc')->paginate($limit);
        //pr($sizeChart);

        $data['sizeCharts'] = $sizeCharts;
        return view('admin.size_charts.index', $data);

    }

    public function add(Request $request){
        
        $id = (isset($request->id))?$request->id:0;
        $sizeChart = '';
        $title = 'Add Size Chart';

        if(is_numeric($id) && $id > 0){
            $sizeChart = SizeChart::find($id);
            $title = 'Edit Size Chart';
        }
        if($request->method() == 'POST' || $request->method() == 'post'){   
        //prd($request->toArray()); die;         
            $ext = 'jpg,jpeg,png,gif';

            $rules['title'] = 'required';
            $rules['status'] = 'required';
            $rules['image'] = 'nullable|image|mimes:'.$ext;

            $this->validate($request, $rules);
            $req_data = [];
            $req_data = $request->except(['_token', 'image', 'back_url', 'old_image','id']);

            if(!empty($sizeChart) && count($sizeChart) > 0){
                $isSaved = SizeChart::where('id', $id)->update($req_data);
                $msg="The Size Chart has been updated successfully.";
            }
            else{
                $isSaved = SizeChart::create($req_data);
                $id = $isSaved->id;
                $msg="The Size Chart has been added successfully.";
            }  

            if ($isSaved) {

                if($request->hasFile('image')) {
                    $file = $request->file('image');
                    $image_result = $this->saveImage($file,$id);
                    if($image_result['success'] == false){
                        //$msg="The Home Image has been added successfully. But Image Can not be Added.";
                        session()->flash('alert-danger', 'Image could not be added');
                    }
                }

                cache()->forget('size_charts');

                return redirect(url('admin/size_charts'))->with('alert-success', $msg);
            } else {
                return back()->with('alert-danger', 'The Size Chart cannot be added, please try again or contact the administrator.');
            }
        }
        $data = [];
        $data['page_heading'] = $title;
        $data['sizeChart'] = $sizeChart;
        $data['id'] = $id;
        return view('admin.size_charts.form', $data);
    }


    public function saveImage($file, $id){
        //prd($file); 
        //echo $id; die;

        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) 
        {
            $path = 'size_charts/';
            $thumb_path = 'size_charts/thumb/';

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('SIZECHART_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('SIZECHART_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('SIZECHART_THUMB_HEIGHT');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('SIZECHART_THUMB_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

            $uploaded_data = CustomHelper::UploadImage($file, $path, $ext='', $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

            if($uploaded_data['success']){
                $new_image = $uploaded_data['file_name'];

                if(is_numeric($id) && $id > 0){
                    $sizeChart = SizeChart::find($id);

                    if(!empty($sizeChart) && count($sizeChart) > 0){

                        $storage = Storage::disk('public');

                        $old_image = $sizeChart->image;

                        $sizeChart->image = $new_image;

                        $isUpdated = $sizeChart->save();

                        if($isUpdated){

                            if(!empty($old_image) && $storage->exists($path.$old_image)){
                                $storage->delete($path.$old_image);
                            }

                            if(!empty($old_image) && $storage->exists($thumb_path.$old_image)){
                                $storage->delete($thumb_path.$old_image);
                            }
                        }
                    }
                }
            }

            if(!empty($uploaded_data))
            {   
                return $uploaded_data;
            }
        }
}

    public function ajax_delete_image(Request $request){
        //prd($request->toArray());
        $result['success'] = false;

        $image_id = ($request->has('image_id'))?$request->image_id:0;

        if (is_numeric($image_id) && $image_id > 0) {
            $is_img_deleted = $this->delete_images($image_id);
            if($is_img_deleted)
            {
                $result['success'] = true;
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Size Chart image has been delete successfully.</div>';
            }
        }

        if($result['success'] == false){
            $result['msg'] = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Something went wrong, please try again.</div>';
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $id=$request->id;
        $method=$request->method();
        $is_deleted = 0;
        $storage = Storage::disk('public');
        $path = 'size_charts/';

        if($method=="POST"){
            if(is_numeric($id) && $id > 0)
            {
                $sizeChart = SizeChart::find($id);
                if(!empty($sizeChart) && count($sizeChart) > 0){
                $countProducts = $sizeChart->countProducts();

                if($countProducts > 0)
                {
                    return back()->with('alert-danger', 'This Size Chart cannot be removed because there are currently ' .$countProducts. ' products associated with it. Please remove the products first.');
                }

                else {
                    if(count($sizeChart) > 0 && !empty($sizeChart->image))
                    {   
                       $image = $sizeChart->image;
                       if(!empty($image) && $storage->exists($path.'thumb/'.$image))
                       {
                        $is_deleted = $storage->delete($path.'thumb/'.$image);
                    }
                    if(!empty($image) && $storage->exists($path.$image))
                    {
                        $is_deleted = $storage->delete($path.$image);
                    }
                }
                $is_deleted = $sizeChart->delete();
            }
        }
    }
 }   
        if($is_deleted){
            return redirect(url('admin/size_charts'))->with('alert-success', 'The Size Chart has been deleted successfully.');
        }else
        {
            return redirect(url('admin.size_charts'))->with('alert-danger', 'The Size Chart cannot be deleted, please try again or contact the administrator.');
        }

    }

    public function delete_images($id)
    {   
        $is_deleted = '';
        $is_updated = '';
        $storage = Storage::disk('public');
        $path = 'size_charts/';
        $sizeChart = SizeChart::find($id);
        
        $image = (isset($sizeChart->image))?$sizeChart->image:'';
            if(!empty($image) && $storage->exists($path.'thumb/'.$image))
            {
                $is_deleted = $storage->delete($path.'thumb/'.$image);
            }
            if(!empty($image) && $storage->exists($path.$image))
            {
                $is_deleted = $storage->delete($path.$image);
            }
            if($is_deleted){
             $sizeChart->image = '';
             $is_updated = $sizeChart->save();
             
         }
           return $is_updated;
    }

    /* end of controller */
}