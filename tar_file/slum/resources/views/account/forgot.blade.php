<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Forgot Password::SlumberJill</title>
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
	  <h1>Forgot password!</h1>
		<div class="loginform">

			@include('snippets.front.flash')

            <form name="forgotForm" method="post">
            	{{csrf_field()}}

            	<input type="email" name="email" placeholder="Your Email ID" />
            	@include('snippets.front.errors_first', ['param' => 'email'])

            	<input class="submitbtn" type="submit" value="Submit" />
            </form>

		</div>

		<div class="formbot">
			 <p><a href="{{url('account/login?referer='.$referer)}}">Login here!</a> </p>
		</div>
	  </div>
  </div>
</section>

@include('common.footer')

</body>
</html>