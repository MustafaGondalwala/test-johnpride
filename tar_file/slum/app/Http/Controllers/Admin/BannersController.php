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
            $rules['title'] = 'required';
            $rules['page'] = 'required';
            $rules['device_type'] = 'required';
            $rules['status'] = 'required';

            if($request->page == 'home_link'){
                $rules['link'] = 'required';
            }
            $this->validate($request, $rules);
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

                if(!empty($files) && count($files)){
                    foreach($files as $file){
                        $images_result = $this->saveImage($banner_id, $file);
                    }
                }
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


    public function saveImage($banner_id, $file){
        //prd($file);
        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) 
        {
            $path = 'banners/';
            $thumb_path = 'banners/thumb/';

            $storage = Storage::disk('public');
            //prd($storage);
            $validator = Validator::make(['file' => $file], ['file' => 'mimes:jpg,jpeg,png']);

            if ($validator->passes()) {
                $handle = fopen($file, "r");
                $opening_bytes = fread($handle, filesize($file));

                fclose($handle);

                if( strlen(strpos($opening_bytes,'<?php')) > 0 && (strpos($opening_bytes,'<?php') >= 0 || strpos($opening_bytes,'<?PHP') >= 0) )
                {
                    $result['errors']['file'] = "Invalid image!";
                }
                else{

                    $IMG_HEIGHT = CustomHelper::WebsiteSettings('BANNER_IMG_HEIGHT');
                    $IMG_WIDTH = CustomHelper::WebsiteSettings('BANNER_IMG_WIDTH');
                    $THUMB_HEIGHT = CustomHelper::WebsiteSettings('BANNER_THUMB_HEIGHT');
                    $THUMB_WIDTH = CustomHelper::WebsiteSettings('BANNER_THUMB_WIDTH');

                    $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:1600;
                    $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:640;
                    $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:400;
                    $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:400;

                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    $fileName = date('dmyhis').'-'.$fileOriginalName;

                    $is_uploaded = Image::make($file)->resize($IMG_WIDTH, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('storage/'.$path . $fileName));

                    //prd($is_uploaded);

                    if($is_uploaded){

                        $thumb = Image::make($file)->resize($THUMB_WIDTH, $THUMB_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('storage/'.$thumb_path . $fileName));

                        $BannerImage = new BannerImage;
                        $BannerImage->banner_id = $banner_id;
                        $BannerImage->name = $fileName;
                        $BannerImage->save();

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                    }
                }
            }
            else
            {
                //prd($validator->errors()->toArray());
                $result['errors'] = $validator->errors();
            }
        }
        return $result;
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

    public function delete(Request $request)
    {
        $banner_id=$request->banner_id;
        $method=$request->method();
        $is_deleted = 0;

        if($method=="POST"){
            if(is_numeric($banner_id) && $banner_id > 0)
            {
                $select_banner = Banner::where('id', $banner_id);
                $banner = $select_banner->get();
                $select_banner_img = BannerImage::where('banner_id', $banner_id)->get();
                if(count($select_banner_img) > 0)
                {
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
        }else
        {
            return redirect(url('admin.banners'))->with('alert-danger', 'The banner cannot be deleted, please try again or contact the administrator.');
        }

    }

    public function delete_banner_images($id)
    {
        $storage = Storage::disk('public');
        $path = 'banners/';
        $banner = BannerImage::where('id', $id)->first();
        $image = (isset($banner['name']))?$banner['name']:'';

        $is_deleted = $banner->delete();

        if($is_deleted){
            if(!empty($image) && $storage->exists($path.'thumb/'.$image))
            {
                $is_deleted = $storage->delete($path.'thumb/'.$image);
            }
            if(!empty($image) && $storage->exists($path.$image))
            {
                $is_deleted = $storage->delete($path.$image);
            }
            return true;
        }else
        {
            return false;
        }
    }

    /* end of controller */
}