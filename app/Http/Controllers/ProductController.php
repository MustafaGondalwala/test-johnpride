<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brand;
use App\Pincode;
use App\Review;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use App\Helpers\CustomHelper;

use Validator;
use DB;

class ProductController extends Controller {

    private $currency = '$';
    private $limit = 30;

    public function __construct(){
        $this->currency = '$';
    }


    public function index(Request $request){
        //prd($request->toArray());
        
        $data = [];
        //$limit = $this->limit;
        $limit = $this->limit;
        $isMobile = CustomHelper::isMobile();
        if($isMobile)
        {
            $limit = 20;
        }

        $keyword = (isset($request->keyword))?$request->keyword:'';
        $sort_by = (isset($request->sort_by))?$request->sort_by:'';
        $pcat_slug = (isset($request->pcat))?$request->pcat:'';
        $p2cat_slug = (isset($request->p2cat))?$request->p2cat:'';

        $priceRange = (isset($request->price_range))?$request->price_range:'';


        $new_arrival = (isset($request->new_arrival))?$request->new_arrival:'';
        $trending = (isset($request->trending))?$request->trending:'';
        $popularity = (isset($request->popularity))?$request->popularity:'';

        $eco = (isset($request->eco))?$request->eco:'';
        $premium = (isset($request->premium))?$request->premium:'';

        $collections = (isset($request->collection))?$request->collection:'';
        $priceRangeArr = explode(',', $priceRange);

        $priceFrom = (isset($priceRangeArr[0]))?$priceRangeArr[0]:0;
        $priceTo = (isset($priceRangeArr[1]))?$priceRangeArr[1]:1000;

        $parentCategory = '';
        $p2Category = '';
        $meta_title = '';
        $meta_description = '';

        $productIdsArr = [];

        if(!empty($pcat_slug)){
            //$parentCategory = Category::select(['id', 'name', 'slug'])->where('slug', $pcat_slug)->first();
            $parentCategory = Category::where('slug', $pcat_slug)->first();

            $meta_title = isset($parentCategory->meta_title) ? $parentCategory->meta_title : '';
            $meta_description = isset($parentCategory->meta_description) ? $parentCategory->meta_description : '';

        }

        if(!empty($p2cat_slug)){
            //$p2Category = Category::select(['id', 'name', 'slug'])->where('slug', $p2cat_slug)->first();
            $p2Category = Category::where('slug', $p2cat_slug)->first();

            $meta_title = isset($p2Category->meta_title) ? $p2Category->meta_title : '';

            $meta_description = isset($p2Category->meta_description) ? $p2Category->meta_description : '';

        }

        $banner_image = '';
        $mobile_banner_image = '';
        if(!empty($p2Category) && count($p2Category) > 0)
        {
            $banner_image = ($p2Category->banner_image!='')?$p2Category->banner_image:'';
            $mobile_banner_image = ($p2Category->mob_banner_image!='')?$p2Category->mob_banner_image:'';

        }
        
        if($banner_image == '' && !empty($parentCategory) && count($parentCategory) > 0)
        {
            $banner_image = ($parentCategory->banner_image!='')?$parentCategory->banner_image:'';
            $mobile_banner_image = ($parentCategory->mob_banner_image!='')?$parentCategory->mob_banner_image:'';

        }


        $getProducts = $this->getProducts($request, $limit);
        $productIdsArr = $getProducts['products']->pluck('id')->toArray();

        //prd($productIdsArr);
        $sizes = [];
        if(!empty($productIdsArr))
        {
            $sizes = DB::table("sizes")->join('product_inventory', 'product_inventory.size_id', '=', 'sizes.id')->select('sizes.*')->where(['sizes.status'=>1])->whereIn('product_inventory.product_id',$productIdsArr)->get();

            //prd($sizeQuery);
        }

        $products = $getProducts['products'];
        $totalCount = $getProducts['totalCount'];
        $viewCount = $getProducts['viewCount'];
        //prd($products);

        //prd($products->toArray());


        $data['sizes'] = $sizes;
        $data['products'] = $products;
        $data['totalCount'] = $totalCount;
        $data['viewCount'] = $viewCount;
        $data['collections'] = $collections;
        $data['parentCategory'] = $parentCategory;
        $data['p2Category'] = $p2Category;
        $data['pcat_slug'] = $pcat_slug;
        $data['p2cat'] = $p2cat_slug;
        $data['keyword'] = $keyword;
        $data['sort_by'] = $sort_by;
        $data['priceFrom'] = $priceFrom;
        $data['priceTo'] = $priceTo;
        $data['banner_image'] = $banner_image;
        $data['mobile_banner_image'] = $mobile_banner_image;
        
        $data['new_arrival'] = $new_arrival;
        $data['trending'] = $trending;
        $data['popularity'] = $popularity;
        $data['eco'] = $eco;
        $data['premium'] = $premium;

        $data['meta_title'] = isset($meta_title) ? $meta_title : 'JohnPride';
        $data['meta_keyword'] = '';
        $data['meta_description'] = isset($meta_description) ? $meta_description : 'JohnPride';
       

        return view('products.index', $data);
    }

