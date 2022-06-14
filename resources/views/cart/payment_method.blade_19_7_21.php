<!DOCTYPE html>

<html>

<head>



@include('common.head')



</head>

<body>



@include('common.header')

  <section class="fullwidth tabcart">

  <div class="container">

     <ul>

      <li><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>

     <li><span><i class="addressicon"></i></span><strong>Address</strong></li>

     <li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>

     <li class="active"><span><i class="payment_n_icon"></i></span><strong>Payment</strong></li>

    </ul>

  </div>

</section>  

<section class="fullwidth innerheading payment-header-sec">

  <div class="container">

     <h1 class="heading" style="font-size: 20px;">Payment Method</h1>

    <!-- <p><a href="{{url('/')}}">Home</a>  <a href="{{url('/cart')}}">Cart</a>   Payment Method  </p> -->

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



$totalWithCouponDiscount = $cartTotal  - $couponDiscountAmt;



$loyaltyDiscount = 0;

$loyaltyDiscountAmt = 0;







$getshpping_data = CustomHelper::findShippingChargeFromDB($totalWithCouponDiscount);



if($getshpping_data > 0)

{

  $totalShipping = $getshpping_data;

}



//pr($totalWithCouponDiscount);



// if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithCouponDiscount >= $FREE_DELIVERY_AMOUNT ){

//   $totalShipping = 0;

// }

// else{

//   $amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $cartTotal;

// }





if(auth()->check()){

  $user = auth()->user();

  $findLoyaltyPonitsCriteria = CustomHelper::findLoyaltyPonitsCriteria($user->id, $totalWithCouponDiscount);



  if(!empty($findLoyaltyPonitsCriteria) && $findLoyaltyPonitsCriteria['freeShipping'] && $findLoyaltyPonitsCriteria['shipping_free_min_order'] <= $totalWithCouponDiscount)

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





/*$total = $totalWithCouponDiscount + $totalShipping;



$totalBagDiscount = $productDiscount + $offerDiscount;



$totalTax = $totalTax - $totalTaxwithCoupn;*/







$total = $totalWithCouponDiscount - $loyaltyDiscountAmt + $totalShipping;



$totalBagDiscount = $productDiscount + $offerDiscount;



$totalTax = $totalTax - $totalTaxwithCoupn- $totalTaxwithLoyalty;



?>



<section class="fullwidth innerpage payment-layout"> 

  <div class="container">



  <div class="row">

    

    

    <div class="col-md-12">



      <div class="sectionright">



        @include('cart._price_details')

      </div>



      <?php 

      //pr($total);

      ?>

      <div class="payment-wrapper">

    <form method="post" action="{{url('order/process')}}">



    {{ csrf_field() }}



    <input type="hidden" name="shppingAddrId" value="{{$shppingAddrId}}">



    <div class="row">



       @include('snippets.front.flash')



      <?php

    //$total = 0;

    $is_wallet = isset(auth()->user()->is_wallet) ? auth()->user()->is_wallet:'';

    $userWallet = auth()->user()->userWallet;



    $walletBalance = 0;



    $walletCredit = $userWallet->sum('credit_amount');

    $walletDebit = $userWallet->sum('debit_amount');



    $walletBalance = $walletCredit - $walletDebit;



//    if(isset($is_wallet) && $is_wallet == '1' && $walletBalance >= $total){



 if(isset($is_wallet) && $is_wallet == '1' && $walletBalance > 0){



     if($walletBalance > 0){

          ?>

          <div class="payment-wrapper-inner">

            

            <!-- <div class="first-title">

              <input type="radio" name="paymentMethod" value="is_wallet"> Use Wallet (Bal: ₹{{number_format($walletBalance)}})

            </div> -->



          <div class="first-title">

            <input type="checkbox" name="isWallet" id="isWallet" value="1"> Use Wallet (Bal: ₹{{number_format($walletBalance)}})

          </div>



          <div class="walletBox" style="display:none;">

            <p>

              Wallet Amount : 

              <span>

              <?php

              if($walletBalance > $total){

                echo '₹'.number_format($total);

              }

              else{

                echo '₹'.number_format($walletBalance);

              }

              ?>

            </span>

            </p>

            <p>

              Payable Amount : 

              <span>

              <?php

              if($walletBalance > $total){

                echo '₹'.'0';

              }

              else{

                echo '₹'.number_format($total - $walletBalance);

              }

              ?>

            </span>

            </p>



          </div>

        </div>

          <?php

        }

    }

    ?>

      </div>



      <?php



 if($is_wallet)
 {

  if($walletBalance > 0 && $walletBalance < $total)
    {
      if($cod_available == 1)
      {

      ?>

      <div id="cod_div" class="row">

        <input type="radio" name="paymentMethod" value="cod" required=""> CASH/ CARD ON DELIVERY<br>

      </div>

      <?php 

      } 

    ?>



      <div class="row">

        <input type="radio" name="paymentMethod" value="payumoney" required=""> Online Payment<br>

      </div>



   <?php 

   

   }



   else if($walletBalance > 0 && $walletBalance >= $total)
   {
    ?>

       <input style="display: none;" type="radio" name="paymentMethod" value="is_wallet" checked=""> 

    <?php
   }

   else

   {

    



    if($cod_available == 1)

      {

      ?>

      <div id="cod_div" class="row">

        <input type="radio" name="paymentMethod" value="cod"  required=""> CASH/ CARD ON DELIVERY<br>

      </div>

      <?php 

      } 

    ?>



      <div class="row">

        <input type="radio" name="paymentMethod" value="payumoney"  required=""> Online Payment<br>

      </div>



    <?php

   }



 } 

 else

 {

   

 if($cod_available == 1)

      {

      ?>

      <div id="cod_div" class="row">

        <input type="radio" name="paymentMethod" value="cod"  required=""> CASH/ CARD ON DELIVERY<br>

      </div>

      <?php 

      } 

    ?>



      <div class="row">

        <input type="radio" name="paymentMethod" value="payumoney"  required=""> Online Payment<br>

      </div>



    <?php

 }    

  

   ?>   



      @include('snippets.front.errors_first', ['param' => 'paymentMethod'])



      <div class="row">

        <input class="button" type="submit" name="paymentSubmitBtn" value="Continue">

      </div>





    </form>

    </div>

    

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

   // $('input:radio').change(function() {

     $('#isWallet').change(function() {

     // alert($(this).val());



     // var paymentMethod = $("input[name='paymentMethod']:checked").val();

       var is_wallet = $(this).val();

        //

        if($(this).prop("checked") == true)

        {

          //$('#cod_div').hide();

          $(".walletBox").show();

        }



        else if($(this).prop("checked") == false)

        {

           //$('#cod_div').show();

            $(".walletBox").hide();

        }



      //if(paymentMethod == 'is_wallet')

      // if(is_wallet == '1')

      // {

      //     $(".walletBox").show();

      // }

      // else

      // {

      //     $(".walletBox").hide();

      

      // }



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