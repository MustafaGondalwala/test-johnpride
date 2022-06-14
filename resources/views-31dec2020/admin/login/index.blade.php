<!DOCTYPE html>
<html>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet"> 


<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/css/bootstrap.min.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<style>
*{box-sizing: border-box; margin: 0; padding: 0;}
.bodybg{ height: 100vh; font-family: 'Open Sans', sans-serif; color:#444550; background:#f1f1f1;}
.boxbg{background:#fff; width: 100%; float: left; border: solid 1px #ddd;}
.loglogo{position: fixed; margin: 20px 35px; top: 0; left: 0; float: left;}
.loginmain{position: absolute; box-sizing: border-box; left: 50%; top:50%; width:700px; margin-left:-350px; margin-top: -190px; font-size: 14px;}
.logbg{width:50%; float: left; background:#2c2928; border-radius: 3px; color:#fff;}
.logtitle{background:#2c2928; margin:0; padding:15px 40px; font-weight:600; font-size:24px; color:#fff; border-radius: 3px 3px 0 0; }
.fullwidth{width: 100%; float: left;}
.formbox{padding:15px; /*border: solid 1px #ddd;*/}
.formbox form, .formbox div{width: 100%; float: left;}
.formbox  label{display: block;}
.formbox input{margin-bottom: 10px; width: 100%; float: left; border: 1px solid #dedfe4; padding:10px; font-size: 16px; height:40px; border-radius: 3px; }
.loginbtn{padding:10px; border: none!important; width:100%; cursor: pointer; text-align: center; font-size: 16px; height:46px; color: #fff; font-weight:600; border-radius: 3px;  border: none; background:#a77736;}	
.box2{ float: left; width: 50%; background: #fff; padding:15px; position: relative; height: 320px;} 
.logo1{ position: absolute; left: 50%; top: 50%; transform:translate(-50%, -50%); width: 90px;}
</style>

<body class="bodybg">
<div class="loginmain">
	<div class="boxbg">
		<div class="logbg">
		<h1 class="logtitle">Admin Login</h1>
		<div class="fullwidth formbox"> 
			<?php
			if(session()->has('err_msg')){
				echo session('err_msg');
			}
			// /echo "asdfadf"; die;
			?>

		<form class="" role="form" method="POST" action="{{ url('admin/login') }}">
			{!! csrf_field() !!}
			<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
				<label class="col-md-3 control-label">Username</label>
				<div class="col-md-6">
					<input type="email" class="form-control" name="username" value="{{ old('username') }}">
					@if ($errors->has('username'))
						<span class="help-block">
							<strong>{{ $errors->first('username') }}</strong>
						</span>
					@endif
				</div>
			</div>
			<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
				<label class="col-md-3 control-label">Password</label>
				<div class="col-md-6">
					<input type="password" class="form-control" name="password">
					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<button type="submit" class="loginbtn">Login</button>
				</div>
			</div>
		</form>

		</div>
		</div>
		<div class="box2">
			
			<img class="logo1" src="{{url('/')}}/images/logo.png" alt="Johnpride" />
			
			<!-- <img class="logo1" src="" alt="Johnpride" /> -->
			
		</div>
	</div>
</div>	
</body>
</html>