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
				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3915.2929066768384!2d77.36636561530327!3d11.091533792108216!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba907e78ae7eb43%3A0x1ef1ec065e636d4!2sFrancis+Wacziarg!5e0!3m2!1sen!2sin!4v1561618089326!5m2!1sen!2sin" width="600" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
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
					<p><i class="mapicon"></i>John Pride <br>India <br>
						Ph No: +91-9599969498
					</p>
					<p><i class="phoneicon"></i> <a href="tel:94437 26891"><i class="fa fa-phone"></i> +91-9599969498</a></p> 
					<p><i class="mailicon"></i> <a href="mailto:support@johnpride.in"><i class="fa fa-envelope-o"></i> support@johnpride.in</a></p>
					<ul>
						<li><a href="https://www.facebook.com/johnpride/" target="_blank"><i class="facebookicon"></i></a></li>
            <li><a href="https://twitter.com/johnpride" target="_blank"><i class="twittericon"></i></a></li>
            <li><a href="#" target="_blank"><i class="linkedinicon"></i></a></li>
            <li><a href="https://www.instagram.com/johnpride/" target="_blank"><i class="instragramicon"></i></a></li>
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