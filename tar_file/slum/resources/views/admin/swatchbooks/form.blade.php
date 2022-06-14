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

    //pr($related_products);

    $IMG_HEIGHT = CustomHelper::WebsiteSettings('PRODUCT_IMG_HEIGHT');
    $IMG_WIDTH = CustomHelper::WebsiteSettings('PRODUCT_IMG_WIDTH');
    $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
    $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;

    $category_id = (isset($product->category_id))?$product->category_id:'';
    
    $related_product_id = (isset($product->related_product_id))?$product->related_product_id:'';

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
    $status = (isset($product->status))?$product->status:'1';

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
                        <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                            <label class="control-label required">Fabric Type:</label>

                            <select name="category_id" id="category_id" class="form-control" >

                                <option value="">--Select--</option>

                                <?php
                                if(!empty($categories) && count($categories) > 0){
                                    foreach($categories as $cr)
                                    {
                                        $selected = '';
                                        if($cr->id == $category_id){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$cr->id}}" {{$selected}} >{{$cr->name}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            @include('snippets.errors_first', ['param' => 'category_id'])
                        </div>
                    </div>


                     <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('related_product_id') ? ' has-error' : '' }}">
                            <label class="control-label required">Included Fabric:</label>

                            <?php //pr($selected_related_products); ?>

                            <select name="related_product_id[]" id="related_product_id"  class="form-control" multiple>

                                

                                <?php 
                                if(!empty($related_products) && count($related_products) > 0){
                                    foreach($related_products as $cr)
                                    {
                                        $selected = '';
                                        if(in_array($cr->id,$selected_related_products))
                                        {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$cr->id}}" {{$selected}} >{{$cr->name}}</option>
                                        <?php
                                    }
                                }
                            ?>
                            </select>
                            @include('snippets.errors_first', ['param' => 'related_product_id'])
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
   
   $('#related_product_id').multiselect({
        numberDisplayed: 2

    });

});
</script>


<script type="text/javascript">
    category_id = '{{ $category_id }}';
    related_product_id = '{{ $related_product_id }}';

   /* if(category_id && category_id != ""){
        load_product( category_id, related_product_id );
    }*/

    $(document).on("change", "select[name='category_id']", function () {
        category_id = $( this ).val();
        load_product(category_id, related_product_id );
    } );

    function load_product( category_id, related_product_id ) {

        var _token = '{{csrf_token()}}';

        $.ajax( {
            url: "{{url('common/ajax_load_product')}}",
            type: "POST",
            data: {category_id: category_id, related_product_id: related_product_id},
            dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': _token
            },
            cache: false,
            beforeSend: function () {},
            success: function ( resp ) {
                if ( resp.success ) {
                    $("select[id='related_product_id']").html( resp.options );
                    
                    $('#related_product_id').multiselect('rebuild');
                }
            }
        } );
    }
</script>
    
@endslot

@endcomponent