@component('admin.layouts.main')

    @slot('title')
        Admin - {{$page_heading}} - {{ config('app.name') }}
    @endslot

    <div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

    if(empty($back_url)){
        $back_url = 'admin/discounts?type='.$type;
    }
    $type = (isset($discount->type))?$discount->type:'';

    $min_len = (isset($discount->min_len))?$discount->min_len:'';
    $max_len = (isset($discount->max_len))?$discount->max_len:'';
    $value = (isset($discount->value))?$discount->value:'';
    $status = (isset($discount->status))?$discount->status:'1';
    ?>

        <div class="col-md-12">

            <h2>{{$page_heading}}</h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="alert_msg"></div>

            <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                <input type="hidden" name="discount_id" value="{{$discount_id}}">

				<div class="bgcolor">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('min_len') ? ' has-error' : '' }}">
                            <label class="control-label required">Min. Length (in meters):</label>

                            <input type="text" name="min_len" class="form-control" value="{{ old('min_len', $min_len) }}" />

                            @include('snippets.errors_first', ['param' => 'min_len'])
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('max_len') ? ' has-error' : '' }}">
                            <label class="control-label required">Max. Length (in meters):</label>

                            <input type="text" name="max_len" class="form-control" value="{{ old('max_len', $max_len) }}" />

                            @include('snippets.errors_first', ['param' => 'max_len'])
                        </div>
                    </div>

                    <?php $type = (request()->has('type'))?request()->type:''; ?>

                     <?php if($type == 'printing' || $type == 'fabric') { ?>
                    <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                            <label class="control-label required">value (%):</label>

                            <input type="text" name="value" class="form-control" value="{{ old('value', $value) }}" />

                            @include('snippets.errors_first', ['param' => 'value'])
                        </div>
                    </div>
                    <?php } ?>



                    <?php if($type == 'designer_commission') { ?>
                    <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                            <label class="control-label required">value (Rs.):</label>

                            <input type="text" name="value" class="form-control" value="{{ old('value', $value) }}" />

                            @include('snippets.errors_first', ['param' => 'value'])
                        </div>
                    </div>
                    <?php } ?>

                    <div class="col-sm-12 col-md-6">

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
                            <a href="{{ url($back_url) }}" class="btn btn-primary" >Cancel</a>
                        </div>
                    </div>

                </div>
				</div>

            </form>

        </div>

    </div>

    @slot('bottomBlock')

    <script type="text/javascript">
    $(document).on("click", ".del_image", function(){
        var curr_sel = $(this);
        var id = $(this).data("id");

        if(id && id != "" ){
            var conf = confirm("Are you sure to delete this Image?");

            if(conf){
                var _token = '{{ csrf_token() }}';

                $.ajax({
                    url: "{{ url('admin/categories/ajax_delete_image') }}",
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

@endslot

@endcomponent