@component('emails.common.layout')

@slot('heading')
<td align="left" style="color: #fff; font-size: 20px; padding: 10px 40px;">Order Confirmed </td>
@endslot

@slot('headingIcon')
<td align="right" style="padding-right: 40px;">
	<img src="<?php echo url('images/checkicon.png'); ?>" alt="icon" width="30" height="30" style="margin-top: -30px;">
</td>
@endslot

@slot('pageBlock')

<?php
$orderId = (isset($orderId))?$orderId:'';

$deliveryDate = date('M d, Y', strtotime("+7 day"));

if(isset($order->created_at) && !empty($order->created_at)){
	$deliveryDate = date('M d, Y', strtotime("+7 day", strtotime($order->created_at)));
}
?>

<tr>
	<td style="padding: 30px 0px 30px 40px;">
		<span style="font-size: 20px; color: #3f4041; font-family: 'Roboto', Arial, sans-serif;">Hello {{$order->billing_name}}</span>
		<p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 32px;">
			Thanks for shopping with us. We have received your order.<br>
			We will send a confirmation after your items ship. 
			<br>
		</p>
		 
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
		<span style="font-size: 18px; font-family: 'Roboto', Arial, sans-serif; color: #3f4041; font-weight: bold;">Order Details</span>
		<p style="font-size: 13px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">Order number <span style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #3d3e40; font-weight: bold; padding: 0px 0px 0px 5px;">{{$orderId}}</span></p>
	</td>
</tr>

@endslot

<?php
$isCustomer = true;
?>


@include('common._order_details')

<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 10px 0;">
	<tr>
	<td style="padding: 10px 40px 10px 40px; font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">
		If you need any assistance with your order, please e-mail us at <a href="mailto:info@johnpride.in">info@johnpride.in</a> or call us at +91-9599969498 (10 AM to 6 PM, working days ).
	</td>
</tr>
 
<tr>
	<td style="padding: 10px 40px 10px 40px;">
		<a href="{{url('users/orders')}}" style="font-size: 20px; font-family: 'Roboto', Arial, sans-serif; font-weight: 400; color: #fff; text-decoration: none; background-color: #A77736; padding: 10px 40px;">Track Order</a>
	</td>
</tr>
<tr>
	<td style="padding: 50px 40px 20px 40px; font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">
		Regards,<br>
		Team Johnpride.
	</td>
</tr> 
</table>

@endcomponent