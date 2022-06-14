<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo (isset($meta_title))?$meta_title:'SlumberJill'?></title>
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
				if(!empty($wishlistProducts) && count($wishlistProducts) > 0){

					$storage = Storage::disk('public');

					$img_path = 'products/';

					foreach($wishlistProducts as $product){
						$product_image = (isset($product->defaultImage))?$product->defaultImage:'';
						$reverse_image = (isset($product->reverseImage))?$product->reverseImage:'';

						$price = $product->price;
						$salePrice = $product->sale_price;

						$productPrice = $price;
						if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
							$productPrice = $product->sale_price;
						}
						else{
							$salePrice = $product->price;
						}

						?>
						<li>
							<div class="removelist"><a href="javascript:void(0)" data-pid="{{$product->id}}" class="delWishlistItem"><i class="deleteicon"></i></a> </div>
							<div class="product">

								<div class="flip-inner">
									<img src="http://johnpride.ii71.com/public/images/blank.png" alt="img">

									<?php
									if(!empty($product_image->image) && $storage->exists($img_path.$product_image->image)){
										?>
										<div class="flip-front">
											<img src="{{url('public/storage/'.$img_path.$product_image->image)}}" alt="{{$product->name}}" />
										</div>
										<?php
									}

									if(!empty($reverse_image->image) && $storage->exists($img_path.$reverse_image->image)){
										?>
										<div class="flip-back">
											<img src="{{url('public/storage/'.$img_path.$reverse_image->image)}}" alt="{{$product->name}}" />
										</div>
										<?php
									}
									?>

								</div>
							</div>
							<div class="procont">
								<p><span> {{$product->name}} </span>
								</p>
								<p> {{$product->name}} </p>
								<p><strong>Just</strong><small>&#x20B9;{{ $productPrice }}</small>
									<?php
									if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
										?>
										<del>&#x20B9;{{$price}}</del> 
										<?php
									}
									?>
								</p>
								<p><a class="movetobag" href="{{url('products/details/'.$product->slug)}}">MOVE TO BAG</a></p>
							</div>
						</li>
						<?php
					}
				}
				else{
					?>					
					<h3>Currently, you do not have any item(s) in your wishlist.</h3>
					<p><a href="{{url('products')}}">click here to browse our collection.</a></p>		
					<?php
				}
				?>
				

			</ul>
		</div>
	</section>


	@include('common.footer')

	<script type="text/javascript">

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
	</script>

</body>
</html>