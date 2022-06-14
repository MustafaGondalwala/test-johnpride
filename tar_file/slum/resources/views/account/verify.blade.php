<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verify::SlumberJill</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')

<?php
$referer = (isset($referer))?$referer:'';
?>

<section class="logsec">
  <div class="container">
    <div class="logbox">
	  

	  <?php
	  if($isVerified){
	  	?>
	  	<h1>Account Verification</h1>
	  	<p>You have succesfully verified your account, you can <a href="{{url('accout/login')}}">login</a> now</p>
	  	<div class="formbot">
	  		<a href="{{url('account/login?referer='.$referer)}}">Login here!</a>
	  	</div>
	  	<?php
	  }
	  else{
	  	?>
	  	<h1>Invalid request</h1>

	  	<div class="formbot">
	  		<a href="{{url('account/register?referer='.$referer)}}">Create new account</a> | <a href="{{url('account/login')}}">Login here!</a>
	  	</div>

	  	<?php
	  }
	  ?>
  </div>
</section>

@include('common.footer')

</body>
</html>