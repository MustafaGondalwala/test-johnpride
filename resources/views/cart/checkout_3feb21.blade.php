<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')

<?php
$addressArr = CustomHelper::formatUserAddress($shppingAddress);

$name = trim($shppingAddress->first_name.' '.$shppingAddress->last_name);
?>
  
<section class="fullwidth tabcart">
  <div class="container">
     <ul>
      <li><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
     <li><span><i class="addressicon"></i></span><strong>Address</strong></li>
     <li class="active"><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
     <li><span><i class="payment_n_icon"></i></span><strong>Payment</strong></li>
    </ul>
  </div>
</section>  
  
<section class="fullwidth innerpage">
	<div class="container">
	  <div class="sectionleft">	
		  
		  <div class="checklist">

				<ul class="cartlist">
					<?php

					//pr($cartContent->toArray());
					$image_path = config('custom.image_path');

					if(!empty($cartContent) && $cartContent->count() > 0){

						$storage = Storage::disk('public');
						$img_path = 'products/';
						$thumb_path = $img_path.'thumb/';

						foreach($cartContent as $cart){

							$cartId = $cart->id;

							$cartIdArr = explode('_', $cartId);

							$product_id = $cart->product_id;

							$product = $productModel->find($product_id);

							$qty = $cart->qty;

							$sizeId = $cart->size_id;
							$sizeName = $cart->size_name;
							$clrName = $cart->color_name;

							//pr($cart->toArray());

							$price = $product->price;
							$salePrice = $product->sale_price;

							//pr($price);

							$productBrand = $product->productBrand;

							$defaultImage = $product->defaultImage;
							$productImages = $product->productImages;

							$imgUrl = '';

							if(!empty($defaultImage) && count($defaultImage) > 0){
								if(!empty($defaultImage->image) ){
									$imgUrl = $defaultImage->image;
									//$imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $defaultImage->image);
								}
							}

							if(empty($imgUrl)){
								if(!empty($productImages) && count($productImages) > 0){
									foreach($productImages as $prodImg){
										if(!empty($prodImg->image) ){
											$imgUrl = $prodImg->image;
											//$imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $prodImg->image);
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
										<img src="{{$image_path.$imgUrl}}" alt="{{$product->name}}">
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
										<p><a href="{{url('products/details/'.$product->slug)}}" target="_blank">{{$product->name}}</a></p>
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
										<div class="size">SIZE : <span data-pid="{{$product->id}}" data-sid="{{$sizeId}}" class="sizeList">{{$sizeName}}</span> </div>
										<div class="qty">Qty : <span data-pid="{{$product->id}}" data-sid="{{$sizeId}}" data-qty="{{$qty}}" class="qtyList">{{$qty}}</span></div>
									</div>
								</li>
								<?php
							}
						}
						?>

					</ul>

		  </div>
		  
		  
		</div>
		
		<div class="sectionright">

			<?php
			$cartProcessButton = '<a href="javascript:void(0)" class="checkoutBtn">Continue</a>';

			$couponRemovable = false;
			$showCoupon = false;

			$couponData = session('couponData');

			if(!empty($couponData) && count($couponData) > 0){
				$showCoupon = true;
			}

			$useWallet = true;
			?>

			@include('cart._price_details')
			

			<div class="addlist"> 
				<h3 class="title3">Shipping Detail</h3>
				<p><strong>{{$name}}</strong></p>
				<p>
					<?php
					if(!empty($addressArr) && count($addressArr) > 0){
						echo implode(', ', $addressArr); 
					}
					?>
				</p> 
				<p>Landmark. {{$shppingAddress->locality}}</p>
				<p>Mobile. {{$shppingAddress->phone}}</p>
			</div>

		</div>

		<?php
		/*
		<div class="sectionright">  

			<div class="pricedetail">

				<h3 class="title3">Price Detail</h3>
				<ul>
					<li><span>Total MRP</span> <strong>₹1499</strong></li>
					<li><span>Bag Discount</span> <strong>-₹99</strong></li>
					<li><span>Delivery Charges</span> <strong>-₹00</strong></li>  
					<li class="totals"><span>Order Total</span> <strong>₹1400</strong></li>
				</ul>
				<div class="placebtn"><a href="#">Continue</a></div>
			</div>

		</div>
		*/
		?>

	</div>


</section>

<form name="checkoutForm" method="POST" action="{{url('cart/payment-method')}}"></form>
	
@include('common.footer')

<script type="text/javascript">
	$(document).on("click", "input[name=is_wallet]", function(){
		if($(this).is(":checked")){
			$(".walletBox").show();
		}
		else{
			$(".walletBox").hide();
		}
	});


	$(document).on("click", ".checkoutBtn", function(){
		//console.log("checkoutBtn");

		var shppingAddrId = "{{$shppingAddrId}}";

		var isWallet = $("input[name=is_wallet]:checked").val();

		isWallet = parseInt(isWallet);

		if(isNaN(isWallet)){
			isWallet = '';
		}

		var html = '';
		html += '{{csrf_field()}}';
		html += '<input type="hidden" name="shppingAddrId" value="'+shppingAddrId+'">';
		html += '<input type="hidden" name="isWallet" value="'+isWallet+'">';

		$("form[name=checkoutForm]").html(html);

		document.checkoutForm.submit();
	});
</script>

</body>
</html>