<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo (isset($meta_title))?$meta_title:'Johnpride'?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/>

	@include('common.head')
	  <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


</head>

<body>

	@include('common.header')

	<?php
	$name = $user->name;
	$dob = $user->dob;
	$wedding_anniversary = ($user->wedding_anniversary && $user->wedding_anniversary !='0000-00-00')?$user->wedding_anniversary:'';
	$company_name = $user->company_name;
	$phone = $user->phone;
	$email = $user->email;
	$address = $user->address;
	$locality = $user->locality;

	$country = $user->country;
	$state = $user->state;
	$city = $user->city;
	$pincode = $user->pincode;

	$dob = CustomHelper::DateFormat($dob, $toFormat='d/m/Y', $fromFormat='Y-m-d');
	$wedding_anniversary = ($wedding_anniversary!='')?CustomHelper::DateFormat($wedding_anniversary, $toFormat='d/m/Y', $fromFormat='Y-m-d'):'';
	?>

	<section class="fullwidth innerpage">
		<div class="container">
			@include('users.nav')

			<div class="rightcontent">
				 <div class="main_inner_box">
				<div class="heading2">Update Profile</div>
				<div class="c-heading formbox">

					@include('snippets.front.flash')

					<form name="updateForm" method="POST">
						{{csrf_field()}}

						<ul>

							<li class="fullwidth">
								<span>Name<cite>*</cite></span>
								<span>
									<input type="text" name="name" value="{{old('name', $name)}}" class="inputfild" >
									@include('snippets.front.errors_first', ['param' => 'name'])
								</span>
							</li>

							<li>
								<span>Date of Birth</span>
								<span>
									<input type="text" name="dob" value="{{old('dob', $dob)}}" class="inputfild inputDate" readonly >
									@include('snippets.front.errors_first', ['param' => 'dob'])
								</span>
							</li>

							<li>
								<span>Date of Marriage Anniversary</span>
								<span>
									<input type="text" name="wedding_anniversary" value="{{old('wedding_anniversary', $wedding_anniversary)}}" class="inputfild inputDate" readonly >
									@include('snippets.front.errors_first', ['param' => 'wedding_anniversary'])
								</span>
							</li>

							<li>
								<span>Email<cite>*</cite></span>
								<span>
									<input type="text" name="email" value="{{old('email', $email)}}" class="inputfild" >
									@include('snippets.front.errors_first', ['param' => 'email'])
								</span>
							</li>

							<li>
								<span>Mobile<cite>*</cite></span>
								<span>
									<input type="text" name="phone" value="{{old('phone', $phone)}}" style="background: #cacbcf;" class="inputfild" readonly>
									@include('snippets.front.errors_first', ['param' => 'phone'])
								</span>
							</li>
							
							<li>
								<span>Pincode<cite>*</cite></span>
								<span>
									<input type="text" name="pincode" value="{{old('pincode', $pincode)}}" class="inputfild" />
									@include('snippets.front.errors_first', ['param' => 'pincode'])
								</span>
							</li>

							<li class="fullwidth fulladd">
								<span>Address (House No, Building, Street, Area) <cite>*</cite></span>
								<span>
									<textarea name="address" class="inputfild">{{old('address', $address)}}</textarea>
									@include('snippets.front.errors_first', ['param' => 'address'])
								</span>
							</li>
							
							<li class="threesec">
								<span>Landmark</span>
								<span>
									<input type="text" name="locality" value="{{old('locality', $locality)}}" class="inputfild" />
									 @include('snippets.front.errors_first', ['param' => 'locality'])
								</span>
							</li>

							

							
							<li class="threesec">
								<span>State<cite>*</cite></span>
								<span>
									<select name="state" class="inputfild">
										<option value="">--Select--</option>

										<?php

										if(!empty($states) && count($states) > 0){
											foreach($states as $st){
												$selected = '';
												if($st->id == $state){
													$selected = 'selected';
												}
												?>
												<option value="{{$st->id}}" {{$selected}} >{{$st->name}}</option>
												<?php
											}
										}
										?>
									</select>

									@include('snippets.front.errors_first', ['param' => 'state'])
								</span>
							</li>
							
							<li class="threesec">
								<span>City / District<cite>*</cite></span>
								<span>
									<select name="city" class="inputfild">
										<option value="">--Select--</option>
									</select>

									@include('snippets.front.errors_first', ['param' => 'city'])
								</span>
							</li>


							
							

							<?php /*?><li>
								<span>Country<cite>*</cite></span>
								<span>
									<select name="country" class="inputfild" >
										<option value="99">India</option>
									</select>

									@include('snippets.front.errors_first', ['param' => 'country'])
								</span>
							</li><?php */?>

							<input type="hidden" name="country" value="99">

							<li><button class="savebtn">Save</button></li>

						</ul>

					</form>

				</div>
				</div>
			</div>
		</div>
	</section>

@include('common.footer')

@include('common._address_form_js')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
	
	$( function() {
        $( ".inputDate" ).datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:+0"
        });
    });

</script>

</body>
</html>