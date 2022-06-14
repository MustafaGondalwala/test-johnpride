<!DOCTYPE html>
<html>
<head>  

@include('common.head')

<link rel="stylesheet" type="text/css" href="{{url('css/owl.carousel.min.css')}}" />
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

.rating > input:checked ~ label, .rating:not(:checked) > label:hover, .rating:not(:checked) > label:hover ~ label { color:#a77736;  }
.rating > input:checked + label:hover, .rating > input:checked ~ label:hover, .rating > label:hover ~ input:checked ~ label, .rating > input:checked ~ label:hover ~ label { color: #c39352; } 	
</style>
<?php 
$isMobile = CustomHelper::isMobile();
    
?>
</head>
<body class="detailpages">

@include('common.header')
	
	<div class="addedto " style="display:none;" ></div>

<?php
$authCheck = auth()->check();

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

$categoryBreadcrumb = '';

$productCategories = (isset($product->productCategories))?$product->productCategories:'';

if(!empty($productCategories) && count($productCategories) > 0){
	$categoryBreadcrumb = CustomHelper::CategoryBreadcrumbFrontend($productCategories[0], '/', '', true);

	//echo $categoryBreadcrumb;
	//pr($productCategories->toArray());
}


/*$SHIPPING_TEXT = CustomHelper::WebsiteSettings('SHIPPING_TEXT');
$DISCOUNT_AMOUNT_TEXT = CustomHelper::WebsiteSettings('DISCOUNT_AMOUNT_TEXT');*/

$productBrand = $product->productBrandStatus;
$brandName = '';

if(!empty($productBrand) && count($productBrand) > 0){
	$brandName = $productBrand->name;
}

$totalStock = 0;

$productInventorySize = $product->productInventorySize;

if(!empty($productInventorySize) && count($productInventorySize) > 0){
	//pr($productInventorySize->toArray());

	$totalStock = $productInventorySize->sum('pivot.stock');
}

//pr($product);
$colorCode = '' ;
$productColor = (isset($product->color))?$product->color:'';
if(!empty($productColor->code)){
	$colorCode = $productColor->code;
}

$net_qty = (isset($product->net_qty))?$product->net_qty:'';
$country_origin = (isset($product->country_origin))?$product->country_origin:'';
$manufacturer = (isset($product->manufacturer))?$product->manufacturer:'';


$BackUrl = CustomHelper::BackUrl();



$websiteSettingsNamesArr = ['PRODUCT_DETAIL_TXT'];

$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

$PRODUCT_DETAIL_TXT = (isset($websiteSettingsArr['PRODUCT_DETAIL_TXT']))?$websiteSettingsArr['PRODUCT_DETAIL_TXT']->value:'';
?>

<section class="breadcrumbs fullwidth">
	<div class="container"> 
		<a href="{{url('')}}">Home</a> <?php echo $categoryBreadcrumb; ?> {{$product->name}}
	</div>
</section>

<section class="fullwidth innerlist">
	<div class="container"> 
		<?php if(!empty($product->productImages) && count($product->productImages) > 0){
			//prd($product->productImages->toArray());
		?>
		<div class="dtimg">
		<div class="detailslide owl-carousel">
		<?php $productImages = $product->productImages->take(5);
		//pr($productImages->toArray());
		foreach ($productImages as $image){ ?>
			<?php if(!empty($image->image)){
			$imageUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $image->image); ?>
			<div>
				<a href="{{$imageUrl}}" data-imagelightbox="g">
					<img src="{{$imageUrl}}" alt="{{$image->image}}" />
				</a>
			</div>
			<?php } } ?>
	  	</div>
	  </div>
	 	<?php  } ?>

		
		<div class="dtright">
			<div class="pricetitle ">
			<?php /* if(!empty($brandName)){ ?>
				<h1>{{$brandName}}</h1>
			<?php }  */ ?>
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
			<p class="cash_back"><?php echo $PRODUCT_DETAIL_TXT; ?></p>
			
			<!-- <div class="color_wrap">
				<span>color</span>
				<div class="color_box">
					<div class="color_inner">
						<small style="background: <?php //echo $colorCode; ?>;"></small>
					</div>
				</div>
			</div> -->
		</div>

			<?php
			$sizeChartImg = '';
			if(!empty($productInventorySize) && count($productInventorySize) > 0){
				?>

				<div class="selectsize">
					<div>
						<span>SELECT SIZE</span>
						<?php
						
						if(!empty($productSizeChart) && count($productSizeChart) > 0){
							$sizeChartImg = (isset($productSizeChart->image))?$productSizeChart->image:'';

							if(!empty($sizeChartImg) && $storage->exists('size_charts/'.$sizeChartImg)){
								?>
								 <!-- <small class="sizechart" data-toggle="modal" data-target="#sizechart">SIZE Chart</small> -->
								<a class="sizechart" data-toggle="modal" data-target="#sizechart" href="{{url('public/storage/size_charts/'.$sizeChartImg)}}" target="_blank">SIZE Chart</a> 
								<?php
							}
						}
						?>
						  <p class="sizeErr" style="color:#f16565;"></p>  
					</div>

					<?php if(!empty($productInventorySize) && count($productInventorySize) > 0){
						$productInventorySizeArr = $productInventorySize->sortBy('sort_order');
						foreach($productInventorySizeArr as $inventorySize){ ?>
						<label>
							<?php if($inventorySize->pivot->stock > 0){ ?>
								<input type="checkbox" name="size[]" value="{{$inventorySize->id}}" class="prodSize"> <span>{{$inventorySize->name}}</span>
								<?php } else{
								if($authCheck){ ?>
									<a href="javascript:void(0)" class="notifySize out-stock" data-slug="{{$product->slug}}" data-size="{{$inventorySize->id}}"><span>{{$inventorySize->name}}</span></a>
									<?php } else{ ?>
									<a href="javascript:void(0)" class="out-stock">
										<span>{{$inventorySize->name}}</span></del>
									</a>
									<?php } } ?>
						</label>							
						<?php } } ?>
				</div>

				<?php
			}
			?>

			
			<div class="btnsec">

				<?php
				if(is_numeric($totalStock) && $totalStock > 0){
					?>
					<button class="addtobag addToCart"><i class="carticon"></i> <span class="addToCartTxt">ADD TO BAG</span></button>
					<?php
				}
				else{
					?>					
					<button class="addtobag notifySize" data-slug="{{$product->slug}}" data-size=""><i class="carticonw"></i> Out of Stock</button>
					<?php
				}
				?>
				
				<?php
				if($authCheck){
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
					<!-- <button class="wishlistbtn" onclick="gotoLogin()"><i class="wishlisticonw"></i> Wishlist</button> -->

					<span class="open_slide "><a href="javascript:void(0)" class="wishlistbtn mainLoginBtn"><i class="wishlisticonw"></i>Wishlist</a></span>

					<?php
				}
				?>
			</div>

			
			<?php
			/*
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
			*/
			?>
			
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
			
		
			
			<div class="checkavel"> 
				<div><strong>Delivery Options :</strong> <i class="detailicon1"></i></div>
				<div class="checkdelivery">
					<form name="pincodeForm" method="post" class="callback_form">
						<span class="available" style="display:none;" ><small class="yesavailable"></small></span>
						<input type="text" name="pincode" value="" placeholder="Enter Pincode" /><button type="button" id="checkPincode" >Check</button>
					</form>
				</div>

				<div class="pincodeAlert"></div>                

				<p class="enter_pin_txt"><small>Please enter PIN code to check delivery time Card on Delivery Availability</small></p>

				<!-- <div class="pincode_avalaibilityContainer" style="display:none;">
					<strong>Expected Delivery Time</strong>
					<p><small>5 days; Actual time may vary depending on other items in your order</small></p>
				</div> -->
				<div style="display: none;" id="field_wrap">
				<div id="field1" class="pincode_report"></div>
				<div id="field2" class="pincode_report"></div>
				<div id="field3" class="pincode_report"></div>
				</div>

				<?php
				if(!empty($SHIPPING_TEXT)){
					?>
					<p>{{$SHIPPING_TEXT}}</p>
					<?php
				}
				?>
				
			</div>


			
			



<!-- <div class="other_information_wrap">
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading active" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
         Other Information
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      <ul class="other_information_list">
      	<li>
      		<div class="heading_txt">
      			Net Qty
      		</div>
      		<div class="des_text">
      			{{$net_qty}} 
      		</div>
      	</li>
      	<li>
      		<div class="heading_txt">
      			Country Of Origin
      		</div>
      		<div class="des_text">
      			{{$country_origin}}
      		</div>
      	</li>
      
      	<li>
      		<div class="heading_txt">
      			Manufacturer / Importer Info
      		</div>
      		<div class="des_text">
      			{{$manufacturer}}
      		</div>
      	</li>
      	
      </ul>
      </div>
    </div>
  </div>
  
  
</div>
</div> -->

		</div>
		
		<div class="fullwidth fullimg">
			<?php 

			$detail_image = $product->desktop_image;

			if($isMobile)        
			{
				$detail_image = $product->mobile_image;
			}


			if(isset($detail_image) && $detail_image!=''){ ?>
				<img src="{{$detail_image}}" alt="Product Image" />
			<?php } ?>
		</div>

		<div class="fullwidth guaranteesec">
			<img src="{{url('images/logo01.png')}}" alt="" />
			<div class="ghead">The Johnpride Guarantee</div>
			<div class="guarntycont"> Go on, explore risk-free convenience and luxury: every purchase is covered for free shipping. And 60 days free returns, for any reason on non sale product(s). Because our guarantee is the north star to our moral compass.M</div>

		</div>
	<?php	if(!empty($similarProducts) && count($similarProducts) > 0){ ?>	
		<div class="fullwidth similarpro">
			<h2 class="heading2">Similar Product</h2>
			<div class="similarslider owl-carousel">
			<?php /* ?>
			<div>
				<a href="http://johnpride.ii71.com/products/details/black-white-polo-t-shirt-3198" class="product">
				<div class="productimg">
				<img src="http://johnpride.ii71.com/images/blank.png" alt="BLACK &amp; WHITE POLO T-SHIRT-3198">
				<div class="pimg">
				<img src="https://www.ehostinguk.com/iidemo/johnpride/img6.jpg" alt="BLACK &amp; WHITE POLO T-SHIRT-3198">
				</div>
				</div>

				<div class="procont">
				<div class="heading3">Black & white polo T-shirt-3198</div>
				<p><strong>Just</strong><small>₹1,499</small> 
				<del>₹1,599</del> 
				</p>
				<p><small class="offpro">(30% OFF)</small></p>
				</div>
				</a>
			</div>
			<?php */ ?> 

				<?php				
					foreach($similarProducts as $sp){
						$spImg = '';
						$spRevImg = '';

						//$discount_per = ($sp->sale_price > 0 && $sp->price > $sp->sale_price)?(($sp->price - $sp->sale_price) * 100)/$sp->price:0;

						$off = CustomHelper::calculateProductDiscount($sp->price ,$sp->sale_price);
						$discount = number_format($off, 2);

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

						$productBrand = $sp->productBrandStatus;
						$brandName = '';

						if(!empty($productBrand) && count($productBrand) > 0){
							$brandName = $productBrand->name;
						}
						$smainImageUrl = str_replace("https://www.dropbox.com/", "https://dl.dropboxusercontent.com/", $spImg);
						$srevImageUrl = str_replace("https://www.dropbox.com/", "https://dl.dropboxusercontent.com/", $spRevImg);
						?>
						<div>
							<a href="{{route('products.details', [$sp->slug])}}" class="product">
								<div class="productimg">
 
									<?php
									if(!empty($spImg)){
										?>
										<img src="{{url('')}}/images/blank.png" alt="{{$sp->name}}"/> 
										<!-- <img src="{{$smainImageUrl}}" alt="{{$sp->name}}"/> -->
										<div class="pimg"> <img src="{{$smainImageUrl}}" alt="{{$sp->name}}" />  </div>
										<?php
									}
									if(!empty($spRevImg)){
										?>                                        
										<!-- <div class="flip-back"><img src="{{$srevImageUrl}}" alt="{{$sp->name}}"/></div> -->
										<?php
									}
									?>
									
												  
								</div>
								 
								<div class="procont">
									<?php /*<p><span>{{$brandName}}</span></p>*/ ?>
									<div class="heading3">{{$sp->name}}</div>
									<?php
									if($sp->sale_price > 0 && $sp->price > $sp->sale_price){
										?>
										<p><strong>Just</strong><small>&#x20B9;{{number_format($sp->sale_price)}}</small> <del>&#x20B9;{{number_format($sp->price)}}</del> </p>
										<?php
									}
									else{
										?>
										<p><strong>Just</strong><small>&#x20B9;{{number_format($sp->price)}}</small> </p>
										<?php
									}
									?>

									<p>
										<?php if($sp->sale_price > 0 && $sp->price > $sp->sale_price && $discount > 0 ){ ?>
											<small class="offpro">({{number_format($discount)}}% OFF)</small>
										<?php } ?>
									</p>
									
								</div>
							</a>
						</div>
						<?php
					}
				
				?>

			</div>
		</div>
	<?php }	?>


		<?php
		if(!empty($recentProducts) && count($recentProducts) > 0){
			?>
			<div class="fullwidth similarpro">
			<h2 class="heading2">Recent Views</h2>
			<div class="similarslider owl-carousel">

				<?php
					foreach($recentProducts as $rp){
						$rpImg = '';
						$rpRevImg = '';

						$rpDefaultImage = (isset($rp->defaultImage))?$rp->defaultImage:'';
						$rpReverseImage = (isset($rp->reverseImage))?$rp->reverseImage:'';

						if(empty($rpDefaultImage) || count($rpDefaultImage) == 0){
							$rpProductImages = (isset($rp->productImages))?$rp->productImages:'';

							if(!empty($rpProductImages) && count($rpProductImages) > 0){
								$rpImg = $rpProductImages[0]->image;
							}
						}
						else{
							$rpImg = $rpDefaultImage->image;
						}

						$rpRevImg = (isset($rpReverseImage->image))?$rpReverseImage->image:'';

						$productBrand = $rp->productBrandStatus;
						$brandName = '';

						if(!empty($productBrand) && count($productBrand) > 0){
							$brandName = $productBrand->name;
						}
						$rmainImageUrl = str_replace("https://www.dropbox.com/", "https://dl.dropboxusercontent.com/", $rpImg);
						$rrevImageUrl = str_replace("https://www.dropbox.com/", "https://dl.dropboxusercontent.com/", $rpRevImg);
						?>
						<div>
							<a href="{{route('products.details', [$rp->slug])}}" class="product">
								<div class="flip-inner">
									<?php
									if(!empty($rpImg)){
										?>
									 <img src="{{url('/')}}/images/blank.png" alt="{{$product->name}}"/>
										
										<div class="flip-front"><img src="{{$rmainImageUrl}}" alt="{{$rp->name}}" /></div>
										<?php
									}
									if(!empty($rpRevImg)){
										?>                                        
										<div class="flip-back"><img src="{{$rrevImageUrl}}" alt="{{$rp->name}}"/></div>
										<?php
									}
									?>			  
								</div>
								<div class="procont">
									<p><span>{{$brandName}}</span></p>
									<p>{{$rp->name}}</p>
									<?php
									if($rp->sale_price > 0 && $rp->price > $rp->sale_price){
										?>
										<p><strong>Just</strong><small>&#x20B9;{{number_format($rp->sale_price)}}</small> <del>&#x20B9;{{number_format($rp->price)}}</del> </p>
										<?php
									}
									else{
										?>
										<p><strong>Just</strong><small>&#x20B9;{{number_format($rp->price)}}</small> </p>
										<?php
									}
									?>
									
								</div>
							</a>
						</div>
						<?php
					}
				?>

			</div>
		</div>
			<?php
		}
		?>
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
					//pr($starRatingArr);
					//$revStarArr = array_reverse($starRatingArr);
					echo implode('', $starRatingArr);
				}
				?>
			</div>

			<?php
			if($authCheck){
				?>
				<div class="writereview writeReviewBtn" ><i class="fa fa-pencil" aria-hidden="true"></i> Write a review</div>
				<?php
			}
			else{
				/*
				<a href="{{url('account/login?referer='.$BackUrl)}}" class="writereview" title="Login to write a review.">Write a review</a>
				*/
				?>
				<a href="javascript:void(0)" class="writereview open_slide" title="Login to write a review.">Write a review</a>
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
        <img src="{{url('storage/size_charts/'.$sizeChartImg)}}" alt="sizechart" />
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

		
<!-- <script type="text/javascript" src="{{url('public')}}/bootstrap/js/bootstrap.min.js"></script> -->
<script type="text/javascript" src="{{url('/')}}/js/owl.carousel.min.js"></script> 
 <link rel="stylesheet" type="text/css" href="{{url('/')}}/css/imagelightbox.css" media="screen">
 <script src="{{url('/')}}/js/imagelightbox.js"></script>
                <script src="{{url('/')}}/js/main.js"></script>
	
<script>

	$(".writeReviewBtn").click(function(){
		$("#reviewpopup").modal("show");
	});

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


$(window).resize(function () {
    var widthWindow = $(window).width();
    if (widthWindow <= '767') {
    	$(".detailslide").addClass("owl-carousel");
       $('.detailslide').owlCarousel({
	loop:true,
	margin:10,
	items:2,
	dots:true,
	nav:true,
	responsive:{
		0:{
			items:1.3,
			nav:true
		},
		768:{
			items:1.4
		} 
	}
});
    }
    else
    {
        $(".detailslide").removeClass("owl-carousel");
    }
});
$(window).trigger('resize');


// $('.detailslide').owlCarousel({
// 	loop:true,
// 	margin:20,
// 	items:1.3,
// 	dots:true,
// 	nav:true,
// 	responsive:{
// 		0:{
// 			items:1
// 		},
// 		768:{
// 			items:1.4
// 		} 
// 	}
// });

	
$('.similarslider').owlCarousel({
		loop:false,
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

$(document).on("submit", "form[name=pincodeForm]", function(e){
	e.preventDefault();
	$("#checkPincode").click();
});

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
					$('#field_wrap').show();
					$(".pincodeAlert").html('<div class="alert alert-success alert-dismissible" > <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a> We ship in this location.</div>');
					$("#field1").html(resp.field1);
					$("#field2").html(resp.field2);
					$("#field3").html(resp.field3);
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
				if(resp.error){
					showAlert(resp.error);
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

$(document).on("click", ".notifySize", function(){

	var conf = confirm("Do you want to get notification by email on availablity of this product/size?");

	if(!conf){
		return false;
	}

	var currSel = $(this);

	var slug = $(this).data("slug");
	var size = $(this).data("size");

	var _token = '{{ csrf_token() }}';

	$.ajax({
		url: "{{ url('users/notify_product_size') }}",
		type: "POST",
		data: {slug:slug, size:size},
		dataType:"JSON",
		headers:{'X-CSRF-TOKEN': _token},
		cache: false,
		beforeSend:function(){

		},
		success: function(resp){
			if(resp.success){
				showAlert("Your request has been submitted.");
			}
			else if(resp.message && resp.message != ""){
				showAlert(resp.message);
			}

		}
	});

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
	reviewsForm.find(".form-group").removeClass( "has-error" );
	reviewsForm.find(".help-block").remove();

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
<script type="text/javascript">
	 $('.panel-collapse').on('show.bs.collapse', function () {
    $(this).siblings('.panel-heading').addClass('active');
  });

  $('.panel-collapse').on('hide.bs.collapse', function () {
    $(this).siblings('.panel-heading').removeClass('active');
  });
</script>

</body>
</html>
