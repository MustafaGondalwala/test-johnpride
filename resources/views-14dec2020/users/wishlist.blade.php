<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo (isset($meta_title))?$meta_title:'Johnpride'?></title>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/> @include('common.head')

</head>

<body>

	@include('common.header')

	<section class="fullwidth innerpage wishlistsec">
		<div class="container">

			@include('snippets.front.flash')

			<ul class="listpro wishlisting">

				<?php
				if(!empty($userWishlist) && count($userWishlist) > 0){

					//prd($userWishlist);

					$storage = Storage::disk('public');

					$img_path = 'products/';

					foreach($userWishlist as $wishlist){
						$product = (isset($wishlist->product))?$wishlist->product:'';
						$size = (isset($wishlist->size))?$wishlist->size:'';

						$sizeName = (isset($size->name))?$size->name:'';

						//prd($sizeName);

						$product_image = (isset($product->defaultImage)) ? $product->defaultImage : '';
						$reverse_image = (isset($product->reverseImage)) ? $product->reverseImage : '';

						//$size = $product->size

						$price = $product->price;
						$salePrice = $product->sale_price;

						$productPrice = $price;
						if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
							$productPrice = $product->sale_price;
						}
						else{
							$salePrice = $product->price;
						}

						$productTotalStock = 0;

						$productInventorySize = (isset($product->productInventorySize))?$product->productInventorySize:'';

						if(!empty($productInventorySize) && count($productInventorySize) > 0){
							//prd($productInventorySize->toArray());

							$productTotalStock = $productInventorySize->sum('pivot.stock');
						}

						$productUrl = url('products/details/'.$product->slug);


						?>
						<li class="listItem">
							<div class="removelist"><a href="javascript:void(0)" data-pid="{{$product->id}}" class="delWishlistItem"><i class="deleteicon"></i></a> </div>
							<div class="product">

								<?php
								if(!empty($product_image) && count($product_image) > 0){

									$img_path = 'products/';
									if(!empty($product_image->image)){
										$mainImg = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $product_image->image);
										?>      
										<div class="flip-inner">
											<img src="{{url('/')}}/images/blank.png" alt="{{$product->name}}"/>

											<div class="flip-front">
												<img src="{{$mainImg}}" alt="{{$product->name}}" />
											</div>  

											<?php
											if(!empty($reverse_image->image)){
												$revImg = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $reverse_image->image);
												?>
												<div class="flip-back">
													<img src="{{$revImg}}" alt="{{$product->name}}" />
												</div>  
												<?php
											}
											?>   

										</div>
										<?php
									}
								}
								?>
							<div class="procont">
								<div class="selectsize sizeBox{{$product->id}}" style="display: none;">
									<div>
										<span class="head">SELECT SIZE</span>
										<small class="closebtn closeSizeBox" data-id="{{$product->id}}">X</small>
										<p class="sizeErr" style="color:#f16565;"></p>
									</div>

									<?php
									if(!empty($productInventorySize) && count($productInventorySize) > 0){

										$productInventorySizeArr = $productInventorySize->sortBy('sort_order');
										foreach($productInventorySizeArr as $inventorySize){

												if($inventorySize->pivot->stock > 0){
													?>
													<label>

														<input type="checkbox" name="size[]" value="{{$inventorySize->id}}" class="prodSize size{{$product->id}}"> <span>{{$inventorySize->name}}</span>

													</label>
													<?php
												}
								/*else{
									?>
									<label>
										<a href="javascript:void(0)" class="notifySize out-stock" data-slug="{{$product->slug}}" data-size="{{$inventorySize->id}}"><span>{{$inventorySize->name}}</span></a>
									</label>
									<?php
								}*/
								?>
							
							<?php
						}
					}
					?>

					<?php
								/*
								<label>
								<input type="checkbox" name="size[]" value="6" class="prodSize"> <span>XS</span>
								</label>

								<label>
								<a href="javascript:void(0)" class="notifySize out-stock" data-slug="printed-top-with-cutaway-shoulders" data-size="5"><span>S</span></a>
								</label>

								<label>
								<input type="checkbox" name="size[]" value="4" class="prodSize"> <span>XL</span>
								</label>

								<label>
								<input type="checkbox" name="size[]" value="2" class="prodSize"> <span>M</span>
								</label>
								*/
								?>



							</div>

							<p>
								<span>
									<a href="{{$productUrl}}">
										{{$product->name}}{{(!empty($sizeName)?'('.$sizeName.')':'')}}
									</a>

								</span>
							</p>
							<p> {{$product->name}} </p>
							<p><strong>Just</strong><small>&#x20B9;{{ number_format($productPrice) }}</small>
								<?php
								if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
									?>
									<del>&#x20B9;{{number_format($price)}}</del> 
									<?php
								}
								?>
							</p>
							<?php
							if($productTotalStock > 0){
								?>
								<p><a class="movetobag sizeshow" data-id="{{$product->id}}">MOVE TO BAG</a></p>
								<p>

									<a href="javascript:void(0)" data-slug="{{$product->slug}}" data-id="{{$product->id}}" class="movetobag doneBtn done{{$product->id}}" style="display:none;" >Done</a>
									<?php
									/*if(isset($size->id) && $size->id > 0){
										?>
										<a href="javascript:void(0)" data-slug="{{$product->slug}}" data-sid="{{$size->id}}" class="movetobag moveToBag moveToBag{{$product->id}}" >MOVE TO BAG</a>
										<?php
									}
									else{
										?>
										<a href="{{url('products/details/'.$product->slug)}}" class="movetobag moveToBagDone{{$product->id}}" style="display:none;" >DONE</a>
										<?php
									}*/
									?>
								</p>

								<?php
							}
							else{
								?>
								<a href="javascript:void(0)" class="movetobag" >Out of Stock</a>
								<?php
							}
							?>
						</div>
						</li>
						<?php
					}
				}
				else{
					?>					
					<div class="cartempt nowishlist ">
						<p><span><i class="wishlisticon"></i></span></p>
				<p>Currently, you do not have any item(s) in your wishlist.</p>
					<p><a href="{{url('products')}}" class="socialbtn">ADD ITEMS</a></p>
				</div>		
					<?php
				}
				?>
				

			</ul>
		</div>
	</section>


	<div class="addedto " style="display:none;" ></div>


	@include('common.footer')

	<script type="text/javascript">
		
		
 	
