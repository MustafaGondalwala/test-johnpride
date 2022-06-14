<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
			
	
<section class="fullwidth logpage">	
	<div class="container">
		<div class="regform">
			<h2>Verify Account</h2>

			@include('snippets.errors')
			@include('snippets.flash')
			

			<form name="verifyAccountForm" method="POST" >

				{{csrf_field()}}

				<ul>

					<li class="{{ $errors->has('email') ? ' has-error' : '' }}">

						<label>Email*</label>
						<input type="text" name="email" value="{{old('email')}}" class="inputfild"/>
						@include('snippets.errors_first', ['param' => 'email'])

						<br>

						<input class="loginbtn" type="submit" value="Verify" />
					</li>
				</ul>
			 
			</form>

		</div> 
	</div>
</section>

@include('common.footer')
 
</body>
</html>