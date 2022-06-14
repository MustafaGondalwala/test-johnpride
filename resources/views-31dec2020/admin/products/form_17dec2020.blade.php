@component('admin.layouts.main')

@slot('title')
Admin - {{$page_heading}} - {{ config('app.name') }}
@endslot

@slot('headerBlock')
<link href="{{url('/')}}/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

<style>
   .multiselect-native-select, span.multiselect-native-select .btn, .btn-group{width:100%; text-align: left;  }
   .btn{  overflow: hidden;}
   .caret{   right: 7px;   position: absolute; top: 15px;}
</style>

@endslot

<div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';
    if(empty($back_url)){
        $back_url = 'admin/fabric';
    }

    $IMG_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_IMG_HEIGHT');
    $IMG_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_IMG_WIDTH');
    $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
    $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;


    $product_id = (isset($product->id))?$product->id:0;

    $category_id = (isset($product->category_id))?$product->category_id:'';
    //$type = (isset($product->type))?$product->type:'';
    $name = (isset($product->name))?$product->name:'';
    $slug = (isset($product->slug))?$product->slug:'';
    $sku = (isset($product->sku))?$product->sku:'';
    $mobile_image = (isset($product->mobile_image))?$product->mobile_image:'';
    $desktop_image = (isset($product->desktop_image))?$product->desktop_image:'';
    $brand_id = (isset($product->brand_id))?$product->brand_id:0;
    $specifications = (isset($product->specifications))?$product->specifications:'';
    $description = (isset($product->description))?$product->description:'';
    $gender = (isset($product->gender))?$product->gender:'f';
    $video = (isset($product->video))?$product->video:'';
    $color_id = (isset($product->color_id))?$product->color_id:'';
    $color_name = (isset($product->color_name))?$product->color_name:'';
    $size_chart_id = (isset($product->size_chart_id))?$product->size_chart_id:'';
    $price = (isset($product->price))?$product->price:'';
    $sale_price = (isset($product->sale_price))?$product->sale_price:'';
    $gst = (isset($product->gst))?$product->gst:'';
    $gsm = (isset($product->gsm))?$product->gsm:'';
    $weight = (isset($product->weight))?$product->weight:'';
    $size = (isset($product->size))?$product->size:'';
    $delivery_duration = (isset($product->delivery_duration))?$product->delivery_duration:'';
    $min_order_qty = (isset($product->min_order_qty))?$product->min_order_qty:'';
    $style_id = (isset($product->style_id))?$product->style_id:'';
    $manufacturer = (isset($product->manufacturer))?$product->manufacturer:'';
    $country_origin  = (isset($product->country_origin))?$product->country_origin:'';
    $net_qty = (isset($product->net_qty))?$product->net_qty:'';
    $business_unit = (isset($product->business_unit))?$product->business_unit:'';
    $product_type = (isset($product->product_type))?$product->product_type:'';
    $standard_size = (isset($product->standard_size))?$product->standard_size:'';
    $hsn = (isset($product->hsn))?$product->hsn:'';  
    $age_group = (isset($product->age_group))?$product->age_group:'';  
    $brand_color = (isset($product->brand_color))?$product->brand_color:'';  
    $base_color = (isset($product->base_color))?$product->base_color:'';  
    $fashion_type = (isset($product->fashion_type))?$product->fashion_type:'';  
    $prod_usage = (isset($product->prod_usage))?$product->prod_usage:'';  
    $year = (isset($product->year))?$product->year:'';  
    $tags = (isset($product->tags))?$product->tags:'';  
    $across_shoulder = (isset($product->across_shoulder))?$product->across_shoulder:'';  
    $season = (isset($product->season))?$product->season:'';  
    $bust = (isset($product->bust))?$product->bust:'';  
    $chest = (isset($product->chest))?$product->chest:'';  
    $front_length = (isset($product->front_length))?$product->front_length:'';  
    $to_fit_bust = (isset($product->to_fit_bust))?$product->to_fit_bust:'';  
    $sleeve_length = (isset($product->sleeve_length))?$product->sleeve_length:'';  
    $to_fit_waist = (isset($product->to_fit_waist))?$product->to_fit_waist:'';  
    $waist = (isset($product->waist))?$product->waist:'';   

    $main_image = (isset($product->main_image))?$product->main_image:'';
    $other_images = (isset($product->other_images))?$product->other_images:'';

    $sort_order = (isset($product->sort_order))?$product->sort_order:'';
    $stamp = (isset($product->stamp))?$product->stamp:'';
    $featured = (isset($product->featured))?$product->featured:'';
    $trending = (isset($product->trending))?$product->trending:'';
    $popularity = (isset($product->popularity))?$product->popularity:'';
    $new_arrival = (isset($product->new_arrival))?$product->new_arrival:'';
    $status = (isset($product->status))?$product->status:'';


    $meta_title = (isset($product->meta_title))?$product->meta_title:'';
    $meta_keyword = (isset($product->meta_keyword))?$product->meta_keyword:'';
    $meta_description = (isset($product->meta_description))?$product->meta_description:'';

    $productCategories = (isset($product->productCategories))?$product->productCategories:'';
    $productImages = (isset($product->productImages))?$product->productImages:'';
    $productAttributes = (isset($product->productAttributes))?$product->productAttributes:'';

    $productSizes = (isset($product->productSizes))?$product->productSizes:'';
    $loyalty_points = (isset($product->loyalty_points))?$product->loyalty_points:0;
    $related_product_cat = (isset($related_product_cat))?$related_product_cat:0;

    $productSizesArr = [];

    $productCategory = '';

    if(!empty($productSizes) && count($productSizes) > 0){
        //pr($productSizes->toArray());

        foreach($productSizes as $ps){
            $productSizesArr[] = $ps->id;
        }
    }

    if(!empty($productCategories) && count($productCategories) > 0){
        $productCategory = $productCategories[0];
    }
    

    if($weight <= 0){
        $weight = '';
    }

    /*if($errors){
        pr($errors->toArray());
    }*/

    $sizes = old('sizes', $productSizesArr);

    $p1_cat_id = 0;
    $p2_cat_id = 0;
    $category_id = 0;

    if(!empty($productCategory) && count($productCategory) > 0){

        if(isset($productCategory->pivot) && count($productCategory->pivot) > 0){
            $p1_cat_id = $productCategory->pivot->p1_cat;
            $p2_cat_id = $productCategory->pivot->p2_cat;
            $category_id = $productCategory->pivot->category_id;
        }
    }


    $p1_cat = old('p1_cat', $p1_cat_id);
    $p2_cat = old('p2_cat', $p2_cat_id);
    $category = old('category', $category_id);
    $related_product_cat = old('related_product_cat', $related_product_cat);

   
    $attrArr = old('attr');

    $attrArr = (!empty($attrArr))?$attrArr:[];

    //pr($attrArr);

    $productStampsArr = config('custom.product_stamps_arr');
    $gstArr = config('custom.gst_arr');

    //pr($productStampsArr);

    $path = 'products/';
    $thumb_path = 'products/thumb/';

    $storage = Storage::disk('public');

    $attrArrJson = json_encode($attrArr);
    //$attrArrJson = serialize($attrArr);

    ?>
	 

    <div class="col-md-12">

        <h2>{{$page_heading}}</h2>
        <div class="bgcolor">

            @include('snippets.errors')
            @include('snippets.flash')

            <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}

                <input type="hidden" name="product_id" value="{{$product_id}}">

                <div class="row">

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">

                        <div class="form-group{{ $errors->has('sku') ? ' has-error' : '' }}">

                            <label class="control-label">SKU:</label>

                            <input type="text" class="form-control" name="sku" value="{{ old('sku', $sku) }}" maxlength="20" />

                            @include('snippets.errors_first', ['param' => 'sku'])
                        </div>
                    </div>


                    <!-- here is dynamic Category dropdown -->
                    <?php

                    $viewData = [];
                    $viewData['categories'] = $categories;
                    $viewData['label'] = 'Category';
                    $viewData['selected_id'] = $p1_cat;
                    ?>
                    

                    <div class="col-sm-4 categoryBox">

                        <div class="form-group{{ $errors->has('p1_cat') ? ' has-error' : '' }}">
                            <label class="control-label required">Category:</label>

                            <select name="p1_cat" class="form-control categoryList">
                                @include('admin.products._category_dropdown', $viewData)
                            </select>

                            <?php
                            if(!is_numeric($p1_cat) || empty($p1_cat)){
                                ?>
                                @include('snippets.errors_first', ['param' => 'p1_cat'])
                                <?php
                            }
                            ?>
                        </div>
                    </div>


                    <?php /* ?>

                    <div class="col-sm-4 categoryBox">

                        <div class="form-group{{ $errors->has('p2_cat') ? ' has-error' : '' }}">
                            <label class="control-label required">Sub-Category:</label>

                            <select name="p2_cat" class="form-control categoryList">
                                <option value="">--Select--</option>                                
                            </select>

                            @include('snippets.errors_first', ['param' => 'p2_cat'])
                        </div>
                    </div>

                    <?php */ ?>

                    <div class="col-sm-4 categoryBox">

                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                            <label class="control-label">Sub-Category:</label>

                            <select name="category" class="form-control categoryList" >
                                <option value="">--Select--</option>
                                
                            </select>

                            @include('snippets.errors_first', ['param' => 'category'])
                        </div>
                    </div>
                    <?php  ?>
                        

                    <?php
                    /*
                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('specifications') ? ' has-error' : '' }}">
                            <label class="control-label">Specifications:</label>

                            <textarea name="specifications" class="form-control" maxlength="255" rows="5">{{ old('specifications', $specifications) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'specifications'])
                        </div>
                    </div>
                    */
                    ?>


                    <?php

                    $viewData = [];
                    $viewData['categories'] = $related_product_categories;
                    $viewData['label'] = 'Raletd Product Category';
                    $viewData['selected_id'] = $related_product_cat;
                    ?>

                    <div class="col-sm-4 categoryBox">

                        <div class="form-group{{ $errors->has('p1_cat') ? ' has-error' : '' }}">
                            <label class="control-label">Raletd Product Category:</label>

                            <select name="related_product_cat" class="form-control">
                                @include('admin.products._category_dropdown', $viewData)
                            </select>

                            <?php
                            if(!is_numeric($related_product_cat) || empty($related_product_cat)){
                                ?>
                                @include('snippets.errors_first', ['param' => 'related_product_cat'])
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    

                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="control-label">Description:</label>

                            <textarea name="description" class="form-control" maxlength="2048" rows="5">{{ old('description', $description) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'description'])
                        </div>
                    </div>




                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">

                            <?php
                            $sel_gender = old('gender', $gender);

                            //pr($sel_gender);
                            ?>

                            <label class="control-label required">Gender:</label>
                            <br>
                            <input type="radio" name="gender" value="f" {{($sel_gender == 'f')?'checked':''}}>Female
                            &nbsp;
                            <input type="radio" name="gender" value="m" {{($sel_gender == 'm')?'checked':''}}>Male

                            @include('snippets.errors_first', ['param' => 'gender'])
                        </div>

                    </div>



                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('brand_id') ? ' has-error' : '' }}">
                            <label class="control-label">Brand:</label>

                            <?php
                            if(!empty($BrandList) && count($BrandList) > 0){
                                ?>

                                <select name="brand_id" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                                    foreach($BrandList as $bl){
                                        $selected = '';

                                        if($bl->id == $brand_id){
                                            $selected = 'selected';
                                        }
                                        
                                            ?>
                                            <option value="{{$bl->id}}" {{$selected}} >{{$bl->name}}</option>
                                            <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'brand_id'])
                        </div>
                    </div>




                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('color_id') ? ' has-error' : '' }}">
                            <label class="control-label">Color:</label>
                            <input type="hidden" name="color_name" value="{{$color_name}}">

                            

                                <select name="color_id" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                            if(!empty($ColorMaster) && count($ColorMaster) > 0){
                                ?>

                                    <?php
                                    foreach($ColorMaster as $cm){
                                        $selected = '';

                                        if($cm->id == $color_id){
                                            $selected = 'selected';
                                        }
                                        
                                            ?>
                                            <option value="{{$cm->id}}" {{$selected}} >{{$cm->name}}</option>
                                            <?php
                                        
                                        ?>
                                        
                                        <?php
                                    }
                                    ?>
                                
                                <?php
                            }
                            ?>

                            </select>

                            @include('snippets.errors_first', ['param' => 'color_id'])
                        </div>
                    </div>




                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label class="control-label">Stamp:</label>

                            <select name="stamp" class="form-control" >
                                <option value="">--Select--</option>

                                <?php
                                if(!empty($productStampsArr) && count($productStampsArr) > 0){
                                    foreach($productStampsArr as $psaKey=>$psa){
                                        $selected = '';

                                        if($psaKey == $stamp){
                                            $selected = 'selected';
                                        }
                                        
                                        ?>
                                        <option value="{{$psaKey}}" {{$selected}} >{{$psa}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>

                            @include('snippets.errors_first', ['param' => 'stamp'])
                        </div>
                    </div>



                    <?php
                    /*
                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('sizes') ? ' has-error' : '' }}">
                            <label class="control-label required">Size(s):</label>

                            <?php
                            if(!empty($SizeList) && count($SizeList) > 0){
                                ?>

                            <select name="sizes[]" id="sizes" class="form-control" multiple >
                        
                                    <?php
                                    foreach($SizeList as $sl){
                                        $selected = '';

                                        if(in_array($sl->id, $sizes)){
                                            $selected = 'selected';
                                        }
                                        
                                        ?>
                                        <option value="{{$sl->id}}" {{$selected}} >{{$sl->name}}</option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'sizes'])
                        </div>
                    </div>
                    */
                    ?>



                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('size_chart_id') ? ' has-error' : '' }}">
                            <label class="control-label">Size Chart:</label>

                            <?php
                            if(!empty($SizeChart) && count($SizeChart) > 0){
                                ?>

                                <select name="size_chart_id" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                                    foreach($SizeChart as $sc){
                                        $selected = '';

                                        if($sc->id == $size_chart_id){
                                            $selected = 'selected';
                                        }
                                        
                                            ?>

                                            <option value="{{$sc->id}}" {{$selected}} >{{$sc->title}}</option>
                                        
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'size_chart_id'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <label class="control-label required">Price :</label>

                            <input type="text" name="price" class="form-control" value="{{ old('price', $price) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'price'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('sale_price') ? ' has-error' : '' }}">
                            <label class="control-label">Sale Price :</label>

                            <input type="text" name="sale_price" class="form-control" value="{{ old('sale_price', $sale_price) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'sale_price'])
                        </div>
                    </div>

                    

                   
                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('gst') ? ' has-error' : '' }}">
                            <label class="control-label required">GST:</label>

                            <select name="gst" class="form-control" >
                                <option value="">--Select--</option>

                                <?php
                                if(!empty($gstArr) && count($gstArr) > 0){
                                    foreach($gstArr as $gstKey=>$gstVal){
                                        $selected = '';

                                        if($gstKey == $gst){
                                            $selected = 'selected';
                                        }
                                        
                                        ?>
                                        <option value="{{$gstKey}}" {{$selected}} >{{$gstVal}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>

                            @include('snippets.errors_first', ['param' => 'gst'])
                        </div>
                    </div>
                    
                  


                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('weight') ? ' has-error' : '' }}">
                            <label class="control-label "> Weight(kg):</label>

                            <input type="text" name="weight" class="form-control" value="{{ old('weight', $weight) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'weight'])
                        </div>
                    </div>



                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('delivery_duration') ? ' has-error' : '' }}">
                            <label class="control-label "> Delivery Duration:</label>

                            <input type="text" name="delivery_duration" class="form-control" value="{{ old('delivery_duration', $delivery_duration) }}" />

                            @include('snippets.errors_first', ['param' => 'delivery_duration'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('loyalty_points') ? ' has-error' : '' }}">
                            <label class="control-label "> Loyalty Points:</label>

                            <input type="text" name="loyalty_points" class="form-control" value="{{ old('loyalty_points', $loyalty_points) }}" />

                            @include('snippets.errors_first', ['param' => 'loyalty_points'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('style_id') ? ' has-error' : '' }}">
                            <label class="control-label "> Style Id:</label>

                            <input type="text" name="style_id" class="form-control" value="{{ old('style_id', $style_id) }}" />

                            @include('snippets.errors_first', ['param' => 'style_id'])
                        </div>
                    </div>  

                    <div class="col-sm-12 col-md-6">

                        <div class="form-group{{ $errors->has('video') ? ' has-error' : '' }}">
                            <label class="control-label "> Video:</label>

                            <textarea name="video" class="form-control" ><?php echo $video; ?></textarea>

                            @include('snippets.errors_first', ['param' => 'video'])
                        </div>
                    </div>
                    <?php
                    /*
                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('min_order_qty') ? ' has-error' : '' }}">
                            <label class="control-label "> Min. Order Qty:</label>

                            <input type="text" name="min_order_qty" class="form-control" value="{{ old('min_order_qty', $min_order_qty) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'min_order_qty'])
                        </div>
                    </div>
                    */
                    ?>


                    <div class="clearfix"></div>

                    <br>
                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('manufacturer') ? ' has-error' : '' }}">
                            <label class="control-label "> Manufacturer:</label>

                            <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $manufacturer) }}" />

                            @include('snippets.errors_first', ['param' => 'manufacturer'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('country_origin') ? ' has-error' : '' }}">
                            <label class="control-label "> Country Origin:</label>

                            <input type="text" name="country_origin" class="form-control" value="{{ old('country_origin', $country_origin) }}" />

                            @include('snippets.errors_first', ['param' => 'country_origin'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('net_qty') ? ' has-error' : '' }}">
                            <label class="control-label "> Net Quantity:</label>

                            <input type="text" name="net_qty" class="form-control" value="{{ old('net_qty', $net_qty) }}" />

                            @include('snippets.errors_first', ['param' => 'net_qty'])
                        </div>
                    </div>                                         

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('business_unit') ? ' has-error' : '' }}">
                            <label class="control-label "> Business Unit:</label>

                            <input type="text" name="business_unit" class="form-control" value="{{ old('business_unit', $business_unit) }}" />

                            @include('snippets.errors_first', ['param' => 'business_unit'])
                        </div>
                    </div> 

                    <div class="clearfix"></div>

                    <br>
                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('product_type') ? ' has-error' : '' }}">
                            <label class="control-label "> Product Type:</label>

                            <input type="text" name="product_type" class="form-control" value="{{ old('product_type', $product_type) }}" />

                            @include('snippets.errors_first', ['param' => 'product_type'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('standard_size') ? ' has-error' : '' }}">
                            <label class="control-label ">Standard Size:</label>

                            <input type="text" name="standard_size" class="form-control" value="{{ old('standard_size', $standard_size) }}" />

                            @include('snippets.errors_first', ['param' => 'standard_size'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('hsn') ? ' has-error' : '' }}">
                            <label class="control-label "> HSN:</label>

                            <input type="text" name="hsn" class="form-control" value="{{ old('hsn', $hsn) }}" />

                            @include('snippets.errors_first', ['param' => 'hsn'])
                        </div>
                    </div>                                         

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('age_group') ? ' has-error' : '' }}">
                            <label class="control-label "> Age-Group:</label>

                            <input type="age_group" name="age_group" class="form-control" value="{{ old('age_group', $age_group) }}" />

                            @include('snippets.errors_first', ['param' => 'age_group'])
                        </div>
                    </div> 

                    <div class="clearfix"></div>

                    <br> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('brand_color') ? ' has-error' : '' }}">
                            <label class="control-label "> Brand Color:</label>

                            <input type="text" name="brand_color" class="form-control" value="{{ old(' brand_color', $brand_color) }}" />

                            @include('snippets.errors_first', ['param' => 'brand_color'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('base_color') ? ' has-error' : '' }}">
                            <label class="control-label ">Base Color:</label>

                            <input type="text" name="base_color" class="form-control" value="{{ old('base_color', $base_color) }}" />

                            @include('snippets.errors_first', ['param' => 'base_color'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('fashion_type') ? ' has-error' : '' }}">
                            <label class="control-label "> Fashion Type:</label>

                            <input type="text" name="fashion_type" class="form-control" value="{{ old('fashion_type', $fashion_type) }}" />

                            @include('snippets.errors_first', ['param' => 'fashion_type'])
                        </div>
                    </div>                                         

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('prod_usage') ? ' has-error' : '' }}">
                            <label class="control-label "> Usage:</label>

                            <input type="prod_usage" name="prod_usage" class="form-control" value="{{ old('prod_usage', $prod_usage) }}" />

                            @include('snippets.errors_first', ['param' => 'prod_usage'])
                        </div>
                    </div> 

                    <div class="clearfix"></div>

                    <br>                    

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label class="control-label "> Year:</label>

                            <input type="text" name="year" class="form-control" value="{{ old(' year', $year) }}" />
                            @include('snippets.errors_first', ['param' => 'year'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('season') ? ' has-error' : '' }}">
                            <label class="control-label ">Season:</label>

                            <input type="text" name="season" class="form-control" value="{{ old('season', $season) }}" />

                            @include('snippets.errors_first', ['param' => 'season'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
                            <label class="control-label "> Tags:</label>

                            <input type="text" name="tags" class="form-control" value="{{ old('tags', $tags) }}" />

                            @include('snippets.errors_first', ['param' => 'tags'])
                        </div>
                    </div>                                         

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('across_shoulder') ? ' has-error' : '' }}">
                            <label class="control-label "> Across Shoulder:</label>

                            <input type="across_shoulder" name="across_shoulder" class="form-control" value="{{ old('across_shoulder', $across_shoulder) }}" />

                            @include('snippets.errors_first', ['param' => 'across_shoulder'])
                        </div>
                    </div> 

                    <div class="clearfix"></div>

                    <br>

                   <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('bust') ? ' has-error' : '' }}">
                            <label class="control-label "> Bust:</label>

                            <input type="text" name="bust" class="form-control" value="{{ old(' bust', $bust) }}" />
                            @include('snippets.errors_first', ['param' => 'bust'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('chest') ? ' has-error' : '' }}">
                            <label class="control-label ">Chest:</label>

                            <input type="text" name="chest" class="form-control" value="{{ old('chest', $chest) }}" />

                            @include('snippets.errors_first', ['param' => 'chest'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('front_length') ? ' has-error' : '' }}">
                            <label class="control-label "> Front Length:</label>

                            <input type="text" name="front_length" class="form-control" value="{{ old('front_length', $front_length) }}" />

                            @include('snippets.errors_first', ['param' => 'front_length'])
                        </div>
                    </div>                                         

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('to_fit_bust') ? ' has-error' : '' }}">
                            <label class="control-label "> To Fit Bust:</label>

                            <input type="to_fit_bust" name="to_fit_bust" class="form-control" value="{{ old('to_fit_bust', $to_fit_bust) }}" />

                            @include('snippets.errors_first', ['param' => 'to_fit_bust'])
                        </div>
                    </div> 

                    <div class="clearfix"></div>

                    <br>

                   <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('sleeve_length') ? ' has-error' : '' }}">
                            <label class="control-label "> Sleeve Length:</label>

                            <input type="text" name="sleeve_length" class="form-control" value="{{ old('sleeve_length', $sleeve_length) }}" />
                            @include('snippets.errors_first', ['param' => 'sleeve_length'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('to_fit_waist') ? ' has-error' : '' }}">
                            <label class="control-label ">To Fit Waist:</label>

                            <input type="text" name="to_fit_waist" class="form-control" value="{{ old('to_fit_waist', $to_fit_waist) }}" />

                            @include('snippets.errors_first', ['param' => 'to_fit_waist'])
                        </div>
                    </div> 

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('waist') ? ' has-error' : '' }}">
                            <label class="control-label "> Waist:</label>

                            <input type="text" name="waist" class="form-control" value="{{ old('waist', $waist) }}" />

                            @include('snippets.errors_first', ['param' => 'waist'])
                        </div>
                    </div>                                                              

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label class="control-label">Sort Order:</label>

                            <input type="text" name="sort_order" class="form-control" value="{{ old('sort_order', $sort_order) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }}">
                            <label class="control-label ">Best Sellers:</label>

                            <input type="checkbox" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> />

                            @include('snippets.errors_first', ['param' => 'featured'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('trending') ? ' has-error' : '' }}">
                            <label class="control-label ">Trending:</label>

                            <input type="checkbox" name="trending" value="1" <?php echo ($trending == '1')?'checked':''; ?> />

                            @include('snippets.errors_first', ['param' => 'trending'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('popularity') ? ' has-error' : '' }}">
                            <label class="control-label ">Popularity:</label>

                            <input type="checkbox" name="popularity" value="1" <?php echo ($popularity == '1')?'checked':''; ?> />

                            @include('snippets.errors_first', ['param' => 'popularity'])
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('new_arrival') ? ' has-error' : '' }}">
                            <label class="control-label ">New Arrival:</label>

                            <input type="checkbox" name="new_arrival" value="1" <?php echo ($new_arrival == '1')?'checked':''; ?> />

                            @include('snippets.errors_first', ['param' => 'new_arrival'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">

                            <?php
                            $sel_status = old('status', $status);

                            //pr($sel_status);
                            ?>

                            <label class="control-label required">Status:</label>
                            <br>
                            <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>Active
                            &nbsp;
                            <input type="radio" name="status" value="0" {{($sel_status == '')?'checked':''}}>Inactive

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

                    </div>

                    <div class="clearfix"></div>

                    <br>
                    <br>


                    <?php
                    /*
                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('main_image') ? ' has-error' : '' }}">

                            <label class="control-label">Main Image:</label>
                            
                            <input type="text" name="main_image" value="{{old('main_image', $main_image)}}" class="form-control">

                            @include('snippets.errors_first', ['param' => 'main_image'])
                            
                        </div>

                    </div>
                    */
                    ?>

                    <div class="col-sm-12 col-md-12">

                        <div class="form-group{{ $errors->has('desktop_image') ? ' has-error' : '' }}">

                            <label class="control-label">Desktop Image Link:</label>

                            <input type="text" class="form-control" name="desktop_image" value="{{ old('desktop_image', $desktop_image) }}" />

                            @include('snippets.errors_first', ['param' => 'desktop_image'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-12">

                        <div class="form-group{{ $errors->has('mobile_image') ? ' has-error' : '' }}">

                            <label class="control-label">Mobile Image Link:</label>

                            <input type="text" class="form-control" name="mobile_image" value="{{ old('mobile_image', $mobile_image) }}" />

                            @include('snippets.errors_first', ['param' => 'mobile_image'])
                        </div>
                    </div>

                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('images.*') ? ' has-error' : '' }}">

                            <label class="control-label">Images:</label>

                            
                                <table class="table img_box">
                                    <tr>
                                        <th>Link</th>
                                        <th>Default</th>
                                        <th>Reverse</th>
                                        <th><a href="javascript:void(0)" class="add_img_row">Add(+)</a></th>
                                    </tr>

                                    <?php

                            //$images_arr = (CustomHelper::isSerialized($other_images))?unserialize($other_images):[];

                            $images_arr = [];
                            $images_ids_arr = [];
                            $is_default_id = 0;
                            $is_reverse_id = 0;

                            if(!empty($productImages)){
                                foreach($productImages as $pi){
                                    $images_arr[] = $pi->image;
                                    $images_ids_arr[] = $pi->id;

                                    if($pi->is_default == 1){
                                        $is_default_id = $pi->id;
                                    }

                                    if($pi->is_reverse == 1){
                                        $is_reverse_id = $pi->id;
                                    }
                                }
                            }

                            $images_arr = old('images', $images_arr);

                            //prd($images_arr);

                            $countheading = 1;

                            if(!empty($images_arr) && count($images_arr) > 0){

                                foreach($images_arr as $iKey=>$image){

                                    $iid = (isset($images_ids_arr[$iKey]))?$images_ids_arr[$iKey]:0;

                                    $row_params = [];
                                    $row_params['countheading'] = $countheading;
                                    $row_params['image'] = $image;
                                    $row_params['iid'] = $iid;
                                    $row_params['is_default_id'] = $is_default_id;
                                    $row_params['is_reverse_id'] = $is_reverse_id;
                                    ?>

                                    @include('admin.products._image_row', $row_params)

                                    <?php

                                    $countheading++;

                                }
                            }
                            if($countheading <= 1){

                                $row_params = [];
                                $row_params['countheading'] = $countheading;
                                ?>

                                @include('admin.products._image_row', $row_params)

                                <?php
                            }
                            ?>
                                </table>
                            
                        </div>

                    </div>


                    <div class="attributes_list"></div>
                    <div class="row">

                        <br>
                            <br>

                        <div class="col-sm-6">
                            <div class="form-group{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                                <label class="control-label">Meta Title:</label>

                                <textarea name="meta_title" class="form-control" maxlength="255" >{{ old('meta_title', $meta_title) }}</textarea>

                                @include('snippets.errors_first', ['param' => 'meta_title'])
                            </div>
                            
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                                <label class="control-label">Meta Keyword:</label>

                                <textarea name="meta_keyword" class="form-control" maxlength="255" >{{ old('meta_keyword', $meta_keyword) }}</textarea>

                                @include('snippets.errors_first', ['param' => 'meta_keyword'])
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group{{ $errors->has('meta_description') ? ' has-error' : '' }}">
                                <label class="control-label">Meta Description:</label>

                                <textarea name="meta_description" class="form-control" maxlength="255" >{{ old('meta_description', $meta_description) }}</textarea>

                                @include('snippets.errors_first', ['param' => 'meta_description'])
                            </div>
                        </div>
                    </div>


                </div>

                <br>
                <br>

                    <div class="form-group">
                        <input type="hidden" name="back_url" value="{{ $back_url }}" >
                            <button type="submit" class="btn btn-success" title="Create this new product"><i class="fa fa-save"></i> Submit</button>

                            <a href="{{ url($back_url) }}" class="btn btn-lg btn-primary" title="Click here to cancel">Cancel</a>
                        </div>

            </form>
        </div>

    </div>


</div>

<?php

$pattern = '/\n*/m';
$replace = '';

$row_params = [];
$row_params['countheading'] = (isset($countheading))?$countheading:1;
$row_params['showRemoveBtn'] = true;

$row_html = view('admin.products._image_row', $row_params)->render();

//$imgRow = preg_replace( $pattern, $replace, $row_html);

$imgRow = '<tr class="img_row">';
$imgRow .= '<td><input type="text" name="images[]" value="" class="form-control"><input type="hidden" name="image_ids[]" value=""></td>';

$imgRow .= '<td><input type="radio" name="is_default" value=""></td>';

$imgRow .= '<td><input type="radio" name="is_reverse" value=""></td>';
    
$imgRow .= ' <td><a href="javascript:void(0)" data-iid="" class="remove_img_row">Remove</a></td>';
$imgRow .= '</tr>';

?>

@slot('bottomBlock')
<script type="text/javascript" src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" src="{{ url('bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>

<script type="text/javascript">
 //var editor = CKEDITOR.replace('specifications');
 var editor2 = CKEDITOR.replace('description');

 $(document).ready(function (){
    $('#sizes').multiselect({
        numberDisplayed: 2

    });
});


 var p1_cat = "{{$p1_cat}}";
 //var p2_cat = "{{$p2_cat}}";
 //var category = "<?php //echo json_encode($category, JSON_NUMERIC_CHECK);?>";
 var category = '<?php echo json_encode($category);?>';
 var category_id = '<?php echo $category;?>';

 p1_cat = parseInt(p1_cat);
 //p2_cat = parseInt(p2_cat);
 //category = parseInt(category);

 /*console.log("p1_cat="+p1_cat);
 console.log("p2_cat="+p2_cat);
 console.log("category="+category);*/

 if(!isNaN(p1_cat) && p1_cat > 0){
    var curr_sel = $("select[name=p1_cat]");
    //populateSubCategories(curr_sel, p1_cat, p2_cat);
    populateSubCategories(curr_sel, p1_cat, category_id);
}

/*if(!isNaN(p2_cat) && p2_cat > 0){
    var curr_sel2 = $("select[name=p2_cat]");
    populateSubCategories(curr_sel2, p2_cat, category);
}*/


$(document).on("change", '.categoryList', function(){

    //alert('hi'); return false;
    var curr_sel = $(this);

    var parent_id = curr_sel.val();

    parent_id = parseInt(parent_id);

    if(!isNaN(parent_id) && parent_id > 0){
        populateSubCategories(curr_sel, parent_id);
    }
    else{
        curr_sel.parents(".categoryBox").nextAll(".categoryBox").find(".categoryList").html('<option value="">--Select--</option>');
    }

});


function populateSubCategories(curr_sel, parent_id, selected_id){
    var categoryListLen = $(".categoryList").length;

    categoryListLen = parseInt(categoryListLen);

    var curr_sel_name = curr_sel.attr("name");

    //alert(curr_sel_name);

    var parent_id = curr_sel.val();

    var categoryBox = curr_sel.parents(".categoryBox");


    if(curr_sel_name != 'category[]' && curr_sel_name != 'category'){

        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ route('admin.products.ajax_get_category_child') }}",
            type: "POST",
            data: {parent_id:parent_id, selected_id:selected_id},
            dataType:"JSON",
            headers:{'X-CSRF-TOKEN': _token},
            cache: false,
            async: false,
            beforeSend:function(){
                $(".ajax_msg").html("");
            },
            success: function(resp){
                if(resp.success){
                    curr_sel.parents(".categoryBox").next(".categoryBox").find(".categoryList").html(resp.categoryDropdownHtml);
                }
            }

        });
    }
    
}

getCategoryAttributes(p1_cat);

$(document).on("change", "[name=p1_cat]", function(){
    var curr_sel = $(this);

    var category_id = curr_sel.val();

    getCategoryAttributes(category_id);
});


function getCategoryAttributes(category_id){
    category_id = parseInt(category_id);
    var product_id = "{{$product_id}}";

    var attrArrJson = '<?php echo $attrArrJson; ?>';

    if(!isNaN(category_id) && category_id > 0){

        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ route('admin.products.ajax_get_category_attributes') }}",
            type: "POST",
            data: {category_id:category_id, product_id:product_id, attrArrJson:attrArrJson},
            dataType:"JSON",
            headers:{'X-CSRF-TOKEN': _token},
            cache: false,
            async: false,
            beforeSend:function(){
                $(".ajax_msg").html("");
                $(".attributes_list").html('');
            },
            success: function(resp){
                if(resp.success){
                    if(resp.attributesListHtml)
                    $(".attributes_list").html(resp.attributesListHtml);
                }
            }

        });

    }
    else{
        $(".attributes_list").html('');
    }
}


