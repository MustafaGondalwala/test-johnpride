<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">Change Password </h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container">
 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')

			
		<div class="sccountsec">

		<form method="post">
		 {{ csrf_field() }}
		<div class="row"> 
		<div class="col-md-6 form-group {{ $errors->has('password') ? ' has-error' : '' }}">
			<label>Password * : </label>
			<input type="password" name="password" class="form-control">			
		</div>
		<div class="col-md-6 form-group {{ $errors->has('c_password') ? ' has-error' : '' }}">
			<label>Confirm Password * : </label>
			<input type="password" name="c_password" class="form-control">			
		</div>

		</div>
		<div class="row">
			<div class="col-md-12">
			<input class="btn" type="submit" value="Save">
			</div>
		</div>

		</form>
			

		</div> 
	</div>
</section>

@include('common.footer')


 
</body>
</html>