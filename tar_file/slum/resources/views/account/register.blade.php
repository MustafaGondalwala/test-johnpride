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

<?php
$referer = (request()->has('referer'))?request()->referer:'';
?>

<section class="logsec">
  <div class="container">
    <div class="logbox">
	  <h1>Login to SlumberJill</h1>
		<div class="logdiv">
		<a class="signfacebook" href="#"><i class="facebooklogin"></i> Sign up with Facebook</a>
		<a class="signgoogle" href="#"><i class="googlelogin"></i> Sign up with Google</a>
		</div>
		<div class="or">OR</div>
		<div class="loginform">

			<!-- @include('snippets.errors')
            @include('snippets.flash') -->

            @include('snippets.front.flash')            


			<form name="registerForm" method="POST">
				{{csrf_field()}}

				<input type="email" name="email" value="{{old('email')}}" placeholder="Your Email ID" />
				@include('snippets.front.errors_first', ['param' => 'email'])

				<input type="password" name="password" placeholder="Choose Password" />
				@include('snippets.front.errors_first', ['param' => 'password'])

				<input type="text" name="phone" value="{{old('phone')}}" placeholder="Mobile Number" />
				@include('snippets.front.errors_first', ['param' => 'mobile'])

				<?php
				$oldGender = old('gender');
				?>

				<span>
					<label><input type="radio" name="gender" value="M" <?php echo ($oldGender == 'M')?'checked':'';?> /> Male</label>
					<label><input type="radio" name="gender" value="F" <?php echo ($oldGender == 'F')?'checked':'';?> /> Female</label>
					@include('snippets.front.errors_first', ['param' => 'gender'])
				</span>

				<input class="submitbtn" type="submit" value="Register" />
			</form>

		</div>
		<div class="formbot">
			 <p>Already have an account? <a href="{{url('account/login?referer='.$referer)}}">Login!</a> </p>
		</div>
	  </div>
  </div>
</section>

@include('common.footer')

</body>
</html>