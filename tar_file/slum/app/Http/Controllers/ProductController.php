<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brand;
use App\Pincode;

use Illuminate\Http\Request;

use App\Helpers\CustomHelper;

use DB;

class ProductController extends Controller {

    private $currency = '$';
    private $limit = 20;

    public function __construct(){
        $this->currency = '$';

    }


    public function index(Request $request){
        
        $data = [];
        $limit = $this->limit;

        $keyword = (isset($request->keyword))?$request->keyword:'';
        $sort_by = (isset($request->sort_by))?$request->sort_by:'';

        $pcat_slug = (isset($request->pcat))?$request->pcat:'';
        $catArr = (isset($request->cat))?$request->cat:'';       
        $brandArr = (isset($request->brand))?$request->brand:'';
        $colorArr = (isset($request->color))?$request->color:'';
        $sizeArr = (isset($request->size))?$request->size:'';

        $priceRange = (isset($request->price_range))?$request->price_range:'';

        //pr($priceRange);

        $priceRangeArr = explode(',', $priceRange);

        $priceFrom = (isset($priceRangeArr[0]))?$priceRangeArr[0]:0;
        $priceTo = (isset($priceRangeArr[1]))?$priceRangeArr[1]:1000;

        $products = '';
        $parentCategory = '';

        /*$productIdsArr = [];

        if(!empty($pcat_slug)){
            $productIdsArr[] = '';
            $parentCategory = Category::select(['id', 'name'])->where('slug', $pcat_slug)->first();

            if(isset($parentCategory->id) && $parentCategory->id > 0){
                $productCategories = DB::table('product_categories')->select(['id', 'product_id', 'p1_cat', 'p2_cat', 'category_id'])->where('p1_cat', $parentCategory->id)->get();

                if(!empty($productCategories) && count($productCategories) > 0){
                    foreach($productCategories as $pc){
                        $productIdsArr[] = $pc->product_id;
                    }
                }
            }
        }*/

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
            $product_query->where('popularity', 1);
            $product_query->orderBy('created_at', 'desc');
        }
        if(!empty($sort_by) && $sort_by == 'discount'){
            $product_query->orderBy('discount', 'desc');
        }
        else{
            $product_query->orderBy('created_at', 'desc');
        }

        /*if(!empty($productIdsArr) && count($productIdsArr) > 0){
            $product_query->whereIn('id', $productIdsArr);
        }*/
        
        if(!empty($pcat_slug)){
            $product_query->whereHas('productP1Categories', function ($query) use ($pcat_slug) {
                $query->where('categories.slug', $pcat_slug);
            });
        }
        if(!empty($catArr) && count($catArr) > 0){
            $product_query->whereHas('productCategories', function ($query) use ($catArr) {
                $query->whereIn('categories.slug', $catArr);
            });
        }

        if(!empty($brandArr) && count($brandArr) > 0){
            $product_query->whereHas('productBrand', function ($query) use ($brandArr) {
                $query->whereIn('brands.slug', $brandArr);
            });
        }

        if(!empty($colorArr) && count($colorArr) > 0){
            $product_query->whereHas('color', function ($query) use ($colorArr) {
                $query->whereIn('colors_master.slug', $colorArr);
            });
        }

        if(!empty($sizeArr) && count($sizeArr) > 0){
            $product_query->whereHas('productSizes', function ($query) use ($sizeArr) {
                $query->whereIn('product_sizes.size_name', $sizeArr);
            });
        }

        if(is_numeric($priceFrom) && is_numeric($priceTo)){

            $product_query->whereRaw('(CASE WHEN products.sale_price < products.price AND products.sale_price > 0 THEN products.sale_price ELSE products.price END) BETWEEN '.$priceFrom.' AND '.$priceTo);
        }

        if(!empty($keyword)){
            $product_query->where('name', 'like', '%'.$keyword.'%');
        }

        //DB::enableQueryLog();
        $products = $product_query->paginate($limit);
        //prd(DB::getQueryLog());
        //prd($products);

        //prd($products->toArray());


        $data['products'] = $products;
        $data['parentCategory'] = $parentCategory;
        $data['pcat_slug'] = $pcat_slug;
        $data['keyword'] = $keyword;
        $data['sort_by'] = $sort_by;
        $data['priceFrom'] = $priceFrom;
        $data['priceTo'] = $priceTo;
        $data['meta_title'] = 'Product';
        $data['meta_keyword'] = 'Product';
        $data['meta_description'] = 'Product';

