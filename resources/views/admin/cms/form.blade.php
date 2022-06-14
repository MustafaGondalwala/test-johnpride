@component('admin.layouts.main')

    @slot('title')
        Admin - Edit User - {{ config('app.name') }}
    @endslot

    <?php
    //pr($page);
    $name = (isset($page->name))?$page->name:'';
    $slug = (isset($page->slug))?$page->slug:'';
    $title = (isset($page->title))?$page->title:'';
    $heading = (isset($page->heading))?$page->heading:'';
    $content = (isset($page->content))?$page->content:'';

    $meta_title = (isset($page->meta_title))?$page->meta_title:'';
    $meta_keyword = (isset($page->meta_keyword))?$page->meta_keyword:'';
    $meta_description = (isset($page->meta_description))?$page->meta_description:'';
    ?>

    <div class="row">

        <div class="col-md-12">

            <h2>{{ $page_heading }}</h2>

            @include('snippets.errors')
            @include('snippets.flash')
			<div class="bgcolor">
            <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}

                <?php /*
                <div class="form-group  col-md-2{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="control-label required">Name:</label>

                    <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $name) }}" maxlength="255"/>
                </div> */?>

                <div class="form-group  col-md-4{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="control-label required">Title:</label>

                    <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $title) }}" maxlength="255" required />

                    @include('snippets.errors_first', ['param' => 'title'])
                </div>

                <div class="form-group  col-md-4{{ $errors->has('heading') ? ' has-error' : '' }}">
                    <label for="heading" class="control-label">Heading:</label>

                    <input type="text" id="heading" class="form-control" name="heading" value="{{ old('heading', $heading)}}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'heading'])
                </div>

                <div class="form-group  col-md-4{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="control-label required">Slug:</label>

                    <input type="text" id="slug" class="form-control" name="slug" value="{{ old('slug', $slug) }}"/>
                </div>


				<div class="clearfix"></div>
                <div class="form-group  col-md-12{{ $errors->has('content') ? ' has-error' : '' }}">
                	<label for="content" class="control-label">Content:</label>

                	<textarea id="content" name="content" class="form-control ckeditor" ><?php echo old('content', $content); ?></textarea>    

                	@include('snippets.errors_first', ['param' => 'content'])
                </div>

                <hr>
				<div class="col-md-12">
                <h3>SEO:</h3>
				</div>
				
                <div class="form-group col-md-2{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                	<label for="meta_title" class="control-label">Meta Title:</label>

                	<input type="text" id="meta_title" class="form-control" name="meta_title" value="{{ old('meta_title', $meta_title)}}" />    

                	@include('snippets.errors_first', ['param' => 'meta_title'])
                </div>

                <div class="form-group col-md-2{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                	<label for="meta_keyword" class="control-label" maxlength="688" >Meta Keyword:</label>

                	<input type="text" id="meta_keyword" class="form-control" name="meta_keyword" value="{{ old('meta_keyword', $meta_keyword)}}" />    

                	@include('snippets.errors_first', ['param' => 'meta_keyword'])
                </div>

                <div class="form-group col-md-2{{ $errors->has('meta_description') ? ' has-error' : '' }}">
                	<label for="meta_description" class="control-label">Meta Description:</label>

                	<textarea id="meta_description" name="meta_description" class="form-control" >{{ old('meta_description', $meta_description) }}</textarea>    

                	@include('snippets.errors_first', ['param' => 'meta_description'])
                </div>
				
				 <div class="clearfix"></div>
                <div class="form-group col-md-12">
                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <a href="{{ route('admin.cms.index') }}" class="btn btn-lg btn-primary" title="Cancel">Cancel</a>
                </div>
				<br/><br/>

            </form>
			</div>
        </div>       

        
    </div>

@endcomponent

    <script type="text/javascript" src="{{ url('js/ckeditor/ckeditor.js') }}"></script>

    <script type="text/javascript">
    	var content = document.getElementById('content');
    	CKEditor.replace(content);
    </script>