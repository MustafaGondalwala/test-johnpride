<table>
    <thead>
    <tr>
        <th>Order ID</th>
        <th>Order No.</th>
        <th>Billing Address</th>
        <th>Shipping Address</th>
        <th>Added on</th>
        <th>Order Status</th>
        <th>Payment Status</th>
        <th>Order Items</th>
        <th>Sub Total</th>
        <th>Discount</th>
        <th>Coupon Discount</th>
        <th>Tax</th>
        <th>Shipping Charge</th>
        <th>Used Wallet Amount</th>
        <th>Online Payment</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
        <?php

        $storage = Storage::disk('public');

        $img_path = 'products/';
        $thumb_path = $img_path.'thumb/';

        if(!empty($orders)){
            foreach($orders as $order){
                $orderStatusDetails = $order->orderStatusDetails;

                $orderStatus = (isset($orderStatusDetails->name))?$orderStatusDetails->name:'';

                $added_on = CustomHelper::DateFormat($order->created_at, 'd F y');

                $orderItems = $order->orderItems;

                $billingCityName = '';
                $billingStateName = '';
                $billingCountryName = '';


                $billingCity = $order->billingCity;
                $billingState = $order->billingState;
                $billingCountry = $order->billingCountry;

                if(isset($billingCity->name) && !empty($billingCity->name)){
                    $billingCityName = $billingCity->name;
                }
                if(isset($billingState->name) && !empty($billingState->name)){
                    $billingStateName = $billingState->name;
                }
                if(isset($billingCountry->name) && !empty($billingCountry->name)){
                    $billingCountryName = $billingCountry->name;
                }


                $shippingCityName = '';
                $shippingStateName = '';
                $shippingCountryName = '';


                $shippingCity = $order->shippingCity;
                $shippingState = $order->shippingState;
                $shippingCountry = $order->shippingCountry;


                if(isset($shippingCity->name) && !empty($shippingCity->name)){
                    $shippingCityName = $shippingCity->name;
                }
                if(isset($shippingState->name) && !empty($shippingState->name)){
                    $shippingStateName = $shippingState->name;
                }
                if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
                    $shippingCountryName = $shippingCountry->name;
                }

                $orderStatusArr = config('custom.order_status_arr');

                $billingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=true, $isPhone=true, $isEmail=true);
                $shippingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=false, $isPhone=true, $isEmail=true);

                ?>
                <tr>
                    <td>{{$order->id }}</td>
                    <td>{{$order->order_no}}</td>
                    <td><?php echo implode('<br/>', $billingAddrArr); ?></td>
                    <td><?php echo implode('<br/>', $shippingAddrArr); ?></td>
                    <td>{{$added_on}}</td>
                    <td>{{$orderStatus}}</td>
                    <td>{{ucfirst($order->payment_status)}}</td>
                    <td>

                        <?php
                        if(!empty($orderItems) && count($orderItems) > 0){
                            ?>
                            <table>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Sale Price</th>
                                    <th>Quantity</th>
                                    <th>Total (Rs)</th>
                                </tr>

                                <?php

                    //prd($orderItems->toArray());

                                foreach($orderItems as $item){

                        //pr($item->toArray());

                                    $product_id = $item->product_id;

                                    $product = $item->productDetail;

                                    //prd($product);

                                    //$defaultImage = $product->defaultImage;
                                    //$productImages = $product->productImages;

                                    $imgUrl = '';

                                    //if(!empty($defaultImage) && count($defaultImage) > 0){
                                    //    if(!empty($defaultImage->image) ){
                                    //        $imgUrl = $defaultImage->image;
                                    //    }
                                    //}

                                    //if(empty($imgUrl)){
                                    //    if(!empty($productImages) && count($productImages) > 0){
                                    //        foreach($productImages as $prodImg){
                                    //            if(!empty($prodImg->image) ){
                                    //                $imgUrl = $prodImg->image;
                                    //                break;
                                    //            }
                                    //        }
                                    //    }
                                    //}

                                    ?>

                                    <tr>

                                        <td>{{$item->product_name}} <br>

                                            <?php 
                                            /*if(!empty($imgUrl)){ 
                                                ?>
                                                <img src="{{ url($imgUrl) }}" style="width: 75px; height:  75;"> <br>
                                                <?php
                                            }*/
                                            ?>


                                        </td>
                                        <td>{{$item->price}}</td>
                                        <td>{{$item->item_price}}</td>
                                        <td>{{$item->qty}}</td>
                                        <td>{{$item->item_price*$item->qty}}</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </table>
                                <?php
                            }
                            ?>
                        



                    </td>

                    <td>{{$order->sub_total}}</td>

                    <td>
                        <?php
                        if($order->discount > 0){
                            echo $order->discount;
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if($order->coupon_discount > 0){
                            echo $order->coupon_discount;
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if($order->tax > 0){
                            echo $order->tax;
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if($order->shipping_charge > 0){
                            echo $order->shipping_charge;
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if($order->used_wallet_amount > 0){
                            echo $order->used_wallet_amount;
                        }
                        ?>
                    </td>

                    <td>{{$order->total - $order->used_wallet_amount}}</td>

                    <td>{{$order->total}}</td>

                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>