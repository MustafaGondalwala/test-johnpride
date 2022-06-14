<table>
	<tr>
		<th> User</th>
		<th> Type</th>
		<th> Record ID</th>
		<th> Transaction Type</th>
		<th> Txn ID / Cheque no.</th>
		<th> Amount</th>
		<th> Status</th>
		<th> Dated</th>
		<th> Added on</th>
	</tr>

	<?php

                if(!empty($payments) && count($payments) > 0){

                	$received_status = config('custom.payment_received_status');

                    foreach($payments as $payment){

                        $transaction_type = config('custom.transaction_type.'.$payment->transaction_type);

                        $dated = date_format2($payment->date_on, $to_format='d/m/Y');
                        $added_on = date_format2($payment->created, $to_format='d/m/Y');

                        //prd($payment->user->toArray());

                        $user_name = trim($payment->user['first_name'] . ' ' . $payment->user['last_name']);
                        $billing_company_name = $payment->billing_company_name;
                        $billing_name = $payment->billing_name;

                        if(!empty($payment->user['company_name'])){
                            $user_name .= ' ('.$payment->user['company_name'].')';
                        }

                        $user_id = $payment->user['id'];

                        ?>
                        <tr class="payment_row_{{$payment->id}}">
                            <td>{{ $user_name }}</td>
                            <td>{{ ucwords($payment->type) }}</td>
                            <td>{{ $payment->record_id }}</td>
                            <td>{{ $transaction_type }}</td>
                            <td>{{ $payment->transaction_id }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>

                            <td>
                            	<?php
                            	if(!empty($payment->status) && isset($received_status[$payment->status])){
                            		echo $received_status[$payment->status];
                            	}
                            	?>
                            </td>

                            <td>{{ $dated }}</td>
                            <td>{{ $added_on }}</td>
                        </tr>
                        
                        <?php
                    }
                }
                ?>
	
</table>

























