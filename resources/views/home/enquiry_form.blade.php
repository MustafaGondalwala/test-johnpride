<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Enquiry Form::Johnpride</title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/>

	@include('common.head')

</head>

<body>

	@include('common.header')

	<?php
	$name = '';
	$email = '';
	$phone = '';

	if(auth()->check()){
		$user = auth()->user();
		//pr($user->toArray());
		//$name = $user->name;
		//$email = $user->email;
		//$phone = $user->phone;
	}
	?>

	<section class="fullwidth innerpage enquiry_form_page">
		<div class="container">
			<h1 class="heading">Enquiry-Form</h1>

			<div class="contactform formbox">

				@include('snippets.front.flash')

				<form name="contactForm" method="POST">
					
					{{csrf_field()}}

					<ul>

						<li>
							<span>Name<cite>*</cite></span>
							<span><input type="text" name="name" value="{{old('name')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'name'])
						</li>

						<li>
							<span>Phone Number<cite>*</cite></span>
							<span><input type="text" name="phone" value="{{old('phone')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'phone'])
						</li>

						<li>
							<span>Alternate Number</span>
							<span><input type="text" name="alternate_phone" value="{{old('alternate_phone')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'alternate_phone'])
						</li>

						<li>
							<span>Your email<cite>*</cite></span>
							<span><input type="email" name="email" value="{{old('email')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'email'])
						</li>

						<li>
							<span>Profession</span>
							<span><input type="text" name="profession" value="{{old('profession')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'profession'])
						</li>

						<li>
							<span>Existing Business</span>
							<span><input type="text" name="business" value="{{old('business')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'business'])
						</li>
						<div style="clear: both;"></div>
						<span class="loc_divide">PROSPECTIVE STORE LOCATION</span> <br>

						<li>
							<span>City</span>
							<span><input type="text" name="city" value="{{old('city')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'city'])
						</li>

						<li>
							<span>Location<cite>*</cite></span>
							<span><input type="text" name="location" value="{{old('location')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'location'])
						</li>

						<li class="fullwidth">
							<span>Market</span>
							<span><input type="text" name="market" value="{{old('market')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'market'])
						</li>

						<li class="fullwidth">
							<span>Location Address<cite>*</cite></span>
							<span><textarea name="location_address" class="inputfild">{{old('location_address')}}</textarea></span>
							@include('snippets.front.errors_first', ['param' => 'location_address'])
						</li>

						<li class="fullwidth">
							<span>Size of the Store</span>
							<span><input type="text" name="store_size" value="{{old('store_size')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'store_size'])
						</li>


						<li><button class="savebtn">Submit</button></li>

					</ul>

				</form>
			</div>

		</div>
	</section>

	@include('common.footer')


</body>
</html>