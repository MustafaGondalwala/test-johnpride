@component('admin.layouts.main')

@slot('title')
Admin - {{$page_heading}} - {{ config('app.name') }}
@endslot

<link href="{{url('/')}}/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

<div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';
    if(empty($back_url)){
        $back_url = 'admin/countries';
    }
    
    $name = (isset($rec->name))?$rec->name:'';
    $iso = (isset($rec->iso))?$rec->iso:'';
    $iso3 = (isset($rec->iso3))?$rec->iso3:'';
    $status = (isset($rec->status))?$rec->status:'1';

    ?>

    <div class="col-md-12">

        <h2>{{$page_heading}}</h2>
        <div class="bgcolor">

            @include('snippets.errors')
            @include('snippets.flash')

            <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}

               

                <div class="row">

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>
                    </div>

                     <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('iso') ? ' has-error' : '' }}">
                            <label class="control-label">ISO:</label>

                            <input type="text" name="iso" class="form-control" value="{{ old('iso', $iso) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'iso'])
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('iso3') ? ' has-error' : '' }}">
                            <label class="control-label">ISO3:</label>

                            <input type="text" name="iso3" class="form-control" value="{{ old('iso3', $iso3) }}" maxlength="255" />

                            @include('snippets.errors_first', ['param' => 'iso3'])
                        </div>
                    </div>


                       <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label class="control-label">Status:</label>
                            <br>

                            <input class="" type="checkbox" <?php if($status==1) { echo 'checked';  } ?> name="status" id="status" value="1">

                          

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

@slot('bottomBlock')




    
@endslot

@endcomponent