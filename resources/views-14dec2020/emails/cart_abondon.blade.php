@component('emails.common.layout')

@slot('heading')
<td align="left" style="font-family: 'Roboto', sans-serif, Arial; color: #fff; font-size: 20px; font-weight: 700; padding: 20px 40px;">We Have Some Unfinished Businesss...</td>
@endslot

@slot('pageBlock')

<tr>
  <td style="padding: 22px 40px;">
    <span style="font-size: 24px; color: #3f4041; font-family: 'Roboto', sans-serif, Arial;">Hi {{$name}}</span>
    <p style="font-size: 22px; font-family: 'Roboto', sans-serif, Arial; font-weight: 300; color: #3f4041; line-height: 32px; margin: 0;">You’ve left some great items in your cart and we really want <br> you to have them.</p>
  </td>
</tr>

@endslot

<?php
if(!empty($cartContent) && count($cartContent) > 0){
  ?>

  <table width="90%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #e1e1e1; border-radius: 4px; margin: 0px 40px 20px 40px;">

    <tr>
      <td colspan="2" width="100%" style="padding:15px 15px; background: #f9f1f5; border-bottom: 1px solid #e1e1e1;">
        <span style="display: block; font-size: 16px; font-family: 'Roboto', sans-serif, Arial; font-weight: 700; color: #3f4041; line-height: 18px;">Your Cart</span>
      </td>
    </tr>

    <?php

    $storage = Storage::disk('public');
    $img_path = 'products/';
    $thumb_path = $img_path.'thumb/';

    $cartTotal = 0;

    $totalMrp = 0;

    foreach($cartContent as $cart){

      //prd($cart->toArray());

      $cartId = $cart->id;

      $cartIdArr = explode('_', $cartId);

      $product_id = $cart->product_id;

      $product = $productModel->find($product_id);
              //prd($product->toArray());

      $qty = $cart->qty;

      $sizeId = $cart->size_id;
      $sizeName = $cart->size_name;
      $clrName = $cart->color_name;

              //pr($cart->toArray());

      $price = $product->price;
      $salePrice = $product->sale_price;

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

      $totalMrp = $totalMrp + $price;

      $cartTotal = $cartTotal + $cart->cart_price;

      ?> 


      <tr>
        <!--  <td width="20%" style="padding: 10px 0px 5px 10px;" class="imgtable"> -->
          <td class="product-item-border" width="20%" style="padding: 20px 0px 20px 15px;">

            <?php
            if(!empty($imgUrl)){
              ?>
              <img src="{{$imgUrl}}" alt="{{$product->name}}" align="products" width="133" height="187">
              <?php
            }
            ?>
          </td>


          <td class="product-item-border" valign="top" width="80%" style="padding: 30px 15px 20px 30px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" valign="top" style="padding-bottom:25px;">
                  <span style="font-size: 14px; font-weight: 600; font-family: Roboto; color:#2c2928; padding: 10px 10px 10px 0px;">{{$brandName}}</span>
                  <p style="font-size: 12px; font-family: Roboto; color: #838488; font-weight: 400; margin: 5px 0px;">{{$product->name}}</p>
                </td>
              </tr>

              <tr>

                <td valign="top">
                  <p style="font-size: 12px; font-family: Roboto; font-weight: 400;  color: #2c2928; margin: 8px 0px;">SIZE :<strong style="margin-left: 10px;">{{$sizeName}}</strong></p>
                  <p style="font-size: 12px; font-family: Roboto; font-weight: 400;  color: #2c2928; margin: 8px 0px;">QTY :<strong style="margin-left: 10px;"> {{$qty}}</strong></p>
                </td>


                <td>
                  <?php

                  if($salePrice > 0 && $salePrice < $price){
                    $discount = 0;
                    //$discount = CustomHelper::calculateProductDiscount($price, $salePrice);

                    $totalPrice = $price*$cart->qty;
                    $totalSaleprice = $salePrice*$cart->qty;

                    $discountAmt = $totalPrice - $totalSaleprice;

                    ?>

                    <p style="font-size: 14px; font-family: Roboto; color: #616161; font-weight: 400; text-align: right; margin: 8px 0px;">Total MRP  &nbsp;&nbsp; :&nbsp;&nbsp;
                      <strong style="font-size: 14px; font-family: Roboto; font-weight: 700; color: #3f4041; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> {{number_format($totalPrice)}}
                      </strong>
                    </p>
                    <p style="font-size: 14px; font-family: Roboto; font-weight: 400; color: #616161; text-align: right; margin: 8px 0px;">Discount   &nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;
                      <strong style="font-size: 14px; font-family: Roboto; font-weight: 700; color: #3f4041;  margin-left: 15px;">
                        <?php echo $inrImgIcon;?> - {{number_format($discountAmt)}}
                      </strong>
                    </p>
                    <p style="font-size: 15px; font-family: Roboto; color: #616161; font-weight: 700; text-align: right; margin: 8px 0px;">Total   &nbsp;&nbsp;&nbsp; :
                      <strong style="font-size: 15px; font-family: Roboto; color: #3f4041; font-weight: 700; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> {{number_format($totalSaleprice)}}
                      </strong>
                    </p>

                    <?php
                  }
                  else{
                    $totalPrice = $price*$cart->qty;


                    ?>

                    <p style="font-size: 15px; font-family: Roboto; color: #616161; font-weight: 700; text-align: right; margin: 8px 0px;">Total   &nbsp;&nbsp;&nbsp; :
                      <strong style="font-size: 15px; font-family: Roboto; color: #3f4041; font-weight: 700; margin-left: 15px;">
                        <?php echo $inrImgIcon;?> {{number_format($totalPrice)}}
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

      $totalDiscount = $totalMrp - $cartTotal;
      
      $grandTotal = $cartTotal + $totalTax;

      $totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;

      if(is_numeric($FREE_DELIVERY_AMOUNT) && $grandTotal >= $FREE_DELIVERY_AMOUNT ){
        $totalShipping = 0;
      }

      $total = $grandTotal + $totalShipping;

      ?>


    </table>

    
    

    <table width="90%" border="0" cellspacing="0" cellpadding="0" style="margin: 25px 40px 30px 40px; border: 1px solid #e1e1e1;">
      <tr>
        <td style="padding:15px 15px; background: #f9f1f5; border-bottom: 1px solid #e1e1e1;">
          <span style="display: block; font-size: 16px; font-family: 'Roboto', sans-serif, Arial; font-weight: 700; color: #3f4041; line-height: 18px;">Price Details</span>
        </td>
      </tr>
      <tr>
        <td>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px;">
            <tr>
              <td width="40%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Cart Total</td>
              <td width="10%" style="padding: 5px 0px;">:</td>
              <td width="50%" style="font-size: 17px; font-family: Roboto; color: #3f4041; padding: 5px 0px; font-weight: bold; margin-left: 15px; text-align: right;"><?php echo $inrImgIcon;?> {{number_format($totalMrp)}}</td>
            </tr>
            <tr>
              <td width="40%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Cart Discount </td>
              <td width="10%" style="padding: 5px 0px;">:</td>
              <td width="50%" style="font-size: 17px; font-family: Roboto; color: #3f4041; padding: 5px 0px; font-weight: bold; margin-left: 15px; text-align: right;"><?php echo $inrImgIcon;?> {{number_format($totalDiscount)}}</td>
            </tr>
            <tr>
              <td width="40%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Tax</td>
              <td width="10%" style="padding: 5px 0px;">:</td>
              <td width="50%" style="font-size: 17px; font-family: Roboto; color: #616161; padding: 5px 0px; font-weight: bold; margin-left: 15px; text-align: right;"><?php echo $inrImgIcon;?> {{number_format($totalTax)}}</td>
            </tr>
            <tr>
              <td width="40%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px 10px 0px;">Shipping charge </td>
              <td width="10%" style="padding: 5px 0px 10px 0px;">:</td>

              <?php
              if(is_numeric($totalShipping) && $totalShipping > 0){
                ?>
                <td width="50%" style="font-size: 17px; font-family: Roboto; color: #e41881; padding: 5px 0px 10px 0px; font-weight: bold; margin-left: 15px; text-align: right;"> Free</td>
                <?php
              }
              else{
                ?>
                <td width="50%" style="font-size: 17px; font-family: Roboto; color: #616161; padding: 5px 0px 10px 0px; font-weight: bold; margin-left: 15px; text-align: right;"><?php echo $inrImgIcon;?> {{number_format($totalShipping)}}</td>
                <?php
              }
              ?>

              
            </tr>
            <tr>
              <td width="40%" style="font-size: 19px;  font-family: Roboto; color: #616161; font-weight: bold; padding: 10px 0px 5px 0px; border-top: 1px solid #ddd;">Total </td>
              <td width="10%" style="font-size: 19px;font-family: Roboto; color: #616161; font-weight: bold; padding: 10px 0px 5px 0px; border-top: 1px solid #ddd;">:</td>
              <td width="50%" style="font-size: 24px; font-family: Roboto; color: #3f4041; font-weight: bold; padding: 10px 0px 5px 0px; margin-left: 15px; border-top: 1px solid #ddd; text-align: right;"><?php echo $inrImgIcon;?> {{number_format($total)}}</td>
            </tr>
            <!-- <tr>
              <td width="40%" style="font-size: 15px; font-family: Roboto; color: #616161; padding: 5px 0px;">Mode of Payment </td>
              <td width="10%" style="padding: 5px 0px;">:</td>
              <td width="50%" style="font-size: 16px; font-family: Roboto; color: #616161; padding: 5px 0px; text-align: right;">Online </td>
            </tr> -->
          </table>

        </td>
      </tr>
    </table> 



    @slot('footerBlock')

    <tr>
      <td style="padding:0 140px 40px 140px; text-align: center;">
        <a href="{{url('cart')}}" style="display:block; font-size: 20px; font-family: Roboto; font-weight: 500; color: #fff; text-decoration: none; background-color: #e41881; padding: 10px 40px;">Place Order</a>
      </td>
    </tr>
    <tr bgcolor="#e41881">
      <td colspan="2" height="4"></td>

    </tr>
    <tr bgcolor="#ffffff">
      <td colspan="2" height="1"></td>

    </tr>
    <tr bgcolor="#3f4041">
      <td colspan="2" height="8"></td>
    </tr>
    <tr>
      <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 50px 40px; background-color: #fff;" >
          <tr>
            <td style="font-size: 15px; font-family: Roboto; color: #2a2928; font-weight: 400; text-align: center;">
              <img src="{{url('public/images/original_product_icon.png')}}" alt="100% Original Products">
              <span style="display: block; padding-top:5px; font-size: 16px; font-weight:700;">100% ORIGINAL</span>
              <span style="display: block;">products</span>
            </td>
            <td style="font-size: 15px; font-family: Roboto; color: #2a2928; font-weight: 400; text-align: center;">
              <img src="{{url('public/images/return_icon.png')}}" alt="Return within 15 days">
              <span style="display: block; padding-top:5px; font-size: 16px; font-weight:700;">Return within 15 days</span>
              <span style="display: block;">of receiving your order</span>
            </td>
            <td style="font-size: 15px; font-family: Roboto; color: #2a2928; font-weight: 400; text-align: center;">
              <img src="{{url('public/images/free_delivery_icon.png')}}" alt="Get free delivery">
              <span style="display: block; padding-top:5px; font-size: 16px; font-weight:700;">Get free delivery</span>
              <span style="display: block;">for every order above Rs.1000/-</span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px 40px; background-color: #f1f1f1;" >
          <tr>
            <td style="font-size: 16px; font-family: Roboto; color: #616161; font-weight: 400; line-height: 29px; text-align: left;">
              We will send you a confirmation once your bag of joy is prepped & ready to ship.<br>
              If you want to reach us, please <a href="{{url('contact')}}" style="color: #e41881; font-weight: 600; text-decoration: none;"> Contact Us </a>here.
            </td>
          </tr> 
        </table>  
      </td>
    </tr>
    @endslot


    <?php


  }

  ?>


@endcomponent