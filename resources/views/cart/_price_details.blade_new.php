<?php

$authCheck = auth()->check();

$websiteSettingsNamesArr = ['FREE_DELIVERY_AMOUNT', 'SHIPPING_CHARGE', 'SHIPPING_TEXT', 'DISCOUNT_AMOUNT', 'DISCOUNT_PERCENTAGE'];

$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

$FREE_DELIVERY_AMOUNT = (isset($websiteSettingsArr['FREE_DELIVERY_AMOUNT']))?$websiteSettingsArr['FREE_DELIVERY_AMOUNT']->value:0;
$SHIPPING_CHARGE = (isset($websiteSettingsArr['SHIPPING_CHARGE']))?$websiteSettingsArr['SHIPPING_CHARGE']->value:0;
$SHIPPING_TEXT = (isset($websiteSettingsArr['SHIPPING_TEXT']))?$websiteSettingsArr['SHIPPING_CHARGE']->value:'';
$DISCOUNT_AMOUNT = (isset($websiteSettingsArr['DISCOUNT_AMOUNT']))?$websiteSettingsArr['DISCOUNT_AMOUNT']->value:'';
$DISCOUNT_PERCENTAGE = (isset($websiteSettingsArr['DISCOUNT_PERCENTAGE']))?$websiteSettingsArr['DISCOUNT_PERCENTAGE']->value:'';

$totalTax = 0;
$offerDiscount = 0;
$amountForFreeDelivery = 0;
$cartTotal = 0;

$totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;

$cartContent = Cart::getContent();
$totalMrp = Cart::getTotalPrice($cartContent);
$cartTotal = Cart::getTotal($cartContent);
//pr($cartTotal);

$productDiscount = $totalMrp - $cartTotal;

$countQty = $cartContent->sum('qty');
//pr($productDiscount);

/*if(is_numeric($DISCOUNT_AMOUNT) && $cartTotal >= $DISCOUNT_AMOUNT){
	if(is_numeric($DISCOUNT_PERCENTAGE) && $DISCOUNT_PERCENTAGE > 0){
		$offerDiscount = ( $cartTotal * ($DISCOUNT_PERCENTAGE / 100) );
	}
}*/

//pr($totalTax);

$subTotal = $cartTotal - $offerDiscount;

$totalTax = Cart::getTax($cartContent);

$totalWithTax = $subTotal + $totalTax;




$totalTaxByLoyaltyPer = 0;
$totalTaxwithLoyalty = 0;

$totalTaxByPer = 0;
$totalTaxwithCoupn = 0;
$minAmountForCouponTxt = '';

$isCoupon = false;

$couponDiscountAmt = 0;	

if($authCheck){

	$couponData = '';

	if(session()->has('couponData')){
		$couponData = session('couponData');
		//pr($couponData);

		if(isset($couponData['id']) && is_numeric($couponData['id']) && $couponData['id'] > 0){
			$isCoupon = true;

			$minAmountForCoupon = (isset($couponData['min_amount']))?$couponData['min_amount']:0;

			if(is_numeric($minAmountForCoupon) && $minAmountForCoupon > 0 && $minAmountForCoupon > $cartTotal){
				$couponData['discount'] = 0;

				$minAmountForCouponTxt = 'To use this Coupon Total should be greater or equal to '.number_format($minAmountForCoupon);
			}

			if(is_numeric($couponData['discount']) && $couponData['discount'] > 0){

				$couponDiscount = $couponData['discount'];
				$couponDiscountAmt = $couponDiscount;

				if($couponData['type'] == 'percentage'){
					$couponDiscountAmt = ( $cartTotal * ($couponDiscount/100) );

					$totalTaxByPer = ($totalTax * $couponDiscount)/100;
				}

				else{
					
					$totalTaxByPer = ($couponDiscount * 100)/ $totalWithTax;

					$totalTaxwithCoupn = ($totalTaxByPer * $totalTax) / 100;
				}


				if(is_numeric($couponData['max_discount_amt']) && $couponData['max_discount_amt'] > 0){
					if($couponDiscountAmt > $couponData['max_discount_amt']){
						$couponDiscountAmt = $couponData['max_discount_amt'];

						$totalTaxByPer = ($couponData['max_discount_amt'] * 100)/ $totalWithTax;

						$totalTaxwithCoupn = ($totalTaxByPer * $totalTax) / 100;
					}

				}
			}
		}
	}
}