    public function getProducts(Request $request, $limit=30){

        //prd($request->toArray());

        $data = [];
        //$limit = $this->limit;

        $offset = 0;

        $products = '';
        $totalCount = 0;
        $currentCount = 0;
        $categoryArr = [];

        $viewCount = (isset($request->view_count))?$request->view_count:0;
        $currentPage = (isset($request->current_page))?$request->current_page:0;

        if(is_numeric($viewCount) && $viewCount > 0){
            $offset = $viewCount;
        }

        /*$nextPage = $currentPage + 1;

        Paginator::currentPageResolver(function () use ($nextPage) {
            return $nextPage;
        });*/

        $keyword = (isset($request->keyword))?$request->keyword:'';
        $sort_by = (isset($request->sort_by))?$request->sort_by:'';

        $pcat_slug = (isset($request->pcat))?$request->pcat:'';
        $p2cat_slug = (isset($request->p2cat))?$request->p2cat:'';
        $catArr = (isset($request->cat))?$request->cat:'';       
        $genderArr = (isset($request->gender))?$request->gender:'';
        
        $collectionArr = (isset($request->collection))?$request->collection:'';
        //$brandArr = (isset($request->brand))?$request->brand:'';
        $colorArr = (isset($request->color))?$request->color:'';
        $sizeArr = (isset($request->size))?$request->size:[];

        $priceRange = (isset($request->price_range))?$request->price_range:'';
        

        $new_arrival = (isset($request->new_arrival))?$request->new_arrival:'';
        $trending = (isset($request->trending))?$request->trending:'';
        $popularity = (isset($request->popularity))?$request->popularity:'';

        $eco = (isset($request->eco))?$request->eco:'';
        $premium = (isset($request->premium))?$request->premium:'';

        //prd($sizeArr);

        $priceRangeArr = explode(',', $priceRange);

        $priceFrom = (isset($priceRangeArr[0]))?$priceRangeArr[0]:0;
        $priceTo = (isset($priceRangeArr[1]))?$priceRangeArr[1]:1000;

        $products = '';
        
        DB::enableQueryLog();
        $product_query = Product::where('status', 1);

        if(!empty($sort_by) && $sort_by == 'price_high_low'){
            $product_query->orderBy('sale_price', 'desc');
            $product_query->orderBy('price', 'desc');
        }
        if(!empty($sort_by) && $sort_by == 'price_low_high'){
            $product_query->orderBy('sale_price', 'asc');
            $product_query->orderBy('price', 'asc');
        }
        if(!empty($sort_by) && $sort_by == 'new'){
            $product_query->orderBy('created_at', 'desc');
        }
        if(!empty($sort_by) && $sort_by == 'popularity'){
            $product_query->orderBy('popularity', 1);
            $product_query->orderBy('created_at', 'desc');
        }
        if(!empty($sort_by) && $sort_by == 'discount'){
            $product_query->orderBy('discount', 'desc');
        }
        else{
            $product_query->orderBy('sort_order', 'desc');
        }

        //if(!empty($new_arrival))
         if(!empty($p2cat_slug) && $p2cat_slug == 'new-arrival')
        {
            //$product_query->where('new_arrival', $new_arrival);
            $product_query->where('stamp', 'New');
        }

        //if(!empty($popularity)){
         if(!empty($p2cat_slug) && $p2cat_slug == 'popularity'){
            //$product_query->where('popularity', $popularity);
            $product_query->where('stamp', 'Popular');
        }

         if(!empty($p2cat_slug) && $p2cat_slug == 'trending'){
        //if(!empty($trending)){
           //$product_query->where('trending', $trending);
            $product_query->where('stamp', 'Trending');
        }

         //if(!empty($eco)){
          if(!empty($p2cat_slug) && $p2cat_slug == 'ECO'){
            $product_query->where('stamp', 'ECO');
        }

          //if(!empty($premium)){
          if(!empty($p2cat_slug) && $p2cat_slug == 'PREMIUM'){
            $product_query->where('stamp', 'PREMIUM');
        }
        
        if(!empty($pcat_slug)){
            $product_query->whereHas('productP1Categories', function ($query) use ($pcat_slug) {
                $query->where('categories.slug', $pcat_slug);
            });
        }


   if(!empty($p2cat_slug) && ($p2cat_slug != 'new-arrival' && $p2cat_slug != 'popularity' && $p2cat_slug != 'trending' && $p2cat_slug != 'ECO' && $p2cat_slug != 'PREMIUM'))
   {

    if(!empty($p2cat_slug)){
            /*$product_query->whereHas('productP2Categories', function ($query) use ($p2cat_slug) {
                $query->where('categories.slug', $p2cat_slug);
            });*/


            $categoryArr[] = $p2cat_slug;

            $product_query->whereHas('productCategories', function ($query) use ($categoryArr) {
                $query->whereIn('categories.slug', $categoryArr);
            });
        }
   }
        
        



        /*if(!empty($catArr) && count($catArr) > 0){
            $product_query->whereHas('productCategories', function ($query) use ($catArr) {
                $query->whereIn('categories.slug', $catArr);
            });
        }*/

        if(!empty($genderArr) && count($genderArr) > 0){
            $product_query->whereIn('gender', $genderArr);
        }

        if(!empty($brandArr) && count($brandArr) > 0){
            $product_query->whereHas('productBrand', function ($query) use ($brandArr) {
                $query->whereIn('brands.slug', $brandArr);
            });
        }

        //pr($collectionArr);
        
        if(!empty($collectionArr) && count($collectionArr) > 0){
            $product_query->whereHas('productCollections', function ($query) use ($collectionArr) {
                $query->where('brands.slug', $collectionArr);
            });
        }

        if(!empty($colorArr) && count($colorArr) > 0){
            $product_query->whereHas('color', function ($query) use ($colorArr) {
                $query->whereIn('colors_master.slug', $colorArr);
            });
        }

        //pr($sizeArr);

        if(!empty($sizeArr) && count($sizeArr) > 0){
            if(!empty($sizeArr[0]))
            {
                $product_query->whereHas('productInventorySize', function ($query) use ($sizeArr) {
                    //$query->whereIn('product_inventory.size_name', $sizeArr);
                    $query->where('product_inventory.size_name', $sizeArr[0]);
                    $query->havingRaw('SUM(stock) > 0');
                });
            }
            
        }

        if(is_numeric($priceFrom) && is_numeric($priceTo)){

            $product_query->whereRaw('(CASE WHEN products.sale_price < products.price AND products.sale_price > 0 THEN products.sale_price ELSE products.price END) BETWEEN '.$priceFrom.' AND '.$priceTo);
        }

        // if(!empty($keyword)){
            // $product_query->where('name', 'like', '%'.$keyword.'%');
        // }   

        if(!empty($keyword)){
            $product_query->where(function($query) use($keyword){
                $query->where('name', 'like', '%'.$keyword.'%');
                $query->orWhere('sku', 'like', '%'.$keyword.'%');
                $query->orWhere('meta_description', 'like', '%'.$keyword.'%');
            });
        }     
        

        $product_query->whereHas('productInventorySize', function($stocks) {
            $stocks->havingRaw('SUM(stock) > 0');
        });

         //$product_query->whereRaw("id in (select product_id from product_to_category where category_id= $searched_category->id)");

        //$products = $product_query->paginate($limit);
        $totalCount = $product_query->count();
        //DB::enableQueryLog();
        $products = $product_query->offset($offset)->limit($limit)->get();
       // prd(DB::getQueryLog());
        //$products = $product_query->paginate($limit, ['*'], 'page', $currentPage);
        //prd($products);
        $currentCount = $products->count();

        $viewCount = $currentCount + $viewCount;

        $data['products'] = $products;
        $data['totalCount'] = $totalCount;
        $data['viewCount'] = $viewCount;

        return $data;

    }

