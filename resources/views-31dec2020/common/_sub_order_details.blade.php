<?php
if(!empty($subOrder) && count($subOrder) > 0){

  $subTotal = $subOrder->sub_total;
  $tax = $subOrder->tax;
  $shippingCharge = $subOrder->shipping_charge;
  $discount = $subOrder->discount;
  $couponDiscount = $subOrder->coupon_discount;
  $loyaltyDiscount = $subOrder->loyalty_discount;
  $total = $subOrder->total;
  //prd($subOrder);
  $order = $subOrder->order;

  $paymentMethod = $order->payment_method;
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

  $delivery_days = '';


  if(isset($shippingCity->id) ){
    $delivery_days = CustomHelper::getShippingZoneDeliveryDays($shippingCity->id);
  }

  if(isset($shippingCity->name) && !empty($shippingCity->name)){
    $shippingCityName = $shippingCity->name;
  }
  if(isset($shippingState->name) && !empty($shippingState->name)){
    $shippingStateName = $shippingState->name;
  }
  if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
    $shippingCountryName = $shippingCountry->name;
  }

  if(!empty($subOrder) && count($subOrder) > 0){
    //pr($shippingCity->toArray());
    ?>

 

    <table class="table" width="90%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #ddd; border-radius: 4px; margin: 0px 40px 20px 40px;">
      

      <?php

      $storage = Storage::disk('public');
      $img_path = 'products/';
      $thumb_path = $img_path.'thumb/';

      //foreach($orderItems as $item){
        $product_id = $subOrder->product_id;

        $product = $subOrder->productDetail;

              //prd($product->toArray());

        $qty = $subOrder->qty;

        $sizeId = $subOrder->size_id;
        $sizeName = $subOrder->size_name;
        $clrName = $subOrder->color_name;

        //pr($item->toArray());

        $price = $product->price;
        $sale_price = $product->sale_price;

        $productBrand = $product->productBrand;

        $defaultImage = $product->defaultImage;
        $productImages = $product->productImages;

        $imgUrl = '';

        if(!empty($defaultImage) && count($defaultImage) > 0){
          if(!empty($defaultImage->image) ){
            //$imgUrl = $defaultImage->image;
            $imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $defaultImage->image);
          }
        }

        if(empty($imgUrl)){
          if(!empty($productImages) && count($productImages) > 0){
            foreach($productImages as $prodImg){
              if(!empty($prodImg->image) ){
                //$imgUrl = $prodImg->image;
                $imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $prodImg->image);

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
		



        <?php
      //}
      ?>


    </table>

    <?php
  }

  ?>


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
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">Product Image</th>
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">Product Detail</th>
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">Taxable Price </th>
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">Without Taxable Price</th>
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">GST(%)</th>
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">GST Amount</th>
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">Qty</th>                              
                <th style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; width: 80px; padding:5px;" >Sub Total <!-- (<i class="fa fa-inr"></i>) --> </th>
              </tr>
              <?php
              //foreach($orderItems as $item){
                $item = $subOrder;
                $product_id = $item->product_id;

                $product = $item->productDetail;

                //prd($product->toArray());

                $qty = $item->qty;

                $sizeId = $item->size_id;
                $sizeName = $item->size_name;
                $clrName = $item->color_name;

                $couponDiscount = isset($item->coupon_discount) ? $item->coupon_discount:'';
                $loyaltyDiscount = isset($item->loyalty_discount) ? $item->loyalty_discount:'';

                //pr($item->toArray());

                $price = $product->price;
                $sale_price = $product->sale_price;

                $item_price = $item->item_price;
                $gst = $item->gst;

                $productBrand = $product->productBrand;

                $defaultImage = $product->defaultImage;
                $productImages = $product->productImages;

                $imgUrl = '';

                if(!empty($defaultImage) && count($defaultImage) > 0){
                  if(!empty($defaultImage->image) ){
                    //$imgUrl = $defaultImage->image;

                    $imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $defaultImage->image);
                  }
                }

                if(empty($imgUrl)){
                  if(!empty($productImages) && count($productImages) > 0){
                    foreach($productImages as $prodImg){
                      if(!empty($prodImg->image) ){
                        //$imgUrl = $prodImg->image;
                        $imgUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $prodImg->image);
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
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">
                <?php
                if(!empty($imgUrl)){
                  ?>
                  <img src="{{$imgUrl}}" alt="{{$product->name}}" align="products" width="50" height="50">
                  <?php
                }
                ?>
              </td>
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">{{$product->name}}<br>SKU: {{$product->sku}}
                  <br>Size: {{$sizeName}}</td>

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
                   
                    ?>

               <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;"><?php echo $inrImgIcon;?> {{number_format($item_price)}}</td>
               <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;"><?php echo $inrImgIcon;?> {{number_format($priceWithoutGst)}}</td>
               <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">{{number_format($gst)}}</td>
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="center"><?php echo $inrImgIcon;?>{{number_format($withOutGstP)}}</td>
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;">{{$qty}}</td>
                <td style="border-bottom:1px solid #ccc;  "><?php echo $inrImgIcon;?> {{number_format($totalSaleprice)}}</td>
              </tr>
              <?php //} ?>
              
               <tr>
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>Sub Total</strong></td>
                <td style="border-bottom:1px solid #ccc; " colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($subTotal)}}</td>
              </tr>       
               <tr>                             
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>GST (included in Total)</strong></td>
                <td style="border-bottom:1px solid #ccc;  " colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($tax)}}</td>
              </tr>

              <?php
              if(is_numeric($couponDiscount) && $couponDiscount > 0){
                ?>
                <tr>
                  <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>Coupon Discount </strong></td>
                  
                  <td style="border-bottom:1px solid #ccc;  " colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($couponDiscount)}}</td>
                </tr>

                <?php
              }
              ?>

              <?php
              if(is_numeric($loyaltyDiscount) && $loyaltyDiscount > 0){
                ?>
                <tr>
                  <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>Loyalty Discount </strong></td>
                  
                  <td style="border-bottom:1px solid #ccc;  " colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($loyaltyDiscount)}}</td>
                </tr>

                <?php
              }
              ?>

              <tr>
                <td style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>Shipping Charge</strong></td>
                <td style="border-bottom:1px solid #ccc;" colspan="1"> <i class="fa fa-inr"></i>
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
                <td  style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px;" align="right" colspan="7"><strong>Order Total</strong></td>
                <td style="border-bottom:1px solid #ccc;; padding:5px;" colspan="1"> <i class="fa fa-inr"></i><?php echo $inrImgIcon;?> {{number_format($total)}}</td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
</table>

<?php } 

  ?>

  <?php
}
?>