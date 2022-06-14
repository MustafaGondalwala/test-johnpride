@component('emails.common.layout')

@slot('heading')
<td align="left" style="color: #fff; font-size: 20px; padding: 10px 40px;">Order {{ucfirst($subOrder->order_status)}} </td>
@endslot

@slot('headingIcon')

@endslot

@slot('pageBlock')

<?php
$subOrderNo = (isset($subOrder->sub_order_no))?$subOrder->sub_order_no:'';

$deliveryDate = date('M d, Y', strtotime("+7 day"));

if(isset($order->created_at) && !empty($order->created_at)){
	$deliveryDate = date('M d, Y', strtotime("+7 day", strtotime($order->created_at)));
}
?>

<tr>
	<td style="padding: 30px 0px 30px 40px;">
		<span style="font-size: 20px; color: #3f4041; font-family: 'Roboto', sans-serif, Arial;">Hi {{$order->billing_name}}</span>
		<p style="font-size: 14px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 32px;"> 
			We have received your return request and we are contacting the courier partner for pick up.
			<br>
			Please pack and keep ready the parcel, once it has been picked up, the refund will be initiated. 
		</p>
		 
	</td>
</tr>

<tr bgcolor="#e41881">
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
		<p style="font-size: 13px; font-family: Roboto; color: #626365;">Order number <span style="font-size: 14px; font-family: Roboto; color: #3d3e40; font-weight: bold; padding: 0px 0px 0px 5px;">{{$subOrderNo}}</span></p>
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
		<a href="{{url('users/orders/'.$orderId)}}" style="font-size: 20px; font-family: 'Roboto', Arial, sans-serif; font-weight: 400; color: #fff; text-decoration: none; background-color: #e41881; padding: 10px 40px;">Track Order</a>
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