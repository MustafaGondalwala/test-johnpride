@include('emails.include.email_header')
<table class="table" >
 <tr>
  <td colspan="2"> <?php echo $tag_line; ?>, please find the details.</td>
</tr>
</table>   


<?php
if(!empty($res) && $res->count() > 0){
  ?>

  <table class="table table-striped" >
    <tr>
      <td><b>Order Id :</b> {{$res->order_id}}</td>
      <td><b>Added on: </b> <?php $added_on = CustomHelper::DateFormat($res->created_at, 'd F y'); ?>{{$added_on}}</td>
      <td><b>Order Status: </b> <?php echo $order_model->orderStatus($res->order_status); ?></td>
      <td><b>Payment Status: </b>  <?php echo  ($res->payment_status==1)?'Recieved':'Pending';  ?></td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: 13px;"><b>Billing Address</b> <br>
        <strong>Name :</strong> {{$res->billing_first_name.' '.$res->billing_last_name}}<br>
        <strong>Email :</strong> {{$res->billing_email}} <br>
        <strong>Phone :</strong> {{$res->billing_phone}} <br>
        <strong>Address1 :</strong> {{$res->billing_address1}} <br>
        <strong>Address2 :</strong> {{$res->billing_address2}}<br>
        <strong>City :</strong> {{$billing_city->name}} <br>
        <strong>Pin Code :</strong> {{$res->billing_pincode}} <br>

        <strong>State :</strong> {{$billing_state->name}} <br>
        <strong>Country :</strong> {{$billing_country->name}} 
      </td> 
      <td colspan="2" style="font-size: 13px;"><b>Shipping Address</b>   <br>
        <strong>Name :</strong> {{$res->shipping_first_name.' '.$res->shipping_last_name}}<br>
        <strong>Email :</strong> {{$res->shipping_email}} <br>
        <strong>Phone :</strong> {{$res->shipping_phone}} <br>
        <strong>Address1 :</strong> {{$res->shipping_address1}} <br>
        <strong>Address2 :</strong> {{$res->shipping_address2}} <br>
        <strong> City : </strong>{{$shipping_city->name}} <br>
        <strong>Pin Code :</strong> {{$res->shipping_pincode}} <br>
        <strong>State :</strong> {{$shipping_state->name}} <br>
        <strong>Country :</strong> {{$shipping_country->name}} <br>
      </td>
    </tr> 
  </table>

  <?php

  $from_currency = (session()->has('from_currency'))?session('from_currency'):'INR';
  $to_currency = (session()->has('to_currency'))?session('to_currency'):'INR';

  $currency_symbol_arr = config('custom.currency_symbol_arr');

  $currency_symbol = (isset($currency_symbol_arr[$to_currency]))?$currency_symbol_arr[$to_currency]:'';

  if($res->order_products->count()) {
    ?>


    <table class="table table-striped">

      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total (Rs)</th> 
      </tr>

      <?php
      foreach($res->order_products as $items) {
        ?>

        <tr>

          <td>{{$items->product_name}} <br>

            <?php 
            if(!empty($items->products_images)){ 
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
            if(!empty($items->fabric_generator)){
              $design_data = json_decode($items->fabric_generator); 
//pr($design_data);?>

<?php if(!empty($design_data->layout)) { ?>
  <b>Layout :</b> {{$design_data->layout}} <br>
<?php } ?>

<b>Rotate :</b> {{$design_data->rotate}} <br>

<b>Scale :</b> {{$design_data->scale}} <br>

<?php } ?>


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


<?php
}
?>

@include('emails.include.email_footer')