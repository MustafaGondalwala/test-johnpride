<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
  
<section class="fullwidth innerheading">
  <div class="container">
     <h1 class="heading">Payment Method</h1>
    <p><a href="{{url('/')}}">Home</a>  <a href="{{url('/cart')}}">Cart</a>   Payment Method  </p>
  </div>
</section>

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

//pr($subTotal);
//pr($totalTax);

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

$totalWithCouponDiscount = $cartTotal + $totalTax - $couponDiscountAmt;

if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithCouponDiscount >= $FREE_DELIVERY_AMOUNT ){
  $totalShipping = 0;
}
else{
  $amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $cartTotal;
}


$total = $totalWithCouponDiscount + $totalShipping;

$totalBagDiscount = $productDiscount + $offerDiscount;

$totalTax = $totalTax - $totalTaxwithCoupn;
?>

<section class="fullwidth innerpage"> 
  <div class="container">

  <div class="row">
    
    
    <div class="col-md-12">

      <div class="sectionright">

        @include('cart._price_details')
      </div>

      <?php 
      //pr($$websiteSettingsNamesArr);
      ?>
    <form method="post" action="{{url('order/process')}}" >

    {{ csrf_field() }}

    <input type="hidden" name="shppingAddrId" value="{{$shppingAddrId}}">

    <div class="row">

      <?php
    //$total = 0;
    $is_wallet = isset(auth()->user()->is_wallet) ? auth()->user()->is_wallet:'';

    if(isset($is_wallet) && $is_wallet == '1'){

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
              <input type="radio" name="paymentMethod" value="is_wallet">Use Wallet (Bal: ₹{{number_format($walletBalance)}})
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
    ?>
      </div>

      <?php
      if($cod_available == 1){
      ?>
      <div class="row">
        <input type="radio" name="paymentMethod" value="cod"> Cod<br>
      </div>
      <?php } ?>

      <div class="row">
        <input type="radio" name="paymentMethod" value="ccavenue"> Online Payment<br>
      </div>
      @include('snippets.front.errors_first', ['param' => 'paymentMethod'])

      <div class="row">
        <input type="submit" name="paymentSubmitBtn" value="Continue">
      </div>


    </form>

    
    </div>
     
  </div>
  </div>
</section>
 
@include('common.footer')

<script type="text/javascript">

  /*$(document).on("click", "input[name=is_wallet]", function(){
    if($(this).is(":checked")){
      $(".walletBox").show();
    }
    else{
      $(".walletBox").hide();
    }
  });*/

  $(document).ready(function() {
    $('input:radio').change(function() {

      var paymentMethod = $("input[name='paymentMethod']:checked").val();
      //alert(paymentMethod);
      if(paymentMethod == 'is_wallet'){
          $(".walletBox").show();
      }
      else{
          $(".walletBox").hide();
          //alert('false');
      }

    });
  });


/*$('#use_wallet_amount').click(function(){


    

    var token= '{{ csrf_token() }}';
    var is_checked= 0;
    if($('#use_wallet_amount').prop('checked')==true)
    {
        is_checked=1;

    }
    $.ajax({
          url: "{{url('cart/use_wallet_amount')}}", 
          type: 'post',
          cache: false,
          dataType: 'html',
          //data: $('#'+form_id).serialize(),
          data: {_token:token,is_checked:is_checked},
          crossDomain: true,
          beforeSend: function()
          {
            
          },
          success: function(response)
          {
              location.reload();
              
             
              
              
          },
    }); 


});*/
  



</script>


</body>
</html>