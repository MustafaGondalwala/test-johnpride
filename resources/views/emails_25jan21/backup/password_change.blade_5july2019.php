@include('emails.include.email_header')
 <table class="table">
 	<tr>
 		<td>
 			Hello {{$name}}
 			<p>Your password has been changed successfully</p>
 		</td>
 	</tr> 
	 
	 <tr>
    <td>Thanks &amp; regards <br>SlumberJill</td>
    </tr>
 </table> 


@include('emails.include.email_footer')