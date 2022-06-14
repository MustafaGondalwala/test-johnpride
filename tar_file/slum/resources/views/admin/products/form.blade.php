@component('admin.layouts.main')

@slot('title')
Admin - {{$page_heading}} - {{ config('app.name') }}
@endslot

@slot('headerBlock')
<link href="{{url('public')}}/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

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

    $sort_order = (isset($product->sort_order))?$product->sort_order:'';
    $stamp = (isset($product->stamp))?$product->stamp:'';
    $featured = (isset($product->featured))?$product->featured:'';
    $trending = (isset($product->trending))?$product->trending:'';
    $popularity = (isset($product->popularity))?$product->popularity:'';
    $status = (isset($product->status))?$product->status:'1';


    $meta_title = (isset($product->meta_title))?$product->meta_title:'';
    $meta_keyword = (isset($product->meta_keyword))?$product->meta_keyword:'';
    $meta_description = (isset($product->meta_description))?$product->meta_description:'';

    $productCategories = (isset($product->productCategories))?$product->productCategories:'';
    $productImages = (isset($product->productImages))?$product->productImages:'';
    $productAttributes = (isset($product->productAttributes))?$product->productAttributes:'';

    $productSizes = (isset($product->productSizes))?$product->productSizes:'';

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

    //pr($category);

    $productStampsArr = config('custom.product_stamps_arr');

    //pr($productStampsArr);

    $path = 'products/';
    $thumb_path = 'products/thumb/';

    $storage = Storage::disk('public');

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

                            <input type="text" class="form-control" name="sku" value="{{ old('sku', $sku) }}" maxlength="10" />

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

                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                            <label class="control-label required">Category:</label>

                            <select name="p1_cat" class="form-control categoryList">
                                @include('admin.products._category_dropdown', $viewData)
                            </select>

                            <?php
                            if(!is_numeric($p1_cat) || empty($p1_cat)){
                                ?>
                                @include('snippets.errors_first', ['param' => 'category'])
                                <?php
                            }
                            ?>
                        </div>
                    </div>




                    <div class="col-sm-4 categoryBox">

                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                            <label class="control-label required">Sub-Category:</label>

                            <select name="p2_cat" class="form-control categoryList">
                                <option value="">--Select--</option>
                                
                            </select>

                            @include('snippets.errors_first', ['param' => 'category'])
                        </div>
                    </div>

                    <div class="col-sm-4 categoryBox">

                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                            <label class="control-label required">Sub-Category:</label>

                            <select name="category" class="form-control categoryList" >
                                <option value="">--Select--</option>
                                
                            </select>

                            @include('snippets.errors_first', ['param' => 'category'])
                        </div>
                    </div>
                        

                    

                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('specifications') ? ' has-error' : '' }}">
                            <label class="control-label">Specifications:</label>

                            <textarea name="specifications" class="form-control" maxlength="255" rows="5">{{ old('specifications', $specifications) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'specifications'])
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

                            <input type="text" name="gst" class="form-control" value="{{ old('gst', $gst) }}" maxlength="10" />

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

                    <div class="col-sm-12 col-md-6">

                        <div class="form-group{{ $errors->has('video') ? ' has-error' : '' }}">
                            <label class="control-label "> Video:</label>

                            <textarea name="video" class="form-control" ><?php echo $video; ?></textarea>

                            @include('snippets.errors_first', ['param' => 'video'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('delivery_duration') ? ' has-error' : '' }}">
                            <label class="control-label "> Delivery Duration:</label>

                            <input type="text" name="delivery_duration" class="form-control" value="{{ old('delivery_duration', $delivery_duration) }}" />

                            @include('snippets.errors_first', ['param' => 'delivery_duration'])
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

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">

                            <?php
                            $sel_status = old('status', $status);
                            ?>

                            <label class="control-label required">Status:</label>
                            <br>
                            <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>Active
                            &nbsp;
                            <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>Inactive

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

                    </div>

                    <div class="clearfix"></div>

                    <br>
                    <br>


                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('main_image') ? ' has-error' : '' }}">

                            <label class="control-label">Main Image: (Preferred dimensions: width:{{$IMG_WIDTH}}, height:{{$IMG_HEIGHT}})</label>
                            
                            <input type="file" name="main_image">

                            @include('snippets.errors_first', ['param' => 'main_image'])
                            
                        </div>

                    </div>

                    


                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('images.*') ? ' has-error' : '' }}">

                            <label class="control-label">Images: (Preferred dimensions: width:{{$IMG_WIDTH}}, height:{{$IMG_HEIGHT}})</label>

                            <?php
                            $count_images = count($productImages);
                            if(!empty($productImages) && count($productImages) > 0){
                                ?>
                                <table class="table">
                                    <tr>
                                        <th>Image</th>
                                        <th>Default</th>
                                        <th>Set Reverse</th>
                                        <th>Remove</th>
                                    </tr>
                                    <?php
                                    foreach ($productImages as $pi){

                                        $img_name = $pi->image;

                                        if(!empty($img_name) && $storage->exists($thumb_path.$img_name)){
                                            ?>
                                            <tr>
                                                <td>

                                                    <a href="{{url('public/storage/'.$path.$img_name)}}" target="_blank"><img src="{{url('public/storage/'.$thumb_path.$img_name)}}" width="100" /></a>
                                                </td>
                                                <td><input type="radio" name="is_default" value="{{ $pi->id}}" {{ $pi->is_default ? 'checked' : '' }} /></td>

                                                <td><input type="radio" name="is_reverse" value="{{ $pi->id}}" {{ $pi->is_reverse ? 'checked' : '' }} /></td>

                                                <td><input type="checkbox" name="images_remove[]" value="{{ $pi->id }}" /></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                            }
                            ?>

                            
                            <input type="file" name="images[]" multiple>

                            <input type="hidden" name="count_images" value="{{$count_images}}" />

                            @include('snippets.errors_first', ['param' => 'images.*'])
                            
                        </div>

                    </div>



                    <div class="attributes_list"></div>



                    <div class="clearfix"></div>

                    <div class="row">
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

@slot('bottomBlock')
<script type="text/javascript" src="{{ url('public/js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" src="{{ url('public/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>

<script type="text/javascript">
 var editor = CKEDITOR.replace('specifications');
 var editor2 = CKEDITOR.replace('description');

 $(document).ready(function (){
    $('#sizes').multiselect({
        numberDisplayed: 2

    });
});


 var p1_cat = "{{$p1_cat}}";
 var p2_cat = "{{$p2_cat}}";
 //var category = "<?php //echo json_encode($category, JSON_NUMERIC_CHECK);?>";
 var category = '<?php echo json_encode($category);?>';
 var category_id = '<?php echo $category;?>';

 p1_cat = parseInt(p1_cat);
 p2_cat = parseInt(p2_cat);
 //category = parseInt(category);

 /*console.log("p1_cat="+p1_cat);
 console.log("p2_cat="+p2_cat);
 console.log("category="+category);*/

 if(!isNaN(p1_cat) && p1_cat > 0){
    var curr_sel = $("select[name=p1_cat]");
    populateSubCategories(curr_sel, p1_cat, p2_cat);
}

if(!isNaN(p2_cat) && p2_cat > 0){
    var curr_sel2 = $("select[name=p2_cat]");
    populateSubCategories(curr_sel2, p2_cat, category);
}


$(document).on("change", '.categoryList', function(){
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

getCategoryAttributes(category_id);

$(document).on("change", "[name=category]", function(){
    var curr_sel = $(this);

    var category_id = curr_sel.val();

    getCategoryAttributes(category_id);
});


function getCategoryAttributes(category_id){
    category_id = parseInt(category_id);
    var product_id = "{{$product_id}}";

    if(!isNaN(category_id) && category_id > 0){

        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ route('admin.products.ajax_get_category_attributes') }}",
            type: "POST",
            data: {category_id:category_id, product_id:product_id},
            dataType:"JSON",
            headers:{'X-CSRF-TOKEN': _token},
            cache: false,
            async: false,
            beforeSend:function(){
                $(".ajax_msg").html("");
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

 </script>

@endslot


@endcomponent