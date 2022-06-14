<!DOCTYPE html>
<html>
<head>

	@include('common.head')
	 <link rel="stylesheet" type="text/css" href="{{url('css/owl.carousel.min.css')}}" />

</head>
<body>

	@include('common.header')

	<?php
	$BackUrl = CustomHelper::BackUrl();

	$websiteSettingsNamesArr = ['FREE_DELIVERY_AMOUNT', 'SHIPPING_CHARGE', 'SHIPPING_TEXT'];

	$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

	$FREE_DELIVERY_AMOUNT = (isset($websiteSettingsArr['FREE_DELIVERY_AMOUNT']))?$websiteSettingsArr['FREE_DELIVERY_AMOUNT']->value:0;
	$SHIPPING_CHARGE = (isset($websiteSettingsArr['SHIPPING_CHARGE']))?$websiteSettingsArr['SHIPPING_CHARGE']->value:0;
	$SHIPPING_TEXT = (isset($websiteSettingsArr['SHIPPING_TEXT']))?$websiteSettingsArr['SHIPPING_TEXT']->value:'';
	

	$authCheck = auth()->check();

	$userWishlist = [];

	if($authCheck){
		$userWishlist = auth()->user()->userWishlist->keyBy('product_id');
	}


	$amountForFreeDelivery = 0;

	$totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;

	$cartContent = Cart::getContent();
	$cartTotal = Cart::getTotal($cartContent);

	$totalTax = Cart::getTax($cartContent);

	$totalWithTax = $cartTotal + $totalTax;

	//After coupon discount
	$totalTaxByPer = 0;
	$totalTaxwithCoupn = 0;
	$minAmountForCouponTxt = '';

	$isCoupon = false;

	$couponDiscountAmt = 0;	

	if($authCheck){

		$couponData = '';

		if(session()->has('couponData')){
			$couponData = session('couponData');


			if(isset($couponData['id']) && is_numeric($couponData['id']) && $couponData['id'] > 0){
				$isCoupon = true;

				$minAmountForCoupon = (isset($couponData['min_amount']))?$couponData['min_amount']:0;

				if(is_numeric($minAmountForCoupon) && $minAmountForCoupon > 0 && $minAmountForCoupon > $cartTotal){
					$couponData['discount'] = 0;
				}

				if(is_numeric($couponData['discount']) && $couponData['discount'] > 0){

					$couponDiscount = $couponData['discount'];
					$couponDiscountAmt = $couponDiscount;

					if($couponData['type'] == 'percentage'){
						$couponDiscountAmt = ( $cartTotal * ($couponDiscount/100) );
						$totalTaxByPer = ($totalTax * $couponDiscount)/100;
					}
					else{					
						$totalTaxByPer = ($couponDiscount * 100)/ $totalWithTax;
						$totalTaxwithCoupn = ($totalTaxByPer * $totalTax) / 100;
					}


					if(is_numeric($couponData['max_discount_amt']) && $couponData['max_discount_amt'] > 0){

						if($couponDiscountAmt > $couponData['max_discount_amt']){
							$couponDiscountAmt = $couponData['max_discount_amt'];

							$totalTaxByPer = ($couponData['max_discount_amt'] * 100)/ $totalWithTax;

							$totalTaxwithCoupn = ($totalTaxByPer * $totalTax) / 100;
						}

					}
				}
			}
		}
	}

	//$totalWithTaxWithCouponDiscount = $cartTotal + $totalTax - $couponDiscountAmt;
	$totalWithCouponDiscount = $cartTotal - $couponDiscountAmt;

	if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithCouponDiscount >= $FREE_DELIVERY_AMOUNT ){
		$totalShipping = 0;
	}
	else{
		$amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $totalWithCouponDiscount;
	}


	if(auth()->check()){
		$user = auth()->user();
		$findLoyaltyPonitsCriteria = CustomHelper::findLoyaltyPonitsCriteria($user->id, $cartTotal);

		if(!empty($findLoyaltyPonitsCriteria) && $findLoyaltyPonitsCriteria['freeShipping'] && $findLoyaltyPonitsCriteria['shipping_free_min_order'] <= $cartTotal)
		{
			$totalShipping = 0;
			$amountForFreeDelivery = 0;

		}


		if(!empty($findLoyaltyPonitsCriteria) && $findLoyaltyPonitsCriteria['freeShipping'])
		{

			$SHIPPING_TEXT = str_replace($FREE_DELIVERY_AMOUNT,number_format($findLoyaltyPonitsCriteria['shipping_free_min_order']),$SHIPPING_TEXT);
			
			//$FREE_DELIVERY_AMOUNT = $findLoyaltyPonitsCriteria['shipping_free_min_order'];


		}		


	}


	$productsSizesArr = [];

	
	?>
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#cart_popup">Open Modal</button> -->




	<section class="fullwidth tabcart">
		<div class="container">
			<ul>
				<li class="active"><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
				<li><span><i class="addressicon"></i></span><strong>Address</strong></li>
				<li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
				<li><span><i class="payment_n_icon"></i></span><strong>Payment</strong></li>
			</ul>
		</div>
	</section>  
	
	<section class="fullwidth innerpage"> 
		<div class="container">
			<div class="sectionleft">
				<div class="offersec">
					<strong>Offers</strong>
					<ul>
						<?php
						if(!empty($SHIPPING_TEXT)){
							?>
							<li>{{$SHIPPING_TEXT}}</li>
							<?php
						}
						?>
						
					</ul>
				</div>

				<div class="freedelivery">

				<?php
				if($amountForFreeDelivery > 0){
					?>
					<i class="detailicon1"></i> Shop for ₹{{number_format($amountForFreeDelivery)}} more to get <strong>Free Delivery.</strong>
					<?php

				}
				elseif($totalShipping == 0){
					?>
					<i class="detailicon1 yay_free_delivery"></i> Yay!  <strong>Free Delivery</strong> on this order. 
					<?php
				}
				?>

				</div>

				<?php
				$countQty = $cartContent->sum('qty');
				?>
				<div class="title3">My Shopping Bag ({{$countQty}} Items) </div>
				<ul class="cartlist">
					<?php

					//pr($cartContent->toArray());

					if(!empty($cartContent) && $cartContent->count() > 0){

						$storage = Storage::disk('public');
						$img_path = 'products/';
						$thumb_path = $img_path.'thumb/';
						
						foreach($cartContent as $cart){


							$cartId = $cart->id;

							$cartIdArr = explode('_', $cartId);

							$product_id = $cart->product_id;

							$product = $productModel->find($product_id);
							if(!isset($product->slug)){
								continue;
							}

							//$attributes = $cart->attributes;

							$productInventorySize = $product->productInventorySize;

							if(!empty($productInventorySize) && count($productInventorySize) > 0){
								//pr($productInventorySize->toArray());

								$productInventorySizeArr = $productInventorySize->sortBy('sort_order');

								foreach($productInventorySizeArr as $inventorySize){
									if($inventorySize->pivot->stock > 0){
										$productsSizesArr[$product_id][$inventorySize->id] = array(
											'size_id' => $inventorySize->id,
											'size_name' => $inventorySize->name,
											'stock' => $inventorySize->pivot->stock,
										);
									}
								}
							}

							$qty = $cart->qty;

							$sizeId = $cart->size_id;
							$sizeName = $cart->size_name;
							$clrName = $cart->color_name;

							$price = $product->price;
							$salePrice = $product->sale_price;

							$productBrand = $product->productBrand;

							$defaultImage = $product->defaultImage;
							$productImages = $product->productImages;

							$imgUrl = '';

							if(!empty($defaultImage) && count($defaultImage) > 0){
								if(!empty($defaultImage->image) ){
									$imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $defaultImage->image);
								}
							}

							if(empty($imgUrl)){
								if(!empty($productImages) && count($productImages) > 0){
									foreach($productImages as $prodImg){
										if(!empty($prodImg->image) ){
											$imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $prodImg->image);
											break;
										}
									}
								}
							}

							$brandName = '';

							if(!empty($productBrand) && count($productBrand) > 0){
								$brandName = $productBrand->name;
							}

							$itemPrice = $price*$qty;
							$itemSalePrice = $salePrice*$qty;

							?>
							<li class="listItem">
								<a href="{{url('products/details/'.$product->slug)}}" class="cartimg">
									<?php
									if(!empty($imgUrl)){
										?>
										<img src="{{$imgUrl}}" alt="{{$product->name}}">
										<?php
									}
									?>
									
								</a>
								<div class="procont">
									<div class="titles">
										<?php
										if(!empty($brandName)){
											?>
											<p><span>{{$brandName}}</span></p>
											<?php
										}
										?>
										<p><a href="{{url('products/details/'.$product->slug)}}" target="_blank">{{$cart->product_name}}</a></p>
									</div>
									<div class="cartprice">
										<?php
										if($salePrice > 0 && $salePrice < $price){
											$discount = CustomHelper::calculateProductDiscount($itemPrice, $itemSalePrice);
											?>
											<span>₹{{number_format($itemSalePrice)}} <del>₹{{number_format($itemPrice)}}</del></span>
											<small>Saving: <cite>{{number_format($discount)}}% off</cite></small>
											<?php
										}
										else{
											?>
											<span>₹{{$itemPrice}} </span>
											<?php
										}
										?>

									</div>

									<div class="sizeqty">
										<div class="size">SIZE : <span data-cid="{{$cart->id}}" data-pid="{{$product->id}}" data-sid="{{$sizeId}}" data-qty="{{$qty}}" class="sizeList sizeQtyList">{{$sizeName}}</span> </div>
										<div class="qty">QTY : <span data-cid="{{$cart->id}}" data-pid="{{$product->id}}" data-sid="{{$sizeId}}" data-qty="{{$qty}}" class="qtyList sizeQtyList">{{$qty}}</span></div>
									</div>

									<div class="removeandwish">
										<div class="remove">
											<a href="javascript:void(0)" data-cartid="{{$cart->id}}" class="removeCartItem">
											<i class="deleteicon"></i> <span>Remove</span>
											</a>
										</div>

										<div class="wishlistmove">

											
											<?php
											//prd($userWishlist->toArray());
											if($authCheck){

												if(isset($userWishlist[$product->id]) && $userWishlist[$product->id]->size_id == $sizeId ){
													?>
													<a href="javascript:void(0)"><i class="wishlistpink"></i> <span>Wishlisted</span></a>
													<?php
												}
												else{
													?>
													<a href="javascript:void(0)" data-cid="{{$cart->id}}" class="moveToWishlist"><i class="wishlistpink"></i> <span>Move To Wishlist</span></a>
													<?php
												}
											}
											else{
												?>
												<!-- <a href="javascript:void(0)" onclick="gotoLogin()"><i class="wishlistpink"></i> <span>Move To Wishlist</span></a> -->

												<span class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><i class="wishlistpink"></i><span>Move To Wishlist</span></a></span>
												
												<?php
											}
											?>

										</div>

									</div>
								</li>
								<?php
							}
						}

						$productsSizesJson = json_encode($productsSizesArr, JSON_FORCE_OBJECT);
						?>

					</ul>

				</div>

				<div class="sectionright">

					<div class="secures"><i class="secureimg"></i> <span>100% <small>Secure</small></span></div>
					
					<?php

					$cartProcessButton = '';
			if($authCheck){
				$cartProcessButton = '<a href="'.url('cart/address').'">Place Order</a>';
				/*?>
				<a href="{{url('cart/address')}}">Place Order</a>
				<?php*/
			}
			else{
				//$cartProcessButton = '<a href="javascript:void(0)" onClick="gotoLogin()">Place Order</a>';
				$cartProcessButton = '<a href="javascript:void(0)" class="open_slide">Place Order</a>';
				/*?>
				<a href="javascript:void(0)" onClick="gotoLogin()">Place Order</a>
				<?php*/
			}

			$couponRemovable = true;
			$showCoupon = true;
			?>

					@include('cart._price_details')

				</div>
				

			</div>
		</section>

		@include('common.footer')
