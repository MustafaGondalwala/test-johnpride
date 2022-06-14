<!-- Alert Modal -->
<div id="alertModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="alert alert-success">
			</div>
		</div>

	</div>
</div>
<!-- End - Alert Modal -->

<!-- Update Modal -->
<div id="updateModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		
		<div class="modal-content">
			<div class="popbox">
				<strong class="modalTitle"></strong>
				<ul>
				</ul>
			</div>
		</div>

	</div>
</div>

<!-- End - Update Modal -->


<div id="couponpopup" class="modal fade" role="dialog">
	<div class="modal-dialog">
		
		<div class="modal-content">
			<div class="popbox">
				<div class="title3 applycoupon-title">APPLY COUPON <button type="button" class="close" data-dismiss="modal">&times;</button></div>
				<div class="couponform checkdelivery">
					<form name="couponForm" method="post" class="callback_form">
						<span class="available" style="display:none;"><small class="yesavailable"></small></span>
						<input type="text" name="coupon" value="" placeholder="Enter Coupon Code">
						<button type="button" id="applyCoupon">Apply</button>
					</form>
				</div>
				<span class="error"	style="color:#f16565;"></span>
					<div class="couponlist" style="margin-top: 0;">
				<?php
				if(!empty($Coupons) && count($Coupons) > 0){
					?>
						<div class="title3">Best coupon for you</div>
						<ul>
							<?php
							foreach($Coupons as $coupon){
							//pr($coupon->toArray());
								?>
								<li>
									<div class="couponCode"><a href="javascript:void(0)" class="couponCodeTxt">{{$coupon->code}}</a></div>
									<?php
									if(is_numeric($coupon->max_discount) && $coupon->max_discount > 0){
										?>
										<div class="benefit">
											Save <strong>₹{{number_format($coupon->max_discount)}}</strong>
										</div>
										<?php
									}
									?>
									<div class="expiry">
										<?php
										if(!empty($coupon->description)){
											echo $coupon->description;
										}
										$expiry_date = CustomHelper::DateFormat($coupon->expiry_date, 'd F Y');
										
										if(!empty($expiry_date)){
											?>
											Expires on <strong>{{$expiry_date}}</strong>
											<?php
										}
										?>
									</div>
								</li>
								<?php
							}
							?>

						</ul>
					<?php
				}
				else{
					?>
					<ul>
						<li>No other coupons available</li>
					</ul>
					<?php
				}
				?>
					</div>


			</div>
		</div>

	</div>
</div>


<!-- Size/Qty -->

<div class="modal fade" id="cart_popup" role="dialog">
	<div class="modal-dialog">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Select size</h4>
			</div>
			<div class="modal-body">

				<form name="sizeQtyForm" method="post">

					<input type="hidden" name="productId">
					<input type="hidden" name="cartId">
					<input type="hidden" name="oldSizeId">
					<input type="hidden" name="oldQty">

					<div class="slider_wrap">
						<div class="owl-carousel owl-theme">
							<!-- <div class="item">
								<div class="size_box">
									<input type="radio" name="" value="1" id="item_one">
									<label for="item_one">1</label>
								</div>
							</div>
							<div class="item">
								<div class="size_box">
									<input type="radio" name="" value="1" id="item_two">
									<label for="item_two">1</label>
								</div>
							</div>
							<div class="item">
								<div class="size_box">3</div>
							</div>
							<div class="item">
								<div class="size_box">4</div>
							</div>
							<div class="item">
								<div class="size_box">5</div>
							</div>
							<div class="item">
								<div class="size_box">10</div>
							</div>
							<div class="item">
								<div class="size_box">12</div>
							</div> -->
						</div>
					</div>

					<div class="qtn_wrap">
						<h4 class="modal-title">Select quantity</h4>
						<button type="button" id="sub" class="sub qtn_btn">-</button>
						<input type="text" name="qty" id="1" value="1" >
						<button type="button" id="add" class="add qtn_btn"> +</button>

					</div>
					<button type="button" class="btn updateSizeQty">Update</button>

				</form>


			</div>
			
		</div>
		
	</div>
</div>