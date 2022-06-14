<!DOCTYPE html>
<html>
<head>  

@include('common.head')

<link rel="stylesheet" type="text/css" href="{{url('public/css/owl.carousel.min.css')}}" />
<style>
@import url(//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css);
	.fa{font-family: FontAwesome; font-style: normal;}
.fa-star:after {content: "\f005";  margin-left: 3px;}
	.fa-pencil::before {content: "\f040";  margin-right: 5px;}
	.fa-thumbs-up::before {content: "\f164";}
	.fa-thumbs-down::before {content: "\f165";}
.rating { border: none; float: left;}
.rating > input { display: none; } 
.rating > label:before {margin:0 5px; font-size: 1.25em;  font-family: FontAwesome; display: inline-block;  content: "\f005";}
.rating > .half:before {content: "\f089"; position: absolute;}
.rating > label { color: #ddd; float: right; }
/***** CSS Magic to Highlight Stars on Hover *****/

.rating > input:checked ~ label, .rating:not(:checked) > label:hover, .rating:not(:checked) > label:hover ~ label { color: #e41881;  }
.rating > input:checked + label:hover, .rating > input:checked ~ label:hover, .rating > label:hover ~ input:checked ~ label, .rating > input:checked ~ label:hover ~ label { color: #ff49b2; } 	
</style>
	
</head>
<body>

@include('common.header')
	
	<div class="addedto " style="display:none;" ></div>

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

$productSizes = (isset($product->productSizes))?$product->productSizes->sortBy('sort_order'):'';
$productSizeChart = (isset($product->productSizeChart))?$product->productSizeChart:'';
$productAttributes = (isset($product->productAttributes))?$product->productAttributes:'';

/*if(!empty($productAttributes) && count($productAttributes) > 0){
	pr($productAttributes->toArray());
}*/

$CategoryBreadcrumb = '';

$productCategories = (isset($product->productCategories))?$product->productCategories:'';

if(!empty($productCategories) && count($productCategories) > 0){
	$CategoryBreadcrumb = CustomHelper::CategoryBreadcrumbFrontend($productCategories[0], '/', '', true);

	//echo $CategoryBreadcrumb;
	//pr($productCategories->toArray());
}




$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');
$DISCOUNT_AMOUNT_TEXT = CustomHelper::WebsiteSettings('DISCOUNT_AMOUNT_TEXT');

$productBrand = $product->productBrand;
$brandName = '';

if(!empty($productBrand) && count($productBrand) > 0){
	$brandName = $productBrand->name;
}


$BackUrl = CustomHelper::BackUrl();

?>

<section class="breadcrumbs fullwidth">
	<div class="container"> 
		<a href="{{url('')}}">Home</a> <?php echo $CategoryBreadcrumb; ?> {{$product->name}}
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
				<?php
				if(!empty($brandName)){
					?>
					<h1>{{$brandName}}</h1>
					<?php
				}
				?>
				<p class="subheading deshborder"> {{$product->name}} </p>

				 <p class="prices">
					<span>&#x20B9; {{ number_format($productPrice) }}</span>
					<?php
					if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
						?>
						<del>&#x20B9; {{number_format($price)}}</del> <small>({{number_format($discount)}} %off)</small>
						<?php
					}
					?>
				</p>

				<p class="additionals">Additional tax may apply: charged at checkout</p>
			</div>
			
			<div class="selectsize">
				<div>
					<span>SELECT SIZE</span>
					<?php
					$sizeChartImg = '';
					if(!empty($productSizeChart) && count($productSizeChart) > 0){
						$sizeChartImg = (isset($productSizeChart->image))?$productSizeChart->image:'';

						if(!empty($sizeChartImg) && $storage->exists('size_charts/'.$sizeChartImg)){
							?>
							<small class="sizechart" data-toggle="modal" data-target="#sizechart">SIZE Chart</small>
					<?php /*?><a href="{{url('public/storage/size_charts/'.$sizeChartImg)}}" target="_blank">SIZE Chart</a><?php */?>
							<?php
						}
					}
					?>
					<p class="sizeErr" style="color:#f16565;"></p>
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
				<button class="addtobag addToCart"><i class="carticonw"></i> <span class="addToCartTxt">ADD TO BAG</span></button>
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
					<?php
					if(!empty($DISCOUNT_AMOUNT_TEXT)){
						?>
						<li>{{$DISCOUNT_AMOUNT_TEXT}}</li>
						<?php
					}
					?>
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

			</div>

			<div class="specifications">
				<strong>Specifications </strong>
				<?php //echo $specifications; ?>
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



		<?php
		$reviewsRatingAvg = 0;
		if(!empty($reviews) && count($reviews) > 0){
			$reviewsRatingAvg = $reviews->avg('rating');
		}

		$starRatingArr = CustomHelper::makeStarRatingArr($reviewsRatingAvg);
		?>
		
		
		<div class="fullwidth reviewsec">
			<h2 class="heading2">Customer Reviews</h2> 
			<div class="starsec">
				<?php
				if(!empty($starRatingArr) && count($starRatingArr) > 0){
					$revStarArr = array_reverse($starRatingArr);
					echo implode('', $revStarArr);
				}
				?>
				<!-- <span class="fa fa-star color"></span><span class="fa fa-star color"></span><span class="fa fa-star color"></span><span class="fa fa-star"></span><span class="fa fa-star"></span> -->
			</div>

			<?php
			if(auth()->check()){
				?>
				<div class="writereview" data-toggle="modal" data-target="#reviewpopup"><i class="fa fa-pencil" aria-hidden="true"></i> Write a review</div>
				<?php
			}
			else{
				?>
				<a href="{{url('account/login?referer='.$BackUrl)}}" class="writereview" title="Login to write a review.">Write a review</a>
				<?php
			}
			?>

			
			<p><strong>{{number_format($reviewsRatingAvg, 1)}}</strong> Out of 5 stars</p>
			<ul>
				<?php
				if(!empty($reviews) && count($reviews) > 0){
					foreach($reviews as $review){
						$reviewDate = CustomHelper::DateFormat($review->created_at, 'd M Y');
						$reviewUser = $review->reviewUser;

						$reviewUserName = (isset($reviewUser->name))?$reviewUser->name:'';

						?>
						<li>
							<div class="title3">{{$review->heading}}</div>
							<p><span>{{$review->rating}} <i class="fa fa-star"></i></span> <strong>{{$reviewUserName}}</strong> <small>{{$reviewDate}}</small></p>
							<p>{{$review->comment}}</p>
						</li>
						<?php
					}
				}
				?>

				<li>
					<div class="title3">Great Product</div>
					<p><span>5 <i class="fa fa-star"></i></span> <strong>Nandini shah</strong> <small>21 Jun 2019</small></p>
					<p>I have purchase the clothes first time but really it's a nice fabric cotton, printing and stitching. I'm feel awesome after purchase it. Thank u SlumberJill</p>
					<div class="thumblike"><span><i class="fa fa-thumbs-up"></i> 2</span> <span><i class="fa fa-thumbs-down"></i> 0</span></div>
				</li>
				<li>
					<div class="title3">Nice Pic</div>
					<p><span>3 <i class="fa fa-star"></i></span> <strong>Nandini shah</strong> <small>21 Jun 2019</small></p>
					<p>I have purchase the clothes first time but really it's a nice fabric cotton, printing and stitching. I'm feel awesome after purchase it. Thank u SlumberJill</p>
					<div class="thumblike"><span><i class="fa fa-thumbs-up"></i> 2</span> <span><i class="fa fa-thumbs-down"></i> 0</span></div>
				</li>
				<li>
					<div class="title3">Nice Product</div>
					<p><span>5 <i class="fa fa-star"></i></span> <strong>Nandini shah</strong> <small>21 Jun 2019</small></p>
					<p>I have purchase the clothes first time but really it's a nice fabric cotton, printing and stitching. I'm feel awesome after purchase it. Thank u SlumberJill</p>
					<div class="thumblike"><span><i class="fa fa-thumbs-up"></i> 2</span> <span><i class="fa fa-thumbs-down"></i> 0</span></div>
				</li>
				<li>
					<div class="title3">Great Product</div>
					<p><span>5 <i class="fa fa-star"></i></span> <strong>Nandini shah</strong> <small>21 Jun 2019</small></p>
					<p>I have purchase the clothes first time but really it's a nice fabric cotton, printing and stitching. I'm feel awesome after purchase it. Thank u SlumberJill</p>
					<div class="thumblike"><span><i class="fa fa-thumbs-up"></i> 2</span> <span><i class="fa fa-thumbs-down"></i> 0</span></div>
				</li>
			</ul>
			
		</div>
		
	</div>
</section>


@include('common.footer')


<!-- Cart Alert Modal -->
<div id="sizechart" class="modal fade" role="dialog">
	<div class="modal-dialog">
		
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Size Chart</h4>
			  </div>
			<div class="modal-body">
        <img src="{{url('public/storage/size_charts/'.$sizeChartImg)}}" alt="sizechart" />
      </div>

			
		</div>

	</div>
</div>

	
	
<div id="reviewpopup" class="modal fade" role="dialog">
	<div class="modal-dialog"> 
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Write a review</h4>
			</div>
			<div class="modal-body">
				<div class="sccMsg"></div>
				
				<form name="reviewsForm" methos="post">
					<div class="form-group starsec">
						<fieldset class="rating">
							<input type="radio" name="rating" value="5" id="star5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
							<input type="radio" name="rating" value="4.5" id="star4half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
							<input type="radio" name="rating" value="4" id="star4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
							<input type="radio" name="rating" value="3.5" id="star3half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
							<input type="radio" name="rating" value="3" id="star3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
							<input type="radio" name="rating" value="2.5" id="star2half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
							<input type="radio" name="rating" value="2" id="star2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
							<input type="radio" name="rating" value="1.5" id="star1half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
							<input type="radio" name="rating" value="1" id="star1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
							<input type="radio" name="rating" value="0.5" id="starhalf" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
						</fieldset>
						<div class="clearfix"></div>
					</div>

					<div class="form-group">
						<input type="text" name="heading" class="form-control" placeholder="Heading" maxlength="100">
					</div>
					<div class="form-group">
						<textarea name="comment" placeholder="Write a review"></textarea>
					</div>

					<input type="hidden" name="slug" value="{{$product->slug}}">

					<button type="button" class="sbtn sbmtReview">Post</button>
				</form>
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
			url: "{{ url('common/ajax_check_pincode') }}",
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
					$(".pincodeAlert").html('<div class="alert alert-success alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> We ship in this location.</div>');
				}
				else{
					$(".pincodeAlert").html('<div class="alert alert-danger alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> Sorry, we are not shipping in this location right now.</div>');
				}

			}

		});
	}
	else{
		$(".pincodeAlert").html('<div class="alert alert-danger alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>!</strong> Please enter valid pincode </div>');
	}
});

