@component('admin.layouts.main')

@slot('title')
Admin - {{$page_heading}} - {{ config('app.name') }}
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


    $category_id = (isset($product->category_id))?$product->category_id:'';
    //$type = (isset($product->type))?$product->type:'';
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
    $threshold = (isset($product->threshold))?$product->threshold:'';
    $swatch_size = (isset($product->swatch_size))?$product->swatch_size:'';
    $swatch_price = (isset($product->swatch_price))?$product->swatch_price:'';
    $fat_size = (isset($product->fat_size))?$product->fat_size:'';
    $fat_price = (isset($product->fat_price))?$product->fat_price:'';
    $default_fabric = (isset($product->default_fabric))?$product->default_fabric:'';
    $sort_order = (isset($product->sort_order))?$product->sort_order:'';
    $featured = (isset($product->featured))?$product->featured:'';
    $status = (isset($product->status))?$product->status:'1';

    $images = (isset($product->Images))?$product->Images:'';

    $path = 'fabrics/';
    $thumb_path = 'fabrics/thumb/';

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

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">

                        <div class="form-group{{ $errors->has('sku') ? ' has-error' : '' }}">

                            <label class="control-label required">SKU:{{$type}}</label>

                            <input type="text" class="form-control" name="sku" value="{{ old('sku', $sku) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'sku'])
                        </div>
                    </div>

                    <?php
                    if($type == 'fabric'){
                        ?>
                        <div class="col-sm-12">

                            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                <label class="control-label">Category:</label>

                                <select name="category" class="form-control">
                                    <option value="">--Select--</option>
                                    <?php
                                    if(!empty($categories) && $categories->count() > 0){
                                        foreach($categories as $cat){

                                            if(!empty($cat->children) && count($cat->children) > 0){
                                                ?>
                                                <optgroup label="{{$cat->name}}">
                                                    <?php
                                                    foreach($cat->children as $ccat){
                                                        $selected = '';
                                                        if($ccat->id == $category_id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="{{$ccat->id}}" {{$selected}} >{{$ccat->name}}</option>
                                                        <?php
                                                    }
                                                    ?>
                                                <?php
                                            }
                                            else{
                                                $selected = '';
                                                if($cat->id == $category_id){
                                                    $selected = 'selected';
                                                }
                                                ?>
                                                <option value="{{$cat->id}}" {{$selected}} >{{$cat->name}}</option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>

                                @include('snippets.errors_first', ['param' => 'category'])
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    

                    <div class="col-sm-12">

                        <div class="form-group{{ $errors->has('brief') ? ' has-error' : '' }}">
                            <label class="control-label">Brief:</label>

                            <textarea name="brief" class="form-control" maxlength="255" rows="5">{{ old('brief', $brief) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'brief'])
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

                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <label class="control-label required">Price:</label>

                            <input type="text" name="price" class="form-control" value="{{ old('price', $price) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'price'])
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

                        <div class="form-group{{ $errors->has('printing_price') ? ' has-error' : '' }}">
                            <label class="control-label required">Printing Price (per meter):</label>

                            <input type="text" name="printing_price" class="form-control" value="{{ old('printing_price', $printing_price) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'printing_price'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('warp_count') ? ' has-error' : '' }}">
                            <label class="control-label required">Warp Count:</label>

                            <input type="text" name="warp_count" class="form-control" value="{{ old('warp_count', $warp_count) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'warp_count'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('weft_count') ? ' has-error' : '' }}">
                            <label class="control-label required">Weft Count:</label>

                            <input type="text" name="weft_count" class="form-control" value="{{ old('weft_count', $weft_count) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'weft_count'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('cons') ? ' has-error' : '' }}">
                            <label class="control-label required">Cons.:</label>

                            <input type="text" name="cons" class="form-control" value="{{ old('cons', $cons) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'cons'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('width') ? ' has-error' : '' }}">
                            <label class="control-label required">Width:</label>

                            <input type="number" name="width" class="form-control" value="{{ old('width', $width) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'width'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('gsm') ? ' has-error' : '' }}">
                            <label class="control-label required">GSM:</label>

                            <input type="text" name="gsm" class="form-control" value="{{ old('gsm', $gsm) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'gsm'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('size') ? ' has-error' : '' }}">
                            <label class="control-label required">Size: (per meter in cm)</label>

                            <input type="text" name="size" class="form-control" value="{{ old('size', $size) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'size'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('thread_count') ? ' has-error' : '' }}">
                            <label class="control-label required">Thread Count:</label>

                            <input type="text" name="thread_count" class="form-control" value="{{ old('thread_count', $thread_count) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'thread_count'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('min_order_qty') ? ' has-error' : '' }}">
                            <label class="control-label required"> Min. Order Qty:</label>

                            <input type="text" name="min_order_qty" class="form-control" value="{{ old('min_order_qty', $min_order_qty) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'min_order_qty'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('threshold') ? ' has-error' : '' }}">
                            <label class="control-label required"> Threshold:</label>

                            <input type="text" name="threshold" class="form-control" value="{{ old('threshold', $threshold) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'threshold'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('swatch_size') ? ' has-error' : '' }}">
                            <label class="control-label required">Swatch Size:</label>

                            <input type="text" name="swatch_size" class="form-control" value="{{ old('swatch_size', $swatch_size) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'swatch_size'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('swatch_price') ? ' has-error' : '' }}">
                            <label class="control-label required">Swatch Price:</label>

                            <input type="text" name="swatch_price" class="form-control" value="{{ old('swatch_price', $swatch_price) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'swatch_price'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('fat_size') ? ' has-error' : '' }}">
                            <label class="control-label required">FAT Size:</label>

                            <input type="text" name="fat_size" class="form-control" value="{{ old('fat_size', $fat_size) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'fat_size'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('fat_price') ? ' has-error' : '' }}">
                            <label class="control-label required">FAT Price:</label>

                            <input type="text" name="fat_price" class="form-control" value="{{ old('fat_price', $fat_price) }}" maxlength="10" />

                            @include('snippets.errors_first', ['param' => 'fat_price'])
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
                            <br>
                            <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>Active
                            &nbsp;
                            <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>Inactive

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

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
<script type="text/javascript" src="{{ url('public/js/ckeditor/ckeditor.js') }}"></script>

<script type="text/javascript">
 var editor = CKEDITOR.replace('description');

 </script>
@endslot

@endcomponent