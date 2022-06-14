<!DOCTYPE html>
<html>
<head>  

@include('common.head')

<link rel="stylesheet" type="text/css" href="{{url('public/css/owl.carousel.min.css')}}" />
</head>
<body>

@include('common.header')

<?php
$storage = Storage::disk('public'); 
$img_path = 'products/';

$mainPrice = $product->price;

$slug = $product->slug;

$price = $product->price;
$salePrice = $product->sale_price;
$sku = $product->sku;
$specifications = $product->specifications;
$description = $product->description;

$productPrice = $mainPrice;
if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
	$productPrice = $product->sale_price;
}
else{
	$productPrice = $product->price;
}

$off = CustomHelper::calculateProductDiscount($mainPrice ,$salePrice);
$discount = number_format($off, 2);

$productSizes = (isset($product->productSizes))?$product->productSizes:'';
$productSizeChart = (isset($product->productSizeChart))?$product->productSizeChart:'';
$productAttributes = (isset($product->productAttributes))?$product->productAttributes:'';

/*if(!empty($productAttributes) && count($productAttributes) > 0){
	pr($productAttributes->toArray());
}*/

$CategoryBreadcrumb = '';

$productCategories = (isset($product->productCategories))?$product->productCategories:'';

if(!empty($productCategories) && count($productCategories) > 0){
	//$CategoryBreadcrumb = CustomHelper::CategoryBreadcrumbFrontend($productCategories[0], '/', '');

	//echo $CategoryBreadcrumb;
}

$FREE_DELIVERY_AMOUNT = CustomHelper::WebsiteSettings('FREE_DELIVERY_AMOUNT');
$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');
$SHIPPING_CHARGE = CustomHelper::WebsiteSettings('SHIPPING_CHARGE');

$BackUrl = CustomHelper::BackUrl();

?>

<section class="breadcrumbs fullwidth">
	<div class="container"> 
		<a href="{{url('')}}">Home</a> <?php //echo $CategoryBreadcrumb; ?>
	</div>
</section>

