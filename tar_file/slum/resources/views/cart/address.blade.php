<!DOCTYPE html>
<html>
<head>

	@include('common.head')

</head>
<body>

	@include('common.header')

	<?php
	$user = auth()->user();

	$userAddresses = $user->userAddresses;

	if(!empty($userAddresses) && count($userAddresses) > 0){
		//pr($userAddresses->toArray());
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
	$first_name = (isset($userAddr->first_name))?$userAddr->first_name:'';
	$last_name = (isset($userAddr->last_name))?$userAddr->last_name:'';
	$company_name = (isset($userAddr->company_name))?$userAddr->company_name:'';
	$phone = (isset($userAddr->phone))?$userAddr->phone:'';
	$address = (isset($userAddr->address))?$userAddr->address:'';

	$country = (isset($userAddr->country))?$userAddr->country:'';
	$state = (isset($userAddr->state))?$userAddr->state:'';
	$city = (isset($userAddr->city))?$userAddr->city:'';
	$pincode = (isset($userAddr->pincode))?$userAddr->pincode:'';


	$totalDiscount = 0;
	$totalShipping = 0;

	if(!empty($cartContent) && $cartContent->count() > 0){

		foreach($cartContent as $cart){

			$cartId = $cart->id;

			$cartIdArr = explode('_', $cartId);

			$product_id = $cartIdArr[0];

			$product = $productModel->find($product_id);

//prd($product->toArray());

			$price = $product->price;
			$sale_price = $product->sale_price;


			if($sale_price < $price){
				$discount = CustomHelper::calculateProductDiscount($price, $sale_price);

				$totalDiscount = $totalDiscount + $discount;

			}

		}
	}

	$cartTotal = Cart::getTotal();

	$total = $cartTotal - $totalDiscount + $totalShipping;
	?>

	<section class="fullwidth tabcart">
		<div class="container">
			<ul>
				<li><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
				<li class="active"><span><i class="addressicon"></i></span><strong>Address</strong></li>
				<li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
			</ul>
		</div>
	</section>  

	<section class="fullwidth innerpage">
		<div class="container">
			<div class="sectionleft">

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

								$name = trim($ua->first_name.' '.$ua->last_name);

								$isDefault = ($ua->is_default == 1)?'(Default)':'';
								?>
								<li>
									<div class="addselectbox">

										<div class="addlist">
											<a href="{{url('cart/address/'.$ua->id)}}" data-id="{{$ua->id}}" class="edit-link addrBtn"><i class="editicon"></i></a>
											<h3 class="title3">Shipping Detail</h3>
											<p><strong>{{$name}} {{$isDefault}}</strong></p>
											<p>
												<?php
												if(!empty($addressArr) && count($addressArr) > 0){
													echo implode(', ', $addressArr); 
												}
												?>												
											</p>

											<p>Mobile. {{$ua->phone}}</p>
										</div>

										<?php
										/*
										<div class="cashondelivery">
											Cash on delivery available Return pick up available
										</div>
										*/
										?>
									</div>
								</li>
								<?php
							}
							?>

							<li>
								<a class="addaddresslink" href="{{url('cart/address')}}"><i class="addaddressicon addrBtn"></i> <span>Add new address</span></a>
							</li>
						</ul>
					</div>
					<?php

				}
				else{
					?>

					<div class="formBox">
						@include('common._address_form')
					</div>

					<?php
				}
				?>



			</div>

			<div class="sectionright">
				<div class="pricedetail">
					<h3 class="title3">Price Detail</h3>
					<ul>
						<li><span>Total MRP</span> <strong>₹{{$cartTotal}}</strong></li>
						<li><span>Bag Discount</span> <strong>-₹{{number_format($totalDiscount, 2)}}</strong></li>
						<li><span>Delivery Charges</span> <strong>₹{{$totalShipping}}</strong></li>  
						<li class="totals"><span>Order Total</span> <strong>₹{{number_format($total, 2)}}</strong></li>
					</ul>
					<div class="placebtn"><a href="{{url('cart/checkout')}}">Place Order</a></div>
				</div>


			</div>

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
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>

		</div>
	</div>

	<!-- End - Address Modal -->


	<script type="text/javascript" src="{{url('public')}}/js/bootstrap.min.js"></script> 



	<script type="text/javascript">
		var state_id = '{{ $state }}';
		var city_id = '{{ $city }}';

		var formBox = $(".formBox")

		if(state_id && state_id != ""){
			load_cities(state_id, city_id, formBox);
		}

		$(document).on("change", "select[name='state']", function () {
			state_id = $(this).val();
			load_cities(state_id, city_id, $(this).parents("form"));
		} );

		function load_cities(state_id, city_id, currSelector){

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('common/ajax_load_cities')}}",
				type: "POST",
				data: {state_id: state_id, city_id: city_id},
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend:function(){},
				success:function(resp ){
					if(resp.success){
						if(currSelector){
							currSelector.find("select[name='city']").html(resp.options);
						}
						else{
							$("select[name='city']").html(resp.options);
						}
					}
				}
			});
		}

		$(document).on("click", ".addrBtn", function(e){
			e.preventDefault();

			var addressId = $(this).data("id");

			console.log("addressId="+addressId);

			getAddressForm( addressId );
		});

		function getAddressForm(addressId) {

			var addressModal = $("#addressModal");

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('users/get_address_form')}}",
				type: "POST",
				data: {addressId: addressId},
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function(){},
				success: function(resp){
					if(resp.success) {
						addressModal.find(".modal-title").html(resp.title);
						addressModal.find(".modal-body").html(resp.htmlData);

						state_id = resp.stateId;
						city_id = resp.cityId;
						var countryId = resp.countryId;

						if(state_id && state_id != ""){
							load_cities(state_id, city_id, addressModal);
						}


						addressModal.modal("show");
					}
				}
			});
		}

		$(document).on("click", ".saveAddrBtn", function(e){
			e.preventDefault();

			var addressModal = $("#addressModal");

			var addressForm = $("form[name=addressForm]");

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('users/save_address')}}",
				type: "POST",
				data: addressForm.serialize(),
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function(){
					addressForm.find( ".help-block" ).remove();
					addressForm.find( ".has-error" ).removeClass( "has-error" );
				},
				success: function(resp){
					if(resp.success) {
						window.location.reload();
					}
					else if(resp.errors){

						var errTag;
						var countErr = 1;

						$.each( resp.errors, function ( i, val ) {

							addressForm.find( "[name='" + i + "']" ).parent().addClass( "has-error" );
							addressForm.find( "[name='" + i + "']" ).parent().append( '<p class="help-block">' + val + '</p>' );

							if(countErr == 1){
								errTag = addressForm.find( "[name='" + i + "']" );
							}
							countErr++;

						});

						if(errTag){
							errTag.focus();
						}
					}
				}
			});
		});
	</script>

</body>
</html>