<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
  
<section class="fullwidth innerheading">
  <div class="container">
     <h1 class="heading">Checkout</h1>
    <p><a href="{{url('/')}}">Home</a>  <a href="{{url('/cart')}}">Cart</a>   Checkout  </p>
  </div>
</section>  
<section class="fullwidth innerpage"> 
  <div class="container">

  <div class="row">
    
    
    <div class="col-md-12">

    <form method="post" action="{{url('order/confirmation')}}" >

    {{ csrf_field() }}

    <?php 
         
          if(!Cart::isEmpty()){
               $cart_items = Cart::getContent();
               //pr($cart_items);

            ?>

            

          <div class="tablewidth">
          <table class="table cartlist" >
          <tr>
          <th>Product</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th> 
          </tr>
          <?php

          $from_currency = (session()->has('from_currency'))?session('from_currency'):'INR';
          $to_currency = (session()->has('to_currency'))?session('to_currency'):'INR';

          $currency_symbol_arr = config('custom.currency_symbol_arr');

          $currency_symbol = (isset($currency_symbol_arr[$to_currency]))?$currency_symbol_arr[$to_currency]:'';

          $total= 0; 
          $subtotal=0; 
          foreach($cart_items as $items) { 
                 
                 $name= $items->name;
                 $item_price= $items->price;
                 $quantity=$items->quantity; 
                 $total_item_price= $items->price * $quantity;

                 $subtotal+=$total_item_price;

                  $attributes= $items->attributes;
                 $products_images = $attributes->products_images;

                  $preview_design_url=''; 

                  if(!empty($attributes)  && isset($attributes['fabric_generator']) && !empty($attributes['fabric_generator']['preview_design']))
                  {
                     $preview_design_url=$attributes['fabric_generator']['preview_design'];

                   
                  }

                




            
            ?>

            <tr>
            <td>
				<div class="imgicon"> <img src="{{$products_images}}" width="75" height="75"></div>
				<div class="designtext">
             <span>{{$name}}</span>

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
                  

            }

            $curr_item_price = CustomHelper::ConvertCurrency($item_price, $from_currency, $to_currency);
            $curr_total_item_price = CustomHelper::ConvertCurrency($total_item_price, $from_currency, $to_currency);
            ?>

				</div>

            </td>
            <td>{{$quantity}}</td>
            <td>{{$currency_symbol.$curr_item_price}}</td>
            <td>{{$currency_symbol.$curr_total_item_price}}</td>

           


            </tr>



          <?php
        }
        $subtotal = Cart::getSubTotal();
        $total = Cart::getTotal();

        $curr_subtotal = CustomHelper::ConvertCurrency($subtotal, $from_currency, $to_currency);

        ?>



          <tr><td></td><td></td><td>Subtotal ({{$currency_symbol}})</td><td colspan="">{{$curr_subtotal}}</td></tr> 
           
          <?php
          if(!empty($coupon_discount)) { 

           $total = $total-$coupon_discount;

           $curr_coupon_discount = CustomHelper::ConvertCurrency($coupon_discount, $from_currency, $to_currency);

           ?>
           <tr><td></td><td></td><td>Discount ({{$currency_symbol}})</td><td colspan="">{{$curr_coupon_discount}}</td></tr> 

           <?php
         }
         ?>

           <?php 

                $wallet_amount= 0;
                if (session()->has('is_wallet_use')) {

                    if($user_wallet_amount > 0 ){

                        if($user_wallet_amount > $total ){

                            $wallet_amount=$total;

                        }
                        else{
                            $wallet_amount=$user_wallet_amount;
                        }
                        
                        $total = $total-$wallet_amount;

                    }

                }

                $curr_total = CustomHelper::ConvertCurrency($total, $from_currency, $to_currency);
            ?>





          <tr><td></td><td></td><td>Total({{$currency_symbol}})</td><td colspan="">{{$curr_total}}</td></tr>



          </table>
		</div>


          <?php if($user_wallet_amount > 0) {  ?>

            <label><input <?php if (session()->has('is_wallet_use')) 
                { echo 'checked';  } ?> type="checkbox" id="use_wallet_amount" name="use_wallet_amount"> Use Wallet Amount (Available Balance Rs <?php echo $user_wallet_amount-$wallet_amount;  ?>)</label>

            <?php } ?>

          <div class="row">

           <div class="col-md-6"> 
          
<div class="formsec">
			 <h4>Billing Info</h4>

          <table class="table">


          <tr><td> First Name :</td>
          <td>{{$user_address->billing_first_name}}</td>
          </tr>

          <tr><td> Last Name :</td>
          <td>
          {{$user_address->billing_last_name}}

          </td>
          </tr>

          <tr><td> Email :</td>
          <td>
          {{$user_address->billing_email}}

          </td>
          </tr>

          <tr><td> Phone :</td>
          <td>

          {{$user_address->billing_phone}}

          </td>
          </tr>

          <tr><td>Address 1 :</td>
          <td>
          {{$user_address->billing_address1}}

          </td>
          </tr>

          <tr><td>Address 2 :</td>
          <td>
           {{$user_address->billing_address2}}
         </td>
          </tr>

          <tr><td>Pincode</td>
          <td>
          {{$user_address->billing_pincode}}
          </td>
          </tr>
          <tr><td>Country :</td>
          <td>

          {{$billing_country->name}}

        



          </td>
          </tr>


         


          <tr><td>State :</td>
          <td>

          {{$billing_state->name}}

            

          </td>
          </tr>

           <tr><td>City :</td>
          <td> {{$billing_city->name}} </td>
          </tr>


          

          </table>

          </div>
		   </div>


          <div class="col-md-6">
 <div class="formsec">
			 <h4>Shipping Info</h4>

          <table class="table">


          <tr><td>First Name :</td>
          <td>
          {{$user_address->shipping_first_name}}

          </td>
          </tr>

          <tr><td>Last Name :</td>
          <td>

          {{$user_address->shipping_last_name}}

          </td>
          </tr>

          <tr><td>Email :</td>
          <td>

          {{$user_address->shipping_email}}

          </td>
          </tr>

          <tr><td>Phone :</td>
          <td>

          {{$user_address->shipping_phone}}

          </td>
          </tr>

          <tr><td>Address 1 :</td>
          <td>

          {{$user_address->shipping_address1}}


         </td>
          </tr>

          <tr><td>Address 2 :</td>
          <td>

          {{$user_address->shipping_address2}}

         </td>
          </tr>

          <tr><td>Pincode :</td>
          <td>

          {{$user_address->shipping_pincode}}

          
          </tr>
          <tr><td>Country :</td>
          <td>

          {{$shipping_country->name}}

          



          </td>
          </tr>


         


          <tr><td>State :</td>
          <td>

          {{$shipping_state->name}}

            

          </td>
          </tr>

           <tr><td>City :</td>
          <td> 

          {{$shipping_city->name}}

          </td>
          </tr>


          

          </table>
	 </div>

          </div>
			  </div>

          <div class="row">
			  <div class="col-md-2">
          <input type="radio" name="payment_method" checked="" value="cod" > Cod
           </div>
			  <div class="col-md-3">
           <input type="submit" name="order_confirm" class="btn" value="Place Order">
				  </div>
          </div>

         

              

          <?php
          }
          else
          {
             echo 'You have no items in your cart';

          }
          

      ?>


  
    

    

    </form>
    </div>
     
  </div>
  </div>
</section>
 
@include('common.footer')

<script type="text/javascript">

$('#use_wallet_amount').click(function(){


    

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


});
  



</script>


</body>
</html>