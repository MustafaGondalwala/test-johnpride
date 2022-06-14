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

        $this->select_cols = ['id','name','title','heading','content','old_content','default_content','meta_title','meta_keyword','meta_description','status','created_at','updated_at'];
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

    public function edit(Request $request)
    {
        $data = [];

        $cms_id = (isset($request->cms_id))?$request->cms_id:0;

        if(is_numeric($cms_id) && $cms_id > 0){

            $data['page_heading']='Edit CMS Page';
            $data['title']='Edit CMS Page';
            $method = $request->method();

            if($method == 'POST' || $method == 'post')
            {
                //prd($request->toArray());

                $cms_page = CmsPages::find($cms_id);

                $old_content = (isset($cms_page->content))?$cms_page->content:'';

                $post_data = $request->all();

                $rules = [];

                $rules['title'] = 'required';
                $rules['content'] = 'required';

                $this->validate($request, $rules);

                $update_data['title'] = $post_data['title'];
                $update_data['heading'] = $post_data['heading'];
                $update_data['content'] = $post_data['content'];
                $update_data['old_content'] = $old_content;
                $update_data['meta_title'] = $post_data['meta_title'];
                $update_data['meta_keyword'] = $post_data['meta_keyword'];
                $update_data['meta_description'] = $post_data['meta_description'];
                $update_data['updated_at'] = date('Y-m-d H:i:s');

                $is_updated = CmsPages::where('id', $cms_id)->update($update_data);

                if($is_updated){
                    session()->flash('alert-success', 'Template updated successfully!');
                }
                else{
                    session()->flash('alert-danger', 'Something went wrong, please try again!');
                }
                
                return redirect('admin/cms');
            }

            $select_cols = $this->select_cols;

            $page = CmsPages::where('id', $cms_id)->select($select_cols)->first();

            $data['page']= $page;

            return view('admin.cms.form',$data);

        }
        else{
            return redirect('admin/cms');
        }
    }

// End of controller
}
?>