<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{$meta_title}}</title>
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
            <div class="topHeading panel-heading"> <span></span><span></span><span></span>{{trans('custom.review_your_order')}} </div>


            <div class="welcomeContent panel-body noPaddings cartpage">
              <div class="col-md-12">

                <?php

                $currency_symb = '<i class="fa fa-inr" aria-hidden="true"></i>';

                $delivery_city_id = $customer_address['delivery_address']['delivery_city'];

                $gst = config('custom.gst_default');
                //$shipping_fee = config('custom.shipping_fee');
                /*$shipping_fee_arr = CustomHelper::ShippingFee($delivery_city_id);
                $shipping_fee = $shipping_fee_arr['rate'];*/

                $total_qty = 0;
                $total_gst = 0;
                $total_amount = 0;
                $total_weight = 0;
                $total_amount_gst = 0;

                if(count($cartItems) > 0){
                  /* @include('cart.cart_list', ['total_amount_gst'=>$total_amount_gst] )*/
                  ?>

                  <div class="">

                  <div class="row">
                    

                    <?php
                      //prd($cartItems);

                    $gst_arr = [];

                      foreach($cartItems as $cart){

                        $cart->inventories;

                        $availabeQty = '';

                        $cart = $cart->toArray();

                        $TaxRate = "";

                        if($cart['tax_rate_id'] > 0){
                          $TaxRate = CustomHelper::GetTaxRate($cart['tax_rate_id'], $col_name='rate');
                        }

                        $gst = (!empty($TaxRate))?$TaxRate:config('custom.gst_default');

                        $gst_arr[] = $gst;

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

                        $product_price = $product->price;

                        $product_weight = $product->weight;
                        $product_weight_with_qty = $product_weight*$quantity;

                        $defaultPhoto = $product->defaultPhoto;

                        $storage = Storage::disk('public');

                        $thumb_path = (isset($defaultPhoto->thumb_path))?$defaultPhoto->thumb_path:'';
                        $photo_name = (isset($defaultPhoto->name))?$defaultPhoto->name:'';

                        //prd($product);
                        //prd($product->category->getUsersCategoryPrice($user_id));

                        $getUsersCategoryPrice = $product->category->getUsersCategoryPrice($user_id);

                        if(isset($getUsersCategoryPrice->price_type_id) && $getUsersCategoryPrice->price_type_id > 0){
                          $price_type_id = $getUsersCategoryPrice->price_type_id;

                          $PriceType = $product->PriceType($product->id, $price_type_id);

                          //prd($PriceType);

                          if(count($PriceType)){
                            if(!empty($PriceType->price) && $price_type_id == $PriceType->price_type_id){
                              $product_price = $PriceType->price;
                            }
                          }
                        }

                        //prd($product_price);

                        $product_gst_price = $product_price*(100+$gst)/100;
                        $price_per_set = $product_price*$product->piece_per_set;

                        //$total_price = number_format(($price_per_set*$quantity), 2);
                        $total_price = ($price_per_set*$quantity);

                        $price_gst = $total_price*($gst)/100;

                        $price_per_set_total = $total_price*(100+$gst)/100;

                        //prd($total_price);


                        $total_gst = $total_gst + $price_gst;

                        $total_amount = $total_amount + $total_price;

                        $total_amount_gst = $total_amount_gst + $price_per_set_total;

                        $total_weight = $total_weight + $product_weight_with_qty;

                      }

                      //prd($total_weight);

                      $CalculateShipping = CustomHelper::CalculateShipping($delivery_city_id, $total_weight);
                      $shipping_fee = (isset($CalculateShipping['shipping_fee']))?$CalculateShipping['shipping_fee']:0;

                      rsort($gst_arr);
                      //pr($gst_arr);

                      $gst_for_ship = $gst_arr[0];

                      $shipping_gst = $shipping_fee*($gst_for_ship/100);

                      $total_amount_gst = $total_amount_gst + $shipping_fee+$shipping_gst;

                      $total_order_gst = $total_gst + $shipping_gst;
                      
                      $payable_amount = $total_amount_gst;

                      $after_wallet_payable_amount = 0;

                      if($payable_amount > $usersWallet['balance']){
                        $after_wallet_payable_amount = $payable_amount - $usersWallet['balance'];
                      }

                      $city_name = '';

                      if(is_numeric($delivery_city_id) && $delivery_city_id > 0){
                        $city_name = CustomHelper::GetTableData($tbl='cities',$id_name='id', $delivery_city_id, $col_name='name');

                        $city_name = ucfirst($city_name);
                      }

                        ?>
                        <div class="col-md-12">
                        <h2>{{trans('custom.order_summary')}}</h2>
                        <p>
                          <strong>{{trans('custom.subtotal')}}: </strong><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }}<br />
                          <strong>{{trans('custom.shipping')}}: </strong><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($shipping_fee, 2) }}<br />
                          <strong>{{trans('custom.total_gst')}}: </strong><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_order_gst, 2) }}
                          <br>
                          ({{trans('custom.order')}} : <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} + {{trans('custom.shipping')}} : <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($shipping_gst, 2) }} at {{$gst_arr[0]}}%)
                          <br />
                          <strong>{{trans('custom.order_total')}}: </strong><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format(round($total_amount_gst),2) }}
                        </p>

                  </div>
                </div>
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

                    <form name="form_checkout" method="POST" action="{{url('order/checkoutSubmit')}}" onsubmit="return validate_place_order()">
                      {{ csrf_field() }}                                              
                          
                          

                    <div class="row">

                      <div class="col-md-12">
                       <div class="cart_sbt_msg"></div>

                    <div class="form-group box_payment_method">
                      <h2>{{trans('custom.select_a_payment_method')}}:</h2>

                      <?php
                            if($wallet_allowed === 1){

                              $wallet_disabled = 'disabled';
                              $wallet_title = trans('custom.insufficient_balance');

                              if($usersWallet['balance'] > 0){
                                $wallet_disabled = '';
                                $wallet_title = '';
                              }

                              ?>
                              <div class="form-group">
                                <input type="checkbox" name="use_wallet" class="" title="{{$wallet_title}}" {{$wallet_disabled}} >&nbsp;<strong> {{trans('custom.use_credit_points')}}</strong>
                                (
                                {{trans('custom.balance')}}:
                                <i class="fa fa-inr" aria-hidden="true"></i> <span class="wallet_bal"><?php echo number_format($usersWallet['balance'], 2); ?></span> <?php //echo $payment_method_msg; ?>
                                )
                                

                                <div class="form-group" style="display:none;">
                                  <label for="billing-name" class="control-label">{{trans('custom.after_payment')}}:</label>
                                  <i class="fa fa-inr" aria-hidden="true"></i>&nbsp;
                                  <span id="after_payment_bal">
                                  </span>
                                </div>
                                
                              </div>
                              <?php
                            }
                            ?>

                  <!--<label class="control-label required"></label>-->
                 
                              <?php
                              //pr($wallet_allowed);
                              $payment_method = config('custom.payment_method');
                              $payment_method_msg = '';

                              if(!empty($payment_method) && count($payment_method) > 0){
                                foreach($payment_method as $pm_key=>$pm_val){
                                  //$payment_method_checked = 'disabled';
                                  $payment_method_checked = '';
                                  if($pm_key == 'wallet' && $wallet_allowed === 1){
                                    /*if($usersWallet['balance'] < $total_amount_gst || $wallet_allowed === 0){
                                      $payment_method_checked = 'disabled';
                                      $payment_method_msg = '(<strong>Less than Order Total</strong>)';
                                    }
                                  
                                  ?>
                                  <input type="radio" name="payment_method" value="{{ $pm_key }}" {{ $payment_method_checked }} class="tooltip_title" title="Less than Order Total">&nbsp;{{ $pm_val }} <br>
                                  <?php*/
                                }
                                else if($pm_key != 'wallet'){
                                  ?>
                                  <input type="radio" name="payment_method" value="{{ $pm_key }}" {{ $payment_method_checked }} >&nbsp;{{trans('custom.'.$pm_key) }}<br>
                                  <?php
                                }
                                }
                              }
                              ?>
                            </div>

                        <p>
                          <strong>{{trans('custom.payable_ammount')}}: </strong><i class="fa fa-inr" aria-hidden="true"></i><span class="payable_amount">{{ number_format(round($payable_amount),2) }}</span>
                        </p>

                          
                          <div class="form-group">
                           <button type="submit" class="btn btn-default place_order_btn">{{trans('custom.place_your_order_and_pay')}} </button>
                         </div>


                            </div>



						

                    <?php //pr( $customer_address); ?>

						<div class="borderdiv" style="padding-top:0px">
                    <?php if(!empty($customer_address['billing_address'])) { ?>
                    <div class="col-md-6" style="border-right:1px solid #ddd">

                        <div class="checkout-box">

                            <h2>{{trans('custom.billing_address')}}</h2>


                            <?php
                            if(isset($customer_address['billing_address']['billing_gst'])){
                              ?>
                             
                                <label for="billing-name" class="control-label">{{trans('custom.gst_no')}}:</label>
                                <?php echo $customer_address['billing_address']['billing_gst'];  ?>
                              <br>
                              <?php
                            }
                            elseif(isset($customer_address['billing_address']['billing_aadhar_number'])){
                              ?>
                              
                                <label for="billing-name" class="control-label">{{trans('custom.aadhaar_no')}}:</label>
                                <?php echo $customer_address['billing_address']['billing_aadhar_number'];  ?>
                              <br>
                              <?php
                            }
                            ?>

                            <strong><?php echo $customer_address['billing_address']['billing_company_name'];  ?></strong>

                            <div class="form-group">
                                <?php 
                if(!empty($customer_address['billing_address']['billing_name']))
                {
                   echo $customer_address['billing_address']['billing_name']."<br>"; 
                }
                 if(!empty($customer_address['billing_address']['billing_address_1']))
                {
                   echo $customer_address['billing_address']['billing_address_1']."<br>"; 
                }  
                 if(!empty($customer_address['billing_address']['billing_address_2']))
                {
                   echo $customer_address['billing_address']['billing_address_2'].",<br>"; 
                } 
                if(!empty($customer_address['billing_address']['billing_city']))
                {
                    echo get_by_id($id= $customer_address['billing_address']['billing_city'], $id_name='id', $table='cities', $field='name').", ";

                }  
                if(!empty($customer_address['billing_address']['billing_state']))
                {
                    echo get_by_id($id= $customer_address['billing_address']['billing_state'], $id_name='id', $table='states', $field='name'); 
                }
                
                if(!empty($customer_address['billing_address']['billing_zipcode']))
                {
                   echo '-'.$customer_address['billing_address']['billing_zipcode']."<br>"; 
                } 
                 if(!empty($customer_address['billing_address']['billing_phone']))
                {
                   echo 'Phone: '.$customer_address['billing_address']['billing_phone']."<br>"; 
                } 
                ?>
                            </div>

                            
                      </div>

                    </div>
                    <?php } ?>

                    <?php if(!empty($customer_address['delivery_address'])) { ?>
                    <div class="col-md-6">

                        <div class="checkout-box">

                            <h2>{{trans('custom.shipping_address')}}</h2>


                            <?php
                            if(isset($customer_address['delivery_address']['delivery_gst'])){
                              ?>
                              
                                <label for="billing-name" class="control-label">{{trans('custom.gst_no')}}:</label>
                                <?php echo $customer_address['delivery_address']['delivery_gst'];  ?>
                                <br>
                              <?php
                            }
                            elseif(isset($customer_address['delivery_address']['delivery_aadhar_number'])){
                              ?>
                              
                                <label for="billing-name" class="control-label">{{trans('custom.aadhaar_no')}}:</label>
                                <?php echo $customer_address['delivery_address']['delivery_aadhar_number'];  ?>
                                <br>
                              <?php
                            }
                            ?>
                            

                            <strong><?php echo $customer_address['delivery_address']['delivery_company_name']; ?></strong>


                            <div class="form-group">

                            <?php 
                if(!empty($customer_address['delivery_address']['delivery_name']))
                {
                   echo $customer_address['delivery_address']['delivery_name']."<br>"; 
                }
                 if(!empty($customer_address['delivery_address']['delivery_address_1']))
                {
                   echo $customer_address['delivery_address']['delivery_address_1']."<br>"; 
                }  
                 if(!empty($customer_address['delivery_address']['delivery_address_2']))
                {
                   echo $customer_address['delivery_address']['delivery_address_2'].",<br>"; 
                } 
                if(!empty($customer_address['delivery_address']['delivery_city']))
                {
                    echo get_by_id($id= $customer_address['delivery_address']['delivery_city'], $id_name='id', $table='cities', $field='name').", ";  ;

                }  
                if(!empty($customer_address['delivery_address']['delivery_state']))
                {
                    echo get_by_id($id= $customer_address['delivery_address']['delivery_state'], $id_name='id', $table='states', $field='name'); 
                }
                
                if(!empty($customer_address['delivery_address']['delivery_zipcode']))
                {
                   echo '-'.$customer_address['delivery_address']['delivery_zipcode']."<br>"; 
                } 
                 if(!empty($customer_address['delivery_address']['delivery_phone']))
                {
                   echo 'Phone: '.$customer_address['delivery_address']['delivery_phone']."<br>"; 
                } 
                ?>
                            </div>


                            


                      </div>



                    </div>

                    <?php
                  }
                  ?>
