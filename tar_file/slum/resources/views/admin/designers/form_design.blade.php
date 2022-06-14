@component('admin.layouts.main')

@slot('title')
Admin - {{ $page_heading }} - {{ config('app.name') }}
@endslot

<link href="{{url('public')}}/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

<?php

$back_url = (request()->has('back_url'))?request()->input('back_url'):'';

if(empty($back_url)){
    $back_url = 'admin/designers/view_design';
}

$name = (isset($design->name))?$design->name:'';
$cat_id = (isset($design->category_id))?$design->category_id:'';
$category_id = explode(',', $cat_id);
$is_approved = (isset($design->is_approved))?$design->is_approved:'';

?>

<div class="row">

    <div class="col-md-12">

        <h2>{{ $page_heading }}
         <a href="{{ url($back_url) }}" class="btn btn-sm btn-success pull-right">Back</a>
     </h2>

     @include('snippets.errors')
     @include('snippets.flash')

     <form method="POST" action="" accept-charset="UTF-8" role="form">
        {{ csrf_field() }}


		<div class="row">
		<div class="col-md-4">
                <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                    <label class="control-label">Category:</label>

                    <select name="category_id[]" class="form-control category_id" multiple="">
                    <?php
                    
                    if(!empty($categories) && count($categories) > 0){
                        foreach($categories as $cat){
                            $selected = '';
                            if(in_array($cat->id, $category_id))
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

                    @include('snippets.errors_first', ['param' => 'category_id'])
                </div>
            </div> 
	
        </div>

        <div class="row">
        <div class="col-md-4">
                <div class="form-group{{ $errors->has('is_approved') ? ' has-error' : '' }}">
                    <label class="control-label required">Status:</label>

                    <select name="is_approved" class="form-control">
                        <option <?php if($is_approved==0){ echo 'selected'; } ?> value="0">Pending</option>
                        <option <?php if($is_approved==1){ echo 'selected'; } ?> value="1">Approved</option>
                        <option <?php if($is_approved==2){ echo 'selected'; } ?> value="2">Disapproved</option>
                    </select>

                    @include('snippets.errors_first', ['param' => 'is_approved'])
                </div>
            </div> 
    
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                    <a href="{{ url($back_url) }}" class="btn btn-primary" style="padding: 10px 17px;">Cancel</a>
                </div>
            </div>

        </div>

    </form>

</div>

</div>

@slot('bottomBlock')

<script type="text/javascript" src="{{ url('public/jquery/jquery.js') }}"></script>

<script type="text/javascript" src="{{ url('public/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>


<script type="text/javascript">

$(document).ready(function() {
   
   $('.category_id').multiselect({
        numberDisplayed: 2

    });

});
</script>

@endslot


@endcomponent
