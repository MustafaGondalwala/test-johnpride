@component('emails.common.layout')

@slot('heading')
<td align="left" style="color: #fff; font-size: 20px; padding: 20px 40px;">Order {{ucfirst($subOrder->order_status)}} </td>
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
		<span style="font-size: 24px; color: #3f4041; font-family: 'Roboto', sans-serif, Arial;">Hi Admin </span>
		<p style="font-size: 16px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 32px;">

			This Order has been returned Successfully. Please find the order details.<br>
			
			<strong>Reason :</strong> {{$reason}}
			<br><br>
			In the meantime, you can track your order below.
		</p>
		<br><br>
		<p style="color: #3a3a3c; font-size: 17px; font-weight: 600; font-family: Roboto; margin: 4px 0px;">Stay Stylish!</p>
		<p style="color: #3a3a3c; font-size: 17px; font-weight: 400; font-family: Roboto; margin: 4px 0px;">Johnpride</p>
	</td>
</tr>
<tr>
	<td style="padding: 30px 40px 60px 40px;">
		<a href="{{url('users/orders/'.$subOrderNo)}}" style="font-size: 20px; font-family: Roboto; font-weight: 400; color: #fff; text-decoration: none; background-color: #e41881; padding: 10px 40px;">Track Order</a>
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


@endcomponent