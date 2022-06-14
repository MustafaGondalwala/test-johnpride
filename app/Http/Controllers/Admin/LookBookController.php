<?php

namespace App\Http\Controllers\Admin;

use App\LookBook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Helpers\CustomHelper;


use Validator;
use Storage;
use Image;

class LookBookController extends Controller
{
    private $limit;
    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){

        $data = [];
        $limit = $this->limit;     

        $query = LookBook::query();
        $query->orderBy('created_at', 'desc');        
        $lookBooks = $query->paginate($limit);

        //prd($customerPictures);

        $data['lookBooks'] = $lookBooks;  
        return view('admin.look_book.index', $data);

    }

    public function add(Request $request){
        
        $id = (isset($request->id))?$request->id:0;
        $lookBook = '';
        $title = 'Add Look Book';

        if(is_numeric($id) && $id > 0){
            $lookBook = LookBook::find($id);
            $title = 'Edit Look Book';
        }
        if($request->method() == 'POST' || $request->method() == 'post'){   
        //prd($request->toArray()); die;         
            $ext = 'jpg,jpeg,png,gif';

            $rules['title'] = 'required';
            $rules['product_sku'] = 'required';
            $rules['url'] = 'required';
            $rules['status'] = 'required';
            $rules['image'] = 'nullable|image|mimes:'.$ext;
            

            $this->validate($request, $rules);
            $req_data = [];
            
            $req_data = $request->except(['_token', 'image', 'back_url', 'old_image','id']);
            $req_data['featured'] = isset($request->featured)?$request->featured:0;

            if(!empty($lookBook) && count($lookBook) > 0){
                $isSaved = LookBook::where('id', $id)->update($req_data);
                $msg="The Look Book has been updated successfully.";
            }
            else{
                $isSaved = LookBook::create($req_data);
                $id = $isSaved->id;
                $msg="The Look Book has been added successfully.";
            }

            if ($isSaved) {

                if($request->hasFile('image')) {
                    $file = $request->file('image');
                    $image_result = $this->saveImage($id, $file, $type='image');

                    if(!$image_result['success']){                        
                        session()->flash('alert-danger', 'Image could not be added');
                    }
                }
                
                cache()->forget('look-book');

                return redirect(url('admin/look-book'))->with('alert-success', $msg);
            } else {
                return back()->with('alert-danger', 'The Look Book cannot be added, please try again or contact the administrator.');
            }
        }
        $data = [];
        $data['page_heading'] = $title;
        $data['lookBook'] = $lookBook;
        $data['id'] = $id;
        return view('admin.look_book.form', $data);
    }


    public function saveImage($id,$file, $type){
        //prd($type); 
        //echo $id; die;

        $result['org_name'] = '';
        $result['file_name'] = '';
        $is_uploaded = '';
        if ($file) {

            if($type == 'image'){

                $path = 'look_book/';
                $thumb_path = 'look_book/thumb/';
                $IMG_HEIGHT = CustomHelper::WebsiteSettings('CUSTOMER_PICTURE_IMG_HEIGHT');
                $IMG_WIDTH = CustomHelper::WebsiteSettings('CUSTOMER_PICTURE_IMG_WIDTH');
                $THUMB_HEIGHT = CustomHelper::WebsiteSettings('CUSTOMER_PICTURE_THUMB_HEIGHT');
                $THUMB_WIDTH = CustomHelper::WebsiteSettings('CUSTOMER_PICTURE_THUMB_WIDTH');

                $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
                $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
                $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
                $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

                $uploaded_data = CustomHelper::UploadImage($file, $path, $ext='',$IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

                if($uploaded_data['success']){
                    $new_image = $uploaded_data['file_name'];

                    if(is_numeric($id) && $id > 0){
                        $lookBook = LookBook::find($id);

                        if(!empty($lookBook) && count($lookBook) > 0){

                            $storage = Storage::disk('public');
                            $old_image = $lookBook->image;
                            $lookBook->image = $new_image;
                            $isUpdated = $lookBook->save();
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
            
           if(!empty($uploaded_data)){
             return $uploaded_data;
         }   
        }
}

    public function ajax_delete_image(Request $request){
        //prd($request->toArray());
        $result['success'] = false;

        $image_id = ($request->has('image_id'))?$request->image_id:0;
        $type = ($request->has('type'))?$request->type:'image';

        if (is_numeric($image_id) && $image_id > 0 && $type =='image') {
            $is_img_deleted = $this->delete_images($image_id, $type);
            if($is_img_deleted)
            {
                $result['success'] = true;
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Look Book image has been delete successfully.</div>';
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
                $lookBook = LookBook::find($id);
                if(!empty($lookBook) && count($lookBook) > 0){

                    

                        $storage = Storage::disk('public');
                        $path = 'look_book/';

                        if(count($lookBook) > 0 && !empty($lookBook->image))
                        {   
                            $image = $lookBook->image;
                            if(!empty($image) && $storage->exists($path.'thumb/'.$image))
                            {
                                $is_deleted = $storage->delete($path.'thumb/'.$image);
                            }
                            if(!empty($image) && $storage->exists($path.$image))
                            {
                                $is_deleted = $storage->delete($path.$image);
                            }
                        }

                        

                        $is_deleted = $lookBook->delete();
                    

                }
            }
        }

        if($is_deleted){
            return redirect(url('admin/look-book'))->with('alert-success', 'The Look Book has been deleted successfully.');
        }
        else
        {
            return redirect(url('admin.look-book'))->with('alert-danger', 'The Look Book cannot be deleted, please try again or contact the administrator.');
        }

    }

    public function delete_images($id, $type='image')
    {        
        $is_deleted = '';
        $is_updated = '';
        $storage = Storage::disk('public');
        $path = 'look_book/';
        $lookBook = LookBook::find($id);
        
        if(!empty($lookBook) && count($lookBook) > 0){

            $image = (isset($lookBook->image))?$lookBook->image:'';

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
                    $lookBook->image = '';
                    $is_updated = $lookBook->save();   
               }
           }

          
       return $is_updated;
   }
}



    /* end of controller */
}