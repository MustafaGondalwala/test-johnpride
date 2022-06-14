@component('admin.layouts.main')

@slot('title')
Admin - {{ $page_heading }} - {{ config('app.name') }}
@endslot


<?php    

$banner_id = (isset($banner->id))?$banner->id:'';
$title = (isset($banner->title))?$banner->title:'';
$subtitle = (isset($banner->subtitle))?$banner->subtitle:'';
$brief = (isset($banner->brief))?$banner->brief:'';
$page = (isset($banner->page))?$banner->page:'';
$device_type = (isset($banner->device_type))?$banner->device_type:'';
$status = (isset($banner->status))?$banner->status:1;
$sort_order = (isset($banner->sort_order))?$banner->sort_order:0;
$image = (isset($banner->image))?$banner->image:'';
$link = (isset($banner->link))?$banner->link:'';

$deviceTypesArr = config('custom.device_types_arr');

$storage = Storage::disk('public');

    //pr($storage);

$path = 'banners/';

$old_image = 0;

$page = old('page', $page);

$image_req = 'required';
$link_req = '';

if($page == 'home_link'){
    $image_req = '';
    $link_req = 'required';
}

$IMG_HEIGHT = CustomHelper::WebsiteSettings('BANNER_IMG_HEIGHT');
$IMG_WIDTH = CustomHelper::WebsiteSettings('BANNER_IMG_WIDTH');
$IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:1600;
$IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:640;

?>

<h2>{{ $page_heading }} <?php if(request()->has('back_url')){ $back_url= request('back_url');  ?>
<a href="{{ url($back_url)}}" class="btn btn-success btn-sm" style='float: right;'>Back</a><?php } ?></h2>