    // load_more
    public function loadMore(Request $request){
        //pr($request->toArray());

        $limit = 30;
        $isMobile = CustomHelper::isMobile();
        if($isMobile)
        {
            $limit = 20;
        }


        $response = [];
        $response['success'] = false;

        $getProducts = $this->getProducts($request, $limit);
        //prd($products->toArray());

        $viewCount = $getProducts['viewCount'];

        $viewData = $getProducts;

        $list = view('products._list', $viewData)->render();

        $response['list'] = $list;
        $response['viewCount'] = $viewCount;
        $response['success'] = true;

        //prd($viewHtml);

        return response()->json($response);

    }

    public function details($slug){
        //prd($slug);

        $data = [];

        $meta_title = '';
        $meta_keyword = '';
        $meta_description = '';
        $og_image = '';
        $image_path = config('custom.image_path');

        if(!empty($slug)){
            $product = Product::where('slug', $slug)->first();

            if(!empty($product) && count($product) > 0){

                $userId = 0;

                $recentViews = '';

                if(auth()->check()){

                    $user = auth()->user();
                    $userId = $user->id;

                    $recentViews = $user->recent_views;
                }

                $productId = $product->id;

                $this->updateRecentViews($productId);

                $recentProducts = '';

                $isSerialized = CustomHelper::isSerialized($recentViews);

                if(!empty($recentViews) && $isSerialized){
                    $recentViewsArr = unserialize($recentViews);

                    if(is_array($recentViewsArr) && count($recentViewsArr) > 0){
                        $recentProductsQry = Product::whereIn('id', $recentViewsArr)->where('id', '!=', $productId)->where('status', 1);
                        
                        

                        $recentProductsQry->whereHas('productInventorySize', function($stocks) {
                            $stocks->havingRaw('SUM(stock) > 0');
                        });

                        $recentProducts = $recentProductsQry->orderBy('created_at', 'desc')->get();
                    }
                }

               /* if(!empty($recentProducts) && count($recentProducts) > 0){
                    pr($recentProducts->toArray());
                }*/

                $data['recentProducts'] = $recentProducts;

                $categoryIdsArr = [];

                $collection_id = $product->collection_id;
                //prd($collection_id);
                /*$productCategories = (isset($product->productCategories))?$product->productCategories:'';

                if(!empty($productCategories) && count($productCategories) > 0){
                    //prd($productCategories->toArray());

                    foreach($productCategories as $cat){
                        $categoryIdsArr[] = $cat->id;
                    }
                }*/
                //prd($categoryIdsArr);

                $breadcrumb = '';
                $similarProducts = '';

                //DB::enableQueryLog();

                //if( (is_numeric($collection_id) && $collection_id > 0) || (!empty($categoryIdsArr) && count($categoryIdsArr) > 0) ){
                /*if( (!empty($categoryIdsArr) && count($categoryIdsArr) > 0) ){
                    $similarProdQuery = Product::where('status', 1)->where('slug', '!=', $slug);

                    //if(is_numeric($collection_id) && $collection_id > 0){
                        //$similarProdQuery->where('collection_id', $collection_id);
                    //}
                    if(!empty($categoryIdsArr) && count($categoryIdsArr) > 0){
                        //if(!empty($catArr) && count($catArr) > 0){
                            $similarProdQuery->whereHas('productCategories', function ($query) use ($categoryIdsArr) {
                                $query->whereIn('categories.id', $categoryIdsArr);
                            });
                        //}
                    }

                    $similarProdQuery->orderBy('id', 'desc');

                    $similarProducts = $similarProdQuery->limit(8)->get();
                }*/

                $relatedProductCategoryAr = $product->relProductCategory;
           
                if(!empty($relatedProductCategoryAr) && count($relatedProductCategoryAr) > 0)
                {
                    $sel_related_product_cat=$relatedProductCategoryAr[0]->category_id; 

                    $similarProdQuery = Product::where('status', 1)->where('slug', '!=', $slug);  

                    $similarProdQuery->whereHas('productCategories', function ($query) use ($sel_related_product_cat) {
                                $query->where('categories.id', $sel_related_product_cat);
                            });

                    $similarProdQuery->whereHas('productInventorySize', function($stocks) {
                        $stocks->havingRaw('SUM(stock) > 0');
                    });

                    $similarProdQuery->orderBy('id', 'desc');
                    $similarProducts = $similarProdQuery->limit(8)->get();            

                }

                $data['similarProducts'] = $similarProducts;
                //pr($similarProducts);

                /*if(!empty($similarProducts) && count($similarProducts) > 0){
                    prd($similarProducts->toArray());
                }*/

                //$query = DB::getQueryLog(); prd($query);

                $reviews = Review::where(['product_id'=>$productId, 'status'=>1])->orderBy('created_at', 'desc')->get();

                //prd($reviews->toArray());

                $data['reviews'] = $reviews;

                $product_image = (isset($product->defaultImage))?$product->defaultImage:'';
                $og_image = '';

                if(!empty($product_image) && count($product_image) > 0){
                    if(!empty($product_image->image)){
                        $og_image = $product_image->image;
                        $og_image = $image_path.$og_image;
                    }
                }

                $meta_title = (!empty($product->meta_title))?$product->meta_title:$product->name;
                $meta_keyword = (!empty($product->meta_keyword))?$product->meta_keyword:'';
                $meta_description = (!empty($product->meta_description))?$product->meta_description:'';

                //prd($breadcrumb);
                $data['meta_title'] = $meta_title;
                $data['meta_keyword'] = $meta_keyword;
                $data['meta_description'] = $meta_description;
                $data['og_image'] = $og_image;

                $data['recentViews'] = $recentViews;
                $currency = $this->currency;

                $data['product'] = $product;
                //$data['currency'] = $currency;
                //$data['category_name'] = $category_name;
                //$data['breadcrumb'] = $breadcrumb;

                return view('products.detail', $data);
            }
        }
        else{
            return back();
        }
    }

