<!DOCTYPE html>
<html>
<head>

  @include('common.head')

</head>
<body>

  @include('common.header')
 
  <section class="fullwidth innerpage ordersuccessmain"> 
    <div class="container">
      @include('snippets.front.flash')
      
		 <h1 class="heading">Order Details</h1> <a href="{{url('products')}}" class="cont-btn">Continue Shopping</a>
      <div class="row"> 
        <div class="col-md-12">

          Hi {{$order->billing_name}}, please find the order details below:    


        </div>
      </div>

      <?php
      //prd($order->toArray());
      //if(!empty($order) && $order->count() > 0){
      if(!empty($order)){

        //$order = $order[0];

        $address = $order->billing_address;
        $locality = $order->billing_locality;
        $pincode = $order->billing_pincode;
        // /prd($order->toArray());

        $billingCity = $order->billingCity;
        $billingState = $order->billingState;
        $billingCountry = $order->billingCountry;

        $billingCityName = '';
        $billingStateName = '';
        $billingCountryName = '';

        if(isset($billingCity->name) && !empty($billingCity->name)){
          $billingCityName = $billingCity->name;
        }
        if(isset($billingState->name) && !empty($billingState->name)){
          $billingStateName = $billingState->name;
        }
        if(isset($billingCountry->name) && !empty($billingCountry->name)){
          $billingCountryName = $billingCountry->name;
        }


        $address = $order->shipping_address;
        $locality = $order->shipping_locality;
        $pincode = $order->shipping_pincode;

        $shippingCity = $order->shippingCity;
        $shippingState = $order->shippingState;
        $shippingCountry = $order->shippingCountry;

        $shippinCityName = '';
        $shippinStateName = '';
        $shippinCountryName = '';

        if(isset($shippingCity->name) && !empty($shippingCity->name)){
          $shippinCityName = $shippingCity->name;
        }
        if(isset($shippingState->name) && !empty($shippingState->name)){
          $shippinStateName = $shippingState->name;
        }
        if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
          $shippinCountryName = $shippingCountry->name;
        }
 
        ?>
        <div class="orderheading bgcolor row" style="margin-top: 15px;">
          <div class="col-sm-6 col-md-3 ">
            <label>Order No :</label> {{$order->order_no}}
          </div>
          <div class="col-sm-6 col-md-3 ">
            <label>Added on:</label> <?php $added_on = CustomHelper::DateFormat($order->created_at, 'd F y'); ?>{{$added_on}}
          </div>
          <div class="col-sm-6 col-md-3 ">
            <label>Order Status:</label> <?php echo ucfirst($order->order_status); ?>
          </div>
          <div class="col-sm-6 col-md-3">
            <label> Payment Status:</label>  <?php echo ucfirst($order->payment_status);  ?> 
          </div>
        </div> 
        <div class="selectadd row"> 
			<div class="col-sm-6">
				<div class="form-group addselectbox">
					<div class="addlist">
					  <h4><strong>Billing Address</strong></h4>
					  <p><span>Name :</span> {{$order->billing_name}}</p>
					  <p><span>Email :</span> {{$order->billing_email}}</p>
					  <p><span>Phone :</span> {{$order->billing_phone}}</p> 
					  <p><span>Address :</span>  <?php
					  if(!empty($order->billing_address)) { echo $order->billing_address; echo ',';} ?>							

					{{$billingCityName}}</p> 
					<p><span>Pin Code :</span> {{$order->billing_pincode}}</p>
          <p><span>Landmark :</span> {{$order->billing_locality}}</p>
					<p><span>State :</span> {{$billingStateName}}</p> 
					<p><span>Country :</span> {{$billingCountryName}}</p> 
				  </div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group addselectbox">
				  <div class="addlist">
					<h4><strong>Shipping Address</strong></h4>
					<p><span>Name :</span> {{$order->shipping_name}}</p>
					<p><span>Email :</span> {{(!empty($order->shipping_email))?$order->shipping_email:$order->billing_email}}</p>
					<p><span>Phone :</span> {{$order->shipping_phone}}</p>
					<p><span>Address :</span> {{$order->shipping_address}}, {{$shippinCityName}}</p> 
					<p><span>Pin Code :</span> {{$order->shipping_pincode}}</p>
          <p><span>Landmark :</span> {{$order->shipping_locality}}</p>
					<p><span>State :</span> {{$shippinStateName}} </p>
					<p><span>Country :</span> {{$shippinCountryName}} </p>
				  </div>
				</div>
			</div>
      </div>

      <?php

      $orderItems = $order->orderItems;

      if(!empty($orderItems) && $orderItems->count()){
        ?>
		<div class="row">
        <div class="col-sm-12 orderdetilatable table-responsive">


          @include('common._order_details')
</div>

        </div>

        <?php
      }
    }
      ?>

    </div>
  </div>
</section>

<script type="text/javascript">
  fbq('track', 'Purchase', {currency:"INR", value:<?php echo $order->total; ?>});
 </script>


@include('common.footer')


</body>
</html>