<section class="fullwidth innerlist">
	<div class="container"> 

		<?php
		if(!empty($product->productImages) && count($product->productImages) > 0){
			?>
			<ul class="dtimg">
				<?php
				$productImages = $product->productImages;
				foreach ($productImages as $image){
					?>
					<li>
					  <?php  
					  if(!empty($image->image) && $storage->exists($img_path.$image->image)){
						?>
						  <img src="{{url('public/storage/'.$img_path.$image->image)}}" alt="{{$image->image}}" />
					  </li>
					  <?php
				  }
			  }
			  ?>
		  </ul>
		  <?php
	  }
	  ?>

		
		<div class="dtright">
			<div class="pricetitle ">
				<h1> {{$product->name}} </h1>
				<p class="subheading deshborder"> {{$product->name}} </p>

				 <p class="prices">
					<span>&#x20B9; {{ $productPrice }}</span>
					<?php
					if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
						?>
						<del>&#x20B9; {{$price}}</del> <small>({{$discount}} %off)</small>
						<?php
					}
					?>
				</p>

				<p class="additionals">Additional tax may apply: charged at checkout</p>
			</div>
			
			<div class="selectsize">
				<div>
					<span>SELECT SIZE</span>
					<p class="sizeErr" style="color:#f16565;"></p>
					<?php
					if(!empty($productSizeChart) && count($productSizeChart) > 0){
						$sizeChartImg = (isset($productSizeChart->image))?$productSizeChart->image:'';

						if(!empty($sizeChartImg) && $storage->exists('size_charts/'.$sizeChartImg)){
							?>
							<small class="sizechart"><a href="{{url('public/storage/size_charts/'.$sizeChartImg)}}" target="_blank">SIZE Chart</a></small>
							<?php
						}
					}
					?>
					
				</div>

				<?php
				if(!empty($productSizes) && count($productSizes) > 0){
					foreach($productSizes as $size){
						?>
						<label><input type="checkbox" name="size[]" value="{{$size->id}}" class="prodSize"> <span>{{$size->name}}</span></label>
						<?php
					}
				}
				?>
			</div>

			
			<div class="btnsec">
				<button class="addtobag addToCart"><i class="carticonw"></i> ADD TO BAG</button>
				<?php
				if(auth()->check()){
					$userWishlist = auth()->user()->userWishlist->keyBy('product_id');

					if(isset($userWishlist[$product->id])){
						?>
						<button class="wishlistbtn"><i class="wishlisticonw"></i> Wishlisted</button>
						<?php
					}
					else{
						?>
						<button class="wishlistbtn addToWishlist"><i class="wishlisticonw"></i> Wishlist</button>
						<?php
					}
				}
				else{
					?>
					<button class="wishlistbtn" onclick="gotoLogin()"><i class="wishlisticonw"></i> Wishlist</button>
					<?php
				}
				?>
			</div>

			
			<div class="bestoffer deshborder">
				<div><span>Best Offers :</span> <i class="offericon"></i></div>
				<ul>
					<li>Get Additional 10% discount for order above 1499 </li>
					<li>Coupon code: NEWW500</li>
				</ul>
			</div>
			
			<div class="prodt">
				<?php
				if(!empty($sku)){
					?>
					<p>Product Code : <strong>{{$sku}}</strong></p>
					<?php
				}
				?>
				<div><strong>Product Details</strong> <i class="detailicon"></i></div>
				
				<?php echo $description; ?>

				<p class="specifications">
				<strong>Specifications </strong>
					<?php echo $specifications; ?>
				</p>
			</div>

			<?php
			if(!empty($productAttributes) && count($productAttributes) > 0){
				?>
				<div class="details deshborder"> 
					<ul>
						<?php
						foreach($productAttributes as $attr){
							?>
							<li><span>{{$attr->label}}</span> <strong>{{$attr->value}}</strong></li>
							<?php
						}
						?>
					</ul> 
				</div>
				<?php
			}
			?>
			
			<style>
				
			</style>
			
			<div class="checkavel"> 
				<div><strong>Delivery Options :</strong> <i class="detailicon1"></i></div>
				<div class="checkdelivery">
					<form name="pincodeForm" method="post" class="callback_form">
						<span class="available" style="display:none;" ><small class="yesavailable"></small></span>
						<input type="text" name="pincode" value="" placeholder="Enter Pincode" /><button type="button" id="checkPincode" >Check</button>
					</form>
				</div>

				<div class="pincodeAlert"></div>                

				<p class="enter_pin_txt"><small>Please enter PIN code to check delivery time & Cash/Card on Delivery Availability</small></p>

				<div class="pincode_avalaibilityContainer" style="display:none;">
					<strong>Expected Delivery Time</strong>
					<p><small>5 days; Actual time may vary depending on other items in your order</small></p>
				</div>

				<?php
				if(!empty($SHIPPING_TEXT)){
					?>
					<p>{{$SHIPPING_TEXT}}</p>
					<?php
				}
				?>
				
			</div>
			
		</div>
		
		
		<div class="fullwidth similarpro">
			<h2 class="heading2">Similar Product</h2>
			<div class="similarslider owl-carousel">

				<?php
				if(!empty($similarProducts) && count($similarProducts) > 0){
					foreach($similarProducts as $sp){
						$spImg = '';
						$spRevImg = '';

						$spDefaultImage = (isset($sp->defaultImage))?$sp->defaultImage:'';
						$spReverseImage = (isset($sp->reverseImage))?$sp->reverseImage:'';

						if(empty($spDefaultImage) || count($spDefaultImage) == 0){
							$spProductImages = (isset($sp->productImages))?$sp->productImages:'';

							if(!empty($spProductImages) && count($spProductImages) > 0){
								$spImg = $spProductImages[0]->image;
							}
						}
						else{
							$spImg = $spDefaultImage->image;
						}

						$spRevImg = (isset($spReverseImage->image))?$spReverseImage->image:'';

						?>
						<div>
							<a href="{{route('products.details', [$sp->slug])}}" class="product">
								<div class="flip-inner">
									<?php
									if(!empty($spImg) && $storage->exists('products/'.$spImg)){
										?>
										<img src="{{url('public/storage/products/'.$spImg)}}" alt="{{$sp->name}}"/>
										<div class="flip-front"><img src="{{url('public/storage/products/'.$spImg)}}" alt="{{$sp->name}}" /></div>
										<?php
									}
									if(!empty($spRevImg) && $storage->exists('products/'.$spRevImg)){
										?>                                        
										<div class="flip-back"><img src="{{url('public/storage/products/'.$spRevImg)}}" alt="{{$sp->name}}"/></div>
										<?php
									}
									?>
									
												  
								</div>
								<div class="procont">
									<p><span>{{$sp->name}}</span></p>
									<p>{{$sp->name}}</p>
									<?php
									if($sp->sale_price > 0 && $sp->price > $sp->sale_price){
										?>
										<p><strong>Just</strong><small>&#x20B9;{{$sp->sale_price}}</small> <del>&#x20B9;{{$sp->price}}</del> </p>
										<?php
									}
									else{
										?>
										<p><strong>Just</strong><small>&#x20B9;{{$sp->price}}</small> </p>
										<?php
									}
									?>
									
								</div>
							</a>
						</div>
						<?php
					}
				}
				?>

			</div>
		</div>
		
	</div>
