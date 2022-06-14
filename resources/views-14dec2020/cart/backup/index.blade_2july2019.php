<!DOCTYPE html>
<html>
<head>

	@include('common.head')

</head>
<body>

	@include('common.header')

	<?php
	$BackUrl = CustomHelper::BackUrl();

	$authCheck = auth()->check();

	$userWishlist = [];

	if($authCheck){
		$userWishlist = auth()->user()->userWishlist->keyBy('product_id');
	}

	$FREE_DELIVERY_AMOUNT = CustomHelper::WebsiteSettings('FREE_DELIVERY_AMOUNT');
	$SHIPPING_CHARGE = CustomHelper::WebsiteSettings('SHIPPING_CHARGE');
	$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');

	$DISCOUNT_AMOUNT = CustomHelper::WebsiteSettings('DISCOUNT_AMOUNT');
	$DISCOUNT_PERCENTAGE = CustomHelper::WebsiteSettings('DISCOUNT_PERCENTAGE');


	$amountForFreeDelivery = 0;

	$totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;

	$cartTotal = Cart::getTotal();

	if(is_numeric($FREE_DELIVERY_AMOUNT) && $cartTotal >= $FREE_DELIVERY_AMOUNT ){
		$totalShipping = 0;
	}
	else{
		$amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $cartTotal;
	}

	


	$productsSizesArr = [];

	
	?>

	<section class="fullwidth tabcart">
		<div class="container">
			<ul>
				<li class="active"><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
				<li><span><i class="addressicon"></i></span><strong>Address</strong></li>
				<li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
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

				<?php
				if($amountForFreeDelivery > 0){
					?>
					<div class="freedelivery"><i class="detailicon1"></i> Shop for ₹{{$amountForFreeDelivery}} more to get <strong>Free Delivery.</strong> </div>
					<?php

				}
				elseif($totalShipping == 0){
					?>
					<div class="freedelivery"><i class="detailicon1"></i> Yay!  <strong>Free Delivery</strong> on this order. </div>
					<?php
				}
				?>


				<div class="title3">My Shopping Bag ({{$cartCount}} Items) <div class="secures"><i class="secureimg"></i> <span>100% <br><small>Secure</small></span></div></div>
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

							$productSizes = $product->productSizes->sortBy('sort_order');

							if(!empty($productSizes) && count($productSizes) > 0){
								//pr($productSizes->toArray());

								foreach($productSizes as $ps){
									$productsSizesArr[$product_id][] = array(
										'size_id' => $ps->id,
										'size_name' => $ps->name,
									);
								}
							}

							//pr($productsSizesArr[$product_id]);

							//$attributes = $cart->attributes;

							$qty = $cart->qty;

							$sizeId = $cart->size_id;
							$sizeName = $cart->size_name;
							$clrName = $cart->color_name;

							//pr($attributes->toArray());

							$price = $product->price;
							$sale_price = $product->sale_price;

							$productBrand = $product->productBrand;

							$defaultImage = $product->defaultImage;
							$productImages = $product->productImages;

							$imgUrl = '';

							if(!empty($defaultImage) && count($defaultImage) > 0){
								if(!empty($defaultImage->image) && $storage->exists($thumb_path.$defaultImage->image) ){
									$imgUrl = url('public/storage/'.$thumb_path.$defaultImage->image);
								}
							}

							if(empty($imgUrl)){
								if(!empty($productImages) && count($productImages) > 0){
									$productImg = $productImages[0];
									if(!empty($productImg->image) && $storage->exists($thumb_path.$productImg->image) ){
										$imgUrl = url('public/storage/'.$thumb_path.$productImg->image);
									}
								}
							}

							$brandName = '';

							if(!empty($productBrand) && count($productBrand) > 0){
								$brandName = $productBrand->name;
							}

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
										if($sale_price < $price){
											$discount = CustomHelper::calculateProductDiscount($price, $sale_price);
											?>
											<span>₹{{number_format($sale_price)}} <del>₹{{number_format($price)}}</del></span>
											<small>Saving: <cite>{{number_format($discount)}}% off</cite></small>
											<?php
										}
										else{
											?>
											<span>₹{{$price}} </span>
											<?php
										}
										?>

									</div>

									<div class="sizeqty">
										<div class="size">SIZE : <span data-cid="{{$cart->id}}" data-pid="{{$product->id}}" data-sid="{{$sizeId}}" class="sizeList">{{$sizeName}}</span> </div>
										<div class="qty">QTY : <span data-cid="{{$cart->id}}" data-pid="{{$product->id}}" data-sid="{{$sizeId}}" data-qty="{{$qty}}" class="qtyList">{{$qty}}</span></div>
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
												<a href="javascript:void(0)" onclick="gotoLogin()"><i class="wishlistpink"></i> <span>Move To Wishlist</span></a>
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

					
					<?php

					$cartProcessButton = '';
			if($authCheck){
				$cartProcessButton = '<a href="'.url('cart/address').'">Place Order</a>';
				/*?>
				<a href="{{url('cart/address')}}">Place Order</a>
				<?php*/
			}
			else{
				$cartProcessButton = '<a href="javascript:void(0)" onClick="gotoLogin()">Place Order</a>';
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

		@include('cart._popups')

		<div class="addedto " style="display:none;" ></div>

		<script type="text/javascript" src="{{url('public')}}/bootstrap/js/bootstrap.min.js"></script>
		
		<script type="text/javascript">

			var productsSizesJson = JSON.parse('<?php echo $productsSizesJson; ?>');

			$(".sizeList").click(function(){
				var updateModal = $("#updateModal");

				var productId = $(this).data("pid");
				var sizeId = $(this).data("sid");
				var cartId = $(this).data("cid");

				productId = parseInt(productId);
				sizeId = parseInt(sizeId);

				/*console.log("productId="+productId);
				console.log("sizeId="+sizeId);*/

				var countProductsSizesJson = Object.keys(productsSizesJson).length;
				//console.log("countProductsSizesJson="+countProductsSizesJson);

				if(countProductsSizesJson > 0){

					if(productsSizesJson[productId]){

						var sizeListing = '';

						$.each(productsSizesJson[productId], function(i, val){

							sizeListing += '<li><a href="javascript:void(0)" data-cid="'+cartId+'" data-pid="'+productId+'" data-sid="'+productsSizesJson[productId][i]['size_id']+'" data-osid="'+sizeId+'" data-type="size" class="changeSize" >'+productsSizesJson[productId][i]['size_name']+'</a></li>';

						});

						if(sizeListing && sizeListing != ''){
							updateModal.find(".modal-content ul").html(sizeListing);
						}
					}

					updateModal.find(".modalTitle").text("CHANGE SIZE");

					updateModal.modal("show");
				}

			});


			$(".qtyList").click(function(){
				var updateModal = $("#updateModal");

				var productId = $(this).data("pid");
				var sizeId = $(this).data("sid");
				var cartId = $(this).data("cid");
				var qty = $(this).data("qty");

				var qtyListing = '';
				for(var i=1; i<=10; i++){

					qtyListing += '<li><a href="javascript:void(0)" data-cid="'+cartId+'" data-pid="'+productId+'" data-sid="'+sizeId+'" data-qty="'+i+'" data-oqty="'+qty+'" data-type="qty" class="changeQty" >'+i+'</a></li>';
				}

				if(qtyListing && qtyListing != ''){
					updateModal.find(".modal-content ul").html(qtyListing);
				}

				updateModal.find(".modalTitle").text("CHANGE QUANTITY");

				updateModal.modal("show");

			});

			$(document).on('click', '.changeSize, .changeQty', function(){
				var productId = $(this).data("pid");
				var cartId = $(this).data("cid");

				var sizeId = $(this).data("sid");
				var oldSizeId = $(this).data("osid");

				var qty = $(this).data("qty");
				var oldQty = $(this).data("oqty");

				var type = $(this).data("type");

				/*console.log("productId="+productId);
				console.log("sizeId="+sizeId);*/

				if( (type == 'size' && sizeId != oldSizeId) || (type == 'qty' && qty != oldQty) ){

					var data = {};

					data['productId'] = productId;
					data['cartId'] = cartId;

					data['sizeId'] = sizeId;
					data['oldSizeId'] = oldSizeId;

					data['qty'] = qty;
					data['oldQty'] = oldQty;

					data['type'] = type;

					var _token = '{{ csrf_token() }}';
					$.ajax({
						url: "{{url('cart/update')}}",
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
								window.location.reload();
							}
						}
					});
				}

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
	</body>
	</html>