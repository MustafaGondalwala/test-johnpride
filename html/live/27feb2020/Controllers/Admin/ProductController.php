<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Product;
use App\ProductImage;
use App\ColorMaster;
use App\SizeChart;
use App\Size;
use App\Brand;
use App\ProductInventory;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use Validator;
use Storage;

use App\Helpers\CustomHelper;

use DB;
use Image;

use App\Exports\ProductExport;
use App\Exports\InventoryExport;
use App\Imports\ProductImport;
use App\Imports\InventoryImport;

use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){

        //echo "products-index"; die;

        $data = [];

        $limit = $this->limit;

        $export_xls = (isset($request->export_xls))?$request->export_xls:'';
        $export_inventory = (isset($request->export_inventory))?$request->export_inventory:'';

        $sortBy = (isset($request->sortBy))?$request->sortBy:'';

        $name = (isset($request->name))?$request->name:'';
        $category = (isset($request->category))?$request->category:'';

        $price_scope = (isset($request->price_scope))?$request->price_scope:'=';
        $price = (isset($request->price))?$request->price:'';

        $stock_scope = (isset($request->stock_scope))?$request->stock_scope:'=';
        $stock = (isset($request->stock))?$request->stock:'';

        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');


        $productQuery = Product::orderBy('id', 'desc');

        if(!empty($sortBy) && $sortBy == 'top_selling'){
            $productQuery->whereRaw("id IN (select product_id from order_items Group By order_items.product_id Order By sum(order_items.qty) desc)");
        }

        if(!empty($name)){
            $productQuery->where(function($query) use($name){
                $query->where('name', 'like', '%'.$name.'%');
                $query->orWhere('sku', 'like', '%'.$name.'%');
            });
        }

        if(is_numeric($category) && $category > 0){
            $productQuery->whereHas('productCategories', function ($query) use ($category) {
                $query->where('category_id', $category);
            });
        }

        if(is_numeric($price) && $price > 0){
            $productQuery->where('price', $price_scope, $price);
        }

        if(is_numeric($stock) && $stock > 0){
            $productQuery->where('stock', $stock_scope, $stock);
        }

        if( strlen($status) > 0 ){
            $productQuery->where('status', $status);
        }

        if(!empty($from_date)){
            $productQuery->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $productQuery->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }

        //dd($productQuery);

        if(!empty($export_xls) && ($export_xls == 1 || $export_xls == '1') ){
            return $this->exportXls($productQuery);
        }
        elseif(!empty($export_inventory) && ($export_inventory == 1 || $export_inventory == '1') ){
            return $this->exportInventory($productQuery);
        }

        //DB::enableQueryLog();
        $products = $productQuery->paginate($limit);
        //prd(DB::getQueryLog());
        
        $data['products'] = $products;
        $data['limit'] = $limit;

        return view('admin.products.index', $data);

    }

    private function exportXls($productQuery){

        $fieldNames = $productQuery->getModel()->getFillable();

        $select = ['id','style_id','sku','name','slug','brand_id','manufacturer','country_origin','business_unit','description','gender','video','color_id','size_chart_id','price','sale_price','weight','delivery_duration','sort_order','stamp','featured','trending','net_qty','product_type','standard_size',
        'age_group','brand_color','base_color','fashion_type','prod_usage','year','season','tags','across_shoulder','bust','chest','front_length','to_fit_bust','sleeve_length','to_fit_waist','waist','popularity','status','meta_title','meta_keyword','meta_description','created_at','updated_at'];

        //$productQuery->select($select);

        $products = $productQuery->select($select)->get();
        //prd($products);
        $exportArr = [];

        if(!empty($products) && $products->count() > 0){
            foreach($products as $product){
                //prd($product->toArray());

                $productCategories = $product->productCategories;

                $productImages = $product->productImages;
                $mainImage = $productImages->where('is_default',1)->first();
                $otherImages = $productImages->where('is_default',0);
                $main_image = '';
                if(isset($mainImage['image'])){
                $main_image = $mainImage['image'];
                }
                $reverseImage = $productImages->where('is_reverse',1)->first();
                $reverse_image = '';
                if(isset($reverseImage['image'])){
                $reverse_image = $reverseImage['image'];
                }                
                $other_img = [];
                if(!$otherImages->isEmpty()){
                    $imgCount = 0;
                    foreach ($otherImages as $image) {
                        $other_img[] = $image->image;
                        $imgCount++;
                        if($imgCount > 2){
                            break;
                        }
                    }
                }
                $category_id = (isset($productCategories[0]->id))?$productCategories[0]->id:0;

                $productAttributes = $product->productAttributes;

                //pr($productCategories->toArray());

                $productArr = [];

                $productArr['id'] = $product->id;
                $productArr['style_id'] = $product->style_id;
                $productArr['sku'] = $product->sku;
                $productArr['name'] = $product->name;
                $productArr['slug'] = $product->slug;
                $productArr['brand_id'] = $product->brand_id;
                $productArr['manufacturer'] = $product->manufacturer;
                $productArr['country_origin'] = $product->country_origin;
                $productArr['business_unit'] = $product->business_unit;
                $productArr['category_id'] = $category_id;
                $productArr['product_type'] = $product->product_type;
                $productArr['standard_size'] = $product->standard_size;
                $productArr['hsn'] = $product->hsn;
                $productArr['price'] = $product->price;
                $productArr['sale_price'] = $product->sale_price;
                $productArr['age_group'] = $product->age_group;
                $productArr['gender'] = $product->gender;
                $productArr['brand_color'] = $product->brand_color;
                $productArr['base_color'] = $product->base_color;
                $productArr['color_id'] = $product->color_id;
                $productArr['fashion_type'] = $product->fashion_type;
                $productArr['prod_usage'] = $product->prod_usage;
                $productArr['year'] = $product->year;
                $productArr['season'] = $product->season;
                $productArr['description'] = $product->description;
                $productArr['tags'] = $product->tags;
                $productArr['across_shoulder'] = $product->across_shoulder;
                $productArr['bust'] = $product->bust;
                $productArr['chest'] = $product->chest;
                $productArr['front_length'] = $product->front_length;
                $productArr['to_fit_bust'] = $product->to_fit_bust;
                $productArr['sleeve_length'] = $product->sleeve_length;
                $productArr['to_fit_waist'] = $product->to_fit_waist;
                $productArr['waist'] = $product->waist;
                $productArr['video'] = $product->video;
                $productArr['main_image'] = $main_image;
                $productArr['reverse_image'] = (isset($reverse_image) ? $reverse_image : "");
                $productArr['other_image1'] = (isset($other_img[0]) ? $other_img[0] : "");
                $productArr['other_image2'] = (isset($other_img[1]) ? $other_img[1] : "");
                $productArr['other_image3'] = (isset($other_img[2]) ? $other_img[2] : "");
                $productArr['size_chart_id'] = $product->size_chart_id;
                $productArr['weight'] = $product->weight;
                $productArr['delivery_duration'] = $product->delivery_duration;
                $productArr['sort_order'] = $product->sort_order;
                $productArr['stamp'] = $product->stamp;
                $productArr['featured'] = $product->featured;
                $productArr['trending'] = $product->trending;
                $productArr['net_qty'] = $product->net_qty;
                $productArr['popularity'] = $product->popularity;
                $productArr['status'] = $product->status;
                $productArr['meta_title'] = $product->meta_title;
                $productArr['meta_keyword'] = $product->meta_keyword;
                $productArr['meta_description'] = $product->meta_description;
                $productArr['created_at'] = $product->created_at->toDateTimeString();
                $productArr['updated_at'] = $product->updated_at->toDateTimeString();

                for($i=0; $i<=9; $i++){
                    $productArr['attribute_'.($i+1)] = (isset($productAttributes[$i]->value))?$productAttributes[$i]->value:'';
                }

                //prd($productArr);

                $exportArr[] = $productArr;
            }
        }

        $fieldNames = array_keys($exportArr[0]);

        //prd($filedNames);

        $fileName = 'products_'.date('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new ProductExport($exportArr, $fieldNames), $fileName);
    }

    private function exportInventory($productQuery){

        $select = ['id', 'name'];

        $products = $productQuery->select($select)->get();

        $exportArr = [];
        if(!empty($products) && $products->count() > 0){
            foreach($products as $product){

                $productName = $product->name;

                $productInventory = $product->productInventory;

                if(!empty($productInventory)){
                    foreach($productInventory as $pi){

                        $inventoryArr = [];

                        $inventoryArr['product_id'] = $product->id;
                        $inventoryArr['product_name'] = $productName;
                        $inventoryArr['sku'] = $pi->sku;
                        $inventoryArr['size'] = $pi->size_name;
                        $inventoryArr['stock'] = $pi->stock;
                        
                        $exportArr[] = $inventoryArr;
                    }
                }

            }
        }
                //prd($exportArr);
        $fieldNames = array_keys($exportArr[0]);

        //prd($filedNames);

        $fileName = 'inventory_'.date('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new ProductExport($exportArr, $fieldNames), $fileName);
    }


    private function exportInventory30oct2019($productQuery){

        $fieldNames = $productQuery->getModel()->getFillable();

        $select = ['id', 'name'];

        $products = $productQuery->select($select)->get();

        $fileName = 'inventory_'.date('Y-m-d-H-i-s').'.xlsx';

        $viewData = [];
        $viewData['products'] = $products;

        //$viewHtml = view('admin.products._inventory_export', $viewData)->render();

        //echo $viewHtml; die;       

        header('Content-Type: application/vnd.ms-excel');
        //tell browser what's the file name
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        //no cache
        header('Cache-Control: max-age=0');

        return view('admin.products._inventory_export', $viewData);
    }


    public function add(Request $request){
        //prd($request->toArray());

        $data = [];

        $product_id = (isset($request->id))?$request->id:0;

        $product = '';
        $selected_cat_ids= []; 

        if(is_numeric($product_id) && $product_id > 0){
            $product = Product::find($product_id);
        }

        if($request->method() == 'POST' || $request->method() == 'post'){
            return $this->save($request, $product, $product_id);
        }

        $categories = Category::where(['status'=>1, 'parent_id'=>0])->orderBy('name')->get();

        $ColorMaster = ColorMaster::where(['parent_id'=>0])->orderBy('name')->get();
        $SizeChart = SizeChart::where(['status'=>1])->orderBy('title')->get();
        $SizeList = Size::where(['status'=>1])->orderBy('name')->get();
        $BrandList = Brand::where(['status'=>1])->orderBy('name')->get();

        $page_heading = 'Add Product';
        if(isset($product->name)){
            $page_heading = 'Edit Product - '.$product->name;
        }


        if(!empty($product_id)){

            $exist_cat_result= DB::table('product_categories')->where(['product_id'=>$product_id])->get();
            
            if(!empty($exist_cat_result)){
                foreach($exist_cat_result as $ex ){
                    $selected_cat_ids[] = $ex->category_id;
                }
            }
        }

        $data['page_heading'] = $page_heading;
        $data['product_id'] = $product_id;
        $data['product'] = $product;
        $data['categories'] = $categories;
        $data['ColorMaster'] = $ColorMaster;
        $data['SizeChart'] = $SizeChart;
        $data['SizeList'] = $SizeList;
        $data['BrandList'] = $BrandList;
        $data['selected_cat_ids'] = $selected_cat_ids;

        return view('admin.products.form', $data);

    }


    public function save(Request $request, $product, $product_id){
        //prd($request->toArray());

        $back_url = (isset($request->back_url))?$request->back_url:'';

        if(empty($back_url)){
            $back_url = 'admin/products';
        }

        $req_product_id = (isset($request->product_id))?$request->product_id:0;
        $featured = (isset($request->featured))?$request->featured:0;
        $trending = (isset($request->trending))?$request->trending:0;
        $popularity = (isset($request->popularity))?$request->popularity:0;

        $images_remove = (isset($request->images_remove))?$request->images_remove:'';
        $is_default = (isset($request->is_default))?$request->is_default:'';
        $is_reverse = (isset($request->is_reverse))?$request->is_reverse:'';

        $sizes_arr = $request->sizes;
        $attr_arr = (isset($request->attr))?$request->attr:'';


        $images = '';

        $rules = [];
        $validation_msg = [];

        //$rules['type'] = 'required';
        $rules['name'] = 'required';
        $rules['sku'] = ['required', Rule::unique('products')->ignore($req_product_id)];
        $rules['category'] = 'required';
        $rules['category.*'] = 'required|numeric';


        $rules['price'] = 'required|numeric';
        $rules['sale_price'] = 'nullable|numeric|max:'.$request->price;
        $rules['gst'] = 'required|numeric';
        //$rules['sizes'] = 'required';
        //$rules['sizes.*'] = 'required';
        $rules['weight'] = 'nullable|numeric';
        //$rules['min_order_qty'] = 'required|integer';
        //$rules['sort_order'] = 'required|integer';

        $rules['status'] = 'required';

        $ext = 'jpg,jpeg,png,gif';

        //$rules['main_image'] = 'nullable|image|mimes:'.$ext;
        //$rules['images.*'] = 'nullable|image|mimes:'.$ext;

        $validation_msg['sale_price.max'] = 'The Sale Price may not be greater than Price: '.$request->price;


        $this->validate($request, $rules, $validation_msg);

        $discount = CustomHelper::calculateProductDiscount($request->price, $request->sale_price);

        //prd($discount);

        //prd($request->toArray());

        $req_data = [];

        $req_data = $request->except(['_token', 'product_id', 'p1_cat', 'p2_cat', 'category', 'images', 'image_ids', 'is_default','is_reverse', 'sizes', 'attr', 'count_images', 'back_url']);

        $slug = CustomHelper::GetSlug('products', 'id', $product_id, $request->name);

        $req_data['slug'] = $slug;
        $req_data['featured'] = $featured;
        $req_data['popularity'] = $popularity;
        $req_data['discount'] = $discount;

        //prd($req_data);

        if(!empty($product) && count($product) > 0 && $req_product_id == $product_id){
            $isSaved = Product::where('id', $product->id)->update($req_data);
        }
        else{
            $isSaved = Product::create($req_data);

            $product_id = $isSaved->id;
        }


        if ($isSaved) {

            $this->removeImages($images_remove, $product_id);
            $this->makeDefaultImage($is_default, $product_id);            
            $this->makeReverseImage($is_reverse, $product_id);
            $this->saveAttributes($attr_arr, $product_id);
            $this->saveSizes($sizes_arr, $product_id);

            $this->saveCategories($request, $product_id);
            $this->saveImageLinks($request, $product_id);

            /*if ($request->hasFile('main_image')) {
                $file = $request->file('main_image');
                $images_result = $this->saveMainImage($file, $product_id);
            }

            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $images_result = $this->saveImages($files, $product_id);
            }*/
            

            return redirect(url($back_url))->with('alert-success', 'The Product has been saved successfully.');
        }
        else{
            return back()->with('alert-danger', 'The product cannot be added, please try again or contact the administrator.');
        }


    }


    private function saveImageLinks($request, $product_id){
        //prd($request->toArray());

        $images = $request->images;
        $image_ids_arr = $request->image_ids;
        $is_default_id = (isset($request->is_default))?$request->is_default:[];
        $is_reverse_id = (isset($request->is_reverse))?$request->is_reverse:[];

        //prd($is_reverse);

        if(!empty($images)){
            foreach($images as $iKey=>$image){

                $image = trim($image);

                $productImage = new ProductImage;

                if(isset($image_ids_arr[$iKey])){
                    $image_id = $image_ids_arr[$iKey];

                    if(is_numeric($image_id) && $image_id > 0){
                        $exist = ProductImage::find($image_id);
                        if(isset($exist->id) && $exist->id == $image_id){
                            $productImage = $exist;
                        }
                    }
                    $is_default = ($is_default_id == $image_id)?1:0;
                    $is_reverse = ($is_reverse_id == $image_id)?1:0;

                    $productImage->is_default = $is_default;
                    $productImage->is_reverse = $is_reverse;

                    //pr($image_id);
                    //prd($is_reverse);

                    /*if(isset($is_reverse[$image_id])){

                        pr($is_reverse);

                        prd($productImage->toArray());
                    }*/
                }                
                if(!empty($image)){
                    $productImage->product_id = $product_id;
                    $productImage->image = $image;
                    $productImage->save();
                }

            }
        }
    }



    //ajax_remove_link
    public function ajaxRemoveLink(Request $request){
        //prd($request->toArray());

        $response = [];
        $response['success'] = false;

        $message = '';

        $image_id = (isset($request->iid))?$request->iid:0;

        if(is_numeric($image_id) && $image_id > 0){
            $productImage = ProductImage::find($image_id);

            if(!empty($productImage)){

                $isDeleted = $productImage->delete();

                if($isDeleted){
                    $response['success'] = true;
                }


            }
        }

        $response['message'] = $message;

        return response()->json($response);

    }


    private function saveCategories($request, $product_id){

        if(is_numeric($product_id) && $product_id > 0){
            $category_data = [];

            $p1_cat = (isset($request->p1_cat))?$request->p1_cat:0;
            $p2_cat = (isset($request->p2_cat))?$request->p2_cat:0;
            $category_id = (isset($request->category))?$request->category:0;

            if(!empty($p1_cat) && !empty($p2_cat) && !empty($category_id)){

                DB::table('product_categories')->where('product_id', $product_id)->delete();

                $category_data['product_id'] = $product_id;
                $category_data['p1_cat'] = $p1_cat;
                $category_data['p2_cat'] = $p2_cat;
                $category_data['category_id'] = $category_id;

                DB::table('product_categories')->insert($category_data);

            } 
        }
    }


    private function saveAttributes($attrArr, $product_id){

        $is_inserted = '';

        if(!empty($attrArr) && count($attrArr) > 0){

            $attrData = [];

            foreach($attrArr as $attrKey=>$attrVal){

                if(!empty($attrVal)){
                    $attrData[] = array(
                        'product_id' => $product_id,
                        'label' => $attrKey,
                        'value' => $attrVal,
                    );

                }
            }

            if(!empty($attrData) && count($attrData) > 0){

                DB::table('product_attributes')->where('product_id', $product_id)->delete();

                $is_inserted = DB::table('product_attributes')->insert($attrData);
            }
        }

        return $is_inserted;

    }


    private function saveSizes($sizeArr, $product_id){

        $is_inserted = '';

        if(!empty($sizeArr) && count($sizeArr) > 0){

            $sizeData = [];

            $Sizes = Size::select(['id', 'name', 'status'])->get()->keyBy('id');

            foreach($sizeArr as $size_id){

                if(is_numeric($size_id) && $size_id > 0){

                    $size_name = '';

                    if(isset($Sizes[$size_id])){
                        $size_name = $Sizes[$size_id]->name;
                    }

                    $sizeData[] = array(
                        'product_id' => $product_id,
                        'size_id' => $size_id,
                        'size_name' => $size_name,
                    );

                }
            }
        }

        return $is_inserted;

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

    public function saveMainImage($file, $product_id, $ext='jpg,jpeg,png,gif'){

        $upload_result = [];

        if ($file) {

            $path = 'products/';
            $thumb_path = 'products/thumb/';

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_THUMB_HEIGHT');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_THUMB_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

            $upload_result = CustomHelper::UploadImage($file, $path, $ext, $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

            $images_data = [];

            if($upload_result['success']){
                $images_data = array(
                    'product_id' => $product_id,
                    'image' => $upload_result['file_name'],
                    'is_default' => 1
                );
            }

            if(!empty($images_data) && count($images_data) > 0){

                $updateData = [];
                $updateData['is_default'] = '';
                ProductImage::where('product_id', $product_id)->update($updateData);

                $is_inserted = ProductImage::insert($images_data);
            }

        }

        return $upload_result;

    }

    public function saveImages($files, $product_id, $ext='jpg,jpeg,png,gif'){

        $is_inserted = '';

        if ($files && count($files) > 0) {

            //prd($files);

            $path = 'products/';
            $thumb_path = 'products/thumb/';

            //UploadImage($file, $path, $ext='', $width=768, $height=768, $is_thumb=false, $thumb_path, $thumb_width=300, $thumb_height=300)

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_THUMB_HEIGHT');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_THUMB_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

            $images_data = [];

            foreach($files as $file){
                $upload_result = CustomHelper::UploadImage($file, $path, $ext, $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

                if($upload_result['success']){
                    $images_data[] = array(
                        'product_id' => $product_id,
                        'image' => $upload_result['file_name']
                    );
                }
            }

            if(!empty($images_data) && count($images_data) > 0){
                $is_inserted = ProductImage::insert($images_data);
            }

        }

        return $is_inserted;

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

    public function removeImages($image_id_arr, $product_id){

        if(is_numeric($product_id) && $product_id > 0 && !empty($image_id_arr) && count($image_id_arr) > 0 ){

            $path = 'products/';
            $thumb_path = 'products/thumb/';

            $storage = Storage::disk('public');

            foreach($image_id_arr as $id){
                $productImage = ProductImage::where(['id'=>$id, 'product_id'=>$product_id])->first();

                if(isset($productImage->id) && $productImage->product_id == $product_id){
                    $image_name = $productImage->image;

                    $productImage->delete();

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

    private function makeDefaultImage($image_id, $product_id){

         if(is_numeric($image_id) && $image_id > 0 && is_numeric($product_id) && $product_id > 0 ){
            $image = ProductImage::where(['id'=>$image_id, 'product_id'=>$product_id])->first();

            if(isset($image->id) && $image->product_id == $product_id){

                $image->is_default = 1;
                $image->save();

                ProductImage::where('product_id', $product_id)->where('id', '!=', $image_id)->update(['is_default'=>0]);
            }           
        }
        elseif(is_numeric($product_id) && $product_id > 0){

            $productImages = ProductImage::where(['product_id'=>$product_id])->get();

            if(!empty($productImages) && count($productImages) > 0){

                $defaultImage = $productImages->where('is_default', 1);

                if(empty($defaultImage) || count($defaultImage) == 0){
                    $firstImage = $productImages->first();

                    if(!empty($firstImage) && count($firstImage) > 0){
                        $firstImage->is_default = 1;
                        $firstImage->save();
                    }
                }
            }
        }
    }


    private function makeReverseImage($image_id, $product_id){

       if(is_numeric($image_id) && $image_id > 0 && is_numeric($product_id) && $product_id > 0 ){
        $image = ProductImage::where(['id'=>$image_id, 'product_id'=>$product_id])->first();

        if(isset($image->id) && $image->product_id == $product_id){

            $image->is_reverse = 1;
            $image->save();

            ProductImage::where('product_id', $product_id)->where('id', '!=', $image_id)->update(['is_reverse'=>0]);
        }           
    }

}


    public function ajax_add_stock(Request $request){
        //prd($request->toArray());
        $response = [];
        $response['success'] = false;

        $message = '';

        $product_id = (isset($request->product_id))?$request->product_id:0;
        $stock = (isset($request->stock))?$request->stock:0;

        if(is_numeric($product_id) && $product_id > 0 && is_numeric($stock) && $stock > 0 ){

            $admin = auth()->guard('admin')->user();

            $admin_id = (isset($admin->id))?$admin->id:'';

            $product = Product::find($product_id);

            $curr_stock = (isset($product->stock))?$product->stock:0;

            $new_stock = $curr_stock + $stock;

            $product->stock = $new_stock;

            $is_saved = $product->save();

            if($is_saved){
                $response['success'] = true;
                $response['new_stock'] = $new_stock;

                $message = '<div class="alert alert-success alert-dismissible"> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Stock has been added. </div>';

                $stock_history = [];
                $stock_history['user_id'] = $admin_id;
                $stock_history['product_id'] = $product_id;
                $stock_history['qty'] = $stock;
                $stock_history['total_stock'] = $new_stock;
                $stock_history['type'] = 'increase';

                DB::table('product_stock_history')->insert($stock_history);
            }
        }

        $response['message'] = $message;

        return response()->json($response);
    }


    public function ajaxGetCategoryChild(Request $request){
        //pr($request->toArray());

        $response = [];
        $response['success'] = false;

        $message = '';

        $parent_id = (isset($request->parent_id))?$request->parent_id:0;
        $selected_id = (isset($request->selected_id))?$request->selected_id:'';

        $selected_id = json_decode($selected_id);

        if(is_numeric($parent_id) && $parent_id > 0){
            $categories = Category::where('parent_id', $parent_id)->get();

            if(!empty($categories) && count($categories) > 0){

                $parentCategory = Category::find($parent_id);

                $parentCatName = (isset($parentCategory->name))?$parentCategory->name:'';

                $label = 'Sub-Category';

                $data = [];
                $data['categories'] = $categories;

                if(!empty($parentCatName)){
                    $label .= '('.$parentCatName.')';
                }
                
                $data['label'] = $label;
                $data['selected_id'] = $selected_id;

                $categoryDropdownHtml = view('admin.products._category_dropdown', $data)->render();

                $response['success'] = true;

                $response['categoryDropdownHtml'] = $categoryDropdownHtml;
            }
        }

        $response['message'] = $message;

        return response()->json($response);

    }

    //ajax_get_category_attributes
    public function ajaxGetCategoryAttributes(Request $request){
        //pr($request->toArray());

        $response = [];
        $response['success'] = false;

        $message = '';

        $category_id = (isset($request->category_id))?$request->category_id:0;
        $product_id = (isset($request->product_id))?$request->product_id:0;
        $attrArrJson = (isset($request->attrArrJson))?$request->attrArrJson:'';

        $attrObj = (!empty($attrArrJson))?(json_decode($attrArrJson)):[];
        //$attrArr = (!empty($attrArrJson))?(unserialize($attrArrJson)):[];

        $attrObjArr = (array)$attrObj;

        //prd($attrObj);

        if(is_numeric($category_id) && $category_id > 0){
            $category = Category::find($category_id);

            if(!empty($category) && count($category) > 0){

                $productAttributesArr = [];

                if(is_numeric($product_id) && $product_id > 0){
                    $product = Product::find($product_id);

                    $productAttributes = (isset($product->productAttributes))?$product->productAttributes:'';
                    if(!empty($productAttributes) && count($productAttributes) > 0){
                        $productAttributesArr = $productAttributes->keyBy('label');
                    }
                }


                $parentCategoryAttributes = '';
                $getParentCategoryAttributes = CustomHelper::getParentCategoryAttributes($category);

                if(!empty($getParentCategoryAttributes) && count($getParentCategoryAttributes) > 0){
                    $parentCategoryAttributes = array_flatten($getParentCategoryAttributes);
                }

                if(!empty($parentCategoryAttributes) && count($parentCategoryAttributes) > 0){
                    $viewData = [];
                    $viewData['attributes'] = $parentCategoryAttributes;
                    $viewData['productAttributesArr'] = $productAttributesArr;
                    $viewData['attrObjArr'] = $attrObjArr;

                    $attributesListHtml = view('admin.products._attributes_list', $viewData)->render();
                    $response['success'] = true;
                    $response['attributesListHtml'] = $attributesListHtml;
                }


            }
        }

        $response['message'] = $message;

        return response()->json($response);

    }

    public function inventory(Request $request){
        //prd($request->toArray());
        $limit = $this->limit;
        $data = [];
        $method = $request->method();
        $form_heading = 'Add Inventory';
        $inventories = [];
        $inventory = [];

        $productId = (isset($request->product_id))?$request->product_id:0;
        $inventoryId = (isset($request->inventory_id))?$request->inventory_id:0;
        $back_url = (isset($request->back_url))?$request->back_url:'';

        //prd($inventoryId);
        if(is_numeric($inventoryId) && $inventoryId > 0){
            $inventory = ProductInventory::find($inventoryId);
            $form_heading = 'Edit Inventory';
        }

        if($method == 'POST' || $method == 'post'){
            
            $rules = [];
            $rules['sku'] = 'required';
            $rules['size_id'] = 'required|numeric';
            $rules['stock'] = 'required|numeric';
            

            $validator = $this->validate($request, $rules);
            $size_id = (isset($request->size_id))?$request->size_id:0;

            $stock = (isset($request->stock))?$request->stock:0;

            $sizeName = '';

            $productInventory = new ProductInventory;

            if(is_numeric($size_id) && $size_id > 0){
                $sizes = Size::find($size_id);

                $existInv = ProductInventory::where(['product_id'=>$productId, 'size_id'=>$size_id])->first();

                if(isset($existInv->product_id) && $existInv->product_id == $productId){
                    $productInventory = $existInv;

                    //$stock = $stock + $existInv->stock;
                }
            }
            if(!empty($sizes) && count($sizes) > 0){
                $sizeName = $sizes->name;  
            }

            $productInventory->sku = $request->sku;
            $productInventory->size_id = (isset($request->size_id))?$request->size_id:0;
            $productInventory->stock = $stock;
            $productInventory->product_id = $productId;
            $productInventory->size_name = $sizeName;

            $isSaved = $productInventory->save();

            if($isSaved){
                session()->flash('alert-success', 'Inventory has been saved successfully');                
                return redirect('admin/products/'.$productId.'/inventory');
            }            
        }
        
        $inventories = ProductInventory::where('product_id',$productId)->paginate($limit);

        $sizes = Size::get();

        $data['sizes'] = $sizes;
        $data['form_heading'] = $form_heading;
        $data['inventories'] = $inventories;
        $data['inventory'] = $inventory;
        $data['productId'] = $productId;

        return view('admin.products.inventory', $data);
    }

    //delete_inventory
    public function deleteInventory(Request $request){
        //prd($request->toArray());
        $productId = (isset($request->product_id))?$request->product_id:0;
        $inventory_id = $request->inventory_id;
        $method = $request->method();
        $is_deleted = 0;

        if($method == "POST"){
            if(is_numeric($inventory_id) && $inventory_id > 0){
                $inventory = ProductInventory::find($inventory_id);
                if(!empty($inventory) && count($inventory) > 0){
                    $is_deleted = $inventory->delete();
                }
            }
        }

        if($is_deleted){
            return redirect(url('admin/products/'.$productId.'/inventory'))->with('alert-success', 'The Inventory has been deleted successfully.');
        }
        else{
            return redirect(url('admin/products/'.$productId.'/inventory'))->with('alert-danger', 'The Inventory cannot be deleted, please try again or contact the administrator.');
        }

    }


    public function upload(Request $request){
        $data = [];

        if($request->method() == 'POST' || $request->method() == 'post'){

            $extArr = ['csv', 'xls', 'xlsx'];

            //prd($request->toArray());

            $rules = [];
            $validation_msg = [];

            $rules['upload'] = 'required';
            $this->validate($request, $rules);

            $path = $request->file('upload')->getRealPath();
            $file = $request->file('upload');

            $file_ext = strtolower($file->getClientOriginalExtension());

            //prd($file->getClientOriginalExtension());

            $rules = [];
            $messages = [];

            //$rules['upload'] = 'required|mimes:csv';
            $rules['upload'] = 'required';

            //$this->validate($request, $rules, $messages);

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($file_ext, $extArr) {
                if ( !in_array($file_ext, $extArr) ) {
                    $validator->errors()->add('upload', 'please upload valid csv/xls/xlsx file.');
                }
            });

            if($validator->fails()){
                return back()->withErrors($validator->errors());
            }

            //prd($request->toArray());
            
            $file_name = $request->file_name;
            $column = $request->column;

            $result = '';

            $result = Excel::import(new ProductImport, $file);

            if($result){
                return redirect('admin/products/upload');
            }
            else{
                return redirect('admin/products/upload')->with('err_msg', 'something went wrong, please try again.');
            }
            

        }


        return view('admin.products.upload', $data);
    }



    // inventory_list
    public function inventoryList(Request $request){

        //echo "products-index"; die;

        $data = [];

        $limit = $this->limit;

        $export_xls = (isset($request->export_xls))?$request->export_xls:'';
        $export_inventory = (isset($request->export_inventory))?$request->export_inventory:'';

        $sortBy = (isset($request->sortBy))?$request->sortBy:'';

        $name = (isset($request->name))?$request->name:'';
        $category = (isset($request->category))?$request->category:'';

        $price_scope = (isset($request->price_scope))?$request->price_scope:'=';
        $price = (isset($request->price))?$request->price:'';

        $stock_scope = (isset($request->stock_scope))?$request->stock_scope:'=';
        $stock = (isset($request->stock))?$request->stock:'';

        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');


        $inventoryQuery = ProductInventory::orderBy('id', 'desc');

        if(!empty($sortBy) && $sortBy == 'top_selling'){
            $productQuery->whereRaw("id IN (select product_id from order_items Group By order_items.product_id Order By sum(order_items.qty) desc)");
        }

        if(!empty($name)){
            $productQuery->where(function($query) use($name){
                $query->where('name', 'like', '%'.$name.'%');
                $query->orWhere('sku', 'like', '%'.$name.'%');
            });
        }

        if(is_numeric($category) && $category > 0){
            $productQuery->whereHas('productCategories', function ($query) use ($category) {
                $query->where('category_id', $category);
            });
        }

        if(is_numeric($price) && $price > 0){
            $productQuery->where('price', $price_scope, $price);
        }

        if(is_numeric($stock) && $stock > 0){
            $productQuery->where('stock', $stock_scope, $stock);
        }

        if( strlen($status) > 0 ){
            $productQuery->where('status', $status);
        }

        if(!empty($from_date)){
            $productQuery->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $productQuery->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }

        //dd($productQuery);

        if(!empty($export_inventory) && ($export_inventory == 1 || $export_inventory == '1') ){
            return $this->exportInventoryList($inventoryQuery);
        }

        //DB::enableQueryLog();
        $inventories = $inventoryQuery->paginate($limit);
        //prd(DB::getQueryLog());
        
        $data['inventories'] = $inventories;
        $data['limit'] = $limit;

        return view('admin.products.inventory_list', $data);

    }

    private function exportInventoryList($inventoryQuery){

        $select = ['id', 'name'];

        $inventories = $inventoryQuery->get();

        $exportArr = [];
        if(!empty($inventories) && $inventories->count() > 0){
            foreach($inventories as $inventory){

                $inventoryArr = [];

                $product = $inventory->inventoryProduct;

                $productName = $product->name;

                $inventoryArr['product_id'] = $product->id;
                $inventoryArr['product_name'] = $productName;
                $inventoryArr['sku'] = $inventory->sku;
                $inventoryArr['size'] = $inventory->size_name;
                $inventoryArr['stock'] = $inventory->stock;

                $exportArr[] = $inventoryArr;
                }

            }
                //prd($exportArr);
        $fieldNames = array_keys($exportArr[0]);

        //prd($filedNames);

        $fileName = 'inventorylist_'.date('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new ProductExport($exportArr, $fieldNames), $fileName);
    }


        private function exportInventoryList30oct2019($inventoryQuery){

        $select = ['id', 'name'];

        $inventories = $inventoryQuery->get();

        $fileName = 'inventory_'.date('Y-m-d-H-i-s').'.xlsx';

        $viewData = [];
        $viewData['products'] = '';
        $viewData['inventories'] = $inventories;

        //$viewHtml = view('admin.products._inventory_export', $viewData)->render();

        //echo $viewHtml; die;       

        header('Content-Type: application/vnd.ms-excel');
        //tell browser what's the file name
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        //no cache
        header('Cache-Control: max-age=0');

        return view('admin.products._inventory_export', $viewData);
    }

    // inventory_upload
    public function inventoryUpload(Request $request){
        $data = [];

        if($request->method() == 'POST' || $request->method() == 'post'){

            $extArr = ['csv', 'xls', 'xlsx'];

            //prd($request->toArray());

            $rules = [];
            $validation_msg = [];

            $rules['upload'] = 'required';
            $this->validate($request, $rules);



            $path = $request->file('upload')->getRealPath();
            $file = $request->file('upload');

            $file_ext = strtolower($file->getClientOriginalExtension());

            //prd($file->getClientOriginalExtension());

            $rules = [];
            $messages = [];

            //$rules['upload'] = 'required|mimes:csv';
            $rules['upload'] = 'required';

            //$this->validate($request, $rules, $messages);

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($file_ext, $extArr) {
                if ( !in_array($file_ext, $extArr) ) {
                    $validator->errors()->add('upload', 'please upload valid csv/xls/xlsx file.');
                }
            });

            if($validator->fails()){
                return back()->withErrors($validator->errors());
            }

            //prd($request->toArray());
            
            $file_name = $request->file_name;
            $column = $request->column;

            $result = '';

            $result = Excel::import(new InventoryImport, $file);

            if($result){
                return redirect('admin/products/inventory_upload');
            }
            else{
                return redirect('admin/products/inventory_upload')->with('err_msg', 'something went wrong, please try again.');
            }
            

        }


        return view('admin.products.inventory_upload', $data);
    }



    /* end of controller */
}