@component('admin.layouts.main')

    @slot('title')
        Admin - {{ $page_heading }} - {{ config('app.name') }}
    @endslot


    <?php    

    $id = (isset($homeImage->id))?$homeImage->id:'';
    $title = (isset($homeImage->title))?$homeImage->title:'';  
    $subtitle = (isset($homeImage->subtitle))?$homeImage->subtitle:'';  
    $link = (isset($homeImage->link))?$homeImage->link:'';  
    $image = (isset($homeImage->image))?$homeImage->image:'';
    $sort_order = (isset($homeImage->sort_order))?$homeImage->sort_order:'';
    $status = (isset($homeImage->status))?$homeImage->status:1;
   
    $storage = Storage::disk('public');

    //pr($storage);

    $path = 'home_images/';

    $old_image = 0;
    $image_req = 'required';
    $link_req = '';

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
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="control-label required">Title:</label>

                            <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $title) }}" required  />

                            @include('snippets.errors_first', ['param' => 'title'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('subtitle') ? ' has-error' : '' }}">
                            <label for="subtitle" class="control-label">Sub Title:</label>

                            <input type="text" id="subtitle" class="form-control" name="subtitle" value="{{ old('subtitle', $subtitle) }}" required  />

                            @include('snippets.errors_first', ['param' => 'subtitle'])
                        </div>
                    </div>

                </div>


                   <div class="row">
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                            <label for="link" class="control-label">Link:</label>

                            <input type="text" id="link" class="form-control" name="link" value="{{ old('link', $link) }}" required  />

                            @include('snippets.errors_first', ['param' => 'link'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label for="sort_order" class="control-label">Sort Order:</label>

                            <input type="text" id="sort_order" class="form-control" name="sort_order" value="{{ old('sort_order', $sort_order) }}" required  />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>

                </div>
                
                
                <?php
                $image_required = $image_req;
                if($id > 0){
                    $image_required = 'required';
                }
                ?>
                <div class="row">
                    <div class="col-md-4">

                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label for="sort_order" class="control-label {{ $image_required }}">Home Image:</label>

                            <input type="file" id="image" name="image"/>

                            @include('snippets.errors_first', ['param' => 'image'])
                        </div>

                        <?php
                        if(!empty($image)){
                                if($storage->exists($path.$image))
                                {
                                    ?>
                                    <div class="col-md-2 image_box">
                                     <img src="{{ url('public/storage/'.$path.'thumb/'.$image) }}" style="width: 100px;"><br>
                                     <a href="javascript:void(0)" data-id="{{ $id }}" class="del_image">Delete</a>
                                 </div>
                                 <?php        
                             }
                         
                         ?>
                         <?php
                     }
                     ?>

                     <input type="hidden" name="old_image" value="{{ $old_image }}">

                 </div>
             </div>


         
             <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                <label class="control-label">Status:</label>
                &nbsp;&nbsp;
                Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                &nbsp;
                Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                @include('snippets.errors_first', ['param' => 'status'])
            </div>
      

 
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <p></p>
                            <input type="hidden" id="id" class="form-control" name="id" value="{{ old('id', $id) }}"  />
                            <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </form>
            <div class="clearfix"></div>
        </div>
 

@endcomponent


<script>
$(document).ready(function(){

  /*  $("select[name='page']").on("change", function(){
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
    });*/

    $(".del_image").click(function(){

        var current_sel = $(this);

        var image_id = $(this).data('id');

        conf = confirm("Are you sure to Delete this Home Image?");

        if(conf){

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('admin.home_images.ajax_delete_image') }}",
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