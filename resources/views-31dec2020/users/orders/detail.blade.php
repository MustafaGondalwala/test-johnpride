<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo (isset($meta_title))?$meta_title:'Johnpride';?></title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

<style type="text/css">
@media print {
   .noPrint {display:none;}
   /*.hide-on-screen {display:block;}*/
}
</style>

@include('common.head')


</head>

<body>

@include('common.header')
<section class="fullwidth innerpage">
	<div class="container">
		@include('users.nav')

		<div class="rightcontent">
			<div class="main_inner_box">
				<div class="heading2">Order # {{$subOrderNo}}</div>

				<?php //prd($orders); ?>

				<div id="sccMsg"></div>
				@include('snippets.front.flash')
				<div class="ordersec ">

					<?php
					//prd($order->toArray());
					if(!empty($order) && $order->count() > 0){


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
						$shipping_locality = $order->shipping_locality;
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


						$billingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=true, $isPhone=true, $isEmail=false);
						$shippingAddrArr = CustomHelper::formatOrderAddress($order, $isBilling=false, $isPhone=true, $isEmail=false);

						$paymentMethod = $order->payment_method;
						//echo implode('<br>', $billingAddrArr);

						?>
						<div class="orderheading bgcolor row">
							<div class="col-sm-12">
								<div class="row">
								<div class="top_orders_sec_wrap" style="overflow: hidden;">
									<div class="col-sm-6">
								<div class="top_orders_sec">
										<label>Order No :</label> {{$subOrderNo}}
									</div>
									<div class="top_orders_sec">
										<label>Added on:</label> <?php $added_on = CustomHelper::DateFormat($order->created_at, 'd F y'); ?>{{$added_on}}
									</div>
									<div class="top_orders_sec">
										<label>Order Status:</label> <?php echo ucfirst($subOrder->order_status); ?>
									</div>

									<div class="top_orders_sec">
										<label>Mode of Payment:</label> 
										<?php
										if(!empty($paymentMethod)){
											echo strtoupper($paymentMethod);
										}
									?>
									</div>
										<div class="top_orders_sec">
										<label> Payment Status:</label>  <?php echo ($order->payment_status=='receieved')?'Received':ucfirst($order->payment_status);  ?> 
									</div>


									
								</div>

								<div class="col-sm-6">
									<?php
									$websiteSettingsNamesArr = ['ORDER_RETURN_DAY'];

									$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

									$ORDER_RETURN_DAY = (isset($websiteSettingsArr['ORDER_RETURN_DAY']))?$websiteSettingsArr['ORDER_RETURN_DAY']->value:'';

									$updated_on = CustomHelper::DateFormat($subOrder->updated_at, 'Y-m-d');
									?>

									<?php
									if($subOrder->order_status == 'confirmed'){
									?>
									<button type="button" class="btn btn-success btnCancel noPrint" data-id="{{ $subOrder->id }}" style="background: #e41881;">Cancel Order</button>
									<?php } ?>

									<?php
									if($subOrder->order_status == 'shipped' || $subOrder->order_status == 'delivered'){

										$now = time();
										$updated_date = strtotime($updated_on);
										$datediff = $now - $updated_date;

										//echo $updated_on;
										$diffDays = round($datediff / (60 * 60 * 24));

										if($diffDays <= $ORDER_RETURN_DAY){
											?>
											<button type="button" class="btn btn-success btnReturn noPrint" data-id="{{ $subOrder->id }}" style="background: #e41881;">Return Order</button>
											<?php
										}
									}
									?>

										<?php
										if($subOrder->order_status == 'shipped'){
										?>
										<button class="print_invoice btn btn-success noPrint" style="background: #e41881;" data-id="{{ $subOrder->id }}">Print Invoice</button>
										<?php } ?>

										<!-- <a href="javascript:void(0)" data-id="{{ $order->id }}" class="print_invoice btn btn-sm btn-success" style="background: #e41881;">Print Invoice</a> -->
									

								</div>

								
							</div>
						</div>
							</div>

							<?php /* ?> <div class="col-sm-6">
								sadte
							</div> -->
							<!-- <div class="col-sm-6 col-md-3 ">
								<label>Order No :</label> {{$orderNo}}
							</div>
							<div class="col-sm-6 col-md-3 ">
								<label>Added on:</label> <?php $added_on = CustomHelper::DateFormat($order->created_at, 'd F y'); ?>{{$added_on}}
							</div>
							<div class="col-sm-6 col-md-3 ">
								<label>Order Status:</label> <?php echo $order->order_status; ?>
							</div>
							<div class="col-sm-6 col-md-3">
								<label> Payment Status:</label>  <?php echo $order->payment_status;  ?> 
							</div> <?php */ ?>
						</div> 
						<div class="selectadd row"> 
							<div class="col-sm-6">

								<div class="form-group addselectbox">

									<div class="addlist">
										<h4><strong>Billing Address</strong></h4>
										
										<?php echo '<p>'.implode('</p><p>', $billingAddrArr).'</p>'; ?>
										
										<?php
										/*
										<p><span>Name :</span> {{$order->billing_name}}</p>
										<p><span>Email :</span> {{$order->billing_email}}</p>
										<p><span>Phone :</span> {{$order->billing_phone}}</p> 
										<p><span>Address :</span>  <?php
										if(!empty($order->billing_address)) { echo $order->billing_address; echo ',';} ?>

									{{$billingCityName}}</p> 
									<p><span>Pin Code :</span> {{$order->billing_pincode}}</p>
									<p><span>State :</span> {{$billingStateName}}</p> 
									<p><span>Country :</span> {{$billingCountryName}}</p>
										*/
										?>
										 
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group addselectbox">
								<div class="addlist">
									<h4><strong>Shipping Address</strong></h4>

									<?php echo '<p>'.implode('</p><p>', $shippingAddrArr).'</p>'; ?>
									
									<?php
									/*
									<p><span>Name :</span> {{$order->shipping_name}}</p>
									<p><span>Email :</span> {{(!empty($order->shipping_email))?$order->shipping_email:$order->billing_email}}</p>
									<p><span>Phone :</span> {{$order->shipping_phone}}</p>
									<p><span>Address :</span> {{$order->shipping_address}}, {{$order->shipping_locality}}, {{$shippinCityName}}</p> 
									<p><span>Pin Code :</span> {{$order->shipping_pincode}}</p>
									<p><span>State :</span> {{$shippinStateName}} </p>
									<p><span>Country :</span> {{$shippinCountryName}} </p>
									*/
									?>
									
								</div>
							</div>
						</div>
					</div>
					    <div class="table-responsive"> 

					@include('common._sub_order_details')
				</div>
					<?php
				}
				?>

			</div>
		</div> 
	</div>
