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
            
            
                <table class="table table-striped" >
                    <tr>
                        <td><b>Order Id :</b> {{$res->order_id}}
                            <br><b>Added on: </b> <?php $added_on = CustomHelper::DateFormat($res->created_at, 'd F y'); ?>{{$added_on}}

                            <br><b>Order Status: </b> <?php echo $order_model->orderStatus($res->order_status); ?>
                            <br><b>


                            Payment Status: </b>  <?php echo  ($res->payment_status==1)?'Recieved':'Pending';  ?> 

                        </td>
                    <td><b>Billing Address</b>   <br>
                        Name : {{$res->billing_first_name.' '.$res->billing_last_name}}<br>
                        Email : {{$res->billing_email}} <br>
                        Phone : {{$res->billing_phone}} <br>
                        Address1 : {{$res->billing_address1}} <br>
                        Address2 : {{$res->billing_address2}}<br>
                        City : {{$billing_city->name}} <br>
                         Pin Code : {{$res->billing_pincode}} <br>
                        
                        State : {{$billing_state->name}} <br>
                        Country : {{$billing_country->name}} 
                        
                       
                        
                              

                        <br><b>Shipping Address</b>   <br>
                        Name : {{$res->shipping_first_name.' '.$res->shipping_last_name}}<br>
                        Email : {{$res->shipping_email}} <br>
                        Phone : {{$res->shipping_phone}} <br>
                        Address1 : {{$res->shipping_address1}} <br>
                        Address2 : {{$res->shipping_address2}} <br>
                        City : {{$shipping_city->name}} <br>
                        Pin Code : {{$res->shipping_pincode}} <br>
                        State : {{$shipping_state->name}} <br>
                        Country : {{$shipping_country->name}} <br>
                        
                       
                       
                       


                         </td>

                    </tr>
                   
                      
                       
                </table>
           


                <?php if($res->order_products->count()) { ?>


                <table class="table table-striped">

                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total (Rs)</th>
                        
                        
                    </tr>

                       <?php foreach($res->order_products as $items) { ?>
                   
                        <tr>

                            <td>{{$items->product_name}} <br>

                                <?php 
                             if(!empty($items->products_images))
                                { 
                                 ?>
                             <img src="{{ url($items->products_images) }}" style="width: 75px; height:  75;"> <br>
                             <?php } ?>

                             <?php if(!empty($items->size)) { ?>
                                <b>Size :</b> {{$items->size}} <br>
                             <?php } ?>

                             <?php if(!empty($items->length)) { ?>
                                <b>Length :</b> {{$items->length}} Meter <br>
                             <?php } ?>


                             <?php

                             $design_data = [];
                             if(!empty($items->fabric_generator))
                             {
                                $design_data = json_decode($items->fabric_generator); 
                                //pr($design_data);?>

                             <?php if(!empty($design_data->layout)) { ?>
                                <b>Layout :</b> {{$design_data->layout}} <br>
                             <?php } ?>

                                <b>Rotate :</b> {{$design_data->rotate}} <br>
        
                                <b>Scale :</b> {{$design_data->scale}} <br>

                            <?php } ?>
                              




                            </td>
                            <td>{{$items->price}}</td>
                            <td>{{$items->qty}}</td>
                            <td>{{$items->price*$items->qty}}</td>
                        </tr>
                        <?php } ?>

                        

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Sub Total</b></td>
                            <td>{{$res->sub_total}}</td>
                        </tr>

                        <?php if($res->discount > 0){?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Discount</b></td>
                            <td>{{$res->discount}}</td>
                        </tr>

                        <?php } ?>

                        <?php if($res->tax > 0){?>

                         <tr>
                            <td></td>
                            <td></td>
                            <td><b>Tax</b></td>
                            <td>{{$res->tax}}</td>
                        </tr>

                        <?php } ?>

                        <?php if($res->shipping_charge > 0){?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Shipping Charge</b></td>
                            <td>{{$res->shipping_charge}}</td>
                        </tr>
                        <?php } ?>

                        <?php if($res->used_wallet_amount > 0){?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Used Wallet Amount</b></td>
                            <td>{{$res->used_wallet_amount}}</td>
                        </tr>

                        <?php } ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td>{{$res->total}}</td>
                        </tr>


                      
                </table>

                <?php } ?>

                

                
           
            <?php
        }
       ?>
     
  </div>
  </div>
</section>
 
@include('common.footer')


</body>
</html>