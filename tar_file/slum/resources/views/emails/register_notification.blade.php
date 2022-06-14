@include('emails.include.email_header')
<table class="table">
	<tr>
		<td>Hello Admin, <br><br> An User has been registered successfully. <br></td>
	</tr>

	<tr>
		<td><strong>Email: </strong>{!! $email !!}</td>
	</tr> 
	<tr>
		<td><strong>Phone: </strong>{!! $phone !!}</td>
	</tr> 

	<tr>
		<td>---------------</td>
	</tr> 

	<tr>
		<td>Thanks &amp; regards <br>SlumberJill</td>
	</tr>
</table>

 
@include('emails.include.email_footer')