$(document).on("change", "[name=color_id]", function(){

    var colorName = '';

    var colorId = $(this).val();

    if(colorId && colorId != ""){
        colorName = $("[name=color_id]").find("option[value="+colorId+"]").text();
    }
    $("[name=color_name]").val(colorName);
});


$(document).on("keyup change", "[name=price], [name=sale_price]", function(){


    var price = parseFloat($("[name=price]").val());
    var salePrice = parseFloat($("[name=sale_price]").val());

    var gstTag = $("[name=gst]");

    var priceForGst = 0

    if(!isNaN(price) && price > 0 && !isNaN(salePrice) && salePrice > 0){
        if(salePrice < price){
            priceForGst = salePrice;
        }
        else{
             priceForGst = price;
        }
    }
    else if(!isNaN(price) && price > 0){
        priceForGst = price;
    }

    var gstVal = 0;

    if(priceForGst > 0){
        if(priceForGst > 1000){
            gstVal = 12;
        }
        else{
            gstVal = 5;
        }
    }

    if(gstVal > 0){
        $("[name=gst] option").prop('selected', false);
        $("[name=gst]").find('option[value="'+gstVal+'"]').prop('selected', true);


    }
});


$(".add_img_row").click(function(){
        var img_row_len = $(".img_row").length;

        if(img_row_len+1 > 10){
            alert('Maximum 10 Attributes are allowed.');
        }
        else{

            var imgRow = '<?php echo $imgRow; ?>';

            $(".img_box").append(imgRow);
        }
    });



    $(document).on("click",".remove_img_row", function(){

        var curr_selector = $(this);
        

        var iid = curr_selector.data("iid");

        iid = parseInt(iid);

        if(iid && iid > 0){
            var conf = confirm("Are you sure to remove this row?");

            if(conf){

                var _token = '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('admin.products.ajax_remove_link') }}",
                    type: "POST",
                    data: {iid:iid},
                    dataType:"JSON",
                    headers:{'X-CSRF-TOKEN': _token},
                    cache: false,
                    async: false,
                    beforeSend:function(){
                    },
                    success: function(resp){
                        if(resp.success){
                            curr_selector.parents(".img_row").remove();
                        }
                    }

                });
            }
        }
        else{
            curr_selector.parents(".img_row").remove();
        }
    });



    /*$(document).on("click",".is_default", function(){
        console.log($(".is_default").index(this));
    });*/

 </script>

@endslot


@endcomponent