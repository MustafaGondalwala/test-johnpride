<?php
if(!empty($order) && count($order) > 0){

  $subTotal = $order->sub_total;
  $tax = $order->tax;
  $shippingCharge = $order->shipping_charge;
  $discount = $order->discount;
  $couponDiscount = $order->coupon_discount;
  $total = $order->total;
  $paymentMethod = $order->payment_method;

  $orderItems = $order->orderItems;

  $shippingName = $order->shipping_name;
  $shippingEmail = $order->shipping_email;
  $shippingPhone = $order->shipping_phone;
  $shippingAddress = $order->shipping_address;
  $shippingLocality = $order->shipping_locality;
  $shippingPincode = $order->shipping_pincode;

  $shippingCity = $order->shippingCity;
  $shippingState = $order->shippingState;
  $shippingCountry = $order->shippingCountry;

  $shippingCityName = '';
  $shippingStateName = '';
  $shippingCountryName = '';


  if(isset($shippingCity->name) && !empty($shippingCity->name)){
    $shippingCityName = $shippingCity->name;
  }
  if(isset($shippingState->name) && !empty($shippingState->name)){
    $shippingStateName = $shippingState->name;
  }
  if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
    $shippingCountryName = $shippingCountry->name;
  }

  if(!empty($orderItems) && count($orderItems) > 0){
    ?>

    <table width="90%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #ddd; border-radius: 4px; margin: 0px 40px 20px 40px;">
      

      <?php

      $storage = Storage::disk('public');
      $img_path = 'products/';
      $thumb_path = $img_path.'thumb/';

      foreach($orderItems as $item){
        $product_id = $item->product_id;

        $product = $item->productDetail;

              //prd($product->toArray());

        $productSizes = $product->productSizes->sortBy('name');

        $qty = $item->qty;

        $sizeId = $item->size_id;
        $sizeName = $item->size_name;
        $clrName = $item->color_name;

        //pr($item->toArray());

        $price = $product->price;
        $sale_price = $product->sale_price;

        $productBrand = $product->productBrand;

        $defaultImage = $product->defaultImage;
        $productImages = $product->productImages;

        $imgUrl = '';

        if(!empty($defaultImage) && count($defaultImage) > 0){
          if(!empty($defaultImage->image) && $storage->exists($thumb_path.$defaultImage->image) ){
            $imgUrl = url('public/storage/'.$thumb_path.$defaultImage->image);
          }
        }

        if(empty($imgUrl)){
          if(!empty($productImages) && count($productImages) > 0){
            $productImg = $productImages[0];
            if(!empty($productImg->image) && $storage->exists($thumb_path.$productImg->image) ){
              $imgUrl = url('public/storage/'.$thumb_path.$productImg->image);
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
          <td width="20%" style="padding: 10px 0px 5px 10px;">

            <?php
            if(!empty($imgUrl)){
              ?>
              <img src="{{$imgUrl}}" alt="{{$product->name}}" align="products" width="133" height="187">
              <?php
            }
            ?>
          </td>

          <td width="70%" style="padding: 10px 0px 5px 10px;">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">

              <tr>
                <td>
                  <span style="font-size: 17px; font-weight: 600; font-family: Roboto; color:#2c2928; padding: 30px 10px 10px 0px;">{{$brandName}}</span>
                  <p style="font-size: 14px; font-family: Roboto; color: #2c2928; margin: 5px 0px;">{{$product->name}}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <p style="font-size: 14px; font-family: Roboto;  color: #2c2928; margin: 8px 0px;">SIZE :<strong style="margin-left: 10px;"> {{$sizeName}}</strong></p>
                  <p style="font-size: 14px; font-family: Roboto;  color: #2c2928; margin: 8px 0px;">QTY :<strong style="margin-left: 10px;"> {{$qty}}</strong></p>
                </td>
                <td>
                  <?php
                  if($sale_price < $price){
                    $discount = 0;
                    //$discount = CustomHelper::calculateProductDiscount($price, $sale_price);

                    $totalPrice = $price*$item->qty;
                    $totalSaleprice = $sale_price*$item->qty;

                    $discountAmt = $totalPrice - $totalSaleprice;
                    ?>

                    <p style="font-size: 16px; font-family: Roboto; color: #616161; text-align: right; margin: 8px 0px;">Total MRP    : 
                      <strong style="font-size: 17px; font-family: Roboto; color: #3f4041; font-weight: bold; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> {{number_format($totalPrice)}}
                      </strong>
                    </p>
                    <p style="font-size: 16px; font-family: Roboto; color: #616161; text-align: right; margin: 8px 0px;">Discount    :
                      <strong style="font-size: 17px; font-family: Roboto; color: #3f4041; font-weight: bold; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> - {{number_format($discountAmt)}}
                      </strong>
                    </p>
                    <p style="font-size: 19px; font-family: Roboto; color: #616161; font-weight: bold; text-align: right; margin: 8px 0px;">Total    :
                      <strong style="font-size: 17px; font-family: Roboto; color: #3f4041; font-weight: bold; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> {{number_format($totalSaleprice)}}
                      </strong>
                    </p>

                    <?php
                  }
                  else{
                    ?>
                    <p style="font-size: 19px; font-family: Roboto; color: #616161; font-weight: bold; text-align: right; margin: 8px 0px;">Total    :
                      <strong style="font-size: 17px; font-family: Roboto; color: #3f4041; font-weight: bold; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> {{number_format($price)}}
                      </strong>
                    </p>
                    <?php
                  }
                  ?>


                </td>
              </tr>


            </table>
          </td>
        </tr>



        <?php
      }
      ?>


    </table>

    <?php
  }

  ?>


  <table width="90%" border="0" cellspacing="0" cellpadding="0" style="margin: 40px 40px 60px 40px;">
    <tr>
      <td valign="top" width="40%" style="border-right: 1px solid #ddd;">
        <span style="font-size: 18px; font-family: Roboto; color: #3f4041; font-weight: bold;">Delivery Address</span>
        <p style="font-size: 16px; font-family: Roboto; color: #595a5b; line-height: 32px;">
          <?php
          $orderAddrArr = CustomHelper::formatOrderAddress($order);

          if(!empty($orderAddrArr) && count($orderAddrArr) > 0){
            echo implode(',<br>', $orderAddrArr);
          }
          ?>
        </p>

        

      </td>
      <td valign="top" width="50%">
        <table width="85%" border="0" cellspacing="0" cellpadding="0" style="margin-left: 50px;">
          <tr>
            <td style="font-size: 18px; font-family: Roboto; color: #3f4041; font-weight: bold; padding-bottom: 12px;">Billing Details</td>
          </tr>
          <tr>
            <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Package value</td>
            <td width="10%" style="padding: 5px 0px;">:</td>
            <td width="20%" style="font-size: 17px; font-family: Roboto; color: #3f4041; padding: 5px 0px; font-weight: bold; margin-left: 15px;"><?php echo $inrImgIcon;?> {{number_format($subTotal)}}</td>
          </tr>
          <tr>
            <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Tax </td>
            <td width="10%" style="padding: 5px 0px;">:</td>
            <td width="20%" style="font-size: 17px; font-family: Roboto; color: #3f4041; padding: 5px 0px; font-weight: bold; margin-left: 15px;"><?php echo $inrImgIcon;?> {{number_format($tax)}}</td>
          </tr>

          <?php
          if(is_numeric($discount) && $discount > 0){
            ?>
            <tr>
              <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px 10px 0px;">Discount </td>
              <td width="10%" style="padding: 5px 0px 10px 0px;">:</td>
              <td width="20%" style="font-size: 17px; font-family: Roboto; color: #3f4041; padding: 5px 0px 10px 0px; font-weight: bold; margin-left: 15px;"><?php echo $inrImgIcon;?> {{number_format($discount)}}</td>
            </tr>
            <?php
          }
          ?>

          <?php
          if(is_numeric($couponDiscount) && $couponDiscount > 0){
            ?>
            <tr>
              <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px 10px 0px;">Coupon Discount </td>
              <td width="10%" style="padding: 5px 0px 10px 0px;">:</td>
              <td width="20%" style="font-size: 17px; font-family: Roboto; color: #3f4041; padding: 5px 0px 10px 0px; font-weight: bold; margin-left: 15px;"><?php echo $inrImgIcon;?> {{number_format($couponDiscount)}}</td>
            </tr>
            <?php
          }
          ?>


          <tr>
            <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Shipping charge</td>
            <td width="10%" style="padding: 5px 0px;">:</td>
            <td width="20%" style="font-size: 17px; font-family: Roboto; color: #e41881; padding: 5px 0px; font-weight: bold; margin-left: 15px;">
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
            <td width="60%" style="font-size: 19px;  font-family: Roboto; color: #616161; font-weight: bold; padding: 10px 0px 5px 0px; border-top: 1px solid #ddd;">Total </td>
            <td width="10%" style="padding: 10px 0px 5px 0px; border-top: 1px solid #ddd;">:</td>
            <td width="20%" style="font-size: 24px; font-family: Roboto; color: #3f4041; font-weight: bold; padding: 10px 0px 5px 0px; margin-left: 15px; border-top: 1px solid #ddd;"><?php echo $inrImgIcon;?> {{number_format($total)}}</td>
          </tr>

          <?php
          $used_wallet_amount = $order->used_wallet_amount;

          if(is_numeric($used_wallet_amount) && $used_wallet_amount > 0){
            ?>
            <tr>
            <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Amount by Wallet </td>
            <td width="10%" style="padding: 5px 0px;">:</td>
            <td width="20%" style="font-size: 16px; font-family: Roboto; color: #616161; padding: 5px 0px;">
              <?php echo $inrImgIcon;?> {{number_format($used_wallet_amount)}}
            </td>
          </tr>
            <?php
          }
          ?>

          
          <tr>
            <td width="60%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Mode of Payment </td>
            <td width="10%" style="padding: 5px 0px;">:</td>
            <td width="20%" style="font-size: 16px; font-family: Roboto; color: #616161; padding: 5px 0px;">

              <?php
              if(!empty($paymentMethod)){
                echo strtoupper($paymentMethod);
              }
              ?>

            </td>
          </tr>
        </table>  
      </td>
    </tr>
  </table>

  <?php
  if(isset($isCustomer) && $isCustomer == true){
    ?>

  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px 40px; background-color: #f1f1f1;" >
    <tr>
      <td style="font-size: 16px; font-family: Roboto; color: #616161; font-weight: 400; line-height: 29px; text-align: left;">
        We will send you a confirmation once your bag of joy is prepared & ready to ship.<br>
        If you want to reach us, please <a href="{{url('contact')}}" style="color: #e41881; font-weight: 600; text-decoration: none;"> Contact Us </a>here.
      </td>
    </tr> 
  </table>

  <?php
  }
  ?>

  <?php
}
?>