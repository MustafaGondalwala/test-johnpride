<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Banner;
use App\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Helpers\CustomHelper;
use Mail;
use Validator;
use DB;
use Storage;


class BlogController extends Controller {

    private $limit = 20;

    public function __construct(){
        
    }

    public function index(Request $request){
        $data = [];

        $limit = $this->limit;

        $keyword = (isset($request->keyword))?$request->keyword:'';
        $cat_slug = (isset($request->category))?$request->category:'';

        $blog_query = Blog::where('status', 1);
        $blog_query->orderBy('created_at', 'desc');

        /*if(!empty($cat_slug)){
            $blog_query->whereHas('Category', function ($query) use ($cat_slug){
                $query->where('slug', $cat_slug);
            });
        }
*/
        if(!empty($keyword)){
            $blog_query->where('title', 'like', '%'.$keyword.'%');
        }

        $blogs = $blog_query->paginate($limit);

        //prd($blogs->toArray());

        $blogCategoryQuery = BlogCategory::where(['status'=>1])->orderBy('sort_order')->limit(6);

        if(!empty($cat_slug)){
            $blogCategoryQuery->where('slug', $cat_slug);
        }
        
        $blogCategoryQuery->has('blogs', '>', 0);
        $blogCategories = $blogCategoryQuery->get();

        $isMobile = CustomHelper::isMobile();

        $bannerType = 'desktop';

        if($isMobile){
            $bannerType = 'mobile';
        }

        $bannerWhere = [];
        $bannerWhere['page'] = 'blog';
        $bannerWhere['status'] = 1;
        $bannerWhere['device_type'] = $bannerType;

        $banners = Banner::where($bannerWhere)->orderBy('sort_order')->limit($limit)->get();

        $data['banners'] = $banners;

        $data['blogs'] = $blogs;
        $data['blogCategories'] = $blogCategories;
        $data['cat_slug'] = $cat_slug;

        $data['meta_title'] = 'Blogs';
        $data['meta_keyword'] = 'Blogs';
        $data['meta_description'] = 'Blogs';
        
        return view('blogs.index', $data);
    }


    public function details(Request $request){

        //prd($request->toArray());
        //prd($request->slug);

        $data = [];

        $slug = (isset($request->slug))?$request->slug:'';

        $storage = Storage::disk('public');

        $og_image = '';

        if(!empty($slug)){
            $blog = Blog::where('slug', $slug)->first();

            if(isset($blog->slug) && $blog->slug == $slug){

                $BlogCategories = BlogCategory::where(['status'=>1])->orderBy('name')->limit(6)->get();

                $recent_blogs = Blog::where('id', '!=', $blog->id)->where(['status'=>1])->orderBy('created_at', 'desc')->limit(4)->get();

                $blog_images = (isset($blog->Images))?$blog->Images:'';


                if(!empty($blog_images) && count($blog_images)){
                    foreach($blog_images as $bimg){
                        if(!empty($bimg->image) && $storage->exists('blogs/'.$bimg->image)){

                            $og_image = url('storage/blogs/thumb/'.$bimg->image);
                            break;
                        }
                    }
                }

                $data['blog'] = $blog;
                $data['BlogCategories'] = $BlogCategories;
                $data['recent_blogs'] = $recent_blogs;

                $data['meta_title'] = $blog->meta_title;
                $data['meta_keyword'] = $blog->meta_keyword;
                $data['meta_description'] = $blog->meta_description;
                $data['og_image'] = $og_image;

                return view('blogs.details', $data);                
            }
        }

        return back();

    }



/* End of Controller */
}