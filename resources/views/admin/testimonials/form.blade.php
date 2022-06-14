@component('admin.layouts.main')

@slot('title')
Admin - {{ $page_heading }} - {{ config('app.name') }}
@endslot


<?php

$back_url = (request()->has('back_url'))?request()->input('back_url'):'';

if(empty($back_url)){
    $back_url = 'admin/testimonials';
}

$name = (isset($testimonial->name))?$testimonial->name:'';
$subject = (isset($testimonial->subject))?$testimonial->subject:'';
$description = (isset($testimonial->description))?$testimonial->description:'';
$date_on = (isset($testimonial->date_on))?$testimonial->date_on:'';
$featured = (isset($testimonial->featured))?$testimonial->featured:1;
$status = (isset($testimonial->status))?$testimonial->status:1;

$date_on = CustomHelper::DateFormat($date_on, 'd/m/Y');

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
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="control-label required">Name:</label>

                    <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" />

                    @include('snippets.errors_first', ['param' => 'name'])
                </div>
            </div>

            <div class="col-md-6">

                <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                    <label class="control-label">Subject:</label>

                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $subject) }}" />

                    @include('snippets.errors_first', ['param' => 'subject'])
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="control-label required">Description:</label>

                    <textarea name="description" class="form-control" >{{ old('description', $description) }}</textarea>

                    @include('snippets.errors_first', ['param' => 'description'])
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('date_on') ? ' has-error' : '' }}">
                    <label class="control-label required">Date on:</label>

                    <input type="text" name="date_on" class="form-control date_on" value="{{ old('date_on', $date_on) }}" readonly />

                    @include('snippets.errors_first', ['param' => 'date_on'])
                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-md-6">
                <br>
                <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }}">
                    <label class="control-label">Featured:</label>
                    &nbsp;&nbsp;
                    Active: <input type="radio" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> >
                    &nbsp;
                    Inactive: <input type="radio" name="featured" value="0" <?php echo ( strlen($featured) > 0 && $featured == '0')?'checked':''; ?> >

                    @include('snippets.errors_first', ['param' => 'featured'])
                </div>
            </div>

            <div class="col-md-6">
                <br>
                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                    <label class="control-label">Status:</label>
                    &nbsp;&nbsp;
                    Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                    &nbsp;
                    Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                    @include('snippets.errors_first', ['param' => 'status'])
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Save</button>
                    <a href="{{ url($back_url) }}" class="btn btn-primary" style="padding: 10px 17px;">Cancel</a>
                </div>
            </div>

        </div>

    </form>

</div>

</div>

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $( function() {
        $( ".date_on" ).datepicker({
            'dateFormat':'dd/mm/yy',
            changeMonth:true,
            changeYear:true,
            yearRange:"1950:0+"
        });
    });
</script>