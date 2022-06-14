                  <div class="tableCustomNew">
                    <table class="tableCustom table cart_list_table">

                      <tr>
                        <th width="5%"> {{trans('custom.item')}}</th>
                        <th width="40%"> {{trans('custom.item_code')}}</th>
                        <th width="28%"> {{trans('custom.price_per_set')}}</th>
                        <th class="noWrap mobile-th" width="5%"> {{trans('custom.no_of_sets')}}</th>
                        <th width="17%"> {{trans('custom.subtotal')}}</th>
                        <th></th>
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
                                echo '<div class="sizeqty qtyname"><small>'.$ai->name.'</small> <small>'.$inventory_id_qty.'</small></div>';
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
                          */$total_price
                          ?>
                          <p class='perItem'>(
                            <?php
                            echo $currency_symb.number_format($total_price, 2).' + '.$currency_symb.number_format($price_gst, 2).' '.trans('custom.gst');
                            ?>
                            )
                          </p>
                          </td>
                          <td>
                            <div class="remove_cart_item">
                            <form name="itemDeleteForm" method="POST" action="{{url('cart/remove/'.$cart['id'])}}">
                              {{ csrf_field() }}
                              <input type="hidden" name="_method" value="DELETE">
                              <button type="button" class="btn deletebtn"><i aria-hidden="true">X</i></button>
                              <!-- <button type="button" class="btn"><i aria-hidden="true" class="fa fa-edit "></i></button> -->
                            </form>
                            <a href="{{url('products/detail/'.$product->uri)}}" class="btn"><i aria-hidden="true" class="fa fa-edit "></i></a>
                          </div>
                          </td>
                        </tr>

                        <?php
                      }
                      ?>
                    </table>

                    <div class="col-md-3  text-center dataItems">
                      <div class="totalItems">
                        <p class="noPaddings paddingsMicro">{{trans('custom.total_qty')}}  <span class="noPaddings paddingsMini">{{$total_qty}}</span></p>
                        
                      </div>
                    </div>
                    <div class="col-md-6 text-right dataItems pull-right ">
                      <div class="totalItems  text-style">
                        <p class="paddingsMicro">{{trans('custom.total_amount')}} <span> <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount_gst,2) }}</span></p>
                        
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }} + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} {{trans('custom.gst')}})</p>
                        <p style="font-size: 12px;">({{trans('custom.shipping_charges_extra')}})</p>
                        <?php
                        /*
                        <p>(<i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }} + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_gst, 2) }} tax + <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($shipping_fee, 2) }} Shipping Fee)</p>
                        */
                        ?>
                      </div>
                    </div>
                  </div>
