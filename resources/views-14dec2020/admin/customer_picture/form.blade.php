@component('admin.layouts.main')

    @slot('title')
        Admin - {{ $page_heading }} - {{ config('app.name') }}
    @endslot


    <?php    

    $id = (isset($customerPicture->id))?$customerPicture->id:'';
    $title = (isset($customerPicture->title))?$customerPicture->title:'';  
    $product_sku = (isset($customerPicture->product_sku))?$customerPicture->product_sku:'';  
    $url = (isset($customerPicture->url))?$customerPicture->url:'';
    $image = (isset($customerPicture->image))?$customerPicture->image:'';
    $sort_order = (isset($customerPicture->sort_order))?$customerPicture->sort_order:'';
    $featured = (isset($customerPicture->featured))?$customerPicture->featured:'';
    $status = (isset($customerPicture->status))?$customerPicture->status:1;
   
    $storage = Storage::disk('public');

    //pr($storage);

    $path = 'customer_picture/';

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
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="control-label required">Title:</label>

                            <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $title) }}" />

                            @include('snippets.errors_first', ['param' => 'title'])
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('product_sku') ? ' has-error' : '' }}">
                            <label for="product_sku" class="control-label required">Product SKU:</label>

                             <input type="text" id="product_sku" class="form-control" name="product_sku" value="{{ old('product_sku', $product_sku) }}" />

                            @include('snippets.errors_first', ['param' => 'product_sku'])
                        </div>
                    </div>

                     <div class="col-md-12">
                        <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                            <label for="url" class="control-label required">URL:</label>

                             <input type="text" id="url" class="form-control" name="url" value="{{ old('url', $url) }}" />

                            @include('snippets.errors_first', ['param' => 'url'])
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
                            <label for="sort_order" class="control-label ">Image:</label>

                            <input type="file" id="image" name="image"/>

                            @include('snippets.errors_first', ['param' => 'image'])
                        </div>

                        <?php
                        if(!empty($image)){
                                if($storage->exists($path.$image))
                                {
                                    ?>
                                    <div class="col-md-2 image_box">
                                     <img src="{{ url('storage/'.$path.'thumb/'.$image) }}" style="width: 100px;"><br>
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
                <label class="control-label required">Status:</label>
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
                            <button type="submit" class="btn btn-success" title="Submit"><i class="fa fa-save"></i> Submit</button>
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

  

    $(".del_image").click(function(){

        var current_sel = $(this);

        var image_id = $(this).data('id');
        var type = $(this).data('type');

        if(type == 'image'){
            conf = confirm("Are you sure to Delete this Image?");
        }
        if(conf){

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('admin.customer-picture.ajax_delete_image') }}",
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