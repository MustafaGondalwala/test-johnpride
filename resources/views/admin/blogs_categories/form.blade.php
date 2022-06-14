@component('admin.layouts.main')

@slot('title')
Admin - {{$page_heading}} - {{ config('app.name') }}
@endslot

<div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';
    if(empty($back_url)){
        $back_url = 'admin/blog_categories';
    }

    $id = (isset($category->id))?$category->id:0;
    $parent_id = (isset($category->parent_id))?$category->parent_id:0;
    $name = (isset($category->name))?$category->name:'';
    $slug = (isset($category->slug))?$category->slug:'';
    $meta_title = (isset($category->meta_title))?$category->meta_title:'';
    $meta_keyword = (isset($category->meta_keyword))?$category->meta_keyword:'';
    $meta_description = (isset($category->meta_description))?$category->meta_description:'';
    $status = (isset($category->status))?$category->status:1;
    $sort_order = (isset($category->sort_order))?$category->sort_order:0;

    $path = 'blog_categories/';
    $thumb_path = 'blog_categories/thumb/';

    $storage = Storage::disk('public');

    ?>

    <div class="col-md-12">
        <div class="alert_msg"></div>

        <h2>{{$page_heading}}</h2>
        <div class="bgcolor">

            @include('snippets.errors')
            @include('snippets.flash')

            <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}

                <input type="hidden" name="id" value="{{$id}}">
                <input type="hidden" name="parent_id" value="{{$parent_id}}">

                <div class="row">

                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>
                    </div>  

                     <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label class="control-label ">Sort Order:</label>

                            <input type="text" name="sort_order" class="form-control" value="{{ old('sort_order', $sort_order) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>                 

                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                            <label class="control-label ">Meta Title:</label>

                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $meta_title) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'meta_title'])
                        </div>
                    </div>


                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                            <label class="control-label ">Meta Keyword:</label>

                            <input type="text" name="meta_keyword" class="form-control" value="{{ old('meta_keyword', $meta_keyword) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'meta_keyword'])
                        </div>
                    </div> 


                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                            <label class="control-label ">Meta Description:</label>

                            <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $meta_description) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'meta_description'])
                        </div>
                    </div>  

                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">

                            <?php
                            $sel_status = old('status', $status);
                            ?>
                            <label class="control-label required">Status:</label>
                            <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>Active
                            &nbsp;
                            <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>Inactive

                            @include('snippets.errors_first', ['param' => 'status'])
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

@endcomponent

