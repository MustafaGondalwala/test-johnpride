<?php

$user_name = trim($payment->user['first_name'] . ' ' . $payment->user['last_name']);
$billing_company_name = $payment->billing_company_name;
$billing_name = $payment->billing_name;

if(!empty($payment->user['company_name'])){
    $user_name .= ' ('.$payment->user['company_name'].')';
}

$BankAccountsArr = [];

$BankAccounts = CustomHelper::BankAccounts();
if(!empty($BankAccounts) && count($BankAccounts) > 0){
    foreach($BankAccounts as $BA){
        $BankAccountsArr[$BA->id] = $BA;
    }
}

$bank_account = '';
if(isset($BankAccountsArr[$payment->bank_accounts_id])){

    $bank_account_details = $BankAccountsArr[$payment->bank_accounts_id];

    $bank_account = $bank_account_details->bank_name.'('.$bank_account_details->account_number.')';
}

$dated = date_format2($payment->date_on, $to_format='d/m/Y');
$added_on = date_format2($payment->created, $to_format='d/m/Y');

?>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th class="text-center">User</th>
            <td align="center">{{ $user_name }}</td>
        </tr>
        <tr>
            <th class="text-center">Type</th>
            <td align="center">{{ config('custom.payment_received_type.'.$payment->type) }}</td>
        </tr>
        <tr>
            <th class="text-center">Record ID</th>
            <td align="center">{{ $payment->record_id }}</td>
        </tr>
        <tr>
            <th class="text-center">Transaction Type</th>
            <td align="center">{{ $payment->transaction_type }}</td>
        </tr>
        <tr>
            <th class="text-center">Bank Account</th>
            <td align="center">
                {{ $bank_account }}
            </td>
        </tr>
        <tr>
            <th class="text-center">Amount</th>
            <td align="center">{{ number_format($payment->amount, 2) }}</td>
        </tr>
        <tr>
            <th class="text-center">Transaction ID</th>
            <td align="center">{{ $payment->transaction_id }}</td>
        </tr>
        <tr>
            <th class="text-center">Dated</th>
            <td align="center">{{ $dated }}</td>
        </tr>
        <tr>
            <th class="text-center">Added</th>
            <td align="center">{{ $added_on }}</td>
        </tr>

        </table>
    </div>

</div>