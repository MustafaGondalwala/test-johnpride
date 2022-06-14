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

                      $currency_symb = '<i class="fa fa-inr" aria-hidden="true"></i>';

                      //prd($cartItems);


                      foreach($cartItems as $cart){

                        $cart->inventories;

                        $availabeQty = '';

                        $cart = $cart->toArray();

                        //prd($cart);

                        $quantity = $cart['quantity'];

                        $product = $ProductModel->where('id', $cart['product_id'])->first();

                        //prd($product);

                        $TaxRate = "";

                        if($cart['tax_rate_id'] > 0){
                          $TaxRate = CustomHelper::GetTaxRate($cart['tax_rate_id'], $col_name='rate');
                        }

                        $gst = (!empty($TaxRate))?$TaxRate:config('custom.gst_default');

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

                        $thumb_path = $defaultPhoto->thumb_path;
                        $photo_name = $defaultPhoto->name;


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
                          <td><a class="carttitle" href="{{url('products/detail/'.$product->uri)}}">{{ $cart['product_name'] }}</a>
                            <p class="perItem">({{ $product->piece_per_set }} Items Per Set)</p>
                            <p class="perItem"><i class="fa fa-inr" aria-hidden="true"></i> {{ $product_price }} / pc + GST @ {{$gst}}%</p>
                          </td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($price_per_set, 2) }} + GST @ {{$gst}}%</td>
                          <td align="center">
                          {{ $quantity }}<?php echo $availabeQty;?>
							  <div class="sizeqty"><small>Size</small> <small>Qty</small></div>
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
                          </td>
                          <td>
                          <span>Total: <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($price_per_set_total, 2)}}</span>
                          <?php
                          /*
                          <p class='perItem'>(Including GST {{$gst}}%)</p>
                          */$total_price
                          ?>
                          <p class='perItem'>(
                            <?php
                            echo $currency_symb.number_format($total_price, 2).' + '.$currency_symb.number_format($price_gst, 2).' GST';
                            ?>
                            )
                          </p>
                          </td>
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
                        <p class="paddingsMini"><span><i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount_gst,2) }}</span></p>
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }} + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} GST)</p>
                        <p style="font-size: 12px;">(Shipping charges extra)</p>
                        <?php
                        /*
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }} + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} tax + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($shipping_fee, 2) }} Shipping Fee)</p>
                        */
                        ?>
                      </div>
                    </div>
                  </div>
