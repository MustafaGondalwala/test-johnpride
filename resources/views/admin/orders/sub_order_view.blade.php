@component('admin.layouts.main')

@slot('title')
Admin - Manage Customers - {{ config('app.name') }}
@endslot


<?php
$back_url = (request()->has('back_url'))?request('back_url'):'';

$image_path = config('custom.image_path');
?>

<style type="text/css">
@media print {
   .noPrint {display:none;}
   /*.hide-on-screen {display:block;}*/
}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="titlehead">
			<h1 class="pull-left">Orders View </h1> 

			<?php
			if(!empty($back_url)){
				?>
				<a href="{{ url($back_url) }}" class="btn btn-sm btn-success pull-right">Back</a>
				<?php
			}
			?>

			<!-- <button onclick="myFunction()" class="noPrint btn btn-sm btn-success pull-right">Print Invoice</button> -->

			<a href="javascript:void(0)" data-id="{{ $subOrder->id }}" class="print_invoice noPrint btn btn-sm btn-success pull-right">Print Invoice</a>
		</div>
	</div>
</div>



@include('snippets.errors')
@include('snippets.flash')



<?php
if(!empty($subOrder) && $subOrder->count() > 0){


	//$orderStatusDetails = $order->orderStatusDetails;
	$subOrderStatusDetails = $subOrder->subOrderStatusDetails;
	//prd($$orderStatusDetails->toArray());

	$orderStatus = (isset($subOrderStatusDetails->name))?$subOrderStatusDetails->name:'';

	$added_on = CustomHelper::DateFormat($order->created_at, 'd F y h:i');

	

	$subTotal = $subOrder->sub_total;
	$tax = $subOrder->tax;
	$shippingCharge = $subOrder->shipping_charge;
	$discount = $subOrder->discount;
	$couponDiscount = $subOrder->coupon_discount;
	$loyaltyDiscount = $subOrder->loyalty_discount;
	$total = $subOrder->total;
	$paymentMethod = $order->payment_method;
	$used_wallet_amount = $order->used_wallet_amount;
	//prd($used_wallet_amount);

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


	$paymentMethod = $order->payment_method;


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
	unset($orderStatusArr['partially_cancelled']);

	//pr($orderStatusArr);

	$billingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=true, $isPhone=true, $isEmail=true);
	$shippingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=false, $isPhone=true, $isEmail=true);

	

	?> 
	<div class="table-responsive ordership orders_view">
		<div class="orderheading">
			<div class="col-sm-4">
				<label>Sub Order No :</label><br>{{$subOrder->sub_order_no}}
			</div>
			<div class="col-sm-8">
				<label>Added on:</label><br>{{$added_on}}
			</div>
			<div class="col-sm-8">
				<label>Order Status:</label><br>{{$orderStatus}}</div>

				<div class="col-sm-4">
					<label>Mode of Payment :</label> <br>
					<?php
					if(!empty($paymentMethod)){
						echo strtoupper($paymentMethod);
					}
					?>
				</div>
				<div class="col-sm-8">
					<label>Payment Status:</label><br>{{ucfirst($order->payment_status)}}
				</div>
			</div>
			<?php
			$reasonArr = [];
			if($subOrder->order_status == 'cancelled'){
				$reasonArr = config('custom.reason_order_cancel_arr');
			}
			elseif($subOrder->order_status == 'return'){
				$reasonArr = config('custom.reason_order_return_arr');
			}

			if(!empty($subOrder->reason) && ($subOrder->order_status == 'cancelled' || $subOrder->order_status == 'return')){
				?>
				<div class="col-sm-6">
					<br>
					<label>Reason:</label> {{$reasonArr[$subOrder->reason]}}
				</div>
				<?php } 

				if(!empty($subOrder->refund_mode)){
					?>
					<div class="col-sm-6">
						<br>
						<label>Refund Mode:</label> {{$subOrder->refund_mode}}
					</div>
					<?php } ?>


					<?php
					if(!empty($subOrder->reason_comment)){
						?>
						<div class="col-sm-6">
							<br>
							<label>Remark:</label> {{$subOrder->reason_comment}}
						</div>
						<?php }

						if(!empty($subOrder->bank_details)){
							?>
							<div class="col-sm-6">
								<br>
								<label>Bank Detail:</label> {{$subOrder->bank_details}}
							</div>
							<?php }
							?>

							<div class="clearfix"></div>

							<div class="row"> 
								<div class="col-md-6 form-group addressfilds">
									<h4><strong>Billing Address</strong></h4>

									<?php echo '<p>'.implode('</p><p>', $billingAddrArr).'</p>'; ?>
				</div>


				<div class="col-md-6 form-group addressfilds">
					<h4><strong>Shipping Address</strong></h4>

					<?php echo '<p>'.implode('</p><p>', $shippingAddrArr).'</p>'; ?>
				</div>


			</div>

			<?php
			if(!empty($subOrder) && count($subOrder) > 0){
				?>
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td style="padding-top: 20px;">
								<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered">
									<tbody>
										<tr>
											<th>Product Image</th>
											<th>Product Detail</th>
											<th>Taxable Price </th>
											<th>Without Taxable Price</th>
											<th>GST(%)</th>
											<th>GST Amount</th>
											<th>Qty</th>                              
											<th>Sub Total <!-- (<i class="fa fa-inr"></i>) --> </th>
										</tr>
										<?php
										
											$product_id = $subOrder->product_id;

											$product = $subOrder->productDetail;

                							//prd($subOrder->order);

											$qty = $subOrder->qty;

											$sizeId = $subOrder->size_id;
											$sizeName = $subOrder->size_name;
											$clrName = $subOrder->color_name;

                							//pr($item->toArray());

											$price = $product->price;
											$sale_price = $product->sale_price;

											$item_price = $subOrder->item_price;
											$gst = $subOrder->gst;

											$productBrand = $product->productBrand;

											$defaultImage = $product->defaultImage;
											$productImages = $product->productImages;

											$couponDiscount = isset($subOrder->coupon_discount) ? $subOrder->coupon_discount:'';

											$loyaltyDiscount = isset($subOrder->loyalty_discount) ? $subOrder->loyalty_discount:'';

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

											$inrImgIconUrl = url('images/inr-icon.png');
											$inrImgIcon = '<img src="'.$inrImgIconUrl.'">';

											?> 

											<tr>
												<td>
													<?php
													if(!empty($imgUrl)){
														?>
														<img src="{{$image_path.$imgUrl}}" alt="{{$product->name}}" align="products" width="50" height="50">
														<?php
													}
													?>
												</td>
												<td>{{$product->name}}<br>SKU: {{$product->sku}}
													<br>Size: {{$sizeName}}</td>

													<?php


                  									//if($sale_price > 0 && $sale_price < $price){
													$discount = 0;
													$priceWithoutGst = 0;
													$withOutGstP = 0;

													$priceWithoutGst = CustomHelper::priceWithoutGst($item_price, $gst);
													$withOutGstP = $item_price - $priceWithoutGst;
                    								//pr($priceWithoutGst);

													$totalPrice = $price*$subOrder->qty;
													$totalSaleprice = $item_price*$subOrder->qty;

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
												

												<tr>
													<td align="right" colspan="7"><strong>Sub Total</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($subTotal)}}</td>
												</tr>       
												<tr>
													<td align="right" colspan="7"><strong>GST (included in Total)</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($tax)}}</td>
												</tr>

												<?php
												if(!empty($couponDiscount) && $couponDiscount > 0){
												?>
												<tr>
													<td align="right" colspan="7"><strong>Coupon Discount</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($couponDiscount)}}</td>
												</tr>
												<?php
												}
												?>							<?php
												if(!empty($loyaltyDiscount) && $loyaltyDiscount > 0){
												?>
												<tr>
													<td align="right" colspan="7"><strong>Loyalty Discount</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($loyaltyDiscount)}}</td>
												</tr>
												<?php
												}
												?>

		



												<tr>
													<td align="right" colspan="7"><strong>Shipping Charge</strong></td>
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
													<td align="right" colspan="7"><strong>Order Total</strong></td>
													<td colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($total)}}</td>
												</tr>


	  <?php 
           // $used_wallet_amount = $order->used_wallet_amount;


         $used_wallet_amount_percentage =  $order->used_wallet_amount / ($order->total) * 100;

		  $used_wallet_amount = ($used_wallet_amount_percentage / 100) * $total;	

             if(is_numeric($used_wallet_amount) && $used_wallet_amount > 0){

              ?>


              <tr>

                <td  align="right" colspan="7"><strong>Used Wallet Amount</strong></td>

                <td style="border-bottom:1px solid #ccc; padding:5px;" colspan="1"> <?php echo $inrImgIcon;?> 

                  <?php echo number_format($used_wallet_amount); ?>

                </td>

              </tr>


               <tr>
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>Total Payable Amount</strong></td>

                <td style="border-bottom:1px solid #ccc; padding:5px;" colspan="1">  <?php echo $inrImgIcon;?> 
                 <?php echo number_format($total-$used_wallet_amount); ?>
                </td>

              </tr>

              <?php 
            }
            ?>



											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>

						<?php } ?>
					</div>
					<?php
				}


	?>


	<div class="row noPrint">
		<div class="col-md-12">
			<h3>Change Order Status </h3>
		</div>
	</div>



	<form method="POST" action="" accept-charset="UTF-8"  role="form">
		{{ csrf_field() }}

		<div class="row noPrint">

			<table class="table table-striped">
				<tr>
					<th>Payment Status</th>
					<th>Order Status</th>
					<th>Comment *</th>
				</tr>

				<tr>
					<td>
						<select class="form-control" name="payment_status">
							<option value="">Please Select</option>
							<option value="pending" <?php if($order->payment_status == 'pending') { echo 'selected'; } ?> >Pending</option>
							<option value="received" <?php if($order->payment_status == 'received') { echo 'selected'; } ?> >Received</option>

							<option value="partially refunded" <?php if($order->payment_status == 'partially refunded') { echo 'selected'; } ?> >Partially refunded</option>

							<option value="COD – no partial refund required" <?php if($order->payment_status == 'COD – no partial refund required') { echo 'selected'; } ?> >COD – no partial refund required</option>

						</select>
					</td>
					
					<td> 
						<?php
						//pr($orderStatusArr);
						?>
						<select class="form-control" name="order_status">

						<?php
						if(!empty($orderStatusArr) && count($orderStatusArr) > 0){
							foreach($orderStatusArr as $osKey=>$osVal){

								$selected = '';
								if($subOrder->order_status == $osKey){
									$selected = 'selected';
								}
								?>
								<option value="{{$osKey}}" {{$selected}} >{{$osVal}}</option>

								<?php
							}
						}
						?>

					</select></td>

					<td class="{{ $errors->has('comment') ? ' has-error' : '' }}">
						<textarea class="form-control" name="comment"></textarea>
						@include('snippets.errors_first', ['param' => 'comment'])
					</td>


				</th>
			</tr>
			<tr>
				<td>
					<input type="submit" name="change_order_status" value="Save" class="btn btn-sm btn-success" >
				</td>
			</tr>

		</table>

	</div>

