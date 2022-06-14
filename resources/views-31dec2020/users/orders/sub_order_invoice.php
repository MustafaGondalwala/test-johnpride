<?php
if(!empty($subOrder) && $subOrder->count() > 0){


	$orderStatusDetails = $subOrder->subOrderStatusDetails;
	//prd($$orderStatusDetails->toArray());

	$orderStatus = (isset($orderStatusDetails->name))?$orderStatusDetails->name:'';

	$invoice_date = (isset($subOrder->invoice_date))?$subOrder->invoice_date:'';
	$added_on = CustomHelper::DateFormat($subOrder->invoice_date, 'd F y');

	

	$subTotal = $subOrder->sub_total;
	$tax = $subOrder->tax;
	$shippingCharge = $subOrder->shipping_charge;
	$discount = $subOrder->discount;
	$couponDiscount = $subOrder->coupon_discount;
	$total = $subOrder->total;
	$paymentMethod = $order->payment_method;

	$billingCityName = '';
	$billingStateName = '';
	$billingCountryName = '';


	$billingCity = $order->billingCity;
	$billingState = $order->billingState;
	$billingCountry = $order->billingCountry;

	if(isset($billingCity->name) && !empty($billingCity->name)){
		$billingCityName = $billingCity->name;
	}
	if(isset($billingState->name) && !empty($billingState->name)){
		$billingStateName = $billingState->name;
	}
	if(isset($billingCountry->name) && !empty($billingCountry->name)){
		$billingCountryName = $billingCountry->name;
	}


	$shippingCityName = '';
	$shippingStateName = '';
	$shippingCountryName = '';


	$shippingCity = $order->shippingCity;
	$shippingState = $order->shippingState;
	$shippingCountry = $order->shippingCountry;
	$invoiceNo = (isset($order->invoice_no))?$order->invoice_no:'';

	if(isset($shippingCity->name) && !empty($shippingCity->name)){
		$shippingCityName = $shippingCity->name;
	}
	if(isset($shippingState->name) && !empty($shippingState->name)){
		$shippingStateName = $shippingState->name;
	}
	if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
		$shippingCountryName = $shippingCountry->name;
	}

	$orderStatusArr = config('custom.order_status_arr');

	$billingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=true, $isPhone=true, $isEmail=true);
	$shippingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=false, $isPhone=true, $isEmail=true);
	$paymentMethod = $order->payment_method;
	//pr($shippingAddrArr);

	?> 


	<div style="margin: 0 auto; max-width: 1000px; display:block; background:#fff; font-size: 13px;">
	<tr>
		<td>
		 
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td colspan="2" style="vertical-align: bottom;">
					 
					<img src="{{url('public')}}/images/logo.png" alt="Johnpride" width="186" height="31" /><br><br> 
					<strong style="font-size: 16px;">Invoice No.</strong> {{$invoiceNo}} 
				</td> 
				<td style="text-align: center; vertical-align: bottom;"><strong style="font-size: 16px;">Invoice Date.</strong>{{$added_on}} </td>
				<td colspan="2" style="text-align: right; ">
					<strong>John Pride</strong><br>
					India<br>
					Ph No: +91-9599969498,  GSTIN:XXXXXXXXXXX<br>
				</td> 
			</tr>

			<tr>
				<td colspan="5" style="height:2px;"></td>
			</tr>

			 <tr>
				<td style="border-left:1px solid #ccc; border-top:1px solid #ccc; border-bottom:1px solid #ccc;padding:0 5px;"><strong>Order Id :</strong> <br>{{$subOrder->sub_order_no}}</td>
				<td style="border-left:1px solid #ccc; border-top:1px solid #ccc; border-bottom:1px solid #ccc;padding:0 5px;"><strong>Added on:</strong> <br>{{$added_on}}</td>
				<!-- <td style="border-left:1px solid #ccc; border-top:1px solid #ccc; border-bottom:1px solid #ccc;padding:0 5px;"><strong>Order Status:</strong> <br>{{$orderStatus}}</td> -->
				<td style="border-left:1px solid #ccc; border-top:1px solid #ccc; border-bottom:1px solid #ccc;padding:0 5px;"><strong>Mode of Payment:</strong> <br><?php
					if(!empty($paymentMethod)){
						echo strtoupper($paymentMethod);
					}
					?></td>
				<td style="border:1px solid #ccc;  padding:10px;"><strong>Payment Status:</strong> <br>{{ucfirst($order->payment_status)}}</td>
			</tr> 

					 <tr>		 
								<td colspan="3" style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;border-left:1px solid #ccc;  padding:5px;">
									<div style="font-size:16px; font-weight: bold;">Billing Address</div>

									<?php echo '<p style="margin-bottom:5px;">'.implode('</p><p style="margin-bottom:5px;">', $billingAddrArr).'</p>'; ?>

									
				</td>


				 
				<td colspan="2" style="border-bottom:1px solid #ccc;border-left:1px solid #ccc;border-top:1px solid #ccc;border-right:1px solid #ccc; padding:5px;">
					<div style="font-size: 16px; font-weight: bold;">Shipping Address</div>

					<?php echo '<p style="margin-bottom:5px;">'.implode('</p><p style="margin-bottom:5px;">', $shippingAddrArr).'</p>'; ?>					
					
				</td> 
			</tr>


			</table>
		</td>
	

			<?php
			if(!empty($subOrder) && count($subOrder) > 0){
				?>
				<td>

				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td style="padding-top: 20px;">
								<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered">
									<tbody>
										<tr> 
											<th>Product Detail</th>
											<th>Taxable Price </th>
											<th>Without Taxable Price</th>
											<th>GST(%)</th>
											<th>GST Amount</th>
											<th>Qty</th>                              
											<th style="width: 80px;">Sub Total <!-- (<i class="fa fa-inr"></i>) --> </th>
										</tr>
										<?php
										
											$product_id = $subOrder->product_id;

											$product = $subOrder->productDetail;

                							//prd($product->toArray());

											$qty = $subOrder->qty;

											$sizeId = $subOrder->size_id;
											$sizeName = $subOrder->size_name;
											$clrName = $subOrder->color_name;

                							//pr($subOrder->toArray());

											$price = $product->price;
											$sale_price = $product->sale_price;

											$item_price = $subOrder->item_price;
											$gst = $subOrder->gst;

											$productBrand = $product->productBrand;

											$defaultImage = $product->defaultImage;
											$productImages = $product->productImages;

											$imgUrl = '';

											if(!empty($defaultImage) && count($defaultImage) > 0){
												if(!empty($defaultImage->image) ){
													$imgUrl = $defaultImage->image;
												}
											}

											if(empty($imgUrl)){
												if(!empty($productImages) && count($productImages) > 0){
													foreach($productImages as $prodImg){
														if(!empty($prodImg->image) ){
															$imgUrl = $prodImg->image;
															break;
														}
													}
												}
											}

											$brandName = '';

											if(!empty($productBrand) && count($productBrand) > 0){
												$brandName = $productBrand->name;
											}

											$inrImgIconUrl = url('public/images/inr-icon.png');
											$inrImgIcon = '<img src="'.$inrImgIconUrl.'">';

											?> 

											<tr>
												<?php /* <td>
													<?php
													if(!empty($imgUrl)){
														?>
														<img src="{{$imgUrl}}" alt="{{$product->name}}" align="products" width="50" height="50">
														<?php
													}
													?>
												</td> */ ?>
												<td>{{$product->name}}- Size: {{$sizeName}}<br>SKU: {{$product->sku}}</td>

													<?php


                  									//if($sale_price > 0 && $sale_price < $price){
													$discount = 0;
													$priceWithoutGst = 0;
													$withOutGstP = 0;

													$priceWithoutGst = CustomHelper::priceWithoutGst($item_price, $gst);
													$withOutGstP = $item_price - $priceWithoutGst;
                    								//pr($priceWithoutGst);

													$totalPrice = $price*$item->qty;
													$totalSaleprice = $item_price*$item->qty;

													$discountAmt = $totalPrice - $totalSaleprice;
                    								//}
													?>

													<td><?php echo $inrImgIcon;?> {{number_format($item_price)}}</td>
													<td><?php echo $inrImgIcon;?> {{number_format($priceWithoutGst)}}</td>
													<td>{{number_format($gst)}}</td>
													<td align="center"><?php echo $inrImgIcon;?>{{number_format($withOutGstP)}}</td>
													<td>{{$qty}}</td>
													<td><?php echo $inrImgIcon;?> {{number_format($totalSaleprice)}}</td>
												</tr>
												<?php } ?>

												<tr>
													<td align="right" colspan="6"><strong>Sub Total</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($subTotal)}}</td>
												</tr>       
												<tr>                             
													<td align="right" colspan="6"><strong>GST (included in Total)</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($tax)}}</td>
												</tr>
												<tr>
													<td align="right" colspan="6"><strong>Shipping Charge</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i>
														<?php
														if(is_numeric($shippingCharge) && $shippingCharge > 0){
															echo $inrImgIcon.' '.number_format($shippingCharge);
														}
														else{
															echo 'Free';
														}
														?>
													</td>
												</tr>
												<tr>
													<td align="right" colspan="6"><strong>Order Total</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i> <?php echo $inrImgIcon;?> {{number_format($total)}}</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>

						<?php } ?>
					</td>
				</tr>
			</div>
					<?php
				}
	?>