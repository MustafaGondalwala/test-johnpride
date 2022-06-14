<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$title}}</title>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/> @include('common.head')

</head>

<body>
	@include('common.user_header')
	<section>
		<div class="contentArea">

			@include('common.left_menu')

			<div class="rightBar">
				<div class="tableArea container-custom">
					<div class="panel panel-default">
						<div class="topHeading panel-heading"> <span></span><span></span><span></span>Billing Address </div>


						<div class="welcomeContent panel-body noPaddings cartpage">



							<?php 
							if(count($cartItems) > 0){
								?>

								<div class="btnOrder">
									<?php
/*
<button class="btn removeBtn "> Remove</button>
<button class="btn btn-default"> Review and Order</button>
*/
?>


<form method="POST" id="billing_address_form" action="{{url('cart/shippingaddress')}}" onsubmit="return validate_billing_address()">
	{{ csrf_field() }}

	<?php

	$gst = ( !empty( $default_address->gst ) ) ? $default_address->gst : '';
	$aadhar_number = ( !empty( $default_address->aadhar_number ) ) ? $default_address->aadhar_number : '';
	$company_name = ( !empty( $default_address->company_name ) ) ? $default_address->company_name : '';


	$name = ( !empty( $default_address->name ) ) ? $default_address->name : '';
	$phone = ( !empty( $default_address->phone ) ) ? $default_address->phone : '';
	$address_1 = ( !empty( $default_address->address_1 ) ) ? $default_address->address_1 : '';
	$address_2 = ( !empty( $default_address->address_2 ) ) ? $default_address->address_2 : '';

	$d_city = ( !empty( $default_address->city ) ) ? $default_address->city : '';
	$d_state = ( !empty( $default_address->state ) ) ? $default_address->state : '';
	$zipcode = ( !empty( $default_address->zipcode ) ) ? $default_address->zipcode : '';

	$is_gst_aadhar = (isset($default_address->is_gst_aadhar ))?$default_address->is_gst_aadhar :'';

      $gst_aadhar_num = (!empty($default_address->{$is_gst_aadhar}))?$default_address->{$is_gst_aadhar}:'';

	/*$gst_selected = '';
	$aadhaar_selected = '';

	$billing_type_num = '';

	if ( !empty( $gst ) ) {
		$gst_selected = 'selected';
		$billing_type_num = $gst;
	} elseif ( !empty( $aadhar_number ) ) {
		$aadhaar_selected = 'selected';
		$billing_type_num = $aadhar_number;
	}*/

	$gst_aadhar_type = $is_gst_aadhar;

	$gst_aadhar_num = (!empty($is_gst_aadhar))?$default_address->{$is_gst_aadhar}:'';

	$gst_aadhar_type = old('gst_aadhar_type', $gst_aadhar_type);

	//prd($default_address);

	?>



	<div class="checkout-box">
		<div class="row">
			<div class="form-group col-md-6">
				<label for="gst_aadhar_type" class="control-label">GST / Aadhaar:</label>

				<?php
				/*
				<select name="billing_type" class="form-control">
					<option value="">--Select--</option>
					<option value="billing_gst" {{ $gst_selected }}>GST</option>
					<option value="billing_aadhar_number" {{ $aadhaar_selected }}>Aadhaar</option>
				</select>
				*/
				?>

				<select name="gst_aadhar_type" class="form-control">
					<option value="">--Select--</option>
					<option value="gst" <?php echo ($gst_aadhar_type == 'gst')?'selected':'';?> >GST</option>
					<option value="aadhar_number" <?php echo ($gst_aadhar_type == 'aadhar_number')?'selected':'';?> >Aadhaar</option>
				</select>

				<?php
	/*
	<input id="billing_gst" class="form-control" name="billing_gst" value="<?php echo $gst;  ?>" maxlength="255" type="text"> */ ?>
</div>
<div class="form-group col-md-6">
	<label for="gst_aadhar_num" class="control-label">GST / Aadhaar No. :</label>

	<input id="gst_aadhar_num" class="form-control" name="gst_aadhar_num" value="<?php echo $gst_aadhar_num;  ?>" maxlength="255" type="text">

	<?php
	/*
	<input id="billing_aadhar_number" class="form-control" name="billing_aadhar_number" value="<?php echo $aadhar_number;  ?>" maxlength="255" type="text"> */ ?>


</div>
</div>	

<div class="row">
	<div class="form-group col-md-6">
		<label for="billing_company_name" class="control-label">Billing Name:</label>

		<input id="billing_company_name" class="form-control" name="billing_company_name" value="<?php echo $company_name;  ?>" maxlength="255" type="text">
	</div>
	<div class="form-group col-md-6">
		<label for="billing-name" class="control-label">Contact Name:</label>

		<input id="billing-name" class="form-control" name="billing_name" value="<?php echo $name;  ?>" maxlength="255" type="text">

	</div>


</div>

<div class="form-group">
	<label for="billing-address-1" class="control-label">Address Line 1:</label>

	<input id="billing-address-1" class="form-control" name="billing_address_1" value="<?php echo $address_1; ?>" maxlength="255" type="text">

</div>	

<div class="form-group">
	<label for="billing-address-2" class="control-label">Address Line 2:</label>

	<input id="billing-address-2" class="form-control" name="billing_address_2" value="<?php echo $address_2; ?>" maxlength="255" type="text">

	<?php
	/*
	<p class="help-block">Apt. / Ste.</p>
	*/
	?>

</div>

<div class="row">
	<div class="form-group col-md-6">
		<label for="billing-state" class="control-label">State:</label>

		<select id="billing-state" class="state form-control billing_state" name="billing_state" data-type="billing" required="">
			<option value="">Select State</option>
			<?php
			if ( count( $states ) > 0 ) {
				foreach ( $states as $state ) {
					$state_sel = '';

					?>
					<option <?php if($d_state==$state->id) { echo 'selected'; } ?> value="{{$state->id}}" {{$state_sel}}>{{$state->name}}</option>
					<?php
				}
			}
			?>



		</select>
	</div>

	<div class="form-group col-md-6">
		<label for="billing_city" class="control-label ">City:</label>
		<select name="billing_city" class="billing_city form-control">
			<option value="">--Select--</option>
			<?php
			if ( count( $cities ) > 0 ) {
				foreach ( $cities as $city ) {
					$city_sel = '';

					?>
					<option <?php if($d_city==$city->id) { echo 'selected'; } ?> value="{{$city->id}}" {{$city_sel}}>{{$city->name}}</option>
					<?php
				}
			}
			?>
		</select>

		<span class="err_msg"></span>


	</div>
</div>

<div class="row">
	<div class="form-group col-md-6">
		<label for="billing-zipcode" class="control-label">Postal code:</label>

		<input id="billing-zipcode" class="form-control" name="billing_zipcode" value="<?php echo $zipcode;  ?>" maxlength="20" type="text">

	</div>

	<div class="form-group col-md-6">
		<label for="billing-phone" class="control-label">Phone Number:</label>

		<input id="billing-phone" class="form-control" name="billing_phone" value="<?php echo $phone;  ?>" maxlength="20" type="text">

	</div>
</div>

<div class="form-group">
	<label for="billing-phone" class="control-label">Shipping Address:</label>
	<br/>
	<input id="is_diffrent_shipping" type="checkbox" name="is_diffrent_shipping" value="1"> My Shipping Address is the same as my Billing Address




</div>

</div>



<div class="btndiv"><button id="billing_address_form_sbmbtn" type="submit" class="btn btn-default">Next</button></div>


</form>


</div>
<?php
} else {
	?>
	<div class="btnOrder">
		<p>Your cart is empty.</p>
		<p><a href="{{url('products/feeds')}}">Click here to shop.</a></p>
	</div>
	<?php
}
?>



