@component('admin.layouts.main')

@slot('title')
Admin - {{$page_heading}} - {{ config('app.name') }}
@endslot

<link href="{{url('public')}}/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

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


    $category_id = (isset($product->category_id))?$product->category_id:'';
    
    $user_id = (isset($product->user_id))?$product->user_id:'';
    $type = (isset($product->type))?$product->type:'';
    $name = (isset($product->name))?$product->name:'';
    $slug = (isset($product->slug))?$product->slug:'';
    $sku = (isset($product->sku))?$product->sku:'';
    $brief = (isset($product->brief))?$product->brief:'';
    $description = (isset($product->description))?$product->description:'';
    $color = (isset($product->color))?$product->color:'';
    $price = (isset($product->price))?$product->price:'';
    $gst = (isset($product->gst))?$product->gst:'';
    $printing_price = (isset($product->printing_price))?$product->printing_price:'';
    $warp_count = (isset($product->warp_count))?$product->warp_count:'';
    $weft_count = (isset($product->weft_count))?$product->weft_count:'';
    $cons = (isset($product->cons))?$product->cons:'';
    $width = (isset($product->width))?$product->width:'';
    $gsm = (isset($product->gsm))?$product->gsm:'';
    $size = (isset($product->size))?$product->size:'';
    $thread_count = (isset($product->thread_count))?$product->thread_count:'';
    $min_order_qty = (isset($product->min_order_qty))?$product->min_order_qty:'';
    $swatch_size = (isset($product->swatch_size))?$product->swatch_size:'';
    $swatch_price = (isset($product->swatch_price))?$product->swatch_price:'';
    $fat_size = (isset($product->fat_size))?$product->fat_size:'';
    $fat_price = (isset($product->fat_price))?$product->fat_price:'';
    $default_fabric = (isset($product->default_fabric))?$product->default_fabric:'';
    $sort_order = (isset($product->sort_order))?$product->sort_order:'';
    $featured = (isset($product->featured))?$product->featured:'';
    $is_approved= (isset($product->is_approved))?$product->is_approved:'';
    $status = (isset($product->status))?$product->status:'';



    $images = (isset($product->Images))?$product->Images:'';

    $path = 'designs/';
    $thumb_path = 'designs/thumb/';

    $storage = Storage::disk('public');

    $img_for_arr = ['swatch', 'fat', 'meter'];

    $swatch_image = ['name'=>'', 'id'=>''];
    $fat_image = ['name'=>'', 'id'=>''];
    $meter_image = ['name'=>'', 'id'=>''];

    if(!empty($images) && count($images) > 0){
        foreach($images as $image){
            if($image->img_for == 'swatch'){
                $swatch_image['name'] = $image->name;
                $swatch_image['id'] = $image->id;
            }
            elseif($image->img_for == 'fat'){
                $fat_image['name'] = $image->name;
                $fat_image['id'] = $image->id;
            }
            elseif($image->img_for == 'meter'){
                $meter_image['name'] = $image->name;
                $meter_image['id'] = $image->id;
            }
        }
    }

    $CategoryDropDown = CustomHelper::CategoryDropDown($dropdown_name='category_id', $type='design', $classAttr='form-control', $idAtrr='category_id', $selected_cat_ids, true);



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

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('default_fabric') ? ' has-error' : '' }}">
                            <label class="control-label required">Default Fabric:</label>

                            <select name="default_fabric" class="form-control" >

                                <option value="">--Select--</option>

                                <?php
                                if(!empty($fabrics) && count($fabrics) > 0){
                                    foreach($fabrics as $fabric){
                                        $selected = '';
                                        if($fabric->id == $default_fabric){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$fabric->id}}" {{$selected}} >{{$fabric->name}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>

                            @include('snippets.errors_first', ['param' => 'default_fabric'])
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                            <label class="control-label">Category:</label>

                            <?php echo $CategoryDropDown; ?>
                           
                            <?php /* ?>

                            <select name="category_id[]" id="category_id" class="form-control category_id" multiple="">
                                <?php
                               
                                if(!empty($design_category) && count($design_category) > 0){
                                    foreach($design_category as $cat)
                                    {
                                        $selected = '';
                                        if(in_array($cat->id, $selected_cat_ids))
                                        {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$cat->id}}" {{$selected}} >{{$cat->name}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>

                            <?php */ ?>



                            @include('snippets.errors_first', ['param' => 'category_id'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('designer') ? ' has-error' : '' }}">
                            <label class="control-label required">Designer:</label>

                            <select name="designer" class="form-control" >

                                <option value="0">Tex India</option>

                                <?php
                                if(!empty($DesignersList) && count($DesignersList) > 0){
                                    foreach($DesignersList as $dl){
                                        $d_first_name = $dl->first_name;
                                        $d_last_name = $dl->last_name;
                                        $designer_name = trim($d_first_name.' '.$d_last_name);
                                        $selected = '';
                                        if($dl->id == $user_id){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$dl->id}}" {{$selected}} >{{$designer_name}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>

                            @include('snippets.errors_first', ['param' => 'designer'])
                        </div>
                    </div>
                    

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label class="control-label required">Color:</label>

                            <?php
                            if(!empty($ColorsMaster) && count($ColorsMaster) > 0){
                                ?>

                                <select name="color" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                                    foreach($ColorsMaster as $cm){
                                        $selected = '';

                                        if($cm->id == $color){
                                            $selected = 'selected';
                                        }
                                        if($cm->children()->count() > 0){
                                            ?>
                                            <optgroup label="{{$cm->name}}">
                                                <?php
                                                foreach($cm->children as $ccm){
                                                    if($ccm->id == $color){
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="{{$ccm->id}}" {{$selected}} >{{$ccm->name}}</option>
                                                    <?php
                                                }
                                                ?>
                                            </optgroup>
                                            <?php
                                        }
                                        else{
                                            ?>
                                            <option value="{{$cm->id}}" {{$selected}} >{{$cm->name}}</option>
                                            <?php
                                        }
                                        ?>
                                        
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'color'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label class="control-label required">Sort Order:</label>

                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $sort_order) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>

                     <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <label class="control-label required">Price:</label>

                            <input type="text" name="price" class="form-control" value="{{ old('price', $price) }}" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>



                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }}">
                            <label class="control-label required">featured:</label>

                            <input type="checkbox" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> />

                            @include('snippets.errors_first', ['param' => 'featured'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">

                            <?php
                            $sel_status = old('status', $status);
                            ?>

                            <label class="control-label required">Status:</label>
                            &nbsp;&nbsp;
                            
                            <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>Active
                            &nbsp;
                            <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>Inactive

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div><br>

                    </div>


               



                       <div class="col-sm-12">

                    


                        <div class="form-group{{ $errors->has('is_approved') ? ' has-error' : '' }}">
                            <label class="control-label required">Approved Status:</label>

                            <select name="is_approved" class="form-control" >

                                <option <?php if($is_approved==0) { echo 'selected'; } ?> value="0">Pending</option>
                                <option <?php if($is_approved==1) { echo 'selected'; } ?> value="1">Approved</option>
                                <option <?php if($is_approved==2) { echo 'selected'; } ?> value="2">Disapproved</option>

                               
                            </select>

                            @include('snippets.errors_first', ['param' => 'designer'])
                        </div>

                    </div>






                    <div class="col-sm-12">

                        <div class="form-group">

                            <label class="control-label required">Swatch Image: (Preferred dimensions: width:{{$IMG_WIDTH}}, height:{{$IMG_HEIGHT}})</label>
                            
                            <input type="file" name="swatch_image">

                            <?php
                            $swatch_img_name = $swatch_image['name'];
                            if(!empty($swatch_img_name) && $storage->exists($thumb_path.$swatch_img_name)){
                                ?>
                                <a href="{{url('public/storage/'.$path.$swatch_img_name)}}" target="_blank"><img src="{{url('public/storage/'.$thumb_path.$swatch_img_name)}}" width="100" /></a>

                                <strong>Remove:</strong> <input type="checkbox" name="images_remove[]" value="{{ $swatch_image['id'] }}" />

                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'swatch_image'])
                        </div><br>

                    </div>


                    <div class="col-sm-12">

                        <div class="form-group">

                            <label class="control-label required">Fat Quarter Image: (Preferred dimensions: width:{{$IMG_WIDTH}}, height:{{$IMG_HEIGHT}})</label>
                            
                            <input type="file" name="fat_image">

                            <?php
                            $fat_img_name = $fat_image['name'];
                            if(!empty($fat_img_name) && $storage->exists($thumb_path.$fat_img_name)){
                                ?>
                                <a href="{{url('public/storage/'.$path.$fat_img_name)}}" target="_blank"><img src="{{url('public/storage/'.$thumb_path.$fat_img_name)}}" width="100" /></a>
                                <strong>Remove:</strong> <input type="checkbox" name="images_remove[]" value="{{ $fat_image['id'] }}" />
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'fat_image'])
                        </div><br>

                    </div>


                    <div class="col-sm-12">

                        <div class="form-group">

                            <label class="control-label required">Meter Image: (Preferred dimensions: width:{{$IMG_WIDTH}}, height:{{$IMG_HEIGHT}})</label>

                            <input type="file" name="meter_image">

                            <?php
                            $meter_img_name = $meter_image['name'];
                            if(!empty($meter_img_name) && $storage->exists($thumb_path.$meter_img_name)){
                                ?>
                                <a href="{{url('public/storage/'.$path.$meter_img_name)}}" target="_blank"><img src="{{url('public/storage/'.$thumb_path.$meter_img_name)}}" width="100" /></a>
                                <strong>Remove:</strong> <input type="checkbox" name="images_remove[]" value="{{ $meter_image['id'] }}" />
                                <?php
                            }
                            ?>                            

                            @include('snippets.errors_first', ['param' => 'meter_image'])
                        </div><br>

                    </div>


                    <div class="col-sm-12">

                        <div class="form-group">

                            <label class="control-label required">Images: (Preferred dimensions: width:{{$IMG_WIDTH}}, height:{{$IMG_HEIGHT}})</label>

                            <?php
                            if(!empty($images) && count($images) > 0){
                                ?>
                                <table class="table">
                                    <tr>
                                        <th>Image</th>
                                        <th>Default</th>
                                        <th>Remove</th>
                                    </tr>
                                    <?php
                                    foreach ($images as $image){

                                        $img_name = $image->name;

                                        if(!in_array($image->img_for, $img_for_arr)){

                                            if(!empty($img_name) && $storage->exists($thumb_path.$img_name)){
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="{{url('public/storage/'.$path.$img_name)}}" target="_blank"><img src="{{url('public/storage/'.$thumb_path.$img_name)}}" width="100" /></a>
                                                    </td>
                                                    <td><input type="radio" name="is_default" value="{{ $image->id}}" {{ $image->is_default ? 'checked' : '' }} /></td>
                                                    <td><input type="checkbox" name="images_remove[]" value="{{ $image->id }}" /></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                            }
                            ?>
                            
                            <input type="file" name="images[]" multiple>
                        </div>

                    </div>



                    <div class="clearfix"></div>


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

<script type="text/javascript" src="{{ url('public/jquery/jquery.js') }}"></script>

<script type="text/javascript" src="{{ url('public/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>


<script type="text/javascript">

$(document).ready(function() {
   
   $('#category_id').multiselect({
        numberDisplayed: 2

    });

});
</script>

@endslot

@endcomponent