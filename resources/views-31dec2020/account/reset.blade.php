<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Signup::Johnpride</title>
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

				if(session()->has('alert-success') || session()->has('alert-danger')){
					?>
					@include('snippets.front.flash')
					<?php
				}
				elseif($isValidToken){
					?>
					<h1>Reset password</h1>

					<div class="loginform">

			<!-- @include('snippets.errors')
				@include('snippets.flash') -->

				@include('snippets.front.flash')            


				<form name="registerForm" method="POST">
					{{csrf_field()}}

					<?php
					/*
					<input type="email" name="email" value="{{old('email')}}" placeholder="Your Email ID" />
					@include('snippets.front.errors_first', ['param' => 'email'])
					*/
					?>

					<div class="form-group">
						<div class="input_warap">
							<input type="password" name="password" placeholder="Enter Password" class="inpPass" />
							<a href="javascript:void(0)" class="passEye"></a>
						</div>
						@include('snippets.front.errors_first', ['param' => 'password'])
					</div>

					<div class="form-group">
						<div class="input_warap">
							<input type="password" name="confirm_password" placeholder="Confirm Password" class="inpPass" />
							<a href="javascript:void(0)" class="passEye"></a>
						</div>
						@include('snippets.front.errors_first', ['param' => 'confirm_password'])

					</div>

					<input class="submitbtn" type="submit" value="Save" />
			</form>

		</div>
		<?php
	}
	else{
		?>
		<h1>Invalid request</h1>

		<div class="formbot">
			<a href="javascript:void(0)" class="open_slide" >Create new account</a> | <a href="javascript:void(0)" class="open_slide">Login here!</a>
		</div>

		<?php
	}
	?>

	<div class="formbot">
		<?php
			 /*
			 <p>Already have an account? <a href="{{url('account/login')}}">Login!</a> </p>
			 */
			 ?>
			 <p>Already have an account? <a href="javascript:void(0)" class="open_slide">Login!</a> </p>
			</div>
		</div>
	</div>
</section>

@include('common.footer')

</body>
</html>