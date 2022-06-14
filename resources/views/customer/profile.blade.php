<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">My Profile</h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container"> 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')

			
		<div class="sccountsec">
		<div class="row">
		<div class="col-md-12">
		<span class="pull-right"><a href="{{url('user/editprofile')}}"><i class="editicon"></i> Edit</a> </span>
		</div>
		</div> 

        <?php /*?><?php if(auth()->user()->type=='designer'){  ?>
		<div class="col-md-12">
			<label>Profile Pic : </label>
			<?php if(!empty($res->profile_pic)) 
			{
			?>
            <img width="150px" src="{{url('public/storage/users/thumb/'.$res->profile_pic)}}">

            <?php } ?>
			
		</div>
		<?php } ?><?php */?>
		<div class="row">
		<div class="col-md-6 form-group">
			<label>First Name : </label>
			{{$res->first_name}}
		</div>

		<div class="col-md-6 form-group">
			<label>Last Name : </label>
			{{$res->last_name}}
		</div>
		</div>
			
		<div class="row">
		<div class="col-md-6 form-group">
			<label>Email : </label>
			{{$res->email}}
		</div>

		<div class="col-md-6 form-group">
			<label>Phone : </label>
			{{$res->phone}}
		</div>
		</div>			

		<div class="row">
		<div class="col-md-6 form-group">
			<label>Country : </label>

			<?php if($country->count())
			{
				echo $country->name; 

			}
		    ?>
			
		</div>

		<div class="col-md-6 form-group">
		<label>State : </label>

		<?php if($state->count())
			{
				echo $state->name; 

			}
		    ?>
			
			
		</div>
		</div>
			
		<div class="row">
		<div class="col-md-6 form-group">
			<label>City : </label>

			<?php if($city->count())
			{
				echo $city->name; 

			}
		    ?>
			
		</div>

		<div class="col-md-6 form-group">
			<label>Pincode : </label>
			{{$res->pincode}}
		</div>
		</div>
			
		<div class="row">
		<div class="col-md-6 form-group">
			<label>Address1 : </label>
			{{$res->address1}}
		</div>


		<div class="col-md-6 form-group">
			<label>Address2 : </label>
			{{$res->address2}}
		</div>
			
		<?php if(auth()->user()->type=='designer'){  ?>
		<div class="col-md-12 form-group">
         <label>About me : </label>  
           <div><?php echo $res->about_me; ?></div>
		</div>
		<?php } ?>

		</div>
			

		
	</div>
</section>

@include('common.footer')


 
</body>
</html>