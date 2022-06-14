<?php

namespace App\Http\Controllers\Admin;

use App\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Helpers\CustomHelper;

use Validator;
use Storage;
use Image;

class BrandController extends Controller
{
    private $limit;
    public function __construct(){
        $this->limit = 20;
    }

    public function index(){

        $data = [];
        $limit = $this->limit;

        $brands = Brand::orderBy('created_at','desc')->paginate($limit);
        //pr($brands);

        $data['brands'] = $brands;  
        return view('admin.brands.index', $data);

    }

    public function add(Request $request){
        
        $id = (isset($request->id))?$request->id:0;
        $brand = '';
        $title = 'Add Brand';

        if(is_numeric($id) && $id > 0){
            $brand = Brand::find($id);
            $title = 'Edit Brand Chart';
        }
        if($request->method() == 'POST' || $request->method() == 'post'){   
        //prd($request->toArray()); die;         
            $ext = 'jpg,jpeg,png,gif';

            $rules['name'] = 'required';
            $rules['status'] = 'required';
            $rules['image'] = 'nullable|image|mimes:'.$ext;
            $rules['icon'] = 'nullable|image|mimes:'.$ext;

            $this->validate($request, $rules);
            $req_data = [];
            
            $req_data = $request->except(['_token', 'image', 'back_url', 'old_image','old_icon','id']);
            $req_data['featured'] = isset($request->featured)?$request->featured:0;
            
            $slug = CustomHelper::GetSlug('brands', 'id', $id, $request->name);
            $req_data['slug'] = $slug;
            //prd($req_data);
            if(!empty($brand) && count($brand) > 0){
                $isSaved = Brand::where('id', $id)->update($req_data);
                $msg="The Brand has been updated successfully.";
            }
            else{
                $isSaved = Brand::create($req_data);
                $id = $isSaved->id;
                $msg="The Brand has been added successfully.";
            }

            if ($isSaved) {

                if($request->hasFile('image')) {
                    $file = $request->file('image');
                    $image_result = $this->saveImage($id, $file, $type='image');

                    if(!$image_result['success']){
                        //$msg="The Brand has been added successfully. But Image Can not be Added.";
                        session()->flash('alert-danger', 'Image could not be added');
                    }
                }
                if($request->hasFile('icon')) {
                    $file = $request->file('icon');
                    $image_result = $this->saveImage($id, $file, $type='icon');

                    if(!$image_result['success']){
                        //$msg="The Brand has been added successfully. But Ican Can not be Added.";
                        session()->flash('alert-danger', 'Icon could not be added');
                    }
                }

                cache()->forget('brands');

                return redirect(url('admin/brands'))->with('alert-success', $msg);
            } else {
                return back()->with('alert-danger', 'The Brands cannot be added, please try again or contact the administrator.');
            }
        }
        $data = [];
        $data['page_heading'] = $title;
        $data['brand'] = $brand;
        $data['id'] = $id;
        return view('admin.brands.form', $data);
    }


