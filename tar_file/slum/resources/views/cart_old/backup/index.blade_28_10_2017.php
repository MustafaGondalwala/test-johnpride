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
                        <p class="paddingsMini"><span><i class="fa fa-inr" aria-hidden="true"></i> {{$total_amount_gst}}</span></p>
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{$total_amount}} + <i class="fa fa-inr" aria-hidden="true"></i> {{$total_gst}} tax + <i class="fa fa-inr" aria-hidden="true"></i> {{$shipping_fee}} Shipping Fee)</p>
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

                     


                      

                    <form method="POST" action="{{url('order/checkoutSubmit')}}" onsubmit="return validate_place_order1()">
                      {{ csrf_field() }}
                      
                      <button type="submit" class="btn btn-default">Place Order</button>
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
        conf = confirm('Are you sure to place this Order?');
        if(conf)
        {

        }
      }
    </script>

  </body>
  </html>