</div>
<!-- <a href="index.php" class="btnNext btn btn-default" ><i class="whites fa fa-angle-right" aria-hidden="true"></i> </a> </div> -->
</div>
</div>
</div>
</section>

@include('common.footer')

<script src="{{url('public/assets')}}/js/function.js"></script>

<script type="text/javascript">
	function validate_place_order() {
		conf = confirm( 'Are you sure to place this Order?' );
		if ( conf ) {

		}
	}

	$( '#is_diffrent_shipping' ).click( function () {
		var form_url = "{{url('cart/shippingaddress')}}";
		if ( $( '#is_diffrent_shipping' ).prop( 'checked' ) == true ) {

			form_url = "{{url('cart/checkout')}}";
			$( '#billing_address_form_sbmbtn' ).html( 'Checkout' );

			$( ".box_payment_method" ).show();
		} else {
			$( '#billing_address_form_sbmbtn' ).html( 'Next' );
			$( ".box_payment_method" ).hide();
		}
		$( '#billing_address_form' ).attr( 'action', form_url );


	} );
</script>

<script type="text/javascript">
	state_id = '{{$d_state}}';
	city_id = '{{$d_city}}';

	$( ".billing_state" ).on( "change", function () {
		state_id = $( this ).val();
		load_cities( state_id, city_id );
	} );

	function load_cities( state_id, city_id ) {
		_token = '{{csrf_token()}}';
		$.ajax( {
			url: "{{url('common/ajax_load_cities')}}",
			type: "POST",
			data: {
				state_id,
				city_id
			},
			dataType: "JSON",
			headers: {
				'X-CSRF-TOKEN': _token
			},
			cache: false,
			beforeSend: function () {},
			success: function ( resp ) {
				if ( resp.success ) {
					$( ".billing_city" ).html( resp.options );
				}
			}
		} );
	}

	function validate_billing_address() {

		is_submit = false;

		_token = '{{csrf_token()}}';
		$.ajax( {
			url: "{{ url('cart/shippingaddress') }}",
			type: "POST",
			data: $( "#billing_address_form" ).serialize(),
			dataType: "JSON",
			headers: {
				'X-CSRF-TOKEN': _token
			},
			cache: false,
			async: false,
			beforeSend: function () {
				$( "#billing_address_form" ).find( ".help-block" ).remove();
				$( ".form-group" ).removeClass( "has-error" );
			},
			success: function ( resp ) {
				if ( resp.success ) {
					is_submit = true;
				} else if ( resp.errors ) {
					$.each( resp.errors, function ( i, val ) {
//console.log(i);
$( "#billing_address_form" ).find( "[name='" + i + "']" ).parent( ".form-group" ).addClass( "has-error" );
$( "#billing_address_form" ).find( "[name='" + i + "']" ).parent( ".form-group" ).append( '<p class="help-block">' + val + '</p>' );
} );
				}
			}
		} );

		return is_submit;

	}
</script>
</body>

</html>