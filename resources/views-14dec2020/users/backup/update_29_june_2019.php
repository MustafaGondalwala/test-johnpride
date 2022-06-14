<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo (isset($meta_title))?$meta_title:'SlumberJill'?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/>

	@include('common.head')

</head>

<body>

	@include('common.header')

	<?php
	$first_name = $user->first_name;
	$last_name = $user->last_name;
	$company_name = $user->company_name;
	$phone = $user->phone;
	$address = $user->address;

	$country = $user->country;
	$state = $user->state;
	$city = $user->city;
	$pincode = $user->pincode;
	?>

	<section class="fullwidth innerpage">
		<div class="container">
			@include('users.nav')

			<div class="rightcontent">
				<div class="heading2">Update Profile</div>
				<div class="c-heading formbox">

					@include('snippets.front.flash')

					<form name="updateForm" method="POST">
						{{csrf_field()}}

						<ul>

							<li class="fullwidth">
								<span>Name<cite>*</cite></span>
								<span>
									<input type="text" name="first_name" value="{{old('first_name', $first_name)}}" class="inputfild" >
									@include('snippets.front.errors_first', ['param' => 'first_name'])
								</span>
							</li>

							<?php /*?><li>
								<span>Last Name<cite>*</cite></span>
								<span>
									<input type="text" name="last_name" value="{{old('last_name', $last_name)}}" class="inputfild" >
									@include('snippets.front.errors_first', ['param' => 'last_name'])
								</span>
							</li>

							<li>
								<span>Business Name</span>
								<span>
									<input type="text" name="company_name" value="{{old('company_name', $company_name)}}" class="inputfild" >
									@include('snippets.front.errors_first', ['param' => 'company_name'])
								</span>
							</li><?php */?>

							<li>
								<span>Mobile<cite>*</cite></span>
								<span>
									<input type="text" name="phone" value="{{old('phone', $phone)}}" class="inputfild" >
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
								<span>Locality / Town<cite>*</cite></span>
								<span>
									<input type="text" name="town" value=" " class="inputfild" />
									 @include('snippets.front.errors_first', ['param' => 'town'])
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

							<li><button class="savebtn">Save</button></li>

						</ul>

					</form>

				</div>
			</div>
		</div>
	</section>

@include('common.footer')

<script type="text/javascript">
	var state_id = '{{ $state }}';
	var city_id = '{{ $city }}';

	if(state_id && state_id != ""){
		load_cities( state_id, city_id );
	}

	$(document).on("change", "select[name='state']", function () {
		state_id = $( this ).val();
		load_cities( state_id, city_id );
	} );

	function load_cities( state_id, city_id ) {

		_token = '{{csrf_token()}}';

		$.ajax( {
			url: "{{url('common/ajax_load_cities')}}",
			type: "POST",
			data: {state_id: state_id, city_id: city_id},
			dataType: "JSON",
			headers: {
				'X-CSRF-TOKEN': _token
			},
			cache: false,
			beforeSend: function () {},
			success: function ( resp ) {
				if ( resp.success ) {
					$("select[name='city']").html( resp.options );
				}
			}
		} );
	}
</script>

</body>
</html>