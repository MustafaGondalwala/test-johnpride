<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Product;
use App\User;
use App\ProductImage;
use App\ColorMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Storage;
use DB;
use App\Helpers\CustomHelper;

use Image;

class DesignController extends Controller{


    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request)
    {

        $data = [];

        $limit = $this->limit;

        $name = (isset($request->name))?$request->name:'';
        $category = (isset($request->category))?$request->category:'';
        $designer = (isset($request->designer))?$request->designer:'';

        $price_scope = (isset($request->price_scope))?$request->price_scope:'=';
        $price = (isset($request->price))?$request->price:'';

        $stock_scope = (isset($request->stock_scope))?$request->stock_scope:'=';
        $stock = (isset($request->stock))?$request->stock:'';

        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');


        $product_query = Product::orderBy('id', 'desc');

        $product_query->where('type', 'design');

        if(!empty($name)){
            $product_query->where(function($query) use($name){
                $query->where('name', 'like', '%'.$name.'%');
                $query->orWhere('sku', 'like', '%'.$name.'%');
            });
        }

        if(is_numeric($category) && $category > 0){
            //$product_query->where('category_id', $category);
         $product_query->whereRaw("id in (select product_id from product_to_category where category_id= $category)");

        }

        if(is_numeric($designer) && $designer >= 0){
            $product_query->where('user_id', $designer);
        }

        if(is_numeric($price) && $price > 0){
            $product_query->where('price', $price_scope, $price);
        }

        if(is_numeric($stock) && $stock > 0){
            $product_query->where('stock', $stock_scope, $stock);
        }

        if( strlen($status) > 0 ){
            $product_query->where('status', $status);
        }

        if(!empty($from_date)){
            $product_query->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $product_query->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }

        $products = $product_query->paginate($limit);

        $DesignersList = User::where(['type'=>'designer', 'status'=>1])->orderBy('first_name')->get();

        //prd($products);

        
        $data['products'] = $products;
        $data['DesignersList'] = $DesignersList;
        $data['limit'] = $limit;

        return view('admin.designs.index', $data);

    }


    public function add(Request $request)
    {
        //prd($request->toArray());

        $data = [];

        $selected_cat_ids=[];

        $product_id = (isset($request->id))?$request->id:0;

        $product = '';

        if(is_numeric($product_id) && $product_id > 0)
        {
            $product = Product::find($product_id);
        }

        $category_id = (isset($request->cid))?$request->cid:0;

        $selected_cat_ids[]=$category_id;
        

        $category = '';

        if(is_numeric($category_id) && $category_id > 0)
        {
            $category = Category::find($category_id);
            $category_id = $category->id;
        }

        
           

            if($request->method() == 'POST' || $request->method() == 'post')
            {

                //prd($request->toArray()); die;

                $back_url = (isset($request->back_url))?$request->back_url:'';

                if(empty($back_url)){
                    $back_url = 'admin/categories';
                }

                $req_product_id = (isset($request->product_id))?$request->product_id:'0';
                $featured = (isset($request->featured))?$request->featured:'0';

                $images_remove = (isset($request->images_remove))?$request->images_remove:'';
                $is_default = (isset($request->is_default))?$request->is_default:'';

                $images ='';
                if(is_numeric($req_product_id) && $req_product_id > 0)
                {
                    $products = Product::find($req_product_id);
                    $images = (isset($products->Images))?$products->Images:'';
                }

                $rules = [];

                $rules['name'] = 'required';
                $rules['default_fabric'] = 'required';
                $rules['p1_cat'] = 'required';
                $rules['color'] = 'required';
                $rules['designer'] = 'required|numeric';
                $rules['sort_order'] = 'required|numeric';
                $rules['is_approved'] = 'required';

                if(!(!empty($images) && count($images) > 0) || $req_product_id==0)
                {
                    $rules['images'] = 'required';
                }

                if(!empty($request->p1_cat))
                {
                    $sub_category_post = DB::table('categories')
                    ->where('parent_id', $request->p1_cat)
                    ->get();

                    if(!empty($sub_category_post) && $sub_category_post->count() > 0)
                    {
                        $rules['p2_cat'] = 'required';
                    }
                }

                if(!empty($request->p1_cat) &&  !empty($request->p2_cat))
                {
                    $sub_sub_category_post = DB::table('categories')
                    ->where('parent_id', $request->p2_cat)
                    ->get();

                    if(!empty($sub_sub_category_post) && $sub_sub_category_post->count() > 0)
                    {
                        $rules['category_id'] = 'required';
                    }
                }
                

                $this->validate($request, $rules);

                $req_data = [];

                $user_id = $request->designer;

                $req_data = $request->except(['_token', 'designer', 'product_id', 'cid', 'swatch_image', 'fat_image', 'meter_image', 'images', 'images_remove', 'is_default', 'back_url', 'p1_cat', 'p2_cat']);

                $req_data['user_id'] = $user_id;

                if($user_id==0)
                {
                    $req_data['is_approved']= 1; 
                    
                }

                $slug = CustomHelper::GetSlug('products', 'id', $product_id, $request->name);

                $req_data['type'] = 'design';
                $req_data['category_id'] = $category_id;

                $req_data['slug'] = $slug;
                $req_data['featured'] = $featured;
                $req_data['is_approved'] =$request->is_approved;
                

                

                if(!empty($product) && count($product) > 0 && $req_product_id == $product_id)
                {
                    $isSaved = Product::where('id', $product->id)->update($req_data);
                }
                else
                {
                    $isSaved = Product::create($req_data);
                    $product_id = $isSaved->id;
                }

                // Notify designer if their design , approve, disapproved, etc

                if(!empty($request->designer))
                {

               

                $DesignerData = User::where('id', $request->designer)->first();
                if(!empty($DesignerData))
                {
                   $to_email = $DesignerData->email;
                   $name = $DesignerData->first_name.' '.$DesignerData->last_name;
               
                
                if($request->is_approved ==0)
                {
                    $is_approved = 'Pending';
                }
                else if($request->is_approved ==1)
                {
                    $is_approved = 'Approved';
                }
                else if($request->is_approved ==2)
                {
                    $is_approved = 'Disapproved';
                }


                
                $subject = 'Your design status has been changed - Tex India';
                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                if(empty($ADMIN_EMAIL))
                {
                    $ADMIN_EMAIL = config('custom.admin_email');
                }

                $from_email = $ADMIN_EMAIL; 

                $email_data = [];
                $email_data['name'] = $name;
                $email_data['design_name'] = $request->name;
                $email_data['is_approved'] = $is_approved;
               
                $is_mail = CustomHelper::SendMail('emails.design.design_change_status', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);
               }

            }

                //end notification email 




















                $this->remove_images($images_remove, $product_id);
                $this->make_default_image($is_default, $product_id);

                if ($request->hasFile('swatch_image')) {
                    $file = $request->file('swatch_image');
                    $images_result = $this->save_image($file, $product_id, 'swatch');
                }

                if ($request->hasFile('fat_image')) {
                    $file = $request->file('fat_image');
                    $images_result = $this->save_image($file, $product_id, 'fat');
                }

                if ($request->hasFile('meter_image')) {
                    $file = $request->file('meter_image');
                    $images_result = $this->save_image($file, $product_id, 'meter');
                }

                if ($request->hasFile('images')) {

                    $files = $request->file('images');

                    if(!empty($files) && count($files)){
                        foreach($files as $file){
                            $images_result = $this->save_image($file, $product_id);
                        }
                    }
                }
                
                
                if(!empty($product_id))
                {
                     //pr($_POST); exit; 

                     DB::table('product_to_category')->where('product_id', '=', $product_id)->delete();

                     if(!empty($request->p1_cat) &&  !empty($request->p2_cat))
                     {

                           $cat_insert_cat_data=[]; 

                           $p1_cat=$request->p1_cat;
                           $p2_cat=$request->p2_cat;
                           $cat_id= 0;
                           if(!empty($request->category_id))
                           {

                             $c_count=0;
                             foreach($request->category_id as $c_id)
                             {

                                        $cat_insert_cat_data[$c_count]['product_id']= $product_id;
                                        $cat_insert_cat_data[$c_count]['p1_cat']= $p1_cat;
                                        $cat_insert_cat_data[$c_count]['p2_cat']= $p2_cat;
                                        $cat_insert_cat_data[$c_count]['category_id']=$c_id;

                                        $c_count++;

                             }



                           }
                           else
                           {

                            $cat_insert_cat_data[0]['product_id']= $product_id;
                            $cat_insert_cat_data[0]['p1_cat']= $p1_cat;
                            $cat_insert_cat_data[0]['p2_cat']= $p2_cat;
                            $cat_insert_cat_data[0]['category_id']=0;
                           }

                            if(!empty($cat_insert_cat_data))
                            {
                                DB::table('product_to_category')->insert($cat_insert_cat_data);

                            }

                     }

                     

                }
                
                
                if ($isSaved) 
                {

                    return redirect(url('admin/designs'))->with('alert-success', 'The Design has been saved successfully.');
                } else {
                    return back()->with('alert-danger', 'The Design cannot be added, please try again or contact the administrator.');
                }
            }

            $fabrics = Product::where('type', 'fabric')->orderBy('name')->get();

            $ColorsMaster = ColorMaster::where(['parent_id'=>0])->orderBy('name')->get();

            $DesignersList = User::where(['type'=>'designer', 'status'=>1])->orderBy('first_name')->get();

            $category_name = (isset($category->name))?$category->name:'';

            $page_heading = 'Add Design';
            if(isset($product->name)){
                $page_heading = 'Edit Design - '.$product->name;
            }

            if(!empty($category_name)){
                $page_heading .= ' ('.$category_name.')';
            }


            //as per new cat dropdown
            $p1_cat_ids_arr=[];
            $p2_cat_ids_arr=[];
            $sub_category="";
            $sub_sub_category="";

            if(!empty($product_id))
            {

                $exist_cat_result= DB::table('product_to_category')->where(['product_id'=>$product_id])->get();
                if(!empty($exist_cat_result))
                {
                     foreach($exist_cat_result as $ex )
                     {
                         $selected_cat_ids[]=$ex->category_id;
                         $p1_cat_ids_arr[]=$ex->p1_cat;
                         $p2_cat_ids_arr[]=$ex->p2_cat;

                     }

                }

                if(!empty($p1_cat_ids_arr))
                {
                    $sub_category = DB::table('categories')
                    ->whereIn('parent_id', $p1_cat_ids_arr)
                    ->get();
                }

                if(!empty($p2_cat_ids_arr))
                {
                    $sub_sub_category = DB::table('categories')
                    ->whereIn('parent_id', $p2_cat_ids_arr)
                    ->get();
                }
                //pr($sub_sub_category); exit;



                //pr($sub_category);exit;
                




            }

            $data['design_category'] = Category::where(['type'=>'design', 'status'=>1])->get();

            

            $data['page_heading'] = $page_heading;
            $data['product_id'] = $product_id;
            $data['product'] = $product;
            $data['fabrics'] = $fabrics;
            $data['ColorsMaster'] = $ColorsMaster;
            $data['category'] = $category;
            
            $data['category_id'] = $category_id;
            $data['DesignersList'] = $DesignersList;

            
            $data['sub_category'] =$sub_category;
            $data['sub_sub_category'] =$sub_sub_category;
            
            $data['p1_cat_ids_arr'] = $p1_cat_ids_arr;
            $data['p2_cat_ids_arr'] = $p2_cat_ids_arr;

            $data['selected_cat_ids'] = $selected_cat_ids;

            $parent_design_category=Category::where(['type'=>'design', 'parent_id'=>0])->get();

            $data['parent_design_category']= $parent_design_category;
            
            return view('admin.designs.form', $data);

        

        return back();

    }

    public function delete($category_id){
        //prd($category_id);

        $category = Category::find($category_id);
        //prd($category->allProducts()->count());
        // The Category must not have any associated Products to be deleted
        if ($category->allProducts() && $category->allProducts()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->allProducts()->count() . ' products associated with it. Please remove the products first.');
        }
        // The Category must not have any associated Sub-categories to be deleted
        if ($category->children() && $category->children()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->children()->count() . ' sub-categories associated with it. Please remove the sub-categories first.');
        }
        else {
            $category->delete();

            return back()->with('alert-success', 'The category has been removed successfully.');
        }
    }

    public function save_image($file, $id, $img_for=''){

        //echo url('public/uploads'); die;

        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) {

            $path = 'designs/';
            $thumb_path = 'designs/thumb/';

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

                    $IMG_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_IMG_HEIGHT');
                    $IMG_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_IMG_WIDTH');
                    $THUMB_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_THUMB_HEIGHT');
                    $THUMB_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_THUMB_WIDTH');

                    $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
                    $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
                    $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
                    $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    $fileName = date('dmyhis').'-'.$fileOriginalName;

                    $is_uploaded = Image::make($file)->resize($IMG_WIDTH, $IMG_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('storage/'.$path . $fileName));

                    if($is_uploaded){

                        $thumb = Image::make($file)->resize($THUMB_WIDTH, $THUMB_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('storage/'.$thumb_path . $fileName));

                        $product = Product::find($id);

                        if(!empty($product) && count($product) > 0){

                            $defaultImage = $product->defaultImage;

                            $ProductImage = new ProductImage;

                            $ProductImage->product_id = $id;
                            $ProductImage->type = 'design';
                            $ProductImage->img_for = $img_for;
                            $ProductImage->name = $fileName;

                            if(isset($defaultImage->is_default) && $defaultImage->is_default == 1){
                                $ProductImage->is_default = 0;
                            }
                            else{
                                $ProductImage->is_default = 1;
                            }

                            $ProductImage->save();
                        }

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                    }
                }
            }
            else{
                $result['errors'] = $validator->errors();
            }

        }

        return $result;

    }


    public function ajax_delete_image(Request $request){
        //prd($request->toArray());

        $response = [];

        $response['success'] = false;

        $message = '';
        $id = (isset($request->id))?$request->id:0;

        $is_deleted = 0;

        if(is_numeric($id) && $id > 0){

            $category = Category::find($id);

            //prd($category->toArray());

            if(!empty($category) && count($category) > 0){

                $storage = Storage::disk('public');

                $path = 'categories/';
                $thumb_path = 'categories/thumb/';

                $image = $category->image;

                if(!empty($image) && $storage->exists($path.$image)){
                    $is_deleted = $storage->delete($path.$image);
                }

                if(!empty($image) && $storage->exists($thumb_path.$image)){
                    $is_deleted = $storage->delete($thumb_path.$image);
                }

                if($is_deleted){
                    $category->image = '';
                    $category->save();
                }
            }
        }

        if($is_deleted){

            $response['success'] = true;

            $message = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Image has been deleted succesfully. </div';
        }
        else{
            $message = '<div class="alert alert-danger alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Something went wrong, please try again... </div';
        }

        $response['message'] = $message;

        return response()->json($response);

    }




    public function remove_images($image_id_arr, $product_id){

        if(is_numeric($product_id) && $product_id > 0 && !empty($image_id_arr) && count($image_id_arr) > 0 ){

            $path = 'designs/';
            $thumb_path = 'designs/thumb/';

            $storage = Storage::disk('public');

            foreach($image_id_arr as $id){
                $image = ProductImage::where(['id'=>$id, 'product_id'=>$product_id])->first();

                if(isset($image->id) && $image->product_id == $product_id){
                    $image_name = $image->name;

                    $image->delete();

                    if(!empty($image_name) && $storage->exists($path.$image_name)){
                        $storage->delete($path.$image_name);
                    }

                    if(!empty($image_name) && $storage->exists($thumb_path.$image_name)){
                        $storage->delete($thumb_path.$image_name);
                    }
                }
            }
        }
    }

    public function make_default_image($image_id, $product_id){

         if(is_numeric($product_id) && $product_id > 0 && is_numeric($product_id) && $product_id > 0 ){
            $image = ProductImage::where(['id'=>$image_id, 'product_id'=>$product_id])->first();

            if(isset($image->id) && $image->product_id == $product_id){

                $image->is_default = 1;
                $image->save();

                ProductImage::where('product_id', $product_id)->where('id', '!=', $image_id)->update(['is_default'=>0]);
            }           
        }
    }



    /* end of controller */
}