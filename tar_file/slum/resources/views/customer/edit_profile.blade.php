<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')

	<style>
		 
	</style>
	
	
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">Edit Profile</h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container"> 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')

			
		<div class="sccountsec">

		<form method="post" enctype="multipart/form-data">
		 {{ csrf_field() }}
		<div class="row"> 
		<div class="col-md-6 form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
			<label>First Name * : </label>
			<input type="text" name="first_name" class="form-control" value="{{$res->first_name}}">			
		</div>
		 

		<div class="col-md-6 form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
			<label>Last Name * : </label>
			<input type="text" name="last_name" class="form-control" value="{{$res->last_name}}">			
		</div>

		<?php if(auth()->user()->type=='designer'){  ?>
		<div class="col-md-12 form-group {{ $errors->has('about_me') ? ' has-error' : '' }}">
			<label>About Me : </label>
			<textarea name="about_me" id="about_me" class="form-control" > <?php echo $res->about_me; ?></textarea>
		</div>

		<div class="col-md-6 form-group {{ $errors->has('profile_pic') ? ' has-error' : '' }}">
			<label>Profie Picture : </label>
			<input type="file" name="profile_pic" id="profile_pic">
		</div>

		<?php /*?><div class="col-md-6 form-group">
		<?php if($res->profile_pic=='') { echo 'No picture found'; } ?>
		</div><?php */?>
		<?php } ?>

		<div class="col-md-6 form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
			<label>Phone * : </label>
			<input type="text" name="phone" class="form-control" value="{{$res->phone}}">			
		</div>

		<div class="col-md-6 form-group {{ $errors->has('pincode') ? ' has-error' : '' }}">
			<label>Pin Code  : </label>
			<input type="text" name="pincode" class="form-control" value="{{$res->pincode}}">
			
		</div>

		<div class="col-md-6 form-group {{ $errors->has('address1') ? ' has-error' : '' }}">
			<label>Address 1  : </label>
			<input type="text" name="address1" class="form-control" value="{{$res->address1}}">
			
		</div>

		<div class="col-md-6 form-group {{ $errors->has('address2') ? ' has-error' : '' }}">
			<label>Address 2  : </label>
			<input type="text" name="address2" class="form-control" value="{{$res->address2}}">
			
		</div>

		<div class="col-md-6 form-group {{ $errors->has('country') ? ' has-error' : '' }}">
			<label>Country</label>
			<select name="country" class="inputfild">
			<?php foreach($countries as $cou)
			{?>
			<option value="{{$cou->id}}">{{$cou->name}}</option>
			<?php 	} ?>							
			</select>
		</div>

		<div class="col-md-6 form-group {{ $errors->has('state') ? ' has-error' : '' }}">
			<label>State</label>
			<?php $state=$res->state;  ?>
			<select name="state" id="state"  class="inputfild">
			<option value="">Please Select</option>
			<?php

			if(!empty($states) && count($states) > 0){
				foreach($states as $st){
					$selected = '';
					if($st->id == $state){
						$selected = 'selected';
					}
					?>
					<option value="{{$st->id}}" {{$selected}} >{{$st->name}}</option>
			<?php } } ?>
			</select>			
		</div>

		<div class="col-md-6 form-group {{ $errors->has('city') ? ' has-error' : '' }}">
			<label>City</label>
			<select id="city" name="city" class="inputfild">
			<option value="">Please Select</option>
			<?php if(!empty($cities))
			{
			foreach($cities as $c)
			{ ?>
			<option <?php if($res->city==$c->id)  { echo 'selected';   } ?> value="{{$c->id}}">{{$c->name}}</option>
			<?php  } } ?>
			</select>
		</div>

		
		 
			<div class="col-md-12 form-group">
			<input class="btn" type="submit" value="Save">
			</div>
		 
		</div>	
		</form>
			

		</div> 
	</div>
</section>

@include('common.footer')

<script type="text/javascript">

$('#state').change(function(){

var token= '{{ csrf_token() }}';
var state=$('#state').val();
   $.ajax({
          url: "{{url('ajaxhit/getCityByStateDropdown')}}", 
          type: 'post',
          cache: false,
          dataType: 'html',
          //data: $('#'+form_id).serialize(),
          data: {_token:token,state:state },
          crossDomain: true,
          beforeSend: function()
          {
            
          },
          success: function(response)
          {
              $('#city').html(response);
              
              
          },
    }); 

   }); 

</script>


 
</body>
</html>