</section>


@include('common.footer')


<!-- Cart Alert Modal -->
<div id="cartAlertModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
				<div class="alert alert-success">
					Item added to the Cart successfully.
				</div>
		</div>

	</div>
</div>

		
<script type="text/javascript" src="{{url('public')}}/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{url('public')}}/js/owl.carousel.min.js"></script> 

<script>
$('.catlist > ul > li.active ul').slideDown();
$( ".catlist > ul > li > span" ).click(function() {
	if ($( this ).parent().hasClass('active')){
	$('.catlist > ul > li').removeClass('active');
		$(this).next().slideUp();   
		
  } else {
	$(this).parent().addClass('active');
	  $(this).next().slideDown();
  }
	
	//$('.catlist > ul > li.active ul').slideToggle();  
}); 
	
$('.similarslider').owlCarousel({
		loop:true,
		margin:20,
		items:4,
		dots:false,
		nav:true,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:3
			},
			1200:{
				items:4
			}
		}
	});

function gotoLogin(){
	window.location = "{{url('account/login?referer='.$BackUrl)}}";
}

$(document).on("click", "#checkPincode", function(){

	var curr_sel = $(this);
	var pincode = curr_sel.siblings("input[name=pincode]").val();

	var alertMsg = '<div class="alert alert-danger alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>!</strong> Please enter valid pincode </div>';

	if(pincode && pincode != ""){
		var _token = '{{ csrf_token() }}';
		$.ajax({
			url: "{{ route('products.ajax_check_pincode') }}",
			type: "POST",
			data: {pincode:pincode},
			dataType:"JSON",
			headers:{'X-CSRF-TOKEN': _token},
			cache: false,
			async: false,
			beforeSend:function(){
				curr_sel.siblings('.available').hide();
				$(".pincodeAlert").html('');
				$('.enter_pin_txt').show();
				$('.pincode_avalaibilityContainer').hide();
			},
			success: function(resp){
				if(resp.success){
					curr_sel.siblings('.available').show();
					$('.enter_pin_txt').hide();
					$('.pincode_avalaibilityContainer').show();
				}
				else{
					$(".pincodeAlert").html('<div class="alert alert-danger alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>!</strong> this pincode is not available  </div>');
				}

			}

		});
	}
	else{
		$(".pincodeAlert").html('<div class="alert alert-danger alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>!</strong> Please enter valid pincode </div>');
	}
});


$(document).on("click", ".prodSize", function(){
	$(".prodSize").not($(this)).attr("checked", false);
});

$(document).on("click", ".addToCart", function(){
	var slug = "{{$slug}}";
	var size = $(".prodSize:checked").val();

	size = parseInt(size);

	console.log("size="+size);

	if(!isNaN(size) && size > 0){
		$(".sizeErr").text("");

		var _token = '{{ csrf_token() }}';

		$.ajax({
			url: "{{ url('cart/add') }}",
			type: "POST",
			data: {slug:slug, size:size},
			dataType:"JSON",
			headers:{'X-CSRF-TOKEN': _token},
			cache: false,
			beforeSend:function(){
				//$(".ajax_msg").html("");
			},
			success: function(resp){
				if(resp.success){
					$("#cartAlertModal").modal("show");
					$("#cart_count").text(resp.cartCount);
				}

			}
		});

	}
	else{
		$(".sizeErr").text("Please select a size");
	}
});

$(document).on("click", ".addToWishlist", function(){
	var currSel = $(this);

	var slug = "{{$slug}}";

	var _token = '{{ csrf_token() }}';

	$.ajax({
		url: "{{ url('users/add_to_wishlist') }}",
		type: "POST",
		data: {slug:slug},
		dataType:"JSON",
		headers:{'X-CSRF-TOKEN': _token},
		cache: false,
		beforeSend:function(){

		},
		success: function(resp){
			if(resp.success){

				$("#cartAlertModal").find(".alert").html("Item has been added to wishlist.");
				$("#cartAlertModal").modal("show");
				currSel.html('<i class="wishlisticonw"></i> Wishlisted');
				currSel.removeClass('addToWishlist');
			}

		}
	});

});
	 
</script>

</body>
</html>
