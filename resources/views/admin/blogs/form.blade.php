@component('admin.layouts.main')

    @slot('title')
        Admin - {{ $page_heading }} - {{ config('app.name') }}
    @endslot

    @slot('headerBlock')

    <link rel="stylesheet" type="text/css" href="{{ url('css/jquery-ui.css') }}"/ >

    <link rel="stylesheet" type="text/css" href="{{ url('datetimepicker/jquery-ui-timepicker-addon.css') }}"/ >

    @endslot


    <?php

    //pr($blog->toArray());

    $id = (isset($blog->id))?$blog->id:'';
    $category_id = (isset($blog->category_id))?$blog->category_id:'';
    $title = (isset($blog->title))?$blog->title:'';
    $subtitle = (isset($blog->subtitle))?$blog->subtitle:'';
    $content = (isset($blog->content))?$blog->content:'';
    $sort_order = (isset($blog->sort_order))?$blog->sort_order:0;
    $author_name = (isset($blog->author_name))?$blog->author_name:'';
    $meta_title = (isset($blog->meta_title))?$blog->meta_title:'';
    $meta_keyword = (isset($blog->meta_keyword))?$blog->meta_keyword:'';
    $meta_description = (isset($blog->meta_description))?$blog->meta_description:'';
    $featured = (isset($blog->featured))?$blog->featured:0;
    $status = (isset($blog->status))?$blog->status:1;
    
    $blog_date = (isset($blog->blog_date))?$blog->blog_date:'';
    $date = CustomHelper::DateFormat($blog_date, 'd/m/Y');

    $storage = Storage::disk('public');

    //pr($storage);

    $path = 'blogs/';

    $old_image = 0;

    $image_req = 'required';
    $link_req = '';

    ?>

    <div class="row">

        <div class="col-md-12 bgcolor">

            <h2>{{ $page_heading }} <?php if(request()->has('back_url')){ $back_url= request('back_url');  ?>
            
        <a href="{{ url($back_url)}}" class="btn btn-success btn-sm" style='float: right;'>Back</a><?php } ?>
    </h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="ajax_msg"></div>

            <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label required">Title:</label>

                            <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $title) }}" required  />

                            @include('snippets.errors_first', ['param' => 'title'])
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('subtitle') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Subtitle:</label>

                            <input type="text" id="subtitle" class="form-control" name="subtitle" value="{{ old('subtitle', $subtitle) }}"  />

                            @include('snippets.errors_first', ['param' => 'subtitle'])
                        </div>
                    </div>
                
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Description:</label>

                            <textarea id="content" class="form-control " name="content">{{ old('content', $content) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'content'])
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                            <label for="status" class="control-label required">Category:</label>

                            <select name="category_id" class="form-control" required>
                                <?php
                                if(!empty($categories) && count($categories) > 0){
                                    foreach($categories as $cat_key=>$cat_val){
                                        //prd($pa_val);
                                        ?>
                                        <option @if($cat_val['id']==$category_id) selected @endif value="{{ $cat_val['id'] }}"><?php echo $cat_val['name']; ?></option>
                                        <?php
                                    }
                                    
                                }
                                ?>
                            </select>

                            @include('snippets.errors_first', ['param' => 'category_id'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('blog_date') ? ' has-error' : '' }}">
                        <label for="blog_date" class="control-label">Date:</label>
                            <input type="text" id="blog_date" class="form-control" name="blog_date" value="{{ old('date', $date) }}"  />

                            @include('snippets.errors_first', ['param' => 'blog_date'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label for="sort_order" class="control-label">Sort Order:</label>

                            <input type="text" id="sort_order" class="form-control" name="sort_order" value="{{ old('sort_order', $sort_order) }}"  />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('author_name') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Author Name:</label>

                            <input type="text" id="author_name" class="form-control" name="author_name" value="{{ old('author_name', $author_name) }}"  />

                            @include('snippets.errors_first', ['param' => 'author_name'])
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Meta Title:</label>

                            <input type="text" id="meta_title" class="form-control" name="meta_title" value="{{ old('meta_title', $meta_title) }}"  />

                            @include('snippets.errors_first', ['param' => 'meta_title'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Meta Keyword:</label>

                            <input type="text" id="meta_keyword" class="form-control" name="meta_keyword" value="{{ old('meta_keyword', $meta_keyword) }}"  />

                            @include('snippets.errors_first', ['param' => 'meta_keyword'])
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('meta_description') ? ' has-error' : '' }}">
                            <label for="meta_description" class="control-label">Meta Description:</label>

                            <input type="text" id="meta_description" class="form-control" name="meta_description" value="{{ old('meta_description', $meta_description) }}"  />

                            @include('snippets.errors_first', ['param' => 'meta_description'])
                        </div>
                    </div>
                


                <?php
                $image_required = $image_req;
                if($id > 0){
                    $image_required = 'required';
                }
                ?>
                        
                    
                    <div class="col-md-12">

                                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                    <label for="sort_order" class="control-label {{ $image_required }}">Blog Images:</label>

                                    <input type="file" id="image" name="image[]" multiple="multiple" />

                                    @include('snippets.errors_first', ['param' => 'image'])
                                </div>
                            <table class="table">
                                <tr>
                            <?php
                            if(!empty($blog_images)){
                                foreach ($blog_images as $key => $img) {
                                    
                                    $image_id = $img->id;
                                    $image_name = $img->image;
                                    if($storage->exists($path.$image_name))
                                    {
                                ?>
                                <div class="col-md-2 image_box">
                                   <img src="{{ url('storage/'.$path.'thumb/'.$image_name) }}" style="width: 100px; height:  100px;"><br>
                                   <a href="javascript:void(0)" data-id="{{ $image_id }}" class="del_banner">Delete</a>
                               </div>
                                <?php        
                                    }

                                }
                                ?>
                               <?php
                           }
                           ?>
                       </tr>
                       </table>

                           <input type="hidden" name="old_image" value="{{ $old_image }}">
                        
                 </div>


                
                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }} col-md-12">
                            <label class="control-label">Status:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

                    <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }} col-md-12">
                            <label class="control-label">Featured:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="featured" value="0" <?php echo ( strlen($featured) > 0 && $featured == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'featured'])
                        </div>
                
                
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

    </div>




@slot('bottomBlock')

<script type="text/javascript" src="{{ url('js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ url('datetimepicker/jquery-ui-timepicker-addon.js') }}"></script>

<script type="text/javascript" src="{{ url('js/ckeditor/ckeditor.js') }}"></script>


<script type="text/javascript">

$(document).ready(function(){

    $(".del_banner").click(function(){

        var current_sel = $(this);

        var image_id = $(this).attr('data-id');

        conf = confirm("Are you sure to Delete this Image?");

        if(conf){

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('admin.blogs.ajax_delete_image') }}",
                type: "POST",
                data: {image_id},
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
    
$(document).ready(function(){

//var editor = CKEDITOR.replace('content');

var editor = CKEDITOR.replace('content', {
                filebrowserImageBrowseUrl: "{{url('public/storage/products/ck')}}",
                filebrowserImageUploadUrl: "<?php echo url('admin/ck_upload?type=blogs&csrf_token='.csrf_token());?>"
            });

$('#blog_date').datepicker({
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true,
    yearRange:"1950:+0"
});

});
 </script>


@endslot

@endcomponent