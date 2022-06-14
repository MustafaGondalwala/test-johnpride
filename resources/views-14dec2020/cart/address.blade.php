<!DOCTYPE html>
<html>
<head>

	@include('common.head')

</head>
<body>

	@include('common.header')

	<?php

	$FREE_DELIVERY_AMOUNT = CustomHelper::WebsiteSettings('FREE_DELIVERY_AMOUNT');
	$SHIPPING_CHARGE = CustomHelper::WebsiteSettings('SHIPPING_CHARGE');
	$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');

	$user = auth()->user();

	$userAddresses = $user->userAddresses;

	$userAddressesCount = 0;

	if(!empty($userAddresses) && count($userAddresses) > 0){
		$userAddressesCount = count($userAddresses);
	}

	$userAddr = '';

	$addrId = (request('id'))?request()->id:0;

	if(is_numeric($addrId) && $addrId > 0){
		$userAddr = $userAddresses->where('id', $addrId)->first();
	}

	if(!empty($userAddr) && count($userAddr) > 0){
		//pr($userAddr->toArray());
	}


	$type = (isset($userAddr->type))?$userAddr->type:'';
	$name = (isset($userAddr->name))?$userAddr->name:'';
	$company_name = (isset($userAddr->company_name))?$userAddr->company_name:'';
	$phone = (isset($userAddr->phone))?$userAddr->phone:'';
	$address = (isset($userAddr->address))?$userAddr->address:'';

	$country = (isset($userAddr->country))?$userAddr->country:'';
	$state = (isset($userAddr->state))?$userAddr->state:'';
	$city = (isset($userAddr->city))?$userAddr->city:'';
	$pincode = (isset($userAddr->pincode))?$userAddr->pincode:'';


	?>

	<section class="fullwidth tabcart">
		<div class="container">
			<ul>
				<li><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
				<li class="active"><span><i class="addressicon"></i></span><strong>Address</strong></li>
				<li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
				<li><span><i class="payment_n_icon"></i></span><strong>Payment</strong></li>
			</ul>
		</div>
	</section>  

	<section class="fullwidth innerpage">
		<div class="container">
			<div class="sectionleft">
				<form name="placeOrderForm" method="POST" action="{{url('cart/checkout')}}">
					{{csrf_field()}}

					@include('snippets.front.flash')

					<?php
					if(!empty($userAddresses) && count($userAddresses) > 0 && (empty($userAddr) || count($userAddr) == 0) ){

						?>
						<div class="selectadd">
							<ul>

								<?php
								foreach($userAddresses as $ua){

								//prd($ua->toArray());

									$addressArr = CustomHelper::formatUserAddress($ua);

									$name = $ua->name;

									$isDefault = ($ua->is_default == 1)?'(Default)':'';

									$checked = '';

									if($ua->is_default == '1'){
										$checked = 'checked';
									}
									?>
									<li>
										<div class="addselectbox">

											<div class="addlist">
												
												<a href="{{url('cart/address/'.$ua->id)}}" data-id="{{$ua->id}}" class="edit-link addrBtn"><i class="editicon"></i></a>
												<label class="selectaddres"><input type="radio" name="shppingAddr" value="{{$ua->id}}" {{$checked}}>
													<span></span>
													<input type="hidden" name="pincode" value="{{$ua->pincode}}"> </label>
												<h3 class="title3">Shipping Detail</h3>
												
													<p><strong>{{$name}} {{$isDefault}}</strong></p>
													
												<p>
													<span><?php
													if(!empty($addressArr) && count($addressArr) > 0){
														echo implode(', ', $addressArr); 
													}
													?></span>
												</p>

												<p>Landmark. {{$ua->locality}}</p>
												<p>Mobile. {{$ua->phone}}</p>
											</div>

											<!--<div class="cashondelivery">Cash on delivery available Return pick up available</div>-->
									</div>
								</li>
								<?php
							}
							?>

							<li>
								<a class="addaddresslink addrBtn" href="JavaScript:Void(0)"><i class="addaddressicon "></i> <span>Add new address</span></a>
							</li>
						</ul>
					</div>
					<?php

				}
				?>

			</form>
			

			<?php
			if(empty($userAddressesCount) || $userAddressesCount == 0 ){
				?>
				<div class="formBox">
					@include('common._address_form')
				</div>
				<?php
			}
			?>
				
			<div class="backtobag"><a href="{{url('cart')}}">Back to Bag</a></div>


		</div>

		<div class="sectionright">

			<?php
			$cartProcessButton = '<a href="javascript:void(0)" class="placeOrderBtn">Place Order</a>';

			$couponRemovable = false;
			$showCoupon = false;

			$couponData = session('couponData');

			if(!empty($couponData) && count($couponData) > 0){
				$showCoupon = true;
			}
			?>

			@include('cart._price_details')
		</div>


		<?php
		/*
		<div class="sectionright">
			<div class="pricedetail">
				<h3 class="title3">Price Detail</h3>
				<ul>
					<li><span>Total MRP</span> <strong>₹{{$cartTotal}}</strong></li>
					<li><span>Bag Discount</span> <strong>-₹{{number_format($totalDiscount, 2)}}</strong></li>
					<li><span>Delivery Charges</span> <strong>₹{{$totalShipping}}</strong></li>  
					<li class="totals"><span>Order Total</span> <strong>₹{{number_format($total, 2)}}</strong></li>
				</ul>

				<div class="placebtn"><a href="javascript:void(0)" class="placeOrderBtn">Place Order</a></div>
			</div>
		</div>
		*/
		?>

		</div>
	</section>

	@include('common.footer')



	<!-- Address Modal -->
	<div id="addressModal" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				</div>
				<div class="modal-body">
					<p>Some text in the modal.</p>
				</div>
				 
			</div>

		</div>
	</div>

	<!-- End - Address Modal -->

	<!-- Alert Modal -->
	<div id="alertModal" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Error!</h4>
				</div>
				<div class="modal-body">
					
				</div>
			</div>

		</div>
	</div>

	<!-- End - Alert Modal -->


	<script type="text/javascript" src="{{url('/')}}/js/bootstrap.min.js"></script> 

@include('common._address_form_js')

	<script type="text/javascript">		

		$(".placeOrderBtn").click(function(){

			var selectedShppingAddr = $("input[name=shppingAddr]:checked");

			var shppingAddr = selectedShppingAddr.val();

			shppingAddr = parseInt(shppingAddr);
			//alert(isDefault);

			if(!isNaN(shppingAddr) && shppingAddr > 0){
				//return true;
			}
			else{
				//alert("please add or select address for delivery.");
				showAlert("please add or select address for delivery.", 'danger');
				return false;
			}

			var pincode = selectedShppingAddr.siblings("input[name=pincode]").val();

			if(pincode && pincode != ""){
				var _token = '{{ csrf_token() }}';
				$.ajax({
					url: "{{ url('common/ajax_check_pincode') }}",
					type: "POST",
					data: {pincode:pincode},
					dataType:"JSON",
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					async: false,
					beforeSend:function(){
					},
					success: function(resp){
						if(resp.success){
							$("form[name=placeOrderForm]").submit();
						}
						else{
							showAlert("Sorry, pincode of selected address is not available for delivery", 'danger')
						}

					}

				});
			}
		});

		function showAlert(msg, type){
			if(msg && msg != ""){

				var alertClass = 'alert';

				if(type && type!= ""){
					alertClass += ' alert-'+type;
				}

				var message = '<div class="'+alertClass+'"> '+msg+' </div>';

				$("#alertModal").find(".modal-body").html(message);
				$("#alertModal").modal("show");
			}
		}
	</script>

</body>
</html>