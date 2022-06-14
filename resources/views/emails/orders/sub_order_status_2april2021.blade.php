@component('emails.common.layout')

@slot('heading')
<td align="left" style="color: #fff; font-size: 20px; padding: 10px 40px;">Order {{ucfirst($subOrder->order_status)}} </td>
@endslot

@slot('headingIcon')

@endslot

@slot('pageBlock')

<?php
$orderNo = (isset($subOrder->sub_order_no))?$subOrder->sub_order_no:'';

$deliveryDate = date('M d, Y', strtotime("+7 day"));

if(isset($order->created_at) && !empty($order->created_at)){
	$deliveryDate = date('M d, Y', strtotime("+7 day", strtotime($order->created_at)));
}
?>

<tr>
	<td style="padding: 30px 0px 30px 40px;">
		<span style="font-size: 20px; color: #3f4041; font-family: 'Roboto', sans-serif, Arial;">Hi {{$order->billing_name}}</span>
		<p style="font-size: 14px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 32px;">
			<?php
			if($subOrder->order_status == 'failed'){
			?>
			Sorry your order is failed due to some reasons. Please find the order details.
			<?php } 
			elseif($subOrder->order_status == 'return'){
			?>
			We have received your return request and we are contacting the courier partner for pick up.
			<br><br>
			Please pack and keep ready the parcel, once it has been picked up, the refund will be initiated.
			<?php } 
			elseif($subOrder->order_status == 'cancelled'){
			?>
			Your order has been cancelled.
			<?php } 
			elseif($subOrder->order_status == 'confirmed'){
			?>
			Your order has been confirmed.
			<?php } 
			elseif($subOrder->order_status == 'pending'){
			?>
			Your order has been pending.
			<?php } 
			elseif($subOrder->order_status == 'shipped'){
			?>
			Thank You for shopping @ <a href="www.johnpride.in">www.johnpride.in</a> Your Orders has been Dispatched.<br>
			Don’t miss out on the latest trends & collections; shop more & often with us.
			<?php } 
			elseif($subOrder->order_status == 'success'){
			?>
			Your order has been success.
			<?php } 
			elseif($subOrder->order_status == 'delivered'){
			?>
			Your order has been delivered.
			<?php }
			?>
			
			
		</p>
		<br><br> 

	</td>
</tr>

<tr bgcolor="#A77736">
	<td colspan="2" height="4"></td>

</tr>
<tr bgcolor="#ffffff">
	<td colspan="2" height="1"></td>

</tr>
<tr bgcolor="#3f4041">
	<td colspan="2" height="8"></td>
</tr>
<tr>
	<td style="padding: 40px 40px 20px 40px;">
		<span style="font-size: 18px; font-family: Roboto; color: #3f4041; font-weight: bold;">Order Details</span>
		<p style="font-size: 13px; font-family: Roboto; color: #626365;">Order number <span style="font-size: 14px; font-family: Roboto; color: #3d3e40; font-weight: bold; padding: 0px 0px 0px 5px;">{{$orderNo}}</span></p>
	</td>
</tr>

@endslot

<?php
$isCustomer = true;
?>


@include('common._sub_order_details')

<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 10px 0;">
	<tr>
	<td style="padding: 10px 40px 10px 40px; font-size: 14px;">
		If you need any assistance with your order, please e-mail us at <a href="mailto:info@johnpride.in">info@johnpride.in</a> or call us at +91421431900 (10 AM to 6 PM, working days ).
	</td>
</tr>
 
<tr>
	<td style="padding: 10px 40px 10px 40px;">
		<a href="{{url('users/orders/'.$orderNo)}}" style="font-size: 20px; font-family: 'Roboto', Arial, sans-serif; font-weight: 400; color: #fff; text-decoration: none; background-color: #A77736; padding: 10px 40px;">Track Order</a>
	</td>
</tr>
<tr>
	<td style="padding: 50px 40px 20px 40px;">
		Regards,<br>
		Team Johnpride.
	</td>
</tr> 
</table>

@endcomponent