    public function saveImage($id,$file, $type){
        //prd($type); 
        //echo $id; die;

        $result['org_name'] = '';
        $result['file_name'] = '';
        $is_uploaded = '';
        if ($file) {

            if($type == 'image'){

                $path = 'brands/';
                $thumb_path = 'brands/thumb/';
                $IMG_HEIGHT = CustomHelper::WebsiteSettings('BRAND_IMG_HEIGHT');
                $IMG_WIDTH = CustomHelper::WebsiteSettings('BRAND_IMG_WIDTH');
                $THUMB_HEIGHT = CustomHelper::WebsiteSettings('BRAND_THUMB_HEIGHT');
                $THUMB_WIDTH = CustomHelper::WebsiteSettings('BRAND_THUMB_WIDTH');

                $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
                $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
                $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
                $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

                $uploaded_data = CustomHelper::UploadImage($file, $path, $ext='',$IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

                if($uploaded_data['success']){
                    $new_image = $uploaded_data['file_name'];

                    if(is_numeric($id) && $id > 0){
                        $brand = Brand::find($id);

                        if(!empty($brand) && count($brand) > 0){

                            $storage = Storage::disk('public');
                            $old_image = $brand->image;
                            $brand->image = $new_image;
                            $isUpdated = $brand->save();
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
            }
            else if($type == 'icon'){

               $path = 'brands/icon/';
               $IMG_HEIGHT = CustomHelper::WebsiteSettings('BRAND_ICON_HEIGHT');
               $IMG_WIDTH = CustomHelper::WebsiteSettings('BRAND_ICON_WIDTH');

               $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:300;
               $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:300;

               $uploaded_data = CustomHelper::UploadImage($file, $path, $ext='',$IMG_WIDTH, $IMG_HEIGHT, $is_thumb=false, $thumb_path='', $thumb_width='', $thumb_hight='');

                  if($uploaded_data['success']){
                    $new_image = $uploaded_data['file_name'];

                    if(is_numeric($id) && $id > 0){
                        $brand = Brand::find($id);

                        if(!empty($brand) && count($brand) > 0){

                            $storage = Storage::disk('public');
                            $old_icon = $brand->icon;
                            $brand->icon = $new_image;
                            $isUpdated = $brand->save();
                            if($isUpdated){

                                if(!empty($old_icon) && $storage->exists($path.$old_icon)){
                                    $storage->delete($path.$old_icon);
                                }
                            }
                        }
                    }
                }
           }
           if(!empty($uploaded_data)){
             return $uploaded_data;
         }   
        }
}

    public function ajax_delete_image(Request $request){
        //prd($request->toArray());
        $result['success'] = false;

        $image_id = ($request->has('image_id'))?$request->image_id:0;
        $type = ($request->has('type'))?$request->type:'';

        if (is_numeric($image_id) && $image_id > 0 && $type =='image') {
            $is_img_deleted = $this->delete_images($image_id, $type);
            if($is_img_deleted)
            {
                $result['success'] = true;
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Brand image has been delete successfully.</div>';
            }
        }

        else if(is_numeric($image_id) && $image_id > 0 && $type =='icon'){
             $is_img_deleted = $this->delete_images($image_id, $type);
            if($is_img_deleted)
            {
                $result['success'] = true;
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Brand Icon has been delete successfully.</div>';
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

        if($method=="POST"){
            if(is_numeric($id) && $id > 0)
            {
                $brand = Brand::find($id);
                if(!empty($brand) && count($brand) > 0){

                    $countProducts = $brand->countProducts();

                    if($countProducts > 0)
                    {
                        return back()->with('alert-danger', 'This Brand cannot be removed because there are currently ' .$countProducts. ' products associated with it. Please remove the products first.');
                    }

                    else{

                        $storage = Storage::disk('public');
                        $path = 'brands/';

                        if(count($brand) > 0 && !empty($brand->image))
                        {   
                            $image = $brand->image;
                            if(!empty($image) && $storage->exists($path.'thumb/'.$image))
                            {
                                $is_deleted = $storage->delete($path.'thumb/'.$image);
                            }
                            if(!empty($image) && $storage->exists($path.$image))
                            {
                                $is_deleted = $storage->delete($path.$image);
                            }
                        }

                        if(count($brand) > 0 && !empty($brand->icon))
                        {    
                            $icon = $brand->icon;
                            if(!empty($icon) && $storage->exists($path.'icon/'.$icon))
                            {
                                $is_deleted = $storage->delete($path.'icon/'.$icon);
                            }
                        }

                        $is_deleted = $brand->delete();
                    }

                }
            }
        }

        if($is_deleted){
            return redirect(url('admin/brands'))->with('alert-success', 'The Brand has been deleted successfully.');
        }
        else
        {
            return redirect(url('admin.brands'))->with('alert-danger', 'The Brand cannot be deleted, please try again or contact the administrator.');
        }

    }

    public function delete_images($id, $type)
    {        
        $is_deleted = '';
        $is_updated = '';
        $storage = Storage::disk('public');
        $path = 'brands/';
        $path_icon = 'brands/icon/';
        $brand = Brand::find($id);
        
        if(!empty($brand) && count($brand) > 0){

            $image = (isset($brand->image))?$brand->image:'';
            $icon = (isset($brand->icon))?$brand->icon:'';

            if($type == 'image'){
                if(!empty($image) && $storage->exists($path.'thumb/'.$image))
                {
                    $is_deleted = $storage->delete($path.'thumb/'.$image);
                }
                if(!empty($image) && $storage->exists($path.$image))
                {
                    $is_deleted = $storage->delete($path.$image);
                }
                if($is_deleted){
                    $brand->image = '';
                    $is_updated = $brand->save();   
               }
           }

           if($type == 'icon'){
            if(!empty($icon) && $storage->exists($path_icon.$icon))
            {
                $is_deleted = $storage->delete($path_icon.$icon);
            }
            if($is_deleted){
                $brand->icon = '';
                $is_updated = $brand->save();   
           }
       }
       return $is_updated;
   }
}

    /* end of controller */
}