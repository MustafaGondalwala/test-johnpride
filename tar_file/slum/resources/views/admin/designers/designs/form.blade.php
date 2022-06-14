@component('admin.layouts.main')

@slot('title')
Admin - {{ $page_heading }} - {{ config('app.name') }}
@endslot


<?php

$back_url = (request()->has('back_url'))?request()->input('back_url'):'';

if(empty($back_url)){
    $back_url = 'admin/designers';
}

$first_name = (isset($designer->first_name))?$designer->first_name:'';
$last_name = (isset($designer->last_name))?$designer->last_name:'';
$screen_name = (isset($designer->screen_name))?$designer->screen_name:'';
$email = (isset($designer->email))?$designer->email:'';
$phone = (isset($designer->phone))?$designer->phone:'';
$address = (isset($designer->address))?$designer->address:'';
$discount = (isset($designer->discount))?$designer->discount:'';
$city = (isset($designer->city))?$designer->city:'';
$state = (isset($designer->state))?$designer->state:'';
$pincode = (isset($designer->pincode))?$designer->pincode:'';
$country = (isset($designer->country))?$designer->country:'';
$status = (isset($designer->status))?$designer->status:1;

$printing_commission = (isset($designer->printing_commission))?$designer->printing_commission:'';
$referral_code = (isset($designer->referral_code))?$designer->referral_code:'';
$referral_commission = (isset($designer->referral_commission))?$designer->referral_commission:'';

$state = old('state', $state);
$city = old('city', $city);
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
                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label class="control-label required">First Name:</label>

                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $first_name) }}" />

                    @include('snippets.errors_first', ['param' => 'first_name'])
                </div>
            </div>

            <div class="col-md-6">

                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label class="control-label">Last Name:</label>

                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $last_name) }}" />

                    @include('snippets.errors_first', ['param' => 'last_name'])
                </div>

            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('screen_name') ? ' has-error' : '' }}">
                    <label class="control-label">Screen Name:</label>

                    <input type="text" name="screen_name" class="form-control" value="{{ old('screen_name', $screen_name) }}" />

                    @include('snippets.errors_first', ['param' => 'screen_name'])
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="control-label">Email:</label>

                    <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" />

                    @include('snippets.errors_first', ['param' => 'email'])
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="control-label">Password:</label>

                    <input type="password" name="password" class="form-control" />

                    @include('snippets.errors_first', ['param' => 'password'])
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label class="control-label">Phone:</label>

                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $phone) }}" />

                    @include('snippets.errors_first', ['param' => 'phone'])
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('printing_commission') ? ' has-error' : '' }}">
                    <label class="control-label">Printing Commission (%):</label>

                    <input type="number" name="printing_commission" class="form-control" value="{{ old('printing_commission', $printing_commission) }}" />

                    @include('snippets.errors_first', ['param' => 'printing_commission'])
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('referral_code') ? ' has-error' : '' }}">
                    <label class="control-label">Refferal Code:</label>

                    <input type="text" name="referral_code" class="form-control" value="{{ old('referral_code', $referral_code) }}" />

                    @include('snippets.errors_first', ['param' => 'referral_code'])
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('referral_commission') ? ' has-error' : '' }}">
                    <label class="control-label">Refferal Commission (%):</label>

                    <input type="number" name="referral_commission" class="form-control" value="{{ old('referral_commission', $referral_commission) }}" />

                    @include('snippets.errors_first', ['param' => 'referral_commission'])
                </div>
            </div>

        </div>



        <div class="row">
            <div class="col-md-12">
                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                    <label class="control-label">Address:</label>

                    <textarea name="address" class="form-control" >{{ old('address', $address) }}</textarea>

                    @include('snippets.errors_first', ['param' => 'address'])
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                    <label class="control-label">State:</label>

                    <select name="state" class="form-control">
                        <option value="">--Select--</option>

                    <?php
                    
                    if(!empty($states) && count($states) > 0){
                        foreach($states as $st){
                            $selected = '';
                            if($st->id == $state){
                                $selected = 'selected';
                            }
                            ?>
                            <option value="{{$st->id}}" {{$selected}} >{{$st->name}}</option>
                            <?php
                        }
                    }
                    ?>
                    </select>

                    @include('snippets.errors_first', ['param' => 'state'])
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                    <label class="control-label">City:</label>

                    <select name="city" class="form-control">
                        <option value="">--Select--</option>
                    </select>

                    @include('snippets.errors_first', ['param' => 'city'])
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                    <label class="control-label">Country:</label>
                    <select class="form-control" >
                        <option value="99">India</option>
                    </select>

                    @include('snippets.errors_first', ['param' => 'country'])
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('pincode') ? ' has-error' : '' }}">
                    <label class="control-label">Pincode:</label>

                    <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $pincode) }}" />

                    @include('snippets.errors_first', ['param' => 'pincode'])
                </div>
            </div>
        </div>

        <div class="row">
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
                    <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                    <a href="{{ url($back_url) }}" class="btn btn-primary" style="padding: 10px 17px;">Cancel</a>
                </div>
            </div>

        </div>

    </form>

</div>

</div>

@endcomponent

<script type="text/javascript">
    state_id = '{{ $state }}';
    city_id = '{{ $city }}';

    load_cities( state_id, city_id )

    $(document).on("change", "select[name='state']", function () {
        state_id = $( this ).val();
        load_cities( state_id, city_id );
    } );

    function load_cities( state_id, city_id ) {

        _token = '{{csrf_token()}}';

        $.ajax( {
            url: "{{url('common/ajax_load_cities')}}",
            type: "POST",
            data: {state_id: state_id, city_id: city_id},
            dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': _token
            },
            cache: false,
            beforeSend: function () {},
            success: function ( resp ) {
                if ( resp.success ) {
                    $("select[name='city']").html( resp.options );
                }
            }
        } );
    }
</script>