<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login::Johnpride</title>
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

<section class="logsec loginBox">
  <div class="container">
    <div class="logbox">
	  <h1>Login to Johnpride</h1>
		<div class="logdiv">
		<?php
		/*
		*/
		?>
		<a href="{{ route('account.fbLogin') }}" class="signfacebook" ><i class="facebooklogin"></i> Log in with Facebook</a>
		<a href="{{ route('account.gLogin') }}" class="signgoogle" ><i class="googlelogin"></i> Log in with Google</a>
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
			<a href="{{url('account/forgot?referer='.$referer)}}">Recover password</a> <span>New to Johnpride ? <a href="{{url('account/register?referer='.$referer)}}">Register</a></span>
		</div>
	  </div>
  </div>
</section>

@include('common.footer')

<script type="text/javascript" src="{{url('public')}}/js/md5.min.js"></script>

<script type="text/javascript">
	$(".submitbtn").click(function(e){
		e.preventDefault();

		/*var password = $("input[name=password]").val();

		$("input[name=password]").val(md5(password))*/

		$("form[name=loginForm]").submit();

		//console.log(md5(password));
	});
</script>

</body>
</html>