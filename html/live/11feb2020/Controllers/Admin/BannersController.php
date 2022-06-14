<?php

namespace App\Http\Controllers\Admin;

use App\Banner;
use App\BannerImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Helpers\CustomHelper;

use Validator;
use Storage;
use Image;

class BannersController extends Controller{

    private $page_arr;
    private $limit;

    public function __construct(){
        $this->page_arr = array(
            'home'=>'Home',
        );
        $this->limit = 20;
    }

    public function index(){

        $data = [];
        $limit = $this->limit;

        $banners = Banner::orderBy('created_at','desc')->paginate($limit);
        //pr($categories->toArray());

        $data['banners'] = $banners;
        $data['page_arr'] = $this->page_arr;

        return view('admin.banners.index', $data);

    }

    public function add(Request $request){
        $banner_id = (isset($request->banner_id))?$request->banner_id:0;
        $banner = '';
        $banner_images = '';
        $title = 'Add Banner';

        if(is_numeric($banner_id) && $banner_id > 0){
            $banner = Banner::find($banner_id);
            $banner_images = BannerImage::where('banner_id', $banner_id)->get();
            $title = 'Edit Banner';
        }
        if($request->method() == 'POST' || $request->method() == 'post'){

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('BANNER_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('BANNER_IMG_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:1600;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:640;
            
            $rules = [];
            $validation_msg = [];

            $rules['title'] = 'required';
            $rules['page'] = 'required';
            $rules['device_type'] = 'required';
            $rules['status'] = 'required';

            $ext = 'jpg,jpeg,png,gif';

            $rules['image.*'] = 'nullable|image|mimes:'.$ext.'|dimensions:max_width='.$IMG_WIDTH.',max_height='.$IMG_HEIGHT;

            $validation_msg['image.*.dimensions'] = 'Image width/height should be '.$IMG_WIDTH.'/'.$IMG_HEIGHT.'px';

            if($request->page == 'home_link'){
                $rules['link'] = 'required';
            }
            $this->validate($request, $rules, $validation_msg);

            $req_data = [];
            $req_data = $request->except(['_token', 'image', 'back_url', 'old_image','banner_id']);

            if(!empty($banner) && count($banner) > 0){
                $isSaved = Banner::where('id', $banner->id)->update($req_data);
                $msg="The Banner has been updated successfully.";
            }
            else{
                $isSaved = Banner::create($req_data);
                $banner_id = $isSaved->id;
                $msg="The Banner has been added successfully.";
            }

            if($request->hasFile('image')) {
                $files = $request->file('image');
                $images_result = $this->saveImages($files, $banner_id, $ext);
            }

            if ($isSaved) {

                cache()->forget('banners');

                return redirect(url('admin/banners'))->with('alert-success', $msg);
            } else {
                return back()->with('alert-danger', 'The Banner cannot be added, please try again or contact the administrator.');
            }
        }

        $data = [];
        $data['page_heading'] = $title;
        $data['banner'] = $banner;
        $data['banner_images'] = $banner_images;
        $data['banner_id'] = $banner_id;
        $data['page_arr'] = $this->page_arr;

        return view('admin.banners.form', $data);
    }


    public function saveImages($files, $banner_id, $ext='jpg,jpeg,png,gif'){

        $is_inserted = '';

        if ($files && count($files) > 0) {

            //prd($files);

            $path = 'banners/';
            $thumb_path = 'banners/thumb/';

            //UploadImage($file, $path, $ext='', $width=768, $height=768, $is_thumb=false, $thumb_path, $thumb_width=300, $thumb_height=300)

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('BANNER_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('BANNER_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('BANNER_THUMB_HEIGHT');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('BANNER_THUMB_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:1600;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:640;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:400;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:400;

            $images_data = [];

            foreach($files as $file){
                $upload_result = CustomHelper::UploadImage($file, $path, $ext, $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

                if($upload_result['success']){
                    $images_data[] = array(
                        'banner_id' => $banner_id,
                        'name' => $upload_result['file_name']
                    );
                }
            }

            if(!empty($images_data) && count($images_data) > 0){
                $is_inserted = BannerImage::insert($images_data);
            }

        }

        return $is_inserted;

    }


    public function ajax_delete_image(Request $request){

        $result['success'] = false;

        $image_id = ($request->has('image_id'))?$request->image_id:0;

        if (is_numeric($image_id) && $image_id > 0) {
            $is_img_deleted = $this->delete_banner_images($image_id);
            if($is_img_deleted)
            {
                $result['success'] = true;
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Banner image has been delete successfully.</div>';
            }
        }

        if($result['success'] == false){
            $result['msg'] = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Something went wrong, please try again.</div>';
        }
        return response()->json($result);
    }

    public function delete(Request $request){
        $banner_id=$request->banner_id;
        $method=$request->method();
        $is_deleted = 0;

        if($method=="POST"){
            if(is_numeric($banner_id) && $banner_id > 0){
                $select_banner = Banner::where('id', $banner_id);
                $banner = $select_banner->get();
                $select_banner_img = BannerImage::where('banner_id', $banner_id)->get();
                if(count($select_banner_img) > 0){
                    foreach ($select_banner_img as $img) {
                        $image_id = $img->id;
                        $this->delete_banner_images($image_id);
                    }
                }
                $is_deleted = $select_banner->delete();
            }
        }
        
        if($is_deleted){
            return redirect(url('admin/banners'))->with('alert-success', 'The Banner has been deleted successfully.');
        }
        else{
            return redirect(url('admin.banners'))->with('alert-danger', 'The banner cannot be deleted, please try again or contact the administrator.');
        }

    }

    public function delete_banner_images($id){
        $storage = Storage::disk('public');
        $path = 'banners/';
        $banner = BannerImage::where('id', $id)->first();
        $image = (isset($banner['name']))?$banner['name']:'';

        $is_deleted = $banner->delete();

        if($is_deleted){
            if(!empty($image) && $storage->exists($path.'thumb/'.$image)){
                $is_deleted = $storage->delete($path.'thumb/'.$image);
            }
            if(!empty($image) && $storage->exists($path.$image)){
                $is_deleted = $storage->delete($path.$image);
            }
            return true;
        }
        else{
            return false;
        }
    }


    /* end of controller */
}