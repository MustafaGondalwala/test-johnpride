<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo (isset($meta_title))?$meta_title:'Johnpride'?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/>

	@include('common.head')

</head>

<body>

	@include('common.header')
	<section class="fullwidth innerpage">
		<div class="container">
			@include('users.nav')

			<div class="rightcontent">
				<div class="main_inner_box">
					<div class="heading2">My Orders</div>

					<?php //prd($orders); ?>


					<div class="ordersec ">
						<ul>

							<?php
							if(!empty($orders) && count($orders) > 0){ 
								foreach ($orders as $order){ 

									$orderItems = isset($order->orderItems) ? $order->orderItems:'';
									//echo "<pre>";print_r($orderItems);

									$orderStatusDetails = isset($order->orderStatusDetails) ? $order->orderStatusDetails:'';
									$orderStatusName = (isset($orderStatusDetails->name))?$orderStatusDetails->name:'';
									$noOfItem = $orderItems->count();

									?>

									<?php
									if(!empty($orderItems) && count($orderItems) > 0){

										//prd($orderItems->toArray());
										?>
										<li>
											<div class="orderlist fullwidth">
												<p><span><strong>{{$orderStatusName}}</strong>Order No: {{$order->order_no}}</span></p>
												<p>Placed on: <?php echo CustomHelper::DateFormat($order->created_at, $toFormat='M d, Y', $fromFormat=''); ?> / ₹{{number_format($order->total)}} / {{$noOfItem}} Item(s) </p>
												
												<div class="orderdetail">Detail</div>
												
												<?php
												/*
												<div class="orderdetail"><a href="{{url('users/orders/'.$order->order_no)}}">View Details</a></div>
												*/
												?>
											</div>


											<div class="detailbox fullwidth" id="">
												<ul class="cartlist">

													<?php 
													$storage = Storage::disk('public');
													$img_path = 'products/';
													$thumb_path = $img_path.'thumb/';

													foreach ($orderItems as $item) {	

														$product = isset($item->productDetail) ? $item->productDetail:'';

														$defaultImage = isset($product->defaultImage) ? $product->defaultImage:'';
														$productBrand = isset($product->productBrand) ? $product->productBrand:'';
														$sale_price = isset($product->sale_price) ? $product->sale_price:'';

														$brandName = '';
														if(!empty($productBrand) && count($productBrand) > 0){
															$brandName = isset($productBrand->name) ? $productBrand->name:'';
														}


														if(!empty($defaultImage) && count($defaultImage) > 0){
															if(!empty($defaultImage->image) && $storage->exists($thumb_path.$defaultImage->image) ){
																$imgUrl = url('public/storage/'.$thumb_path.$defaultImage->image);
															}
														}

														$subOrderStatusDetails = isset($item->subOrderStatusDetails) ? $item->subOrderStatusDetails:'';
														//pr($subOrderStatusDetails);
														$orderStatusName = (isset($subOrderStatusDetails->name))?$subOrderStatusDetails->name:'';

														?>
														<li>
															<div class="col-md-3">
															<p>
																<span><strong>{{$orderStatusName}}
																</strong>
															</span>
															</p>
															<p>
															<span>
																Sub Order No: {{isset($item->sub_order_no) ? $item->sub_order_no:''}}</span>
															</p>
															<p>
																Total: ₹{{number_format($item->total)}}  
															</p>
															</div>
															<div class="col-md-9">
															<?php
															if(!empty($imgUrl)){
																?>
																<div class="cartimg">
																	<img src="{{$imgUrl}}">
																</div>
																<?php
															}
															?>

															<div class="procont">
																<div class="titles">
																	<p><span>{{$brandName}}</span></p>
																	<p>{{isset($product->name) ? $product->name:''}}</p>
																</div>
																<div class="cartprice"><span>₹<?php if(!empty($sale_price)){ echo number_format($sale_price); } ?></span></div>

																<div class="sizeqty">
																	<div class="size">SIZE : {{isset($item->size_name) ? $item->size_name:''}}</div>
																	<div class="qty">QTY : {{isset($item->qty) ? $item->qty:''}}</div>
																</div>

															</div>


															<div class="orderdetail"><a href="{{url('users/orders/'.$item->sub_order_no)}}">View Details</a></div>

															</div>
														</li>
														<?php
													}
													?>

												</ul>
											</div>
										</li>
										<?php
									} 
								} 
							}
							?>

						</ul>
					</div>
				</div> 
			</div>
		</div>
	</section>


	@include('common.footer')
	
	<script>

		$(".orderdetail").click(function(){	
			$(this).parent().next().slideToggle();
		});
	</script>

</body>
</html>