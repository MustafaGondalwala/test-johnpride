@component('admin.layouts.main')

@slot('title')
Admin - {{ $page_heading }} - {{ config('app.name') }}
@endslot


<?php

$back_url = (request()->has('back_url'))?request()->input('back_url'):'';

if(empty($back_url)){
    $back_url = 'admin/customers';
}


$first_name = (isset($customer->first_name))?$customer->first_name:'';
$last_name = (isset($customer->last_name))?$customer->last_name:'';
$business_name = (isset($customer->business_name))?$customer->business_name:'';
$email = (isset($customer->email))?$customer->email:'';
$phone = (isset($customer->phone))?$customer->phone:'';
$address = (isset($customer->address))?$customer->address:'';
$city = (isset($customer->city))?$customer->city:'';
$state = (isset($customer->state))?$customer->state:'';
$pincode = (isset($customer->pincode))?$customer->pincode:'';
$country = (isset($customer->country))?$customer->country:'';
$status = (isset($customer->status))?$customer->status:1;
$is_wallet = (isset($customer->is_wallet))?$customer->is_wallet:0;


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
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label class="control-label required">First Name:</label>

                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $first_name) }}" />

                    @include('snippets.errors_first', ['param' => 'first_name'])
                </div>
            </div>

            <div class="col-md-4">

                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label class="control-label">Last Name:</label>

                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $last_name) }}" />

                    @include('snippets.errors_first', ['param' => 'last_name'])
                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group{{ $errors->has('business_name') ? ' has-error' : '' }}">
                    <label class="control-label">Business Name:</label>

                    <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $business_name) }}" />

                    @include('snippets.errors_first', ['param' => 'business_name'])
                </div>

            </div>
        </div>

        <div class="row">

             <div class="col-md-4">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="control-label required">Email:</label>

                    <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" />

                    @include('snippets.errors_first', ['param' => 'email'])
                </div>
            </div>

			<div class="col-md-4">
                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label class="control-label">Phone:</label>

                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $phone) }}" />

                    @include('snippets.errors_first', ['param' => 'phone'])
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="control-label">Password:</label>

                    <input type="password" name="password" class="form-control" />

                    @include('snippets.errors_first', ['param' => 'password'])
                </div>
            </div>
        </div>

        <div class="row">
            

           
        </div>



        <div class="row">
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                    <label class="control-label required">Address:</label>

                    <textarea name="address" class="form-control" >{{ old('address', $address) }}</textarea>

                    @include('snippets.errors_first', ['param' => 'address'])
                </div>
            </div>

            <div class="clearfix"> </div>


            <div class="col-md-4">
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

            <div class="col-md-4">
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
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                    <label class="control-label">Country:</label>
                    <select name="country" class="form-control" >
                        <option value="99">India</option>
                    </select>

                    @include('snippets.errors_first', ['param' => 'country'])
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group{{ $errors->has('pincode') ? ' has-error' : '' }}">
                    <label class="control-label">Pincode:</label>

                    <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $pincode) }}" />

                    @include('snippets.errors_first', ['param' => 'pincode'])
                </div>
            </div> 
            <div class="clearfix"></div>
            <div class="col-md-4">
                
                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                    <label class="control-label">Status:</label><br>
                    
                    Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                    &nbsp;
                    Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                    @include('snippets.errors_first', ['param' => 'status'])
                </div>
            </div>

            <div class="col-md-4"> 
                <div class="form-group{{ $errors->has('is_wallet') ? ' has-error' : '' }}">
                    <label class="control-label">Wallet:</label><br>
                     
                    Active: <input type="radio" name="is_wallet" value="1" <?php echo ($is_wallet == '1')?'checked':''; ?> >
                    &nbsp;
                    Inactive: <input type="radio" name="is_wallet" value="0" <?php echo ( strlen($is_wallet) > 0 && $is_wallet == '0')?'checked':''; ?> >

                    @include('snippets.errors_first', ['param' => 'is_wallet'])
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                    <a href="{{ url($back_url) }}" class="btn btn-lg btn-primary" >Cancel</a>
                </div>
            </div>

        </div>

    </form>

</div>

</div>

@endcomponent

<script type="text/javascript">
    var state_id = '{{ $state }}';
    var city_id = '{{ $city }}';

    if(state_id && state_id != ""){
        load_cities( state_id, city_id );
    }

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