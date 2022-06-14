@component('emails.common.layout')
<tr bgcolor="#e41881">
    <td colspan="2" height="4"></td>

</tr>
<tr bgcolor="#ffffff">
    <td colspan="2" height="1"></td>

</tr>
<tr bgcolor="#3f4041">
    <td colspan="2" height="8"></td>
</tr>
<tr>
    <td style="padding: 10px 40px 10px 40px;">
        <span style="font-size: 20px; color: #3f4041; font-family: 'Roboto', Arial, sans-serif;"><?php echo $tag_line; ?></span>
        <p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 0px;">
            please find the details.
            <br>
        </p>
         
    </td>
</tr>

<?php if($wallet_data->credit_amount > 0) { ?>
<tr>
    <td style="padding: 0px 40px 0px 40px;">
        <p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 0px;">
            <strong>Credit amount :</strong>Rs.{{$wallet_data->credit_amount}}
            <br>
        </p>
    </td>
</tr>
 <?php } ?>
 <?php if($wallet_data->debit_amount > 0) { ?>
 <tr>
    <td style="padding: 0px 40px 0px 40px;">
        <p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 0px;">
            <strong>Debit amount :</strong>Rs.{{$wallet_data->debit_amount}}
            <br>
        </p>
    </td>
</tr>
 <?php } ?>
 <tr>
    <td style="padding: 0px 40px 0px 40px;">
        <p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 0px;">
            <strong>Available amount :</strong>Rs.{{$av_bal}}
            <br>
        </p>
    </td>
</tr>
 <tr>
    <td style="padding: 0px 40px 0px 40px;">
        <p style="font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365; line-height: 0px;">
            <strong>Description </strong><br>{{$wallet_data->description}}
        </p>
    </td>
</tr>
<tr>
    <td style="padding: 10px 40px 10px 40px; font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">
        If you need any assistance with your wallet, please e-mail us at <a href="mailto:info@johnpride.in">info@johnpride.in</a> or call us at +91421431900 (10 AM to 6 PM, working days ).
    </td>
</tr>
<tr>
    <td style="padding: 50px 40px 20px 40px; font-size: 14px; font-family: 'Roboto', Arial, sans-serif; color: #626365;">
        Regards,<br>
        Team Johnpride.
    </td>
</tr>
@endcomponent