        return view('products.list', $data);
    }

    public function details($slug){
        //prd($slug);

        $data = [];

        $meta_title = '';
        $meta_keyword = '';
        $meta_description = '';

        if(!empty($slug)){
            $product = Product::where('slug', $slug)->first();

            if(!empty($product) && count($product) > 0){

                $categoryIdsArr = [];

                $brand_id = $product->brand_id;
                //prd($brand_id);
                $productCategories = (isset($product->productCategories))?$product->productCategories:'';

                if(!empty($productCategories) && count($productCategories) > 0){
                    //prd($productCategories->toArray());

                    foreach($productCategories as $cat){
                        $categoryIdsArr[] = $cat->id;
                    }
                }
                //prd($categoryIdsArr);

                $breadcrumb = '';
                $similarProducts = '';

                if( (is_numeric($brand_id) && $brand_id > 0) || (!empty($categoryIdsArr) && count($categoryIdsArr) > 0) ){
                    $similarProdQuery = Product::where('status', 1)->where('slug', '!=', $slug);

                    if(is_numeric($brand_id) && $brand_id > 0){
                        $similarProdQuery->where('brand_id', $brand_id);
                    }
                    if(!empty($categoryIdsArr) && count($categoryIdsArr) > 0){
                        if(!empty($catArr) && count($catArr) > 0){
                            $similarProdQuery->whereHas('productCategories', function ($query) use ($catArr) {
                                $query->whereIn('categories.id', $categoryIdsArr);
                            });
                        }
                    }

                    $similarProdQuery->orderBy('id', 'desc');

                    $similarProducts = $similarProdQuery->limit(8)->get();
                }

                $data['similarProducts'] = $similarProducts;

                /*if(!empty($similarProducts) && count($similarProducts) > 0){
                    prd($similarProducts->toArray());
                }*/

                $meta_title = (!empty($product->meta_title))?$product->meta_title:'';
                $meta_keyword = (!empty($product->meta_keyword))?$product->meta_keyword:'';
                $meta_description = (!empty($product->meta_description))?$product->meta_description:'';

                //prd($breadcrumb);
                $data['meta_title'] = $meta_title;
                $data['meta_keyword'] = $meta_keyword;
                $data['meta_description'] = $meta_description;
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


    public function add_to_compare(Request $request){

        $result['success'] = true;

        $message = 'To view compare page ';

        $message = '<div class="alert alert-success alert-dismissable">'.$message.'<a href="'.url('products/compare').'" class="pull-right btn btn-default">View compare page</a></div>';

        if($request->method() == 'POST'){

            $products_compare = [];

            if(session()->has('products_compare')){
                $products_compare = session()->pull('products_compare',[]);
            }

            $count = count($products_compare);

            $product_ids = ($request->has('product_ids'))?$request->product_ids:0;
            if(count($product_ids) > 0){                

                if(isset($product_ids['add']) && $product_ids['add'] > 0){
                    if($count < 4){
                        $products_compare[$product_ids['add']] = $product_ids['add'];
                    }
                    else{
                        $result['success'] = false;
                        //$message = 'You cannot compare more than 4 products.';
                        $message = '<i class="fa fa-exclamation"></i> You have already selected 4 products';

                        $err_message = '<div class="alert alert-danger alert-dismissable">'.$message.'<a href="'.url('products/compare').'" class="pull-right btn btn-default">View compare page</a></div>';

                        $result['err_message'] = $message;
                    }
                }
                elseif(isset($product_ids['del']) && $product_ids['del'] > 0){
                    //prd($products_compare);

                    if(isset($products_compare[$product_ids['del']])){
                        unset($products_compare[$product_ids['del']]);
                    }
                }
                
            }

            session(['products_compare'=>$products_compare]);
        }

        $comp_html = view('products._compare_popup')->render();
        $result['comp_html'] = $comp_html;

        //$count = count($products_compare);

        $result['message'] = $message;

        //$result['session'] = session('products_compare');

        return response()->json($result);
    }


    public function compare(){
        $data = [];
        $products = [];
        $product_ids = [];

        $products_compare = (session()->has('products_compare'))?session('products_compare'):'';

        if(count($products_compare) > 0){
            foreach($products_compare as $comp){
                $product_ids[] = $comp;
            }
        }


        if(count($product_ids) > 0){
            $products = Product::whereIn('id', $product_ids)->get();
        }
        //prd($products->toArray());

        $currency = $this->currency;

        $data['currency'] = $currency;

        $meta_title = '';
        $meta_keyword = '';
        $meta_description = '';

        $data['meta_title'] = $meta_title;
        $data['meta_keyword'] = $meta_keyword;
        $data['meta_description'] = $meta_description;

        $data['products_compare'] = $products_compare;
        $data['products'] = $products;

        return view('products.compare', $data);
    }

    public function remove_from_compare(Request $request){

        $result['success'] = false;

        //prd($request->toArray());

        if($request->method() == 'POST'){
            $product_id = ($request->has('product_id'))?$request->product_id:0;
            $remove_all = ($request->has('remove_all'))?$request->remove_all:0;

            if(is_numeric($product_id) && $product_id > 0){

                //prd(session('products_compare'));
                if(session()->has('products_compare')){
                    $count_before = count(session('products_compare'));

                    $products_compare = session()->pull('products_compare',[]);

                    if(isset($products_compare[$product_id])){
                        unset($products_compare[$product_id]);
                    }

                    session(['products_compare'=>$products_compare]);

                    $count_after = count(session('products_compare'));

                    if($count_after < $count_before){
                        $result['success'] = true;
                    }

                }

            }
            elseif($remove_all){
                //prd($request->toArray());

                if (session()->has('products_compare')) {
                    session(['products_compare' => []]);
                    $result['success'] = true;
                }
            }
        }
        return response()->json($result);
    }


    public function _get_compare_list(){
        return view('products._compare_popup');
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

            $Products = Product::where('name', 'like', '%'.$keyword.'%')->limit(10)->get();

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


    //ajax_check_pincode
    public function ajaxCheckPincode(Request $request){
        //prd($request->toArray());

        $response = [];

        $response['success'] = false;

        $message = '';

        $pincode = (isset($request->pincode))?$request->pincode:'';

        if(!empty($pincode)){

            $selectArr = ['id','state_id','city_id','pin','status','created_at','updated_at'];

            $Pincode = Pincode::select($selectArr)->where('pin', $pincode)->first();

            if(!empty($Pincode) && count($Pincode) > 0){
                //prd($Pincode->toArray());

                $response['success'] = true;
            }
        }


        return response()->json($response);
    }


/* end of controller */
}
