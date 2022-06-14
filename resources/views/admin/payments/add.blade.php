@component('layouts.admin')

    @slot('title')
        Admin - {{$title}} - {{ config('app.name') }}
    @endslot

    <div class="row">

        <div class="col-md-8">

        <?php
        //pr($order->toArray());
        ?>

            <h2>

            {{ $heading }} <?php echo (isset($order->order_number))?' - Order # '.$order->order_number:''; ?>

            <a href="{{ url($back_url) }}" class="btn btn-sm btn-primary pull-right"> Back</a>

            </h2>

  <div class="bgcolor">
            @include('snippets.errors')
            @include('snippets.flash')

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif

            <?php
            $BankAccounts = CustomHelper::BankAccounts();
            $payment_method_arr = config('custom.payment_method');

            //pr($order->user);

            $user_name = '';

            if(!empty($order) && count($order) > 0){
                $user_name = trim($order->user->first_name.' '.$order->user->last_name);
                if(!empty($order->user->company_name)){
                    $user_name .= ' ('.$order->user->company_name.')';
                }
            }
            ?>


            <form method="post" class="" name="paymentForm" id="paymentForm" action="{{ url('admin/payments/add') }}">

            {{ csrf_field() }}

            <div class="row">

                <div class="col-md-12">
                    <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                        <label for="user" class="control-label">User:</label>
                        <?php
                        echo $user_name;
                        /*
                        <select name="user" class="form-control" disabled="disabled">
                            <option value="">--Select--</option>

                            <?php
                            $user_id = old('user', $user_id);
                            if(!empty($users) && count($users) > 0){
                                foreach($users as $user){
                                    if($user->status == 1){
                                        $user_name = trim($user->first_name.' '.$user->last_name);
                                        if(!empty($user->company_name)){
                                            $user_name .= ' ('.$user->company_name.')';
                                        }
                                        $selected = '';
                                    if($user->id == $user_id){
                                        $selected = 'selected';
                                    }
                                        ?>
                                        <option value="{{ $user->id }}" {{ $selected }} >{{ $user_name }}</option>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                        @include('snippets.errors_first', ['param' => 'user'])
                        */
                        ?>
                    </div>
                </div>

                <div class="col-md-12">
                    <?php
                    $payment_received_type = config('custom.payment_received_type');
                    ?>
                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        <label for="type" class="control-label">Type:</label>
                        <?php
                        $type = old('type', $type);
                        echo ucwords($type);
                        /*
                        <select name="type" class="form-control" disabled="disabled">
                            <option value="">--Select--</option>

                            <?php
                            $type = old('type', $type);
                            if(!empty($payment_received_type) && count($payment_received_type) > 0){
                                foreach($payment_received_type as $t_key=>$t_val){
                                    $selected = '';
                                    if($t_key == $type){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="{{ $t_key }}" {{ $selected }} >{{ $t_val }}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        @include('snippets.errors_first', ['param' => 'type'])
                        */
                        ?>
                    </div>
                </div>


                <?php
                /*
                <div class="col-md-12">
                    <div class="form-group{{ $errors->has('record_id') ? ' has-error' : '' }}">
                        <label class="control-label required" for="record_id">Record ID:</label>
                        
                        <input type="text" name="record_id" value="{{ old('record_id', $record_id) }}" class="form-control">
                        @include('snippets.errors_first', ['param' => 'record_id'])
                    </div>
                </div>
                */
                ?>

                <div class="col-md-12">

                    <?php
                    $transaction_type = config('custom.transaction_type');
                    ?>
                    <div class="form-group{{ $errors->has('transaction_type') ? ' has-error' : '' }}">
                        <label for="transaction_type" class="control-label required">Transaction type:</label>
                        <select name="transaction_type" class="form-control" >
                            <option value="">--Select--</option>

                            <?php
                            if(!empty($transaction_type) && count($transaction_type) > 0){
                                foreach($transaction_type as $tt_key=>$tt_val){
                                    $selected = '';
                                    if($tt_key == old('transaction_type')){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="{{ $tt_key }}" {{$selected}}>{{ $tt_val }}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        @include('snippets.errors_first', ['param' => 'transaction_type'])
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group{{ $errors->has('transaction_id') ? ' has-error' : '' }}">
                        <label class="control-label required" for="transaction_id">Transaction ID / Cheque no.:</label>

                        <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" class="form-control">
                        @include('snippets.errors_first', ['param' => 'transaction_id'])
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group{{ $errors->has('bank_account') ? ' has-error' : '' }}">
                        <label class="control-label required" for="bank_account">Funds transferred to:(Bank Accounts):</label>
                        
                        <select name="bank_account" class="form-control" >
                            <option value="">--Select--</option>
                            <?php
                                if(!empty($BankAccounts) && count($BankAccounts) > 0){
                                    foreach($BankAccounts as $BA){
                                        $selected = '';
                                        if($BA->id == old('bank_account')){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{ $BA->id }}" {{$selected}} >{{ $BA->bank_name.'('.$BA->account_number.')' }}</option>
                                        <?php
                                    }
                                }
                                ?>
                        </select>

                        @include('snippets.errors_first', ['param' => 'bank_account'])
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group{{ $errors->has('transaction_amount') ? ' has-error' : '' }}">
                        <label class="control-label required" for="transaction_amount">Transaction Amount:</label>
                        
                        <input type="text" name="transaction_amount" value="{{ old('transaction_amount') }}" class="form-control">
                        @include('snippets.errors_first', ['param' => 'transaction_amount'])
                    </div>
                </div>

                <?php
                //$old_transaction_date = CustomHelper::DateFormat(old('transaction_date'), $to_format='d/m/Y', $from_format='');
                ?>

                <div class="col-md-12">
                    <div class="form-group{{ $errors->has('transaction_date') ? ' has-error' : '' }}">
                    <label class="control-label required" for="transaction_date">Transaction Date:</label>
                        <input type="text" name="transaction_date" value="{{old('transaction_date')}}" class="form-control payment_date" placeholder="">
                        @include('snippets.errors_first', ['param' => 'transaction_date'])
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label" for="payment_remarks">Remarks:</label>
                        <textarea name="payment_remarks" class="form-control">{{old('payment_remarks')}}</textarea>
                        <span class="form-error" style="color:red"></span>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">

                    <input type="hidden" name="user" value="{{ old('user', $user_id) }}" >
                    <input type="hidden" name="record_id" value="{{ old('record_id', $record_id) }}" >
                    <input type="hidden" name="type" value="{{ old('type', $type) }}" >

                    <button type="submit" class="btn btn-success save_payment">Submit</button>
                    </div>
                </div>

            </form>



        </div>

        
</div>
           
            
        
    </div>

    <script>
        function deleteLogo(brand_id){
            conf = confirm('Are you sure to delete this logo?');

            if(conf){
                document.delete_logo.submit();
            }
        }
    </script>

@endcomponent



<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    $( function() {
        $( ".to_date, .from_date, .payment_date" ).datepicker({
            dateFormat:'dd/mm/yy',
            maxDate: new Date()
        });
    });

</script>



    