    private function updateRecentViews($productId){
        if(is_numeric($productId) && $productId > 0 && auth()->check()){

            $user = auth()->user();

            $recentViewsArr = [];

            $recentViews = $user->recent_views;

            $isSerialized = CustomHelper::isSerialized($recentViews);

            if(!empty($recentViews) && $isSerialized){
                $recentViewsArr = unserialize($recentViews);

                if(!in_array($productId, $recentViewsArr)){
                    $recentViewsArr[] = $productId;

                    $user->recent_views = serialize($recentViewsArr);
                    $user->save();
                }
            }
            elseif(empty($recentViews)){
                $recentViewsArr[] = $productId;

                $user->recent_views = serialize($recentViewsArr);
                $user->save();
            }
        }
    }

    //ajax_get_list_by_search
    public function ajaxGetListBySearch(Request $request){
        //prd($request->toArray());

        $response = [];

        $response['success'] = false;

        $message = '';

        $keyword = (isset($request->keyword))?$request->keyword:'';

        if(!empty($keyword)){

            $selBrands = ['id','name','slug','description','icon','image','featured','sort_order','status','created_at','updated_at'];

            $selCategories = ['id','parent_id','name','slug','description','sort_order','having_child','status','featured','meta_title','meta_keyword','meta_description','created_at','updated_at'];

            $Brands = Brand::where('name', 'like', '%'.$keyword.'%')->select($selBrands)->get();

            /*$categoriesQuery = Category::select($selCategories);

            $categoriesQuery->whereHas('categoryProducts', function($query) use ($keyword) {
                $query->where('categories.name', 'like', '%'.$keyword.'%');
            });

            $Categories = $categoriesQuery->get();*/

            /*if(!empty($Categories) && count($Categories) > 0){
                //prd($Categories->toArray());

                foreach($Categories as $cat){

                    if($cat->parent && count($cat->parent) > 0){
                        $parentCat = $cat->parent;

                        if($parentCat->parent && count($parentCat->parent) > 0){
                            $pParentCat = $parentCat->parent;

                            prd($pParentCat->toArray());
                        }
                    }

                }
            }*/

            // $product_qry = Product::where('name', 'like', '%'.$keyword.'%')->where('status',1);
            $product_qry = Product::where('status',1);

            if(!empty($keyword)){
                $product_qry->where(function($query) use($keyword){
                    $query->where('name', 'like', '%'.$keyword.'%');
                    $query->orWhere('sku', 'like', '%'.$keyword.'%');
                    $query->orWhere('meta_description', 'like', '%'.$keyword.'%');
                });
            }

            $product_qry->whereHas('productInventorySize', function($stocks) {
                $stocks->havingRaw('SUM(stock) > 0');
            });

            $Products = $product_qry->limit(10)->get();

            if( (!empty($Brands) && count($Brands) > 0) || (!empty($Products) && count($Products) > 0) ){
                $viewData = [];
                $viewData['Brands'] = $Brands;
                //$viewData['Categories'] = $Categories;
                $viewData['Products'] = $Products;

                $searchListHtml = view('products._search_list', $viewData)->render();

                $response['success'] = true;
                $response['searchListHtml'] = $searchListHtml;
            }
        }


        return response()->json($response);
    }

