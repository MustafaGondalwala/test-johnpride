@include('emails.include.email_header')
 <table class="table">
 	<tr>
 		<td>
 			Hello {{$name}},
 			<p style="padding-top: 5px;">Your password has been changed successfully.</p>
 		</td>
 	</tr> 
	 
	 <tr>
    <td>Thanks &amp; Regards <br>Johnpride</td>
    </tr>
 </table> 


@include('emails.include.email_footer')