var addedSizeArr = [];

$(document).on("click", ".prodSize", function(){
	$(".prodSize").not($(this)).attr("checked", false);

	checkSelectedSize();	
});


$(document).on("click", ".addToCart", function(){

	var currSel = $(this);

	var slug = "{{$slug}}";
	var size = $(".prodSize:checked").val();

	size = parseInt(size);

	//console.log("size="+size);

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

					addedSizeArr.push(size);

					showAlert("Added to Bag");

					checkSelectedSize();

					$("#cart_count").text(resp.cartCount);
				}

			}
		});

	}
	else{
		$(".sizeErr").text("Please select a size");
	}
});

$(document).on("click", ".gotoCart", function(){
	window.location = "{{url('cart')}}";
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

				showAlert('Item has been added to wishlist');

				currSel.html('<i class="wishlisticonw"></i> Wishlisted');
				currSel.removeClass('addToWishlist');
			}

		}
	});

});

$("#reviewpopup").click(function(){
	var reviewsForm = $("form[name=reviewsForm]");

	reviewsForm.find(".form-group").removeClass( "has-error" );
	reviewsForm.find(".help-block").remove();
	$("#reviewpopup").find(".sccMsg").html('');
});

$(document).on("click", ".sbmtReview", function(){
	var currSel = $(this);

	var reviewsForm = $("form[name=reviewsForm]");

	var _token = '{{ csrf_token() }}';

	$.ajax({
		url: "{{ url('products/save_review') }}",
		type: "POST",
		data: reviewsForm.serialize(),
		dataType:"JSON",
		headers:{'X-CSRF-TOKEN': _token},
		cache: false,
		beforeSend:function(){
			reviewsForm.find(".form-group").removeClass( "has-error" );
			reviewsForm.find(".help-block").remove();
		},
		success: function(resp){
			if(resp.success) {
				$("#reviewpopup").find(".sccMsg").html('<div class="alert alert-success"> Your review has been submitted. </div>');

				document.reviewsForm.reset();
			}
			else if(resp.errors){

				var errTag;
				var countErr = 1;

				$.each( resp.errors, function ( i, val ) {

					reviewsForm.find( "[name='" + i + "']" ).parents(".form-group").addClass( "has-error" );
					reviewsForm.find( "[name='" + i + "']" ).parents(".form-group").append( '<p class="help-block">' + val + '</p>' );

					if(countErr == 1){
						errTag = reviewsForm.find( "[name='" + i + "']" );
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

function checkSelectedSize(){

	var selectedSize = $(".prodSize:checked").val();

	selectedSize = parseInt(selectedSize);

	var isInSizeArr = jQuery.inArray( selectedSize, addedSizeArr );

	if(isInSizeArr >= 0){
		
		$(".addToCartTxt").parent().addClass("gotoCart");
		$(".addToCartTxt").parent().removeClass("addToCart");

		$(".addToCartTxt").text('GO TO BAG');
	}
	else{
		$(".addToCartTxt").parent().addClass("addToCart");
		$(".addToCartTxt").parent().removeClass("gotoCart");

		$(".addToCartTxt").text('ADD TO BAG');
	}

}

function showAlert($msg){

	if($msg && $msg != ""){
		$(".addedto").html($msg);
		$(".addedto").show();

		setTimeout(function(){ $(".addedto").hide(); }, 2000);
	}
}
	 
</script>

</body>
</html>
