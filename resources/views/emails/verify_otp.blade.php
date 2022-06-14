@include('emails.include.email_header')
 <table class="table">
    <tr>
    <td>Hello, <br>Please enter given below to OTP to verify your email.<br>{!! $smsMessage !!} </td>
    </tr> 
	 
	 <tr>
    <td>Thanks &amp; regards <br>Johnpride</td>
    </tr>
 </table> 


@include('emails.include.email_footer')