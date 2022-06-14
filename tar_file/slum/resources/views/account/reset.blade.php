<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Signup::SlumberJill</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')

<section class="logsec">
  <div class="container">
    <div class="logbox">


    	<?php
	  if($isValidToken){
	  	?>
	  	<h1>Reset password</h1>

		<div class="loginform">

			<!-- @include('snippets.errors')
            @include('snippets.flash') -->

            @include('snippets.front.flash')            


			<form name="registerForm" method="POST">
				{{csrf_field()}}

				<input type="email" name="email" value="{{old('email')}}" placeholder="Your Email ID" />
				@include('snippets.front.errors_first', ['param' => 'email'])

				<input type="password" name="password" placeholder="Enter Password" />
				@include('snippets.front.errors_first', ['param' => 'password'])

				<input type="password" name="confirm_password" placeholder="Confirm Password" />
				@include('snippets.front.errors_first', ['param' => 'confirm_password'])

				<input class="submitbtn" type="submit" value="Register" />
			</form>

		</div>
	  	<?php
	  }
	  else{
	  	?>
	  	<h1>Invalid request</h1>

	  	<div class="formbot">
	  		<a href="{{url('account/register')}}">Create new account</a> | <a href="{{url('account/login')}}">Login here!</a>
	  	</div>

	  	<?php
	  }
	  ?>
	  
		<div class="formbot">
			 <p>Already have an account? <a href="{{url('account/login')}}">Login!</a> </p>
		</div>
	  </div>
  </div>
</section>

@include('common.footer')

</body>
</html>