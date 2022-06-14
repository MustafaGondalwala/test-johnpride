@include('emails.include.email_header')
<table class="table">
	<tr>
		<td>Hello Admin, <br><br> A Customer want to know availability of size of product given below. <br></td>
	</tr>

	<tr>
		<td><strong>Product: </strong>{!! $productName !!}</td>
	</tr>
	<tr>
		<td><strong>Size: </strong>{!! $sizeName !!}</td>
	</tr>
	<tr>
		<td><strong>Customer Name: </strong>{!! $customerName !!}</td>
	</tr>
	<tr>
		<td><strong>Customer Email: </strong>{!! $customerEmail !!}</td>
	</tr> 
	<tr>
		<td><strong>Customer Phone: </strong>{!! $customerPhone !!}</td>
	</tr> 

	<tr>
		<td>---------------</td>
	</tr> 

	<tr>
		<td>Thanks &amp; regards <br>SlumberJill</td>
	</tr>
</table>

 
@include('emails.include.email_footer')