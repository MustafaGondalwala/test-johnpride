<?php
namespace App\Http\Controllers\Admin;

use App\CmsPages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Helpers\CustomHelper;

class CmsController  extends Controller 
{
    //protected $foo;

   protected $select_cols;

    public function __construct()
    {

        $this->select_cols = ['id','name','slug','title','heading','content','old_content','default_content','meta_title','meta_keyword','meta_description','status','created_at','updated_at'];
    }

    public function index() 
    {
        $data=array();

        $data['page_title'] = 'CMS Page';

        $data['title'] = 'CMS Page';

        $select_cols = $this->select_cols;

        $pages = CmsPages::select($select_cols)->where('status', 1)->get();

        $data['pages']= $pages;

        return view('admin.cms.index',$data);
    }

    public function edit(Request $request) {

        $data = [];

        $cms_id = (isset($request->cms_id))?$request->cms_id:'';

        $cms_page = '';

        $page_heading = 'Add CMS Page';
        $title = 'Add CMS Page';

        if(!empty($cms_id) && $cms_id > 0){
            $cms_page = CmsPages::find($cms_id);

            $page_heading = 'Edit CMS Page';
            $title = 'Edit CMS Page';
        }

        //if(is_numeric($cms_id) && $cms_id > 0){
            $method = $request->method();

            if($method == 'POST' || $method == 'post')
            {
                //prd($request->toArray());

                $old_content = (isset($cms_page->content))?$cms_page->content:'';

                $post_data = $request->all();

                $rules = [];

                $slug = isset($request->slug) ? $request->slug:'';
                
                $rules['title'] = 'required';
                $rules['content'] = 'required';

                if(!empty($cms_id)){
                    $rules['slug'] = 'required';
                }

                $this->validate($request, $rules);
                
                if(!empty($cms_id)){
                    $slug = CustomHelper::GetSlug('cms_pages', 'id', $cms_id, $post_data['slug']);
                }
                else{
                    $slug = CustomHelper::GetSlug('cms_pages', 'id', $cms_id, $post_data['title']);   
                }
                //prd($slug);
                //$update_data['name'] = $request->name;
                $update_data['slug'] = $slug;
                $update_data['title'] = $post_data['title'];
                $update_data['heading'] = $post_data['heading'];
                $update_data['content'] = $post_data['content'];
                $update_data['old_content'] = $old_content;
                $update_data['meta_title'] = $post_data['meta_title'];
                $update_data['meta_keyword'] = $post_data['meta_keyword'];
                $update_data['meta_description'] = $post_data['meta_description'];
                $update_data['updated_at'] = date('Y-m-d H:i:s');

                if(!empty($cms_id) && $cms_id > 0){
                    $is_saved = CmsPages::where('id', $cms_id)->update($update_data);
                    session()->flash('alert-success', 'Template updated successfully!');
                }
                else{
                    $is_saved = CmsPages::create($update_data);
                    session()->flash('alert-success', 'Template added successfully!');
                }


                if($is_saved){
                    
                }
                else{
                    session()->flash('alert-danger', 'Something went wrong, please try again!');
                }
                
                return redirect('admin/cms');
            }

            $select_cols = $this->select_cols;

            $page = '';

            if(!empty($cms_id) && $cms_id > 0){
                $page = CmsPages::where('id', $cms_id)->select($select_cols)->first();
            }

            $data['page']= $page;

            $data['page_heading'] = $page_heading;
            $data['title'] = $title;

            return view('admin.cms.form',$data);

        /*}
        else{
            return redirect('admin/cms');
        }*/
    }

    public function delete(Request $request){

        $id = isset($request->id) ? $request->id:'';
        $method = $request->method();
        $is_deleted = 0;

        if($method == "POST"){
            if(is_numeric($id) && $id > 0) {
                $page = CmsPages::find($id);
                $is_deleted = $page->delete();
            }
        }
        
        if($is_deleted){
            return redirect(url('admin/cms'))->with('alert-success', 'The Page has been deleted successfully.');
        }
        else {
            return redirect(url('admin.cms'))->with('alert-danger', 'The Page cannot be deleted, please try again or contact the administrator.');
        }
    }

// End of controller
}
?>