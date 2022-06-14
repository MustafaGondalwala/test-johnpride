@include('emails.include.email_header')
<table class="table">
	<tr>
		<td>Hello Admin, <br><br> An review has been submitted for {{$productName}}. <br></td>
	</tr>

	<tr>
		<td><strong>By: </strong>{!! $userName !!}</td>
	</tr> 
	<tr>
		<td><strong>Date: </strong>{!! $reviewDate !!}</td>
	</tr> 

	<tr>
		<td>---------------</td>
	</tr> 

	<tr>
		<td>Thanks &amp; regards <br>Johnpride</td>
	</tr>
</table>

 
@include('emails.include.email_footer')