</div>


                </div>


                

                            
                

                      
                           <!-- <button type="submit" class="btn btn-default">Place Your Order and Pay </button>-->

                    </form>
                    
                  </div>


                  <h2>{{trans('custom.shopping_cart')}} </h2>
                  <div class="tableCustomNew table-responsive cart_list_table">
                    <table class="tableCustom table">

                      <tr>
                        <th width="5%"> {{trans('custom.item')}}</th>
                        <th width="40%"> {{trans('custom.item_code')}}</th>
                        <th width="28%"> {{trans('custom.price_per_set')}}</th>
                        <th class="noWrap mobile-th" width="5%"> {{trans('custom.no_of_sets')}}</th>
                        <th width="17%"> {{trans('custom.subtotal')}}</th>
                        <?php
                        /*
                        <th width="5%"> </th>
                        */
                        ?>
                      </tr>

                      <?php

                      //prd($cartItems);

                      $total_qty = 0;
                      $total_amount = 0;
                      $total_gst = 0;
                      $total_amount_gst = 0;

                      $gst_arr = [];

                      foreach($cartItems as $cart){

                        $cart->inventories;

                        $availabeQty = '';

                        $cart = $cart->toArray();

                        $TaxRate = "";

                        if($cart['tax_rate_id'] > 0){
                          $TaxRate = CustomHelper::GetTaxRate($cart['tax_rate_id'], $col_name='rate');
                        }

                        $gst = (!empty($TaxRate))?$TaxRate:config('custom.gst_default');

                        $gst_arr[] = $gst;

                        //prd($cart);

                        $quantity = $cart['quantity'];

                        $product = $ProductModel->where('id', $cart['product_id'])->first();

                        //prd($product);

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

                        $product_price = $product->price;

                        $defaultPhoto = $product->defaultPhoto;

                        $storage = Storage::disk('public');

                        $thumb_path = (isset($defaultPhoto->thumb_path))?$defaultPhoto->thumb_path:'';
                        $photo_name = (isset($defaultPhoto->name))?$defaultPhoto->name:'';

                        $getUsersCategoryPrice = $product->category->getUsersCategoryPrice($user_id);

                        if(isset($getUsersCategoryPrice->price_type_id) && $getUsersCategoryPrice->price_type_id > 0){
                          $price_type_id = $getUsersCategoryPrice->price_type_id;

                          $PriceType = $product->PriceType($product->id, $price_type_id);

                          //prd($PriceType);

                          if(count($PriceType)){
                            if(!empty($PriceType->price) && $price_type_id == $PriceType->price_type_id){
                              $product_price = $PriceType->price;
                            }
                          }
                        }

                        $product_gst_price = $product_price*(100+$gst)/100;
                        $price_per_set = $product_price*$product->piece_per_set;

                        //$total_price = number_format(($price_per_set*$quantity), 2);
                        $total_price = ($price_per_set*$quantity);

                        $price_gst = $total_price*($gst)/100;

                        $price_per_set_total = $total_price*(100+$gst)/100;

                        //prd($total_price);


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
                          <td><a class="carttitle cart_title_view" data-title="{{trans('custom.item_code')}}" href="{{url('products/detail/'.$product->uri)}}">{{ $product->code }}</a>
                            <p class="perItem">({{ $product->piece_per_set }} {{trans('custom.items_per_set')}})</p>
                            <p class="perItem"><i class="fa fa-inr" aria-hidden="true"></i> {{ $product_price }} / {{trans('custom.pc')}} + {{trans('custom.gst')}} @ {{$gst}}%</p>
                            <div class="mobile_view price_set_box" data-title="{{trans('custom.price_per_set')}}">
                              <span class="inline_block"><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($price_per_set, 2) }} + {{trans('custom.gst')}} @ {{$gst}}% </span>
                            </div>
                            <div class="mobile_view no_sets_outer">
                                <div class="no_sets_box" data-title="{{trans('custom.no_of_sets')}}">{{ $quantity }}<?php echo $availabeQty;?></div>
                                 <div class="sizeqty"><small>{{trans('custom.size')}}</small> <small>{{trans('custom.qty')}}</small></div>
                                <?php
                                //pr($availableInventory);
                                if(count($availableInventory) > 0){
                                  foreach($availableInventory as $ai){
                                    if(isset($inventory_id_arr[$ai->id]['qty'])){
                                      $inventory_id_qty = $inventory_id_arr[$ai->id]['qty'];
                                      //echo "(Size : $ai, Qty : $inventory_id_qty)<br>";
                                      echo '<div class="sizeqty qtyname"><small>'.$ai->name.'</small> <small>'.$inventory_id_qty.'</small></div>';
                                    }
                                  }
                                }
                                ?>
                            </div>
                          </td>
                          <td class="mobile_hide"><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($price_per_set, 2) }} + {{trans('custom.gst')}} @ {{$gst}}%</td>
                          <td align="center" class="mobile_hide">
                          {{ $quantity }}<?php echo $availabeQty;?>
							  <div class="sizeqty"><small>{{trans('custom.size')}}</small> <small>{{trans('custom.qty')}}</small></div>
                          <?php
                          //pr($availableInventory);
                          if(count($availableInventory) > 0){
                            foreach($availableInventory as $ai){
                              if(isset($inventory_id_arr[$ai->id]['qty'])){
                                $inventory_id_qty = $inventory_id_arr[$ai->id]['qty'];
                                //echo "(Size : $ai, Qty : $inventory_id_qty)<br>";
								  
                                echo ' <div class="sizeqty qtyname"><small>'.$ai->name.'</small> <small>'.$inventory_id_qty.'</small></div>';
                              }
                            }
                          }
                          ?>
                          </td>
                          <td>
                          <span>{{trans('custom.total')}}: <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($price_per_set_total, 2)}}</span>
                          <?php
                          /*
                          <p class='perItem'>(Including GST {{$gst}}%)</p>
                          */
                          ?>
                          <p class='perItem'>(
                            <?php
                            echo $currency_symb.number_format($total_price, 2).' + '.$currency_symb.number_format($price_gst, 2).trans('custom.gst');
                            ?>
                            )
                          </p>
                          </td>
                          <?php
                        /*
                        <td>

                            <form method="POST" action="{{url('cart/remove/'.$cart['id'])}}" onsubmit="return confirm('Are you sure to remove this item from your Cart?')">
                              {{ csrf_field() }}
                              <input type="hidden" name="_method" value="DELETE">
                              <button type="submit" class="btn deletebtn"><i aria-hidden="true" class="fa fa-trash-o"></i><!--<img src="{{url('public/assets/img/del.png')}}" />--></button>
                            </form>
                          </td>
                        */
                        ?>
                          
                        </tr>

                        <?php
                      }
                      rsort($gst_arr);
                      //pr($gst_arr);

                      $gst_for_ship = $gst_arr[0];

                      $shipping_gst = $shipping_fee*($gst_for_ship/100);

                      $total_amount_gst = $total_amount_gst + $shipping_fee+$shipping_gst;

                      $total_order_gst = $total_gst + $shipping_gst;
                      ?>
                    </table>
                  </div>
                    <div class=" col-sm-12 col-md-3  text-center dataItems checkout_bot_info">
                      <div class="">
                        <p class="noPaddings paddingsMicro">{{trans('custom.total_qty')}} : {{$total_qty}}</p>
                        <!-- <p class="noPaddings paddingsMini"></p> -->
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-6 pull-right dataItems checkout_bot_info">
                      <div class="">
                        <p style="font-size:14px;"><span class="checkout-span2">{{trans('custom.order_subtotal')}}</span> <span class="checkout-span1"> <i class="fa fa-inr" aria-hidden="true"></i>{{ number_format($total_amount, 2) }}</span></p>
                        
                        <p style="font-size:14px;">
                         <span class="checkout-span2"> {{trans('custom.shipping_charges')}} </span><span class="checkout-span1"> <i class="fa fa-inr" aria-hidden="true"></i>{{ number_format($shipping_fee, 2) }}</span>
                          
                      </p>
                        

                        <p style="font-size:14px;">
                         <span class="checkout-span2"> {{trans('custom.total_gst')}} </span><span class="checkout-span1"> <i class="fa fa-inr" aria-hidden="true"></i>{{ number_format($total_order_gst, 2) }}</span>
                            </p>
						  <p  style="font-size:14px;">
                          ({{trans('custom.order')}} : <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} + {{trans('custom.shipping')}} : <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($shipping_gst, 2) }} at {{$gst_arr[0]}}%)
                        </p>

                        <p class="paddingsMicro"><span style="width:47%; float:left;"> {{trans('custom.total_amount')}}</span> :&nbsp;<span><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format(round($total_amount_gst),2) }}</span></p>

                        <?php
                        /*
                        <p class="paddingsMicro">Total Amount :&nbsp;<span><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount_gst,2) }}</span></p>
                        <!-- <p class="paddingsMini"></p> -->
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }} + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} GST + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($shipping_fee, 2) }} Shipping Fee)</p>
                        */
                        ?>

                        <?php
                        /*
                        <p style="font-size: 12px;">
                          ({{trans('custom.shipping_charges_calculation_based_on_city_weight', ['city'=>$city_name, 'weight'=>$total_weight])}} {{trans('custom.city', ['City'])}}:{{$city_name}} {{trans('custom.and', ['and'])}} {{trans('custom.weight', ['Weight'])}}:{{$total_weight}} kg)
                        </p>
                        */
                        ?>

                        <p style="font-size: 12px;">
                          ({{trans('custom.shipping_charges_calculation_based_on_city_weight', ['city'=>$city_name, 'weight'=>$total_weight])}})
                        </p>
                        
                      </div>
                    </div>

                  <div class="form-group" style="padding-top: 10px;">
                   <button type="submit" class="btn btn-default pull-right place_order_btn" onclick="submit_checkout()">{{trans('custom.place_your_order_and_pay')}} </button>
                 </div>

                  <?php
                }
                else{
                  ?>
                  <p>{{trans('custom.your_cart_is_empty')}}</p>
                  <p><a href="{{url('products/feeds')}}">{{trans('custom.click_here_to_shop')}}</a></p>
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

    <script src="{{url('public/assets')}}/js/load_spinner.js"></script>

    <script type="text/javascript">

    $(".tooltip_title").tooltip();

    function submit_checkout(){
      if(validate_place_order()){
        document.form_checkout.submit();
      }
      else{
        $('html,body').animate({
          scrollTop: $(".cart_sbt_msg").offset().top},
          'slow');
      }
    }

    function validate_place_order()
    {
      var place_order_btn = $(".place_order_btn");

      loadSpinner(place_order_btn);

      if($("input[name='payment_method']").is(":checked")){
        return true;
      }
      else{
        var payable_amount = $(".payable_amount").text();
        payable_amount = payable_amount.replace(",", "");
        payable_amount = parseFloat(payable_amount);
        
        //console.log(payable_amount);
        if(payable_amount < 1 || payable_amount === 0){
          return true;
        }
        else{
          var cart_msg = "<?php echo trans('custom.alert_please_select_payment_method');?>";
          if($("input[name='use_wallet']").is(":checked")){
            var cart_msg = "<?php echo trans('custom.please_select_payment_method_for_rest_amount');?>";
          }
          $(".cart_sbt_msg").html('<div class="alert alert-danger alert-dismissable" style="display"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+cart_msg+'.</div>');
        }

        removeSpinner(place_order_btn);
        
        return false;
      }

    }

      function show_wallet_balance(){
        if($("input[name='payment_method']").is(":checked")){

          if($("input[name='payment_method']:checked").val() == "wallet"){

            var usersWallet_balance = parseFloat("{{$usersWallet['balance']}}");
            var total_amount_gst = parseFloat("{{$total_amount_gst}}");
            var payable_amount = parseFloat("{{round($payable_amount)}}");
            var after_wallet_payable_amount = parseFloat("{{round($after_wallet_payable_amount)}}");

            //console.log("total_amount_gst="+total_amount_gst);
            //console.log("usersWallet_balance="+usersWallet_balance);

            if(total_amount_gst > usersWallet_balance){
              $(".payable_amount").text("{{number_format(round($after_wallet_payable_amount), 2)}}");
            }
            else{

              $(".payable_amount").text("0");

              after_payment_bal = "{{ number_format(($usersWallet['balance'] - $total_amount_gst), 2) }}";

              $("#after_payment_bal").text(after_payment_bal);

              $("#after_payment_bal").parent().show();
            }
          }
          else{
            $(".payable_amount").text("{{number_format(($payable_amount), 2)}}");
            $("#after_payment_bal").parent().hide();
          }
        }
      }

    /*show_wallet_balance();

    $(document).on("click", "input[name='payment_method']", function(){
       show_wallet_balance();
    });*/

    function use_wallet_balance(){
        if($("input[name='use_wallet']").is(":checked")){

            var usersWallet_balance = parseFloat("{{$usersWallet['balance']}}");
            var total_amount_gst = parseFloat("{{$total_amount_gst}}");
            var payable_amount = parseFloat("{{round($payable_amount)}}");
            var after_wallet_payable_amount = parseFloat("{{round($after_wallet_payable_amount)}}");

            //console.log("total_amount_gst="+total_amount_gst);
            //console.log("usersWallet_balance="+usersWallet_balance);

            if(total_amount_gst > usersWallet_balance){
              $(".payable_amount").text("{{number_format(round($after_wallet_payable_amount), 2)}}");

              //$(".wallet_bal").text("0");
              $("#after_payment_bal").text("0");
              $("#after_payment_bal").parent().show();
            }
            else{

              $(".payable_amount").text("0");

              after_payment_bal = "{{ number_format(($usersWallet['balance'] - $total_amount_gst), 2) }}";

              $("#after_payment_bal").text(after_payment_bal);

              $("#after_payment_bal").parent().show();

              $("input[name='payment_method']").prop("disabled", true);
              $("input[name='payment_method']").prop("checked", false);
            }
          
        }
        else{
            $(".payable_amount").text("{{number_format(round($payable_amount), 2)}}");
            //$(".wallet_bal").text("{{number_format($usersWallet['balance'], 2)}}");
            $("#after_payment_bal").parent().hide();
            $("input[name='payment_method']").prop("disabled", false);
          }
      }

    $(document).on("click", "input[name='use_wallet']", function(){
       use_wallet_balance();
    });

      
    </script>
	<style>
	.checkout-span1{color: #000!important;font-size: 17px !important}
	.checkout-span2{color:#333 !important;font-size:13px !important; width: 50%; float: left;}
	.checkout-span2:after{      content: ":"; float: right;  font-size: 19px;  margin-right: 20px;}
	</style>

  </body>
  </html>