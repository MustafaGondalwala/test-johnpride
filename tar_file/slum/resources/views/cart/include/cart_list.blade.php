
<?php
if(!Cart::isEmpty()) {
	$cart_items = Cart::getContent();
    //pr($cart_items);

  $from_currency = (session()->has('from_currency'))?session('from_currency'):'INR';
  $to_currency = (session()->has('to_currency'))?session('to_currency'):'INR';

  $currency_symbol_arr = config('custom.currency_symbol_arr');

  $currency_symbol = (isset($currency_symbol_arr[$to_currency]))?$currency_symbol_arr[$to_currency]:'';
?>
    <form method="post" action="{{url('cart/checkout')}}" >
    {{ csrf_field() }}
		<div class="tablewidth">
          <table class="table cartlist" >
          <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Total</th>
          </tr>
          <?php

          $total= 0; 
          $subtotal=0; 

          $discount=0;
          foreach($cart_items as $items){

            //pr($items->attributes->products_images);
            $name = $items->name;
            $item_price = $items->price;
            $quantity = $items->quantity; 
            $total_item_price = $items->price * $quantity;
            $subtotal += $total_item_price;
            $products_images = $items->attributes->products_images;
            $attributes= $items->attributes;
            $preview_design_url=''; 

            if(!empty($attributes)  && isset($attributes['fabric_generator']) && !empty($attributes['fabric_generator']['preview_design'])){
              $preview_design_url=$attributes['fabric_generator']['preview_design'];
            }
            ?>

            <tr>
            <td>
				<a href="javascript::void(0)" data-id="{{$items->id}}" class="delete_item" >X</a>
             <div class="imgicon"><img src="{{$products_images}}" width="75" height="75"></div>
				
			<div class="designtext">
            <span>{{$name}} </span>
            <?php
            if(!empty($preview_design_url)){
              ?> 
              <a target="_blank"  href="{{$preview_design_url}}">Click to view design</a>
              <?php
            }
            ?>

            <?php
            if(!empty($attributes)){
              if(isset($attributes['size'])){
                echo '<br>';
                echo '<strong>Size:</strong>'.$attributes['size'];

              }

              if(isset($attributes['length'])){
                echo '<br>';
                echo '<strong>Length:</strong>'.$attributes['length']. " meter ";
              }

              if(isset($attributes['fabric_generator'])){

                if(isset($attributes['fabric_generator']['layout'])){
                  echo '<br>';
                  echo '<strong>Layout:</strong>'.$attributes['fabric_generator']['layout'];
                }

                if(isset($attributes['fabric_generator']['rotate'])){
                  echo '<br>';
                  echo '<strong>Rotate:</strong>'.$attributes['fabric_generator']['rotate'];
                }
                if(isset($attributes['fabric_generator']['scale'])){
                  echo '<br>';
                  echo '<strong>Scale:</strong>'.$attributes['fabric_generator']['scale'];
                }

              }


            }

            $curr_item_price = CustomHelper::ConvertCurrency($item_price, $from_currency, $to_currency);
            $curr_total_item_price = CustomHelper::ConvertCurrency($total_item_price, $from_currency, $to_currency);

            ?>
				</div>	


            </td>
            <?php /* ?><td>{{$quantity}}<input type="hidden" class="qty" min="1" data-id={{$items->id}}  value="{{$quantity}}"></td> <?php */ ?>
 
            <td>{{$currency_symbol.$curr_item_price}}</td>
            <td>{{$currency_symbol.$curr_total_item_price}}</td> 
            </tr>
          <?php
        }
          $total = Cart::getTotal();
          
          ?>

          
          </table>
		</div>
		
		<div class="coupondiv">
		<?php
    if (session()->has('coupon_sess_data')) { 
               $coupon_sess_data=session('coupon_sess_data'); 
            ?>

           <input type="button" id="remove_coupon_btn" class="couponbtn" value="Remove Coupon">
           <div id="couponcode_success_error_mess">
           <span style="color:green;"><b>Coupon:</b> {{$coupon_sess_data['coupon_code']}}, applied successfully.</span> 
           </div>
           <?php
         }
           else
            {
              ?>
			  <input type="text" name="coupon_code" id="coupon_code" placeholder="Enter Coupon Code"  >
			  <input type="button" id="apply_coupon_btn" class="couponbtn" value="Apply Coupon">
			  <div id="couponcode_success_error_mess">
			  </div>
          <?php
        }
          ?> 
		</div>

    <?php
    $subtotal = Cart::getSubTotal();

    $curr_subtotal = CustomHelper::ConvertCurrency($subtotal, $from_currency, $to_currency);
    ?>
		
		<div class="cart-totals tablewidth">
			<div class="headings">Cart Totals</div>
			<table class="table table-borderless">

				<tr class="totals"><td><strong>Subtotal</strong></td><td colspan="">{{$currency_symbol.$curr_subtotal}}</td></tr>
			  <?php
        if(!empty($coupon_discount) > 0){  
				  $total = $total-$coupon_discount;

          $curr_coupon_discount = CustomHelper::ConvertCurrency($coupon_discount, $from_currency, $to_currency);
				?>
			  <tr><td><strong>Discount</strong></td><td colspan="">{{$currency_symbol.$curr_coupon_discount}}</td></tr>
			  <?php
      }

      $curr_total = CustomHelper::ConvertCurrency($total, $from_currency, $to_currency);
        ?>
			  <tr><td><strong>Total</strong></td><td colspan=""><strong>{{$currency_symbol.$curr_total}}</strong></td></tr>
			</table>
		</div>
		
          <div class="fullwidth proceedto text-right">
			<input type="submit" name="proceed_checkout" class="btn" value="Proceed To Checkout">
		</div>
    
          </form>
                  


          <?php
          }
          else{
             echo 'You have no items in your cart';
          }
          

      ?>