//$totalWithCouponDiscount = $cartTotal + $totalTax - $couponDiscountAmt;
$totalWithCouponDiscount = $cartTotal  - $couponDiscountAmt;
$loyaltyDiscount = 0;
$loyaltyDiscountAmt = 0;





// GET SHIPPING CHARGE FROM NEW TABLE

$getshpping_data = CustomHelper::findShippingChargeFromDB($totalWithCouponDiscount);

if($getshpping_data > 0)
{
	$totalShipping = $getshpping_data;
}


//pr($totalWithCouponDiscount);

// if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithCouponDiscount >= $FREE_DELIVERY_AMOUNT ){
// 	$totalShipping = 0;
// }
// else{
// 	$amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $cartTotal;
// }






if(auth()->check()){
	$user = auth()->user();
	$findLoyaltyPonitsCriteria = CustomHelper::findLoyaltyPonitsCriteria($user->id, $totalWithCouponDiscount);

	//prd($findLoyaltyPonitsCriteria);

	//if(!empty($findLoyaltyPonitsCriteria) && $findLoyaltyPonitsCriteria['freeShipping'] && $findLoyaltyPonitsCriteria['shipping_free_min_order'] <= $totalWithCouponDiscount)
	if(!empty($findLoyaltyPonitsCriteria) && $findLoyaltyPonitsCriteria['freeShipping'])
	{
		$totalShipping = 0;
		$amountForFreeDelivery = 0;

	}

if($findLoyaltyPonitsCriteria && $findLoyaltyPonitsCriteria['shipping_free_min_order'] <= $totalWithCouponDiscount)
	{
		$totalShipping = 0;
		$amountForFreeDelivery = 0;

	}
	if(!empty($findLoyaltyPonitsCriteria) && is_numeric($findLoyaltyPonitsCriteria['discount']) && $findLoyaltyPonitsCriteria['discount'] > 0)
	{	


				$loyaltyDiscount = $findLoyaltyPonitsCriteria['discount'];
				$loyaltyDiscountAmt = $loyaltyDiscount;


				if($findLoyaltyPonitsCriteria['discount_type'] == 'percentage'){
					$loyaltyDiscountAmt = ( $totalWithCouponDiscount * ($loyaltyDiscount/100) );

					$totalTaxByLoyaltyPer = ($totalTax * $loyaltyDiscount)/100;

					$totalTaxwithLoyalty = ($totalTaxByLoyaltyPer * $totalTax) / 100;
				}

				else{
					
					$totalTaxByLoyaltyPer = ($loyaltyDiscount * 100)/ $totalWithTax;

					$totalTaxwithLoyalty = ($totalTaxByLoyaltyPer * $totalTax) / 100;
				}




	}

}









$total = $totalWithCouponDiscount - $loyaltyDiscountAmt + $totalShipping;

$totalBagDiscount = $productDiscount + $offerDiscount;

$totalTax = $totalTax - $totalTaxwithCoupn- $totalTaxwithLoyalty;




?>