    public function details_pop_up(Request $request){
        //prd($request->toArray());

        $result['success'] = false;

        $product_id = ($request->has('product_id'))?$request->product_id:0;

        if(is_numeric($product_id) && $product_id > 0){
            $product = Product::find($product_id);

            //prd($product);

            if(!empty($product) && count($product) > 0){
                //$result['product'] = $product->toArray();

                $currency = $this->currency;

                $view_data['currency'] = $currency; 
                $view_data['product'] = $product;

                $html = view('products.details_pop_up', $view_data)->render();

                $result['html'] = $html;
                $result['product_name'] = $product->name;

                $result['success'] = true;
            }
        }

        return response()->json($result);
    }


    //save_review
    public function saveReview(Request $request){

        $response['success'] = false;

        $method = $request->method();

        if($method == 'POST' && auth()->check()){
            //prd($request->toArray());   

            $user = auth()->user();

            $userId = $user->id;

            $userName = (isset($user->name))?$user->name:'';

            //prd($request->toArray());

            $rules = [];
            $validation_msg = [];

            $rules['rating'] = 'required|numeric';
            $rules['comment'] = 'required';
            $rules['slug'] = 'required';

            $validation_msg['comment.required'] = 'The rating is required.';
            $validation_msg['comment.required'] = 'Please write something...';

            //$this->validate($request, $rules, $validation_msg);

            $validator = Validator::make($request->all(), $rules, $validation_msg);

            if($validator->fails()){
                $response['errors'] = $validator->errors();
            }
            else{

                $slug = $request->slug;

                $product = Product::select('id', 'name')->where('slug', $slug)->first();

                $productName = (isset($product->name))?$product->name:'';

                $productId = (isset($product->id))?$product->id:0;

                $reviewData = $request->except(['slug']);

                $reviewData['user_id'] = $userId;
                $reviewData['product_id'] = $productId;

                //prd($reviewData);

                //prd($userAddress->toArray());

                $isSaved = Review::create($reviewData);

                $reviewDate = CustomHelper::DateFormat($isSaved->created_at, 'd M Y H:i: A');

                //prd($isSaved->toArray());

                if($isSaved){

                    $subject = 'New Review on Product: '.$productName.' - SlumberJill';

                    $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                    if(empty($ADMIN_EMAIL)){
                        $ADMIN_EMAIL = config('custom.admin_email');
                    }

                    $from_email = $ADMIN_EMAIL;
                    $to_email = $ADMIN_EMAIL;

                    $email_data = [];
                    $email_data['productName'] = $productName;
                    $email_data['userName'] = $userName;
                    $email_data['reviewDate'] = $reviewDate;

                    $is_mail = CustomHelper::sendEmail('emails.product_review', $email_data, $to=$to_email, $from_email, $replyTo = $from_email, $subject);

                    $response['success'] = true;
                }

            }

        }

        return response()->json($response);
    }


/* end of controller */
}