/*$( ".closebtn" ).click(function() {
	$('.selectsize').slideUp();
});*/

$( ".closeSizeBox" ).click(function() {
	var productId = $(this).data("id");

	$('.sizeBox'+productId).slideUp();
	$('.done'+productId).slideUp();
	$('.sizeshow').slideDown();
});

$( ".sizeshow" ).click(function() {
	//$('.selectsize').slideDown();

	var productId = $(this).data("id");

	$(this).hide();
	$('.done'+productId).slideDown();
	$('.sizeBox'+productId).slideDown();

}); 
	
		
		
		
		

		$(".delWishlistItem").on("click", function(){
			var currSel = $(this);

			var conf = confirm("Are you sure you want to delete this Item?");

			if(conf){

				var productId = currSel.data("pid");

				var _token = '{{ csrf_token() }}';

				$.ajax({
					url: "{{ url('users/delete_from_wishlist') }}",
					type: "POST",
					data: {productId:productId},
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

		});

		$(document).on("click", ".prodSize", function(){
			$(".prodSize").not($(this)).attr("checked", false);
		});


		$(document).on("click", ".doneBtn", function(){

			var currSel = $(this);

			var prodId = currSel.data("id");
			var slug = currSel.data("slug");
			//var size = currSel.data("sid");
			var size = $(".size"+prodId+":checked").val();

			//alert(size); return false;

			size = parseInt(size);

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

					},
					success: function(resp){
						if(resp.success){

							currSel.parents(".listItem").remove();

							var countItem = $(".listItem").length;
							if(countItem > 0){
								var countItem = $(".listItem").length;
								$("#cart_count").text(resp.cartCount);

								showAlert("Added to Bag");
							}
							else{
								window.location.reload();
							}
						}

					}
				});

			}
			else{
				$(".sizeErr").text("Please select a size");
			}
		});

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