</div>
</section>

<!-- Cancel Order Modal -->
<div id="cancelOrderModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Order Cancel</h4>
			</div>
			<div class="modal-body">
				<p>Some text in the modal.</p>
			</div>

		</div>

	</div>
</div>


<!-- Return Order Modal -->
<div id="returnOrderModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Order Return</h4>
			</div>
			<div class="modal-body">
				<p>Some text in the modal.</p>
			</div>

		</div>

	</div>
</div>
 

@include('common.footer')

@include('common._order_cancel_return_js')

<script>
function myFunction() {
  $('.show_header').show();
  window.print();
}
</script>

<script>
/*function myFunction() {
  window.print();
}*/

$(document).ready(function(){

	$(".print_invoice").click(function(){

		var current_sel = $(this);

		var order_id = $(this).attr('data-id');

            //alert(order_id); return false;

            var _token = '{{ csrf_token() }}';

            $.ajax({
            	url: "{{ route('users.ajax_print_invoice') }}",
            	type: "POST",
            	data: {order_id:order_id},
            	dataType:"JSON",
            	headers:{'X-CSRF-TOKEN': _token},
            	cache: false,
            	beforeSend:function(){
                       //$(".ajax_msg").html("");
                   },
                   success: function(resp){
                   	if(resp.success){

                   		printdiv(resp.viewHtml);

                   	}
                   	else{

                   	}

                   }
               });

        });

});


function printdiv(printpage){

	var headstr = "<html><head><title></title></head><body>";
  //var headstr = '<html><head>'+'<meta charset="utf-8" />'+'<title></title></head><body>';
  //var headstr = '<html><head>'+'<meta charset="utf-8" /><title>'+'</title>'+'<style type="text/css">body {-webkit-print-color-adjust: exact; font-family: Arial; }</style></head><body>';
  var footstr = '</body>';
  var newstr = printpage
  var oldstr = document.body.innerHTML;
  document.body.innerHTML = headstr+newstr+footstr;
  window.print();
  window.location.reload();
  return false;
}
</script>

</body>
</html>