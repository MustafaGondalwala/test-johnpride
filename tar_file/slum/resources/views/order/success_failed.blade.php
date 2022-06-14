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

          {{$tag_line}}, please find the details.    


        </div>
      </div>

      <?php
      if(!empty($res) && $res->count() > 0){
        ?>
        <div class="orderheading bgcolor">
          <div class="col-sm-6 col-md-3 ">
            <label>Order Id :</label> {{$res->order_id}}
          </div>
          <div class="col-sm-6 col-md-3 ">
            <label>Added on:</label> <?php $added_on = CustomHelper::DateFormat($res->created_at, 'd F y'); ?>{{$added_on}}
          </div>
          <div class="col-sm-6 col-md-3 ">
            <label>Order Status:</label> <?php echo $order_model->orderStatus($res->order_status); ?>
          </div>
          <div class="col-sm-6 col-md-3">
            <label> Payment Status:</label>  <?php echo  ($res->payment_status==1)?'Recieved':'Pending';  ?> 
          </div>
        </div> 
        <div class="row"> 
          <div class="col-md-6 form-group addressfilds">
            <div class="whitebg">
              <h4><strong>Billing Address</strong></h4>
              <p><span>Name :</span> {{$res->billing_first_name.' '.$res->billing_last_name}}</p>
              <p><span>Email :</span> {{$res->billing_email}}</p>
              <p><span>Phone :</span> {{$res->billing_phone}}</p> 
              <p><span>Address :</span>  <?php
              if(!empty($res->billing_address1)) { echo $res->billing_address1; echo ',';   } ?>							

              {{$res->billing_address1}}, {{$res->billing_address2}}, {{$billing_city->name}}</p> 
              <p><span>Pin Code :</span> {{$res->billing_pincode}}</p>
              <p><span>State :</span> {{$billing_state->name}}</p> 
              <p><span>Country :</span> {{$billing_country->name}}</p> 
            </div>
          </div>

          <div class="col-md-6 form-group addressfilds">
            <div class="whitebg">
              <h4><strong>Shipping Address</strong></h4>
              <p><span>Name :</span> {{$res->shipping_first_name.' '.$res->shipping_last_name}}</p> 
              <p><span>Email :</span> {{$res->shipping_email}}</p> 
              <p><span>Phone :</span> {{$res->shipping_phone}}</p> 
              <p><span>Address :</span> {{$res->shipping_address1}}, {{$res->shipping_address2}}, {{$shipping_city->name}}</p> 
              <p><span>Pin Code :</span> {{$res->shipping_pincode}}</p> 
              <p><span>State :</span> {{$shipping_state->name}} </p>
              <p><span>Country :</span> {{$shipping_country->name}} </p>  
            </div>
          </div>


        </div>






        <?php

        $from_currency = (session()->has('from_currency'))?session('from_currency'):'INR';
        $to_currency = (session()->has('to_currency'))?session('to_currency'):'INR';

        $currency_symbol_arr = config('custom.currency_symbol_arr');

        $currency_symbol = (isset($currency_symbol_arr[$to_currency]))?$currency_symbol_arr[$to_currency]:'';

        if($res->order_products->count()){
          ?>
          <div class="table-responsive"> 

            <?php
            if($res->order_products->count()){
              ?>


              <table class="table bordermain headth table-hover bgcolor" >

                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Total (Rs)</th>
                </tr>

                <?php
                foreach($res->order_products as $items){
                  ?>

                  <tr>

                    <td><strong>{{$items->product_name}} </strong><br>

                      <?php 
                      if(!empty($items->products_images)){ 
                        ?>
                        <span><img src="{{ url($items->products_images) }}" style="width: 75px; height:  75;"></span>
                        <?php
                      }
                      ?>
                      <span class="property">

                        <?php
                        if(!empty($items->size)){
                          ?>
                          <b>Size :</b> {{$items->size}} <br>
                          <?php
                        }
                        ?>

                        <?php
                        if(!empty($items->length)){
                          ?>
                          <b>Length :</b> {{$items->length}} Meter <br>
                          <?php
                        }
                        ?>


                        <?php

                        $design_data = [];
                        if(!empty($items->fabric_generator)){

                          $design_data = json_decode($items->fabric_generator); 
                          ?>

                          <?php
                          if(!empty($design_data->layout)){
                            ?>
                            <b>Layout :</b> {{$design_data->layout}},
                            <?php
                          }
                          ?>

                          <b>Rotate :</b> {{$design_data->rotate}}, <b>Scale :</b> {{$design_data->scale}} <br>

                          <?php
                        }
                        ?>
                      </span>


                      <?php

                      $item_price = $items->price;
                      $total_item_price = $items->price*$items->qty;

                      $curr_item_price = CustomHelper::ConvertCurrency($item_price, $from_currency, $to_currency);
                      $curr_total_item_price = CustomHelper::ConvertCurrency($total_item_price, $from_currency, $to_currency);
                      ?>


                    </td>
                    <td>{{$currency_symbol.$curr_item_price}}</td>
                    <td>{{$items->qty}}</td>
                    <td>{{$currency_symbol.$curr_total_item_price}}</td>
                  </tr>
                  <?php
                }

                $subtotal = $res->sub_total;

                $curr_subtotal = CustomHelper::ConvertCurrency($subtotal, $from_currency, $to_currency);
                ?>

                <tr>
                  <td></td>
                  <td></td>
                  <td><b>Sub Total</b></td>
                  <td>{{$currency_symbol.$curr_subtotal}}</td>
                </tr>

                <?php
                if($res->discount > 0){

                  $discount = $res->discount;

                  $curr_discount = CustomHelper::ConvertCurrency($discount, $from_currency, $to_currency);
                  ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>Discount</b></td>
                    <td>{{$currency_symbol.$curr_discount}}</td>
                  </tr>

                  <?php
                }
                ?>

                <?php
                if($res->tax > 0){

                  $tax = $res->tax;

                  $curr_tax = CustomHelper::ConvertCurrency($tax, $from_currency, $to_currency);
                  ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>Tax</b></td>
                    <td>{{$currency_symbol.$curr_tax}}</td>
                  </tr>

                  <?php
                }
                ?>

                <?php
                if($res->shipping_charge > 0){

                  $shipping_charge = $res->shipping_charge;

                  $curr_shipping_charge = CustomHelper::ConvertCurrency($shipping_charge, $from_currency, $to_currency);
                  ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>Shipping Charge</b></td>
                    <td>{{$currency_symbol.$curr_shipping_charge}}</td>
                  </tr>
                  <?php
                }
                ?>

                <?php
                if($res->used_wallet_amount > 0){
                  $used_wallet_amount = $res->used_wallet_amount;

                  $curr_used_wallet_amount = CustomHelper::ConvertCurrency($used_wallet_amount, $from_currency, $to_currency);
                  ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>Used Wallet Amount</b></td>
                    <td>{{$currency_symbol.$curr_used_wallet_amount}}</td>
                  </tr>

                  <?php
                }

                $total = $res->total;

                $curr_total = CustomHelper::ConvertCurrency($total, $from_currency, $to_currency);
                ?>
                <tr>
                  <td></td>
                  <td></td>
                  <td><b>Total</b></td>
                  <td>{{$currency_symbol.$curr_total}}</td>
                </tr>
              </table>

              <?php
            }
            ?>

          </div>

          <?php
        }
      }
      ?>

    </div>
  </div>
</section>

@include('common.footer')


</body>
</html>