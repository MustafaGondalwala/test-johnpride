<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login::SlumberJill</title>
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

			<form name="loginForm" method="post">
				{{csrf_field()}}
				
				<input type="email" name="email" value="{{old('email')}}" placeholder="Your Email ID" />
				@include('snippets.front.errors_first', ['param' => 'email'])

				<input type="password" name="password" placeholder="Enter Password" />
				@include('snippets.front.errors_first', ['param' => 'password'])

				<input type="submit" value="Login" class="submitbtn" />
			</form>

		</div>
		<div class="formbot">
			<a href="{{url('account/forgot?referer='.$referer)}}">Recover password</a> <span>New to SlumberJill ? <a href="{{url('account/register?referer='.$referer)}}">Create Account</a></span>
		</div>
	  </div>
  </div>
</section>

@include('common.footer')

</body>
</html>