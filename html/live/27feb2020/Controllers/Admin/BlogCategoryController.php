<?php

namespace App\Http\Controllers\Admin;

use App\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Storage;
use App\Helpers\CustomHelper;
use DB;
use Image;

class BlogCategoryController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $parent_id = (isset($request->parent_id))?$request->parent_id:'';

        $category_query = BlogCategory::orderBy('id', 'desc');

        if($parent_id !='' && $parent_id>0)
        {
            $category_query->where('parent_id', $parent_id);
        }
        $categories = $category_query->paginate($limit);
        $data['categories'] = $categories;
        $data['limit'] = $limit;
        return view('admin.blogs_categories.index', $data);

    }


    public function add(Request $request){
        //prd($request->toArray());
        $data = [];
        $id = (isset($request->id))?$request->id:0;
        $category = '';

        if(is_numeric($id) && $id > 0){
            $category = BlogCategory::find($id);
        }

        if($request->method() == 'POST' || $request->method() == 'post'){
            //prd($request->toArray());

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = 'admin/blogs_categories';
            }

            $req_id = (isset($request->id))?$request->id:0;

            $rules = [];
            $rules['name'] = 'required';
            $rules['status'] = 'required';

            $this->validate($request, $rules);

            $req_data = [];

            $req_data = $request->except(['_token', 'id','back_url']);
            
            $slug = CustomHelper::GetSlug('blog_categories', 'id', $id, $request->name);
            $req_data['slug'] = $slug;

            if(!empty($category) && count($category) > 0 && $req_id == $id){
                $isSaved = BlogCategory::where('id', $category->id)->update($req_data);
                //prd($isSaved);
            }
            else{
                $isSaved = BlogCategory::create($req_data);
                $id = $isSaved->id;
            }
            
            if ($isSaved) {

                return redirect(url($back_url))->with('alert-success', 'Blog Category has been saved successfully.');
            } else {
                return back()->with('alert-danger', 'Blog Category cannot be added, please try again or contact the administrator.');
            }
        }
        $page_heading = 'Add Blog Category';
        if(isset($category->name)){
            $page_heading = 'Edit Blog Category - '.$category->name;
        }
        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['category'] = $category;

        return view('admin.blogs_categories.form', $data);

    }

    public function delete($id){
        $category = BlogCategory::find($id);
        if ($category->blogs() && $category->blogs()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->blogs()->count() . ' blogs associated with it. Please remove the blogs first.');
        }
        // The Category must not have any associated Sub-categories to be deleted
        if ($category->children() && $category->children()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->children()->count() . ' blog sub-categories associated with it. Please remove the blog sub-categories first.');
        }
        else {      
            $category->delete();

            return back()->with('alert-success', 'The category has been removed successfully.');
        }
    }

    /* end of controller */
}