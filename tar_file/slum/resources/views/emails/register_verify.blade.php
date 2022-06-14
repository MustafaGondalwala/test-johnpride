@include('emails.include.email_header')
 <table class="table">
    <tr>
    <td>Hello, <br><br> You have successfully registered. <br>Please click on link given below to verify your email address.
		<br>{!! $verify_link !!} </td>
    </tr> 
	 
	 <tr>
    <td>Thanks &amp; regards <br>SlumberJill</td>
    </tr>
 </table> 
 
@include('emails.include.email_footer')