<script type="text/javascript" src="{{url('/')}}/js/owl.carousel.min.js"></script> 
		@include('cart._popups')

		<div class="addedto " style="display:none;" ></div>
		
		<script type="text/javascript">
			

			var countQty = {{$countQty}};

			//var productsSizesJson = JSON.parse('<?php //echo $productsSizesJson; ?>');
			var productsSizesJson = '<?php echo $productsSizesJson; ?>';
		//	var productsSizesJson = '{"199":{"5":{"size_id":5,"size_name":"S","stock":3},"2":{"size_id":2,"size_name":"M","stock":3},"3":{"size_id":3,"size_name":"L","stock":3},"4":{"size_id":4,"size_name":"XL","stock":3},"7":{"size_id":7,"size_name":"XXL","stock":3}}}';


		function jsonParse(data)
		{
			return JSON.parse(data);
		}

			//$(".sizeQtyList").click(function(){
			$(document).on('click', '.sizeQtyList', function(){
				//var updateModal = $("#updateModal");

				var data = {};

				var updateModal = $("#cart_popup");

				var productId = $(this).data("pid");
				var oldSizeId = $(this).data("sid");
				var cartId = $(this).data("cid");
				var oldQty = $(this).data("qty");

				productId = parseInt(productId);
				oldSizeId = parseInt(oldSizeId);

				//console.log("productId="+productId);
				//console.log("sizeId="+oldSizeId);

				//productsSizesJson = JSON.stringify(productsSizesJson);
				productsSizesJson = JSON.parse(productsSizesJson);
				//productsSizesJson = jsonParse(productsSizesJson);
				//console.log('productsSizesJson');
				//console.log(productsSizesJson);
				var countProductsSizesJson = Object.keys(productsSizesJson).length;
				//console.log("countProductsSizesJson="+countProductsSizesJson);

				if(countProductsSizesJson > 0){



					//	console.log("productsSizesJson: "+productsSizesJson);
					//	console.log("productId: "+productId);
					//	console.log('productsSizesJson[productId]: '+productsSizesJson[productId]);
						//console.log("productsSizesJsonDD"+productsSizesJson[productId]);
					if(productsSizesJson[productId]){

						//console.log('Hiiii');


						var sizeQtyLen = Object.keys(productsSizesJson[productId]).length;

						if(sizeQtyLen > 6){
							updateModal.find(".slider_wrap").addClass("active_slider");
						}
						else{
							updateModal.find(".slider_wrap").removeClass("active_slider");
						}

						//console.log(Object.keys(productsSizesJson[productId]).length);

						var sizeListing = '';

						data['productId'] = productId;
						data['oldSizeId'] = oldSizeId;
						data['cartId'] = cartId;
						data['oldQty'] = oldQty;
						data['productsSizesJson'] = JSON.stringify(productsSizesJson);

						var _token = '{{ csrf_token() }}';

						$.ajax({
							url: "{{url('cart/ajax_get_size_qty')}}",
							type: 'post',
							data: data,
							dataType: 'JSON',
							headers:{'X-CSRF-TOKEN': _token},
							cache: false,
							crossDomain: true,
							beforeSend: function(){

							},
							success: function(resp){
								if(resp.success){
									if(resp.sizeListing){

										updateModal.find(".modal-body").html(resp.sizeListing);
										initOwlCarousel();
										updateModal.modal("show");

									}
								}
							}
						});

					}

					//updateModal.find(".modalTitle").text("Select Size");
				}

			});

			$(document).on('click', '.size_box', function(){
				$("input[name=sizeId]").prop("checked", false);

				$(this).find("input[name=sizeId]").prop("checked", true);

				$(".size_box").css({"background":"","color":"#000"});
				$(this).css({"background":"#a77736","border-color":"#a77736", "color":"#fff"});

				var sizeStock = $("input[name=sizeId]:checked").data("stock");

				sizeStock = parseInt(sizeStock);

				var currQty = $("input[name=qty]").val();

				if(currQty > sizeStock){
					$("input[name=qty]").val(sizeStock);
				}
			});



			$(document).on('click', '.updateSizeQty', function(){
				var sizeQtyForm = $("form[name=sizeQtyForm]");

				//console.log(sizeQtyForm.serialize());

				//return false;

				var _token = '{{ csrf_token() }}';
					$.ajax({
						url: "{{url('cart/update')}}",
						type: 'post',
						data: sizeQtyForm.serialize(),
						dataType: 'JSON',
						headers:{'X-CSRF-TOKEN': _token},
						cache: false,
						crossDomain: true,
						beforeSend: function(){

						},
						success: function(resp){
							if(resp.success){
								window.location.reload();
							}
						}
					});
			});


			$(document).on('click', '.couponCodeTxt', function(e){
				e.preventDefault();

				var couponCode = $(this).text();

				if(couponCode && couponCode != ""){
					$("form[name=couponForm]").find("input[name=coupon]").val(couponCode);
				}
			});

			$(document).on('click', '#applyCoupon', function(e){
				e.preventDefault();

				var couponForm = $("form[name=couponForm]");

				var coupon = couponForm.find("input[name=coupon]").val().trim(); 
				if(coupon==''){
					alert('Please enter coupon code');
					return false;
				}

				var _token = '{{ csrf_token() }}';
				$.ajax({
					url: "{{url('cart/apply_coupon')}}",
					type: 'post',
					data: {coupon:coupon },
					dataType: 'JSON',
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					crossDomain: true,
					beforeSend: function(){
						$("#couponpopup").find(".error").html('');
					},
					success: function(resp){
						if(resp.success){
							alert("Coupon applied successfully.");

							window.location.reload();
						}
						else if(resp.message){
							$("#couponpopup").find(".error").html(resp.message);
							//alert(resp.message);
						}
					}
				}); 


			});


			$(document).on('click', '#removeCoupon', function(){

				var _token = '{{ csrf_token() }}';

				$.ajax({
					url: "{{url('cart/remove_coupon')}}",
					type: 'post',
					data: {},
					dataType: 'JSON',
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					crossDomain: true,
					beforeSend: function(){

					},
					success: function(resp){
						if(resp.success){
							alert("Coupon removed successfully.");

							window.location.reload();
						}
					}
				}); 


			});

			$(".removeCartItem").on("click", function(){

				var conf = confirm("Are you sure you want to remove this item?");

				if(conf){

					var cartId = $(this).data("cartid");

					var _token = '{{ csrf_token() }}';

					$.ajax({
						url: "{{ url('cart/delete') }}",
						type: "POST",
						data: {cartId:cartId},
						dataType:"JSON",
						headers:{'X-CSRF-TOKEN': _token},
						cache: false,
						beforeSend:function(){

						},
						success: function(resp){
							if(resp.success){
								window.location.reload();
							}

						}
					});

				}
				else{
					$(".sizeErr").text("Please select a size");
				}
			});

			$(".moveToWishlist").on("click", function(){
				var currSel = $(this);

				var cartId = currSel.data("cid");

				var _token = '{{ csrf_token() }}';

				$.ajax({
					url: "{{ url('users/add_to_wishlist') }}",
					type: "POST",
					data: {cartId:cartId},
					dataType:"JSON",
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					beforeSend:function(){

					},
					success: function(resp){
						if(resp.success){

							setTimeout(function(){ window.location.reload(); }, 1000);
								
							currSel.parents("li.listItem").remove();

							var countItem = $(".listItem").length;
							if(countItem > 0){
								showAlertScc("Item has been added to wishlist.");

								if(resp.cartCount){
									$("#cart_count").text(resp.cartCount);
								}
							}
							else{
								window.location.reload();
							}
						}

					}
				});

			});

			function gotoLogin(){
				window.location = "{{url('account/login?referer='.$BackUrl)}}";
			}

			function showAlert(msg, type){
				if(msg && msg != ""){

					var alertClass = 'alert';

					if(type && type!= ""){
						alertClass += ' alert-'+type;
					}

					var message = '<div class="'+alertClass+'"> '+msg+' </div>';

					$("#alertModal").find(".modal-content").html(message);
					$("#alertModal").modal("show");
				}
			}

			function showAlertScc($msg){

				if($msg && $msg != ""){
					$(".addedto").html($msg);
					$(".addedto").show();

					setTimeout(function(){ $(".addedto").hide(); }, 2000);
				}
			}



		</script>

		<script type="text/javascript">
			$(document).on("click", ".add", function () {

				var qtyStock = $("input[name=sizeId]:checked").data("stock");

				qtyStock = parseInt(qtyStock);
				//console.log(qtyStock);

				if ($(this).prev().val() > qtyStock) {
					$(this).prev().val(qtyStock);
				}

				if ($(this).prev().val() < qtyStock) {
					$(this).prev().val(+$(this).prev().val() + 1);
				}
			});

			$(document).on("click", ".sub", function () {
				if ($(this).next().val() > 1) {
					$(this).next().val(+$(this).next().val() - 1);
				}
			});

			function initOwlCarousel(){

				$('.owl-carousel').owlCarousel('destroy');

				$('.owl-carousel').owlCarousel({
					//loop:false,
					margin:0,
					dots:false,
					mouseDrag: false,
					nav:true,
					responsive:{
						0:{
							items:3
						},
						600:{
							items:3
						},
						1000:{
							items:8
							
						}
					}
				});
			}

			//initOwlCaeousel();
		</script>
	</body>
	</html>