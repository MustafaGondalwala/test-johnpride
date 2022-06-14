
<?php 
         
          if(!Cart::isEmpty())
          
          {
               $cart_items=Cart::getContent();
               //pr($cart_items); 

            ?>
    <form method="post" action="{{url('cart/checkout')}}" >

    {{ csrf_field() }}
          <table class="table">
          <tr>
          <th>Product</th>
          
          <th>Price</th>
          <th>Total</th>
          <th></th>

          </tr>
          <?php

          $total= 0; 
          $subtotal=0; 

          $discount=0;
          foreach($cart_items as $items) 
          {
            //pr($items->attributes->products_images);
            $name = $items->name;
            $item_price = $items->price;
            $quantity = $items->quantity; 
            $total_item_price = $items->price * $quantity;
            $subtotal += $total_item_price;
            $products_images = $items->attributes->products_images;
            $attributes= $items->attributes;
            $preview_design_url=''; 

            if(!empty($attributes)  && isset($attributes['fabric_generator']) && !empty($attributes['fabric_generator']['preview_design']))
            {
               $preview_design_url=$attributes['fabric_generator']['preview_design'];

             
            }
            


            ?>

            <tr>
            <td>
              <img src="{{$products_images}}" width="75" height="75">
            {{$name}}

            <?php if(!empty($preview_design_url)) { ?>
            <br>

            <a target="_blank"  href="{{$preview_design_url}}">Click to view design</a>
            <?php } ?>

            <?php if(!empty($attributes))
            {
                 if(isset($attributes['size']))
                 {
                   echo '<br>';
                   echo '<b>Size:</b>'.$attributes['size'];

                 }

                 if(isset($attributes['length']))
                 {
                   echo '<br>';
                   echo '<b>Length:</b>'.$attributes['length']. " meter ";

                 }

                 if(isset($attributes['fabric_generator']))
                 {


                   if(isset($attributes['fabric_generator']['layout']))
                   {
                     echo '<br>';
                     echo '<b>Layout:</b>'.$attributes['fabric_generator']['layout'];

                   }

                   if(isset($attributes['fabric_generator']['rotate']))
                   {
                     echo '<br>';
                     echo '<b>Rotate:</b>'.$attributes['fabric_generator']['rotate'];

                   }
                    if(isset($attributes['fabric_generator']['scale']))
                   {
                     echo '<br>';
                     echo '<b>Scale:</b>'.$attributes['fabric_generator']['scale'];

                   }

                 }
                  

            } ?>



            </td>
            <?php /* ?><td>{{$quantity}}<input type="hidden" class="qty" min="1" data-id={{$items->id}}  value="{{$quantity}}"></td> <?php */ ?>



            <td>{{$item_price}}</td>
            <td>{{$total_item_price}}</td>

            <td><a class="delete_item" data-id={{$items->id}} href="javascript::void(0)">Delete</a></td>


            </tr>



          <?php }

          $total=Cart::getTotal();

          ?>

         




          <tr><td></td><td></td><td>Subtotal (Rs)</td><td colspan="">{{Cart::getSubTotal()}}</td></tr> 

          <?php if(!empty($coupon_discount) > 0) 
          {  

              $total=$total-$coupon_discount;



            ?>
          <tr><td></td><td></td><td>Discount (Rs)</td><td colspan="">{{$coupon_discount}}</td></tr> 


          <?php } ?>




          <tr><td></td><td></td><td>Total(Rs)</td><td colspan="">{{$total}}</td></tr>
          




          </table>

          

           <?php 
           if (session()->has('coupon_sess_data')) 
           { 
               $coupon_sess_data=session('coupon_sess_data'); 

            ?>

             <input type="button" id="remove_coupon_btn" value="Remove Coupon">

           <div id="couponcode_success_error_mess">
           <span style="color:green;"><b>Coupon:</b> {{$coupon_sess_data['coupon_code']}}, applied successfully.</span> 
           </div>



           <?php 

          }else {?>

          

          <input type="text" name="coupon_code" id="coupon_code" placeholder="Enter Coupon Code"  >
          <input type="button" id="apply_coupon_btn" value="Apply Coupon">

          <div id="couponcode_success_error_mess">
          </div>

          <?php } ?>





          <br>



          <input type="submit" name="proceed_checkout" value="Proceed To Checkout">
    
          </form>
                  


          <?php
          }
          else
          {
             echo 'You have no items in your cart';

          }
          

      ?>