<div class="pricedetail">

	<?php
	if(isset($showCoupon) && $showCoupon == true){
		?>


	<div class="cartcoupon">
		<h3 class="title3">Coupons</h3>	
		<div></div>

		<?php
		if($authCheck){

			if(!$isCoupon){
					?>
					<div class="applybtnarea">
						<button class="applycoupon" data-toggle="modal" data-target="#couponpopup">Apply</button>
					</div>
					<?php
				
			}
			else{
				?>
				<div class="applybtnarea">
					<span>
						<strong>{{$couponData['code']}}</strong>
						<?php
						if(!empty($minAmountForCouponTxt)){
							?>
							<small style="color:#f16565;">{{$minAmountForCouponTxt}}</small>
							<?php
						}
						else{
							?>
							<small>You saved ₹{{number_format($couponDiscountAmt)}}</small>
							<?php
						}
						?>
						
					</span>
					<?php
					if(isset($couponRemovable) && $couponRemovable == true){
						?>
						<button class="applycoupon" id="removeCoupon">Remove</button>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
		else{
			?>
			<p>
				<!-- <a href="{{url('account/login?referer='.$BackUrl)}}" class="coupons-base-logIn">Log In</a> -->
				<span class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><span>Login</span></a></span>
				<span>to use account-linked coupons</span>
			</p>
			<?php
		}
		?>

	</div>

	<?php
	}
	?>


	<div>
		<h3 class="title3">Price Detail ({{$countQty}} Items)</h3>
		<ul>
			<li><span>Total MRP</span> <strong>₹{{number_format($totalMrp)}}</strong></li>

			<li><span>Bag Discount</span> <strong>-₹{{number_format($totalBagDiscount)}}</strong></li>

			<?php
			/*
			if(is_numeric($totalTax) && $totalTax > 0){
				?>
				<li><span>GST Amount</span> <strong>₹{{number_format($totalTax)}}</strong></li>
				<?php
			}*/
			?>

			<?php
			if(is_numeric($couponDiscountAmt) && $couponDiscountAmt > 0){
				?>
				<li><span>Coupon Discount</span> <strong>-₹{{number_format($couponDiscountAmt)}}</strong></li>  
				<?php
			}
			?>

			<?php
			if(is_numeric($loyaltyDiscountAmt) && $loyaltyDiscountAmt > 0){
				?>
				<li><span>Loyalty Discount</span> <strong>-₹{{number_format($loyaltyDiscountAmt)}}</strong></li>  
				<?php
			}
			?>

			<li><span>Delivery Charges</span> <strong>₹{{$totalShipping}}</strong></li>  
			<li class="totals"><span>Order Total</span> <strong>₹{{number_format($total)}}</strong></li>
		</ul>
	</div>

	<?php /*
	$is_wallet = isset(auth()->user()->is_wallet) ? auth()->user()->is_wallet:'';

	if(isset($is_wallet) && $is_wallet == '1'){

		if(isset($useWallet) && $useWallet == true){
			$userWallet = auth()->user()->userWallet;

			//prd($is_wallet);

			$walletBalance = 0;

			$walletCredit = $userWallet->sum('credit_amount');
			$walletDebit = $userWallet->sum('debit_amount');

			$walletBalance = $walletCredit - $walletDebit;

			if($walletBalance > 0){
				?>
				<div>
					<p>
						<input type="checkbox" name="is_wallet" value="1"> Use Wallet (Bal: ₹{{number_format($walletBalance)}})
					</p>
				</div>
				<div class="walletBox" style="display:none;">
					<p>
						Wallet Amount: 
						<?php
						if($walletBalance > $total){
							echo '₹'.number_format($total);
						}
						else{
							echo '₹'.number_format($walletBalance);
						}
						?>
					</p>
					<p>
						Payble Amount: 
						<?php
						if($walletBalance > $total){
							echo '₹'.'0';
						}
						else{
							echo '₹'.number_format($total - $walletBalance);
						}
						?>
					</p>

				</div>
				<?php
			}
		}
	}
	*/ ?>

	<div class="placebtn">
		<?php
			/*if($authCheck){
				?>
				<a href="{{url('cart/address')}}">Place Order</a>
				<?php
			}
			else{
				?>
				<a href="javascript:void(0)" onClick="gotoLogin()">Place Order</a>
				<?php
			}*/
			if(isset($cartProcessButton) && !empty($cartProcessButton)){
				echo $cartProcessButton;
			}
			?>
		</div>
	</div>