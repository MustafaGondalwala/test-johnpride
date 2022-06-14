@component('emails.common.layout')
<tr bgcolor="#A77736">
    <td colspan="2" height="4"></td>

</tr>
<tr bgcolor="#ffffff">
    <td colspan="2" height="1"></td>

</tr>
<tr bgcolor="#3f4041">
    <td colspan="2" height="8"></td>
</tr>
<tr>
    <td style="padding: 10px 40px 15px 40px;">
        <span style="font-size: 20px; color: #3f4041; font-family: 'Roboto', Arial, sans-serif;"><?php echo $tag_line; ?></span>
        <p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 0px;">
            please find the details.
            <br>
        </p>
         
    </td>
</tr>


<tr>
    <td style="padding: 5px 0px 5px 40px;">
        
        <p style="font-size: 14px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 10px;">

             <?php if($loyalty_points_data->credit_amount > 0) { ?>
                <strong>Credit Loyalty Points :</strong>{{$loyalty_points_data->credit_amount}} Points
                <br>

             <?php } ?>   
             <?php if($loyalty_points_data->debit_amount > 0) { ?>

                <strong>Debit Loyalty Points :</strong>{{$loyalty_points_data->debit_amount}} Points
                <br>

             <?php } ?>                           

            
            <br>
        </p>
        

    </td>
</tr>

<tr>
    <td style="padding: 5px 0px 5px 40px;">
        
        <p style="font-size: 14px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 10px;">
            <strong>Available Loyalty Points :</strong> {{$av_loyalty_points}} Points
            <br>
        </p>
    </td>
</tr>
<tr>
    <td style="padding: 5px 0px 10px 40px;">
        
        <p style="font-size: 14px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 20px;">
            <strong>Description </strong><br>{{$loyalty_points_data->description}}
        </p>
    </td>
</tr>
<tr>
    <td style="padding: 10px 40px 10px 40px; font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">
        If you need any assistance with your loyalty points, please e-mail us at <a href="mailto:support@johnpride.in">support@johnpride.in</a> or call us at +91421431900 (10 AM to 6 PM, working days ).
    </td>
</tr>
<tr>
    <td style="padding: 50px 40px 20px 40px; font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">
        Regards,<br>
        Team Johnpride.
    </td>
</tr>
@endcomponent