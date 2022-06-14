@include('emails.include.email_header')
 <table class="table">
    <tr>
    <td>Hello, <br>Please click on link given below to reset your password.<br>{!! $reset_link !!} </td>
    </tr> 
	 
	 <tr>
    <td>Thanks &amp; regards <br>SlumberJill</td>
    </tr>
 </table> 


@include('emails.include.email_footer')