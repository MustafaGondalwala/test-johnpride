@component('admin.layouts.main')

    @slot('title')
        Admin - {{$page_heading}} - {{ config('app.name') }}
    @endslot

    <div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

    if(empty($back_url)){
        $back_url = 'admin/usages';
    }

    $name = (isset($usage->name))?$usage->name:'';
    $description = (isset($usage->description))?$usage->description:'';
    $image = (isset($usage->image))?$usage->image:'';
    $sort_order = (isset($usage->sort_order))?$usage->sort_order:'';
    $meta_title = (isset($usage->meta_title))?$usage->meta_title:'';
    $meta_keyword = (isset($usage->meta_keyword))?$usage->meta_keyword:'';
    $meta_description = (isset($usage->meta_description))?$usage->meta_description:'';

    $status = (isset($usage->status))?$usage->status:'1';

    $path = 'usages/';
    $thumb_path = 'usages/thumb/';

    $storage = Storage::disk('public');
    ?>

        <div class="col-md-12">

            <h2>{{$page_heading}}</h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="alert_msg"></div>

            <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                <input type="hidden" name="usage_id" value="{{$usage_id}}">

				<div class="bgcolor">
                <div class="row">
                    <div class="col-sm-12 col-md-6">

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="100" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>

                        

                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label class="control-label">Sort Order:</label>

                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $sort_order) }}" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>

                        <div class="form-group{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                            <label class="control-label">Meta Title:</label>

                            <textarea name="meta_title" class="form-control" maxlength="255">{{old('meta_title', $meta_title)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'meta_title'])
                        </div>

                        <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                            <label class="control-label">Meta Keywords:</label>

                            <textarea name="meta_keyword" class="form-control" maxlength="255">{{old('meta_keyword', $meta_keyword)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'meta_keyword'])
                        </div>

                        <div class="form-group{{ $errors->has('meta_description') ? ' has-error' : '' }}">
                            <label class="control-label">Meta Description:</label>

                            <textarea name="meta_description" class="form-control" maxlength="255">{{old('meta_description', $meta_description)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'meta_description'])
                        </div>

                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label class="control-label">Image:</label>

                            <input type="file" name="image">

                            <br>

                            <?php
                            if(!empty($image) && $storage->exists($thumb_path.$image)){

                                $img_url = url('public/storage/usages/thumb/'.$image);

                                if(!empty($image) && $storage->exists($path.$image)){
                                    $img_url = url('public/storage/usages/'.$image);
                                }
                                ?>
                                <div class="img_box">
                                    <a href="{{$img_url}}" target="_blank"><img src="{{url('public/storage/usages/thumb/'.$image)}}" style="width:100px;" ></a>

                                    <a href="javascript:void(0)" data-id="{{$usage->id}}" class="del_image">Delete</a>
                                </div>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'image'])
                        </div>

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label class="control-label">Status:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

                        <div class="form-group">

                            <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                            <a href="{{ url($back_url) }}" class="btn btn-primary">Cancel</a>
                        </div>
                    </div>

                </div>
				</div>

            </form>

        </div>

    </div>

@endcomponent

<script type="text/javascript">
    $(document).on("click", ".del_image", function(){
        var curr_sel = $(this);
        var id = $(this).data("id");

        if(id && id != "" ){
            var conf = confirm("Are you sure to delete this Image?");

            if(conf){
                var _token = '{{ csrf_token() }}';

                $.ajax({
                    url: "{{ url('admin/usages/ajax_delete_image') }}",
                    type: "POST",
                    data: {id:id},
                    dataType:"JSON",
                    headers:{'X-CSRF-TOKEN': _token},
                    cache: false,
                    beforeSend:function(){
                    },
                    success: function(resp){
                        if(resp.success){
                            curr_sel.parent(".img_box").remove();
                        }

                        if(resp.message){
                            $(".alert_msg").html(resp.message);
                        }
                    }
                });
            }
        }
    });
</script>