<div class="bgcolor">


    @include('snippets.errors')
    @include('snippets.flash')

    <div class="ajax_msg"></div>

    <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="link_name" class="control-label required">Title:</label>

                    <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $title) }}" required  />

                    @include('snippets.errors_first', ['param' => 'title'])
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group{{ $errors->has('subtitle') ? ' has-error' : '' }}">
                    <label for="link_name" class="control-label">Subtitle:</label>

                    <input type="text" id="subtitle" class="form-control" name="subtitle" value="{{ old('subtitle', $subtitle) }}"  />

                    @include('snippets.errors_first', ['param' => 'subtitle'])
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('page') ? ' has-error' : '' }}">
                    <label for="page" class="control-label required">Page:</label>

                    <select name="page" class="form-control" required>
                        <?php
                        if(!empty($page_arr) && count($page_arr) > 0){
                            foreach($page_arr as $pa_key=>$pa_val){
                                        //prd($pa_val);
                                ?>
                                <option @if($pa_key==$page) selected @endif value="{{ $pa_key }}"><?php echo $pa_val; ?></option>
                                <?php
                            }

                        }
                        ?>
                    </select>

                    @include('snippets.errors_first', ['param' => 'page'])
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group{{ $errors->has('device_type') ? ' has-error' : '' }}">
                    <label for="device_type" class="control-label required">Device Type:</label>

                    <select name="device_type" class="form-control" required>
                        <?php
                        if(!empty($deviceTypesArr) && count($deviceTypesArr) > 0){
                            foreach($deviceTypesArr as $dtaKey=>$dta){

                                $selected = '';

                                if($dtaKey==$device_type){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{ $dtaKey }}" <?php echo $selected; ?> ><?php echo $dta; ?></option>
                                <?php
                            }

                        }
                        ?>
                    </select>

                    @include('snippets.errors_first', ['param' => 'device_type'])
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                    <label for="link_name" class="control-label {{ $link_req }}">Link:</label>

                    <input type="text" id="link" class="form-control" name="link" value="{{ old('link', $link) }}"  />

                    @include('snippets.errors_first', ['param' => 'link'])
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('brief') ? ' has-error' : '' }}">
                    <label for="link_name" class="control-label">Brief:</label>

                    <textarea id="brief" class="form-control" name="brief">{{ old('brief', $brief) }}</textarea>

                    @include('snippets.errors_first', ['param' => 'brief'])
                </div>
            </div>



            <?php
            $image_required = $image_req;
            if($banner_id > 0){
                $image_required = 'required';
            }
            ?>

            <div class="col-md-6">

                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                    <label for="sort_order" class="control-label {{ $image_required }}">Desktop Images (Max. width: {{$IMG_WIDTH}}px, Max. height: {{$IMG_HEIGHT}}px):</label><br>
                    <label for="sort_order" class="control-label">Mobile Images (Max. width: 500px, Max. height: 400px):</label>

                    <input type="file" id="image" name="image[]" multiple="multiple" />

                    @include('snippets.errors_first', ['param' => 'image'])
                </div>

                <?php
                if(!empty($banner_images)){
                    foreach ($banner_images as $key => $img) {

                        $image_id = $img->id;
                        $image_name = $img->name;
                        if($storage->exists($path.$image_name)){
                            ?>
                            <div class=" image_box" style="display: inline-block">
                                <a href="{{ url('storage/'.$path.$image_name) }}" target="_blank">
                                    <img src="{{ url('storage/'.$path.'thumb/'.$image_name) }}" style="width:70px;">
                                </a>
                                <br>
                                <a href="javascript:void(0)" data-id="{{ $image_id }}" class="del_banner">Delete</a>
                            </div>
                            <?php
                        }

                    }
                    ?>
                    <?php
                }
                ?>

                <input type="hidden" name="old_image" value="{{ $old_image }}">

            </div>

          <!--   <div class="clearfix"></div> -->
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                    <label for="sort_order" class="control-label">Sort Order:</label>

                    <input type="text" id="sort_order" class="form-control" name="sort_order" value="{{ old('sort_order', $sort_order) }}"  />

                    @include('snippets.errors_first', ['param' => 'sort_order'])
                </div>
            </div>


            <div class="col-md-12">
                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                    <label class="control-label">Status:</label>
                    &nbsp;&nbsp;
                    Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                    &nbsp;
                    Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                    @include('snippets.errors_first', ['param' => 'featured'])
                </div>
            </div>


            <div class="col-md-12">
                <div class="form-group">
                    <p></p>
                    <input type="hidden" id="banner_id" class="form-control" name="banner_id" value="{{ old('banner_id', $banner_id) }}"  />
                    <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                </div>
            </div>
        </div>

    </div>

</div>

</form>
<div class="clearfix"></div>
</div>

@slot('bottomBlock')

<script>
    $(document).ready(function(){

        $("select[name='page']").on("change", function(){
            var page_name = $(this).val();

            if(page_name == 'home_link'){
                $("#image").siblings("label").removeClass("required");
                $("#link").siblings("label").addClass("required");
            }
            else{
                if(!($("#image").siblings("label").hasClass("required"))){
                    $("#image").siblings("label").addClass("required");
                }
                $("#link").siblings("label").removeClass("required");
            }
        });

        $(".del_banner").click(function(){

            var current_sel = $(this);

            var image_id = $(this).attr('data-id');

            conf = confirm("Are you sure to Delete this Banner Image?");

            if(conf){

                var _token = '{{ csrf_token() }}';

                $.ajax({
                    url: "{{ route('admin.banners.ajax_delete_image') }}",
                    type: "POST",
                    data: {image_id:image_id},
                    dataType:"JSON",
                    headers:{'X-CSRF-TOKEN': _token},
                    cache: false,
                    beforeSend:function(){
                       $(".ajax_msg").html("");
                   },
                   success: function(resp){
                    if(resp.success){
                        $(".ajax_msg").html(resp.msg);
                        current_sel.parent('.image_box').remove();
                    }
                    else{
                        $(".ajax_msg").html(resp.msg);
                    }

                }
            });
            }

        });

    });
</script>

@endslot


@endcomponent