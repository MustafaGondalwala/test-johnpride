<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Category;
use App\CategoryImage;
use App\CategoryAttribute;

use Illuminate\Http\Request;

use Validator;
use Storage;

use App\Helpers\CustomHelper;
use App\Exports\CategoriesExport;
use Maatwebsite\Excel\Facades\Excel;

use Image;

class CategoryController extends Controller{

    /**
     * Admin - Store Category
     * URL: /admin/categories (POST)
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){
        //prd($request->toArray());
        

        $data = [];
        $export_xls = (isset($request->export_xls))?$request->export_xls:'';


        $type = (isset($request->type))?$request->type:'';

        $limit = $this->limit;

        $parent_id = (isset($request->parent_id))?$request->parent_id:'';

        //$categories = Category::where(['parent_id'=>$parent_id])->paginate($limit);

        $query = Category::query();
        if(!empty($export_xls) && ($export_xls == 1 || $export_xls == '1') ){
            return $this->exportXls($query);
        }



        $categories = $query->where(['parent_id'=>$parent_id])->paginate($limit);

        $parentCategory = '';

        if(is_numeric($parent_id) && $parent_id > 0){
            $parentCategory = Category::where('id', $parent_id)->first();
        }

        $countParents = 0;

        if(!empty($parentCategory) && count($parentCategory) > 0){
            $countParents++;
            if($parentCategory->parent && count($parentCategory->parent) > 0){
                $countParents++;
                $pParentCategory = $parentCategory->parent;
                if($pParentCategory->parent && count($pParentCategory->parent) > 0){
                    $countParents++;
                    $ppParentCategory = $pParentCategory->parent;
                }
            }
        }

        //die;
        
        $data['type'] = $type;
        $data['categories'] = $categories;
        $data['parent_id'] = $parent_id;
        $data['parentCategory'] = $parentCategory;
        $data['countParents'] = $countParents;
        $data['Category_model'] = new Category;

        return view('admin.categories.index', $data);

    }

    public function add(Request $request){
       //prd($request->toArray());

        $data = [];

        $category_id = (isset($request->id))?$request->id:0;

        $parent_id = (isset($request->parent_id))?$request->parent_id:0;

        $category = '';

        if(is_numeric($category_id) && $category_id > 0){
            $category = Category::where('id', $category_id)->first();
            if(!isset($category->id) || $category->id != $category_id){
                return redirect('admin/categories');
            }
        }

        //prd($category->toArray());

        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            $images_remove = (isset($request->images_remove))?$request->images_remove:'';

            if(empty($back_url)){
                $back_url = 'admin/categories?type';
            }

            $parent_id = (isset($request->parent_id))?$request->parent_id:'';
            $featured = (isset($request->featured))?$request->featured:'0';
            $category_id = (isset($request->category_id))?$request->category_id:0;
            $is_default = (isset($request->is_default))?$request->is_default:0;

            $attr_labels_arr = (isset($request->attr_labels))?$request->attr_labels:'';

            //prd($headings_arr);

            $rules = [];
            
            $rules['name'] = 'required';
            $rules['status'] = 'required';

            $ext = 'jpg,jpeg,png,gif';

            if ($request->hasFile('images')) {
                $images = $request->file('images');

                $rules['images'] = 'required';
                //$rules['image.*'] = 'image|mimes:jpeg,jpg,png,gif|max:2048';
                $rules['images.*'] = 'image|mimes:'.$ext;
            }

            $this->validate($request, $rules);

            //prd($request->toArray());

            $req_data = [];

            $req_data = $request->except(['_token', 'category_id', 'images', 'images_remove', 'is_default', 'count_images', 'attr_labels', 'attr_sort_order', 'back_url']);

            $slug = CustomHelper::GetSlug('categories', 'id', $category_id, $request->name);

            $req_data['parent_id'] = $parent_id;
            $req_data['slug'] = $slug;
            $req_data['featured'] = $featured;

            //pr($images_remove);

            //prd($req_data);

            if(!empty($category->id) && $category->id == $category_id){
                $isSaved = Category::where('id', $category->id)->update($req_data);
            }
            else{
                $isSaved = Category::create($req_data);

                $category_id = $isSaved->id;
            }
            
            if ($isSaved) {

                if ($request->hasFile('images')) {

                    $images = $request->file('images');

                    $upload_result = $this->saveImages($images, $category_id, $ext);
                }

                $this->setDefautImage($is_default, $category_id);
                $this->saveCategoryAttributes($request, $category_id);

                if(!empty($images_remove) && count($images_remove) > 0){
                    $category_images = CategoryImage::whereIn('id', $images_remove)->get();
                    $this->deleteImages($category_images);
                }


                if(is_numeric($parent_id) && $parent_id > 0){
                    $parent_category = Category::find($parent_id);
                    if(!empty($parent_category) && count($parent_category) > 0){
                        $parent_category->having_child = 1;
                        $parent_category->save();
                    }
                }
            }


            if ($isSaved) {
                return redirect(url($back_url))->with('alert-success', 'The category has been saved successfully.');
            } else {
                return back()->with('alert-danger', 'The category cannot be added, please try again or contact the administrator.');
            }
        }

        $parent_category = '';

        if(is_numeric($parent_id) && $parent_id > 0){
            $parent_category = Category::find($parent_id);
        }

        $page_heading = 'Add Category';

        if(isset($category->name)){
             $page_heading = 'Update Category - '.$category->name;
        }

        $data['page_heading'] = $page_heading;
        $data['category_id'] = $category_id;
        $data['category'] = $category;
        $data['parent_id'] = $parent_id;
        $data['parent_category'] = $parent_category;

        return view('admin.categories.form', $data);

    }

    public function saveCategoryAttributes($request='', $category_id=0){

        $attr_labels_arr = (isset($request->attr_labels))?$request->attr_labels:'';
        $attr_sort_arr = (isset($request->attr_sort_order))?$request->attr_sort_order:'';
        if(!empty($attr_labels_arr) && count($attr_labels_arr) > 0){

            //prd($attr_labels_arr);

            $attrData = [];

            foreach($attr_labels_arr as $attr_key=>$attr_label){

                $attr_label = trim($attr_label);

                if(!empty($attr_label)){

                    $sort_order = (isset($attr_sort_arr[$attr_key]))?$attr_sort_arr[$attr_key]:0;
                    $attrData[] = array(
                        'category_id' => $category_id,
                        'label' => $attr_label,
                        'sort_order' => $sort_order,
                    );
                }
            }
            //prd($attrData);

            if(!empty($attrData) && count($attrData) > 0){

                CategoryAttribute::where('category_id', $category_id)->delete();

                CategoryAttribute::insert($attrData);

            }
        }
    }

    public function setDefautImage($imageIDForDefault=0, $category_id=0){

        //prd($category_id);

        if(is_numeric($imageIDForDefault) && $imageIDForDefault > 0){
            
            $category_image = CategoryImage::find($imageIDForDefault);

            if(isset($category_image->is_default) && $category_image->is_default != '1'){

                CategoryImage::where(['category_id'=>$category_id])->update(['is_default'=>'']);

                $category_image->is_default = 1;
                $category_image->save();

            }
        }
        elseif(is_numeric($category_id) && $category_id > 0){
            $category_images = CategoryImage::where(['category_id'=>$category_id])->get();

            if(!empty($category_images) && count($category_images) > 0){

                $default_image = $category_images->where('is_default', 1);

                if(empty($default_image) || count($default_image) == 0){
                    $first_image = $category_images->first();

                    if(!empty($first_image) && count($first_image) > 0){
                        $first_image->is_default = 1;
                        $first_image->save();
                    }
                }
            }

        }
    }

    public function saveImages($files, $category_id, $ext='jpg,jpeg,png,gif'){

        //echo url('public/uploads'); die;

        $is_inserted = '';

        $category = '';

        if(is_numeric($category_id) && $category_id > 0){
            $category = Category::find($category_id);
        }

        //if ($file && !empty($category) && count($category) > 0) {
        if ($files && count($files) > 0) {

            //prd($files);

            $path = 'categories/';
            $thumb_path = 'categories/thumb/';

            //UploadImage($file, $path, $ext='', $width=768, $height=768, $is_thumb=false, $thumb_path, $thumb_width=300, $thumb_height=300)

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('CATEGORY_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('CATEGORY_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('CATEGORY_THUMB_HEIGHT');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('CATEGORY_THUMB_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

            $images_data = [];

            foreach($files as $file){
                $upload_result = CustomHelper::UploadImage($file, $path, $ext, $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

                if($upload_result['success']){
                    $images_data[] = array(
                        'category_id' => $category_id,
                        'image' => $upload_result['file_name']
                    );
                }
            }

            if(!empty($images_data) && count($images_data) > 0){
                $is_inserted = CategoryImage::insert($images_data);
            }

        }

        return $is_inserted;

    }

    public function delete($category_id){
        //prd($category_id);

        $category = Category::find($category_id);
        //prd($category->allProducts()->count());
        // The Category must not have any associated Products to be deleted
        if ($category->products() && $category->products()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->products()->count() . ' products associated with it. Please remove the products first.');
        }
        // The Category must not have any associated Sub-categories to be deleted
        if ($category->children() && $category->children()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->children()->count() . ' sub-categories associated with it. Please remove the sub-categories first.');
        }
        else {
            $is_deleted = $category->delete();

            if($is_deleted){
                $category_images = CategoryImage::where('category_id', $category_id)->get();
                $this->deleteImages($category_images);
            }
            
            CategoryAttribute::where('category_id', $category_id)->delete();

            return back()->with('alert-success', 'The category has been removed successfully.');
        }
    }

    public function deleteImages($category_images=''){

        if(!empty($category_images) && count($category_images) > 0){

            $storage = Storage::disk('public');

            $path = 'categories/';
            $thumb_path = 'categories/thumb/';

            foreach($category_images as $ci){

                $image = $ci->image;

                if(!empty($image) && $storage->exists($path.$image)){
                    $is_deleted = $storage->delete($path.$image);
                }

                if(!empty($image) && $storage->exists($thumb_path.$image)){
                    $is_deleted = $storage->delete($thumb_path.$image);
                }

                $ci->delete();
            }
        }
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

    private function exportXls($query){

        $fieldNames = ['id','name'];

        $categories = $query->get();

        $exportArr = [];

        if(!empty($categories) && $categories->count() > 0){
            foreach($categories as $category){
                //prd($color->toArray());
               
                $sizeArr = [];
                $sizeArr['id'] = $category->id;
                $sizeArr['name'] = $category->name;
                

                $exportArr[] = $sizeArr;
            }
        }

        $fieldNames = array_keys($exportArr[0]);

        //prd($exportArr);

        $fileName = 'categories_'.date('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new CategoriesExport($exportArr, $fieldNames), $fileName);
    }

    /* end of controller */
}