@component('admin.layouts.main')

@slot('title')
Admin - {{$title}} - {{ config('app.name') }}
@endslot

<?php
$back_url = (request()->has('back_url'))?request()->input('back_url'):'';
    if(empty($back_url)){
        $back_url = 'admin';
    }
    else{
        $back_url = urldecode($back_url);
    }
?>

<div class="row">

    <div class="col-md-12">

        <h2>{{$heading}}</h2>
        <div class="bgcolor">
            
            @include('snippets.errors')
            @include('snippets.flash')

            <form method="POST" action="" accept-charset="UTF-8" role="form">
                {{ csrf_field() }}

                <div class="row">

                    <div class="form-group col-md-3{{ $errors->has('old_password') ? ' has-error' : '' }}">
                        <label for="old_password" class="control-label required">Old Password:</label>

                        <input type="password" name="old_password" id="old_password" class="form-control" maxlength="255" />

                        @include('snippets.errors_first', ['param' => 'old_password'])
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-3{{ $errors->has('new_password') ? ' has-error' : '' }}">
                        <label for="new_password" class="control-label required">New Password:</label>

                        <input type="password" name="new_password" id="new_password" class="form-control" maxlength="255" />

                        @include('snippets.errors_first', ['param' => 'new_password'])
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-3{{ $errors->has('confirm_password') ? ' has-error' : '' }}">
                        <label for="confirm_password" class="control-label required">Confirm Password:</label>

                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" maxlength="255" />

                        @include('snippets.errors_first', ['param' => 'confirm_password'])
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-12">

                        <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                        <a href="{{ url($back_url) }}" class="btn btn-lg btn-primary" title="Cancel">Cancel</a>
                    </div>
                </div>

            </form>
        </div>

        
    </div>



</div>

@endcomponent



