@component('admin.layouts.main')

    @slot('title')
        Admin - {{ $page_heading }} - {{ config('app.name') }}
    @endslot


    <?php    

    $id = (isset($brand->id))?$brand->id:'';
    $name = (isset($brand->name))?$brand->name:'';  
    $description = (isset($brand->description))?$brand->description:'';  
    $image = (isset($brand->image))?$brand->image:'';
    $icon = (isset($brand->icon))?$brand->icon:'';
    $sort_order = (isset($brand->sort_order))?$brand->sort_order:'';
    $featured = (isset($brand->featured))?$brand->featured:'';
    $status = (isset($brand->status))?$brand->status:1;
   
    $storage = Storage::disk('public');

    //pr($storage);

    $path = 'brands/';
    $path_icon = 'brands/icon/';

    $old_image = 0;
    $old_icon = 0;
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
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label required">Name:</label>

                            <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $name) }}" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>
                    </div>

                     <div class="col-md-12">
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="control-label">Description:</label>

                             <textarea name="description" class="form-control" maxlength="255">{{old('description', $description)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'description'])
                        </div>
                    </div>

                </div>
                
                <?php
                $image_required = $image_req;
                if($id > 0){
                    $image_required = '';
                }
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label for="sort_order" class="control-label ">Brand Image:</label>

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
                                     <a href="javascript:void(0)" data-id="{{ $id }}" data-type ="image" class="del_image">Delete</a>
                                 </div>
                                 <?php        
                             } 
                         ?>
                         <?php
                     }
                     ?>
                     <input type="hidden" name="old_image" value="{{ $old_image }}">
                 </div>

                 <div class="col-md-6">
                    <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
                        <label for="icon" class="control-label ">Icon Image:</label>

                        <input type="file" id="icon" name="icon"/>

                        @include('snippets.errors_first', ['param' => 'icon'])
                    </div>
                    <?php
                    if(!empty($icon)){
                        if($storage->exists($path_icon.$icon))
                        {
                            ?>
                            <div class="col-md-2 image_box">
                               <img src="{{ url('public/storage/'.$path_icon.$icon) }}" style="width: 100px;"><br>
                               <a href="javascript:void(0)" data-id="{{ $id }}" data-type ="icon" class="del_image">Delete</a>
                           </div>
                        <?php        
                       } 
                       ?>
                       <?php
                   }
                   ?>
                   <input type="hidden" name="old_icon" value="{{ $old_icon }}">
               </div>

             </div>

             <div class="row">
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label ">Sort Order:</label>

                            <input type="text" id="sort_order" class="form-control" name="sort_order" value="{{ old('sort_order', $sort_order) }}" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
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


        <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }}">
                <label class="control-label">Featured:</label>

                <input type="checkbox" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> >

                @include('snippets.errors_first', ['param' => 'featured'])
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


@slot('bottomBlock')

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
        var type = $(this).data('type');

        if(type == 'image'){
            conf = confirm("Are you sure to Delete this Brand Image?");
        }
        else if(type == 'icon'){
            conf = confirm("Are you sure to Delete this Brand Icon?");
        }

        if(conf){

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('admin.brands.ajax_delete_image') }}",
                type: "POST",
                data: {image_id:image_id, type:type},
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