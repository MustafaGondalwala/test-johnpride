<?php

namespace App\Http\Controllers\Admin;

use App\HomeImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Validator;
use Storage;


class HomeImageController extends Controller
{
    private $limit;
    public function __construct(){
        $this->limit = 20;
    }

    public function index(){

        $data = [];
        $limit = $this->limit;

        $homeImages = HomeImage::orderBy('created_at','desc')->paginate($limit);
        $data['homeImages'] = $homeImages;
        return view('admin.home_images.index', $data);

    }

    public function add(Request $request){
        
        $id = (isset($request->id))?$request->id:0;
        $homeImage = '';
        $title = 'Add Home Image';

        if(is_numeric($id) && $id > 0){
            $homeImage = HomeImage::find($id);
            $title = 'Edit Home Image';
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


            if(!empty($homeImage) && count($homeImage) > 0){
                $isSaved = HomeImage::where('id', $id)->update($req_data);
                $msg="The Home Image has been updated successfully.";
            }
            else{
                $isSaved = HomeImage::create($req_data);
                $id = $isSaved->id;
                $msg="The Home Image has been added successfully.";
            }

            if ($isSaved) {

                if($request->hasFile('image')) {
                    $file = $request->file('image');
                    $image_result = $this->saveImage($file, $id);

                    if($image_result['success']== false){
                         //$msg="The Home Image has been added successfully. But Image Can not be Added.";
                        session()->flash('alert-danger', 'Image could not be added');
                    }
                }

                cache()->forget('home_images');

                return redirect(url('admin/home_images'))->with('alert-success', $msg);
            } else {
                return back()->with('alert-danger', 'The Home Image cannot be added, please try again or contact the administrator.');
            }
        }
        $data = [];
        $data['page_heading'] = $title;
        $data['homeImage'] = $homeImage;
        $data['id'] = $id;
        return view('admin.home_images.form', $data);
    }


    public function saveImage($file, $id){
        //prd($file); 
        //echo $id; die;

        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) 
        {
            $path = 'home_images/';
            $thumb_path = 'home_images/thumb/';

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('HOMEIMAGE_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('HOMEIMAGE_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('HOMEIMAGE_THUMB_WIDTH');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('HOMEIMAGE_THUMB_HEIGHT');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

            $uploaded_data = CustomHelper::UploadImage($file, $path, $ext='', $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

            if($uploaded_data['success']){
                $new_image = $uploaded_data['file_name'];

                if(is_numeric($id) && $id > 0){
                    $HomeImage = HomeImage::find($id);

                    if(!empty($HomeImage) && count($HomeImage) > 0){

                        $storage = Storage::disk('public');

                        $old_image = $HomeImage->image;

                        $HomeImage->image = $new_image;

                        $isUpdated = $HomeImage->save();

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
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Home Image has been delete successfully.</div>';
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
        $path = 'home_images/';

        if($method=="POST"){
            if(is_numeric($id) && $id > 0)
            {
                $homeImage = HomeImage::find($id);

                if(!empty($homeImage) && count($homeImage) > 0){
 
                    if(count($homeImage) > 0 && !empty($homeImage->image))
                    {   
                       $image = $homeImage->image;
                       if(!empty($image) && $storage->exists($path.'thumb/'.$image))
                       {
                        $is_deleted = $storage->delete($path.'thumb/'.$image);
                    }
                    if(!empty($image) && $storage->exists($path.$image))
                    {
                        $is_deleted = $storage->delete($path.$image);
                    }
                }
                $is_deleted = $homeImage->delete();
            
        }
    }
 }   
        if($is_deleted){
            return redirect(url('admin/home_images'))->with('alert-success', 'The Home Image has been deleted successfully.');
        }else
        {
            return redirect(url('admin.home_images'))->with('alert-danger', 'The Home Image cannot be deleted, please try again or contact the administrator.');
        }

    }

    public function delete_images($id)
    {   
        $is_deleted = '';
        $is_updated = '';
        $storage = Storage::disk('public');
        $path = 'home_images/';
        $homeImage = HomeImage::find($id);
        
        $image = (isset($homeImage->image))?$homeImage->image:'';
            if(!empty($image) && $storage->exists($path.'thumb/'.$image))
            {
                $is_deleted = $storage->delete($path.'thumb/'.$image);
            }
            if(!empty($image) && $storage->exists($path.$image))
            {
                $is_deleted = $storage->delete($path.$image);
            }
            if($is_deleted){
                $homeImage->image = ''; 
               $is_updated = $homeImage->save();
               
           }
           return $is_updated;
    }

    /* end of controller */
}