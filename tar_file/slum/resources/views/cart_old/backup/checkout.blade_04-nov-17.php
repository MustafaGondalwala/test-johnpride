<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{$title}}</title>
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="robots" content="index, follow"/>
  <meta name="robots" content="noodp, noydir"/>
  @include('common.head')

</head>
<body>
  @include('common.user_header')
  <section>
    <div class="contentArea">

      @include('common.left_menu')

      <div class="rightBar">
        <div class="tableArea container-custom">
          <div class="panel panel-default">
            <div class="topHeading panel-heading"> <span></span><span></span><span></span>Shopping Cart </div>         


            <div class="welcomeContent panel-body noPaddings cartpage">
              <div class="col-md-12">

                <?php
                $gst = config('custom.gst_default');
                $shipping_fee = config('custom.shipping_fee');

                $total_qty = 0;
                $total_gst = 0;
                $total_amount = 0;
                $total_amount_gst = $shipping_fee;

                if(count($cartItems) > 0){
                  ?>
                  <div class="tableCustomNew table-responsive">
                    <table class="tableCustom table">

                      <tr>
                        <th width="5%"> Item</th>
                        <th width="40%"> Product Name</th>
                        <th width="28%"> Price/Set</th>
                        <th class="noWrap" width="5%"> No. of Set(s)</th>
                        <th width="17%"> Subtotal</th>
                        <th width="5%"> </th>
                      </tr>

                      <?php

                      //prd($cartItems);


                      foreach($cartItems as $cart){

                        $cart->inventories;

                        $availabeQty = '';

                        $cart = $cart->toArray();

                        //prd($cart);

                        $quantity = $cart['quantity'];

                        $product = $ProductModel->where('id', $cart['product_id'])->first();

                        $stock = 0;

                        $availableInventory = $product->availableInventory($product->id);                        

                        if(count($availableInventory) > 0){
                          foreach($availableInventory as $ai){
                            $stock = $stock + $ai->stock;
                          }
                        }

                        //pr($stock);

                        if($stock > 0){
                          if($stock < $quantity){
                            $availabeQty = '<p class="perItem">(Available Qty: '.$stock.')</p>';
                            $quantity = $stock;
                          }
                          /*else{

                          }*/
                        }
                        else{
                          $quantity = 0;
                          $availabeQty = '<p class="perItem">(Available Qty: '.$stock.')</p>';
                        }

                        $total_qty = $total_qty + $quantity;

                        $price_type_id = 0;

                        $product_price = round($product->price);

                        $defaultPhoto = $product->defaultPhoto;

                        $storage = Storage::disk('public');

                        $thumb_path = $defaultPhoto->thumb_path;
                        $photo_name = $defaultPhoto->name;


                        if(isset($product->price_type_id) && $product->price_type_id > 0){
                          $price_type_id = $product->price_type_id;


                          $PriceType = $ProductModel->PriceType($product->id, $price_type_id);

                          if(count($PriceType)){
                            if(!empty($PriceType->price) && $price_type_id == $PriceType->price_type_id){
                              $product_price = $PriceType->price;
                            }
                          }
                        }

                        $product_gst_price = round($product_price*(100+$gst)/100);
                        $price_per_set = round($product_price*$product->piece_per_set);

                        $total_price = round($price_per_set*$quantity);

                        $price_gst = round($total_price*($gst)/100);

                        $price_per_set_total = round($total_price*(100+$gst)/100);


                        $total_gst = $total_gst + $price_gst;

                        $total_amount = $total_amount + $total_price;

                        $total_amount_gst = $total_amount_gst + $price_per_set_total;


                        //$availabeQty = $stock;

                        //$hipping_charges = 25;

                        //$inventory_id_arr = $cart['inventory_id'];
                        $inventory_id_arr = [];

                        //$inventory_id_arr = $cart['inventory_id'];
                        $cart_inventories = $cart['inventories'];

                        if(count($cart_inventories) > 0){
                          foreach($cart_inventories as $ci){
                            $inventory_id_arr[$ci['inventory_id']] = $ci;
                          }
                        }
                       // $inventory_id_arr = $cart['inventories'];

                        //prd($inventory_id_arr);
                        ?>

                        <tr>
                          <td>
                            <?php
                            if (!empty($defaultPhoto) && $storage->exists($thumb_path . $photo_name)) {
                              ?>
                              <a href="{{url('products/detail/'.$product->uri)}}"><img src="{{url('public/storage/'.$thumb_path . $photo_name)}}" alt="{{$product->name}}" style="width: 75px;"/></a>
                              <?php
                            }

                            ?>

                          </td>
                          <td><a class="carttitle" href="{{url('products/detail/'.$product->uri)}}">{{$cart['product_name']}}</a>
                            <p class="perItem">({{$product->piece_per_set}} Items Per Set)</p>
                            <p class="perItem"><i class="fa fa-inr" aria-hidden="true"></i> {{round($product_price)}} / pc + GST @5%</p>
                          </td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> {{$price_per_set}} + GST @5%</td>
                          <td>
                          {{$quantity}}<?php echo $availabeQty;?>
                          <?php
                          //pr($availableInventory);
                          if(count($availableInventory) > 0){
                            foreach($availableInventory as $ai){
                              if(isset($inventory_id_arr[$ai->id]['qty'])){
                                $inventory_id_qty = $inventory_id_arr[$ai->id]['qty'];
                                //echo "(Size : $ai, Qty : $inventory_id_qty)<br>";
                                echo "<p class='perItem'>(Size : $ai->name, Qty : $inventory_id_qty)</p>";
                              }
                            }
                          }
                          ?>
                          </td>
                          <td><span>Total: <i class="fa fa-inr" aria-hidden="true"></i> {{$price_per_set_total}}</span></td>
                          <td>

                            <form method="POST" action="{{url('cart/remove/'.$cart['id'])}}" onsubmit="return confirm('Are you sure to remove this item from your Cart?')">
                              {{ csrf_field() }}
                              <input type="hidden" name="_method" value="DELETE">
                              <button type="submit" class="btn deletebtn"><i aria-hidden="true" class="fa fa-trash-o"></i><!--<img src="{{url('public/assets/img/del.png')}}" />--></button>
                            </form>
                          </td>
                        </tr>

                        <?php
                      }
                      ?>
                    </table>

                    <div class="col-md-3  text-center dataItems">
                      <div class="totalItems">
                        <p class="noPaddings paddingsMicro">Total Qty</p>
                        <p class="noPaddings paddingsMini">{{$total_qty}}</p>
                      </div>
                    </div>
                    <div class="col-md-9 text-right dataItems">
                      <div class="totalItems">
                        <p class="paddingsMicro">Total Amount</p>
                        <p class="paddingsMini"><span><i class="fa fa-inr" aria-hidden="true"></i> {{ $total_amount_gst }}</span></p>
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{ $total_amount }} + <i class="fa fa-inr" aria-hidden="true"></i> {{ $total_gst }} tax + <i class="fa fa-inr" aria-hidden="true"></i> {{ $shipping_fee }} Shipping Fee)</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 btnOrder">
                    <?php
                    /*
                    <button class="btn removeBtn "> Remove</button>
                    <button class="btn btn-default"> Review and Order</button>
                    */
                    ?>

                    <?php /* ?>

                    <form method="POST" action="{{url('order/checkoutSubmit')}}" onsubmit="return validate_place_order()">
                      {{ csrf_field() }}
                      <?php
                      <!--
                      <label class="caseon"><input type="radio" name="payment[method]" value="cash" class="" checked> Case on delivery</label>
                      
                      --> 
                      <button type="submit" class="btn btn-default">Checkout</button>
                    </form>
                     <?php */ ?>

                    <form method="POST" action="{{url('order/checkoutSubmit')}}" onsubmit="return validate_place_order()">
                      {{ csrf_field() }}

                    <div class="row">

                    <?php //pr( $customer_address); ?>

                    <?php if(!empty($customer_address['billing_address'])) { ?>
                    <div class="col-md-6">

                        <div class="checkout-box">

                            <h2>Billing Address</h2>

                            <div class="form-group">
                                <label for="billing-name" class="control-label">GST Number:</label>
                                <?php echo $customer_address['billing_address']['billing_gst'];  ?>
                            </div>
                             <div class="form-group">
                                <label for="billing-name" class="control-label">Aadhar Number:</label>
                                <?php echo $customer_address['billing_address']['billing_aadhar_number'];  ?>
                            </div>

                            <div class="form-group">
                                <label for="billing-name" class="control-label">Company Name:</label>
                                <?php echo $customer_address['billing_address']['billing_company_name'];  ?>
                            </div>

                            <div class="form-group">
                                <label for="billing-name" class="control-label">Name:</label>
                                <?php echo $customer_address['billing_address']['billing_name'];  ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Address 1:</label>
                                <?php echo $customer_address['billing_address']['billing_address_1'];  ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Address 2:</label>
                                <?php echo $customer_address['billing_address']['billing_address_2'];  ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">State:</label>
                                <?php
                                if($customer_address['billing_address']['billing_state'] > 0)
                                { 

                                echo get_by_id($id= $customer_address['billing_address']['billing_state'], $id_name='id', $table='states', $field='name') ; 
                                } 
                               ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">City:</label>
                                <?php if($customer_address['billing_address']['billing_city'] > 0) 
                                {     echo get_by_id($id= $customer_address['billing_address']['billing_city'], $id_name='id', $table='cities', $field='name') ;
                                }
                              ?>


                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Postal code:</label>
                                <?php echo $customer_address['billing_address']['billing_zipcode'];  ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Phone Number:</label>
                                <?php echo $customer_address['billing_address']['billing_phone'];  ?>
                            </div>
                      </div>

                    </div>
                    <?php } ?>

                    <?php if(!empty($customer_address['delivery_address'])) { ?>
                    <div class="col-md-6">

                        <div class="checkout-box">

                            <h2>Shipping Address</h2>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">GST Number:</label>
                                <?php echo $customer_address['delivery_address']['delivery_gst']; ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Aadhar Number:</label>
                                <?php echo $customer_address['delivery_address']['delivery_aadhar_number']; ?>
                            </div>

                            <div class="form-group">
                                <label for="billing-name" class="control-label">Company Name:</label>
                                <?php echo $customer_address['delivery_address']['delivery_company_name']; ?>
                            </div>






                            <div class="form-group">
                                <label for="billing-name" class="control-label">Name:</label>
                                <?php echo $customer_address['delivery_address']['delivery_name']; ?>
                            </div>


                            <div class="form-group">
                                <label for="billing-name" class="control-label">Address 1:</label>
                                <?php echo $customer_address['delivery_address']['delivery_address_1']; ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Address 2:</label>
                                <?php echo $customer_address['delivery_address']['delivery_address_2']; ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">State:</label>
                                 <?php
                                if($customer_address['delivery_address']['delivery_state'] > 0)
                                { 

                                  echo get_by_id($id= $customer_address['delivery_address']['delivery_state'], $id_name='id', $table='states', $field='name') ; 
                                } 
                               ?>



                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">City:</label>
                                 <?php if($customer_address['delivery_address']['delivery_city'] > 0) 
                                {     echo get_by_id($id= $customer_address['delivery_address']['delivery_city'], $id_name='id', $table='cities', $field='name') ;
                                 }
                              ?>

                                
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Postal code:</label>
                                <?php echo $customer_address['delivery_address']['delivery_zipcode']; ?>
                            </div>
                            <div class="form-group">
                                <label for="billing-name" class="control-label">Phone Number:</label>
                                <?php echo $customer_address['delivery_address']['delivery_phone']; ?>
                            </div>


                      </div>



                    </div>

                      <div class="form-group box_payment_method">
                              <?php
                              //pr($wallet_allowed);
                              $payment_method = config('custom.payment_method');

                              if(!empty($payment_method) && count($payment_method) > 0){
                                foreach($payment_method as $pm_key=>$pm_val){
                                  //$payment_method_checked = 'disabled';
                                  $payment_method_checked = '';
                                  if($pm_key == 'credit' && $wallet_allowed === 1){
                                    if($usersWallet['balance'] < $total_amount_gst || $wallet_allowed === 0){
                                    $payment_method_checked = 'disabled';
                                    }
                                  
                                  ?>
                                  <input type="radio" name="payment_method" value="{{ $pm_key }}" {{ $payment_method_checked }} >&nbsp;{{ $pm_val }}<br>
                                  <?php
                                }
                                else if($pm_key != 'credit'){
                                  ?>
                                  <input type="radio" name="payment_method" value="{{ $pm_key }}" {{ $payment_method_checked }} >&nbsp;{{ $pm_val }}<br>
                                  <?php
                                }
                                }
                              }
                              ?>
                            </div>

                            <?php
                            if($wallet_allowed === 1){
                              ?>
                              <div class="form-group">
                                <label for="billing-name" class="control-label">Available Credit:</label>
                                <i class="fa fa-inr" aria-hidden="true"></i> <?php echo $usersWallet['balance']; ?>
                               
                            </div>


                            <div class="form-group" style="display:none;">
                                <label for="billing-name" class="control-label">After Payment:</label>
                                <i class="fa fa-inr" aria-hidden="true"></i>&nbsp;
                                <span id="after_payment_bal">
                                </span>
                            </div>
                              <?php
                            }
                            ?>

                            

                    <?php } ?>



                </div>




                      <button type="submit" class="btn btn-default">Placing Order</button>
                    </form>

                    
                  </div>
                  <?php
                }
                else{
                  ?>
                  <p>Your cart is empty.</p>
                  <p><a href="{{url('products/feeds')}}">Click here to shop.</a></p>
                  <?php
                }
                ?>


              </div>
            </div>
            <!-- <a href="index.php" class="btnNext btn btn-default" ><i class="whites fa fa-angle-right" aria-hidden="true"></i> </a> </div> -->
          </div>
        </div>
      </div>
    </section>

    @include('common.footer')

    <script src="{{url('public/assets')}}/js/function.js"></script>
    <script type="text/javascript">
      function validate_place_order()
      {
        var z=false; 
        conf = confirm('Are you sure to place this Order?');
        if(conf)
        {
           z=true;

        }
        return z;  
      }

      function show_credit_balance(){
        if($("input[name='payment_method']").is(":checked")){

          if($("input[name='payment_method']:checked").val() == "credit"){

            after_payment_bal = "{{ $usersWallet['balance'] - $total_amount_gst }}";

            $("#after_payment_bal").text(after_payment_bal);

            $("#after_payment_bal").parent().show();
          }
          else{
            $("#after_payment_bal").parent().hide();
          }
        }        
      }

    show_credit_balance();

    $(document).on("click", "input[name='payment_method']", function(){
       show_credit_balance();
    });

      
    </script>

  </body>
  </html>