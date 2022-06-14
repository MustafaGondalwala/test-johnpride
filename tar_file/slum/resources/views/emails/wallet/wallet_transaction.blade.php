
@include('emails.include.email_header')
 
    <table class="table">
<tr>
    <td><?php echo $tag_line; ?>, <br>please find the details.</td>
    </tr>
    <?php if($wallet_data->credit_amount > 0) { ?>
    <tr>
    <td><strong>Credit amount:</strong> </td><td>Rs.{{$wallet_data->credit_amount}}</td>
    </tr>

    <?php } ?>
    
    <?php if($wallet_data->debit_amount > 0) { ?>
    <tr>
    <td><strong>Debit amount:</strong> </td><td>Rs.{{$wallet_data->debit_amount}}</td>
    </tr>
    <?php } ?>

    <tr>
    <td><strong>Available Balance:</strong> </td><td>Rs.{{$av_bal}}</td>
    </tr>



     <tr>
    <td colspan="2"><strong>Description</strong><br>{{$wallet_data->description}} </td> 
    </tr>
	 



    </table>
   

    

    
  

    
       @include('emails.include.email_footer')