</form>

<div class="row noPrint">
	<div class="col-md-12">
		<h3>Order History </h3>

		<?php
		if(!empty($orderHistory) && $orderHistory->count() >0 ){			

			?>

			<table class="table table-striped">
				<tr>
					<th>Order Status</th>
					<th>Comment</th>
					<th>Added On</th>
				</tr>

				<?php
				foreach($orderHistory as $oh){
					$added_on = CustomHelper::DateFormat($oh->created_at, 'd F y g:i:s A');
					?> 
					<tr>
						<td>
							<?php
							if(isset($orderStatusArr[$oh->order_status])){
								echo $orderStatusArr[$oh->order_status];
							}
							?>

						</td>
						<td>{{$oh->comment}}</td>
						<td>{{ $added_on}}</td>

					</tr>


					<?php
				}
				?>
			</table>
			<?php
		}
		else echo 'No history found';
		?>

		</div>
	</div>


@slot('bottomBlock')

<script type="text/javascript" src="{{ url('js/jquery-ui.js') }}"></script>

<script>
/*function myFunction() {
  window.print();
}*/

$(document).ready(function(){

        $(".print_invoice").click(function(){

            var current_sel = $(this);

            var order_id = $(this).attr('data-id');

            //alert(order_id); return false;

                var _token = '{{ csrf_token() }}';
			setTimeout(() => {
				
			
                $.ajax({
                    url: "{{ route('admin.orders.ajax_print_invoice') }}",
                    type: "POST",
                    data: {order_id:order_id},
                    dataType:"JSON",
                    headers:{'X-CSRF-TOKEN': _token},
                    cache: false,
                    beforeSend:function(){
                       //$(".ajax_msg").html("");
                   },
                   success: function(resp){
						if(resp.success){
							
							printdiv(resp.viewHtml);

						}
						else{
							
						}

					}
				});
			}, 1000);

        });

    });


function printdiv(printpage){

	var headstr = "<html><head><title></title></head><body>";
  //var headstr = '<html><head>'+'<meta charset="utf-8" />'+'<title></title></head><body>';
  //var headstr = '<html><head>'+'<meta charset="utf-8" /><title>'+'</title>'+'<style type="text/css">body {-webkit-print-color-adjust: exact; font-family: Arial; }</style></head><body>';
  var footstr = '</body>';
  var newstr = printpage
  var oldstr = document.body.innerHTML;
  document.body.innerHTML = headstr+newstr+footstr;
  window.print();
  window.location.reload();
  return false;
}
</script>


@endslot

@endcomponent
