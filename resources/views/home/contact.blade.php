<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Contact us::Johnpride</title>
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
		$name = $user->name;
		$email = $user->email;
		$phone = $user->phone;
	}
	?>

	<section class="fullwidth innerpage">
		<div class="container">
			<h1 class="heading">Contact us</h1>
			<div class="mapsec">
			<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3499.7281432006025!2d77.1378471150841!3d28.69777778239208!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d03d1c72064db%3A0x6f0f0f95afe1920f!2s358%2C%20Kohat%20Enclave%2C%20Pitam%20Pura%2C%20New%20Delhi%2C%20Delhi%20110034!5e0!3m2!1sen!2sin!4v1629725090887!5m2!1sen!2sin" width="600" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

			</div>

			<div class="contactform formbox">

				@include('snippets.front.flash')

				<form name="contactForm" method="POST">
					
					{{csrf_field()}}

					<ul>

						<li>
							<span>Name<cite>*</cite></span>
							<span><input type="text" name="name" value="{{old('name', $name)}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'name'])
						</li>

						<li>
							<span>Email<cite>*</cite></span>
							<span><input type="email" name="email" value="{{old('email', $email)}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'email'])
						</li>

						<li>
							<span>Telephone</span>
							<span><input type="text" name="phone" value="{{old('phone', $phone)}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'phone'])
						</li>

						<li>
							<span>Subject</span>
							<span><input type="text" name="subject" value="{{old('subject')}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'subject'])
						</li>

						<li class="fullwidth">
							<span>Message<cite>*</cite></span>
							<span><textarea name="message" class="inputfild">{{old('message')}}</textarea></span>
							@include('snippets.front.errors_first', ['param' => 'message'])
						</li>

						<li class="fullwidth captchali">
							<span>Security Code<cite>*</cite></span>
							<span class="captcha">
								<span class="captchImgBox"><?php echo $captcha_img; ?></span>
								<small><img src="{{url('')}}/images/refresh.png" class="changeCaptcha" alt="refresh"/></small>
								<input type="text" name="scode" value="{{old('scode')}}" class="inputfild" placeholder="Input Code">
							</span>
							@include('snippets.front.errors_first', ['param' => 'scode'])
						</li>


						<li><button class="savebtn">Submit</button></li>

					</ul>

				</form>
			</div>

			<div class="addressmap">
				<div class="faddress">
					<h4>For any query</h4> 
					<!-- <p><i class="mapicon"></i>G-4, Ground Floor, City Center Mall, <br>Sector 10, Rohini, Delhi- 110085  
						
					</p> -->
					<p><i class="mapicon"></i>358,1st Floor Kohat Enclave,<br/> Opposite Kohat Metro Station, <br>Sector 10, Rohini, Delhi- 110034

  
						
					</p>
					<p><i class="phoneicon"></i> <a href="tel:01147555314"><i class="fa fa-phone"></i> 011-47555314</a></p> 
					<p><i class="mailicon"></i> <a href="mailto:support@johnpride.in"><i class="fa fa-envelope-o"></i> support@johnpride.in</a></p>
					<ul>
						<li><a href="https://www.facebook.com/johnprideclothing/" class="Link Link--primary" target="_blank" rel="noopener" aria-label="Facebook"><i class="facebookiconb"></i></a></li>
				<li><a href="https://www.instagram.com/johnprideclothing/" class="Link Link--primary" target="_blank" rel="noopener" aria-label="Instagram"><i class="instagramiconb"></i></a></li>
					</ul>

				</div>




			</div>
		</div>
	</section>

	@include('common.footer')

	<script type="text/javascript">
		$('.changeCaptcha').on('click', function(e){
			e.preventDefault();
			
			var captcha = $(".captchImgBox").find('img');

			var _token = '{{csrf_token()}}';

			$.ajax( {
				url: "{{url('common/ajax_regenerate_captcha')}}",
				type: "POST",
				data: {},
				dataType: "JSON",
				headers: {
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function () {},
				success: function ( resp ) {
					if ( resp.success ) {
						if(resp.captcha_src){
							captcha.attr('src', resp.captcha_src);
						}
					}
				}
			} );
		});
	</script>

</body>
</html>