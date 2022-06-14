@component('layouts.admin')

    @slot('title')
        Admin - Payments - {{ config('app.name') }}
    @endslot
    
    <div class="row">
		<div class="col-md-12">
			<div class="titlehead">
				<h1 class="pull-left">Payments</h1>
				<?php
                /*
                <a href="{{ url('admin/payments/add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Payment</a>
                */
                ?>

                @permission('export_xls')
            <form name="exportForm" method="post" action="{{url('admin/payments/export')}}" target="_blank" >
                {{ csrf_field() }}
                <input type="hidden" name="export_xls" value="1">

                <?php
                if(count(request()->input())){
                    foreach(request()->input() as $input_name=>$input_val){
                        ?>
                        <input type="hidden" name="{{$input_name}}" value="{{$input_val}}">
                        <?php
                    }
                }
                ?>

                <button type="submit" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export XLS</button>
            </form>
            @endpermission

			</div>
		</div>
	</div> 

    <div class="row">

        <div class="col-md-12">

            <div class="topsearch">
            <?php
            $back_url = CustomHelper::BackUrl();

            $BankAccounts = CustomHelper::BankAccounts();

            $received_status = config('custom.payment_status');

            $payment_received_type = config('custom.payment_received_type');
            $transaction_type = config('custom.transaction_type');

            $old_user = app('request')->input('user');
            $old_type = app('request')->input('type');
            $old_record_id = app('request')->input('record_id');
            $old_bank_account = app('request')->input('bank_account');

            $old_amt_from = app('request')->input('amt_from');
            $old_amt_to = app('request')->input('amt_to');

            $old_from = app('request')->input('from');
            $old_to = app('request')->input('to');

            $old_status = app('request')->input('status');
            ?>

            <form class="form-inline" method="GET">

                <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="user" class="control-label   required">User:</label><br/>
                    <select name="user" class="form-control admin_input1" >
                        <option value="">--Select--</option>

                        <?php
                        //prd($users->toArray());
                        if(!empty($users) && count($users) > 0){
                            foreach($users as $user){
                                if($user->status == 1){
                                    $user_name = trim($user->first_name.' '.$user->last_name);
                                    if(!empty($user->company_name)){
                                        $user_name .= ' ('.$user->company_name.')';
                                    }
                                    $selected = '';
                                    if($user->id == $old_user){
                                        $selected = 'selected';
                                    }
                                    if(!empty($user_name)){
                                        ?>
                                        <option value="{{ $user->id }}" {{ $selected }} >{{ $user_name }}</option>
                                        <?php
                                    }
                                    
                                }
                            }
                        }
                        ?>
                    </select>
                    <span class="form-error" style="color:red"></span>
                </div>
                
                   <div class="col-md-2 col-sm-6 col-xs-12">
                        <label for="type" class="control-label required">Type:</label><br/>
                        <select name="type" class="form-control admin_input1" >
                            <option value="">--Select--</option>

                            <?php
                            if(!empty($payment_received_type) && count($payment_received_type) > 0){
                                foreach($payment_received_type as $t_key=>$t_val){
                                    $selected = '';
                                    if($t_key == $old_type){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="{{ $t_key }}" {{ $selected }} >{{ $t_val }}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <label class="control-label required" for="record_id">Record ID:</label><br/>
                        
                        <input type="text" name="record_id" value="{{ old('record_id', $old_record_id) }}" class="form-control admin_input1">
                    </div>

                   <div class="col-md-2 col-sm-6 col-xs-12">
                        <label class="control-label required" for="record_id">Bank Account:</label><br/>
                        
                        <select name="bank_account" class="form-control admin_input1" >
                        <option value="">--Select--</option>

                        <?php
                        if(!empty($BankAccounts) && count($BankAccounts) > 0){
                            foreach($BankAccounts as $BA){                                    
                                    $selected = '';
                                    if($BA->id == $old_bank_account){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="{{ $BA->id }}" {{ $selected }} >{{ $BA->bank_name.'('.$BA->account_number.')' }}</option>
                                    <?php
                                
                            }
                        }
                        ?>
                    </select>
                    </div>

                   <div class="col-md-2 col-sm-6 col-xs-12">
                        <label for="order">Amt From:</label><br/>
                        <input type="text" name="amt_from" class="form-control admin_input1" value="{{ $old_amt_from }}">
                    </div>
                   <div class="col-md-2 col-sm-6 col-xs-12">
                        <label for="business">Amt To:</label><br/>
                        <input type="text" name="amt_to" class="form-control admin_input1" value="{{ $old_amt_to }}">
                    </div>

                   <div class="col-md-2 col-sm-6 col-xs-12">
                        <label for="order">From Date:</label><br/>
                        <input type="text" name="from" class="form-control admin_input1 to_date" value="{{ $old_from }}">
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <label for="business">To Date:</label><br/>
                        <input type="text" name="to" class="form-control admin_input1 from_date" value="{{ $old_to }}">
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <label for="business">Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <?php
                            if(!empty($received_status) && count($received_status)>0){
                                foreach($received_status as $rc_k=>$rc_v){
                                    $selected = '';
                                    if($rc_k == $old_status){
                                    $selected = 'selected';
                                    }
                                    ?>
                                    <option value="{{$rc_k}}" {{$selected}} >{{$rc_v}}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        </div>
				<div class="col-md-6 col-sm-6 col-xs-12">
                <button type="submit" class="btn btn-success btn1search">Search</button>
                <a href="{{url('admin/payments')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
				</div>
            </form>

            </div>


            @if (session('sccMsg'))
            <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('sccMsg') }}
            </div>
            @endif

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif

            <?php
            $total_amount = 0;

            foreach($payments as $payment){
                $total_amount = $total_amount + $payment->amount;
            }

            ?>

<div class="row">

    <div class="col-md-12">
        <table class="table table-striped table-bordered">
            <tr>
                <td><strong>Total Amount</strong> : <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</div>

            <div class="table-responsive">

                <div class="ajax_scc_msg"></div>

            {{ $payments->appends(request()->query())->links() }}

            <table class="table table-striped table-bordered table-hover">
                <tr>
                   <th> User</th>
                   <th> Type</th>
                   <th> Record ID</th>
                   <th> Transaction Type</th>
                   <th> Txn ID / Cheque no.</th>
                    <!-- <th width="40%"> Account</th> -->
                    <th> Amount</th>
                    <th> Status</th>
                    <th> Dated</th>
                    <th> Added on</th>
                    <th></th>
                </tr>
                <?php

                if(!empty($payments) && count($payments) > 0){
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

                        $record_url = "javascript:void(0)";

                        if($payment->type == 'orders'){
                            $record_url = url('admin/orders/'.$payment->record_id.'?back_url='.$back_url);
                        }

                        $status = (isset($received_status[$payment->status]))?$received_status[$payment->status]:"";

                        ?>
                        <tr class="payment_row_{{$payment->id}}">
                            <td><a href="javascript:void(0)" class="view_user" data-userid="{{$user_id}}">{{ $user_name }}</a></td>
                            <td>{{ ucwords($payment->type) }}</td>
                            <td>
                                <a href="{{$record_url}}">
                                {{ $payment->record_id }}
                                </a>
                            </td>
                            <td>{{ $transaction_type }}</td>
                            <td>{{ $payment->transaction_id }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>

                            <td>
                                <select name="rc_status" class="form-control rc_status" data-oldstatus="{{$payment->status}}" onchange="change_status('{{$payment->id}}', this.value)">
                                <?php
                                if(!empty($received_status) && count($received_status)>0){
                                    foreach($received_status as $rc_k=>$rc_v){
                                        $selected = '';
                                        if($rc_k == $payment->status){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$rc_k}}" {{$selected}} >{{$rc_v}}</option>
                                        <?php
                                    }
                                }
                                ?>
                                </select>
                        </td>

                            <td>{{ $dated }}</td>
                            <td>{{ $added_on }}</td>
                           
                            <td>
                            <?php
                            /*
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary" title="view"><i class="fa fa-eye"></i></a>
                            */
                            ?>
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary view_payment" title="view" data-id="{{ $payment->id }}"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                        
                        <?php
                    }
                }
                ?>
                
            </table>

            {{ $payments->appends(request()->query())->links() }}

        </div>

        <div class="row">

            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td><strong>Total Amount</strong> : <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
            <hr />


        </div>

    </div>


    <!-- View Payment Modal -->
  <div class="modal fade" id="viewPaymentModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Profile</h4>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
        </div>
      </div>
    </div>
  </div>

  <!-- End - View Payment Modal -->


@endcomponent

<script type="text/javascript">


    function change_status(id, status){

        var old_status = $(".payment_row_"+id).find('select[name="rc_status"]').attr("data-oldstatus");
        console.log(old_status);

        var conf = confirm("Are you sure to change status of this Payment?");

        if(conf){

            _token = '{{csrf_token()}}';

            $.ajax({
                url: "{{ url('admin/payments/ajax_change_status') }}",
                method: 'POST',
                data:{id, status},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){},
                success: function(resp) {
                    if(resp){
                        if(resp.success){
                            $(".payment_row_"+id).find('select[name="rc_status"]').attr("data-oldstatus", status);
                        }
                        $(".ajax_scc_msg").html(resp.scc_msg);
                    }
               },
               error: function(resp) {

               }
           });
        }
        else{
            $(".payment_row_"+id).find('select[name="rc_status"] option[value="'+old_status+'"]').prop("selected", true);
        }
    }

    $(".view_payment").click(function(){
        id = $(this).data('id');
        //alert(user_id);
        _token = '{{csrf_token()}}';

        $.ajax({
                url: "{{ url('admin/payments/ajax_view_payment') }}",
                method: 'POST',
                data:{id},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){},
                success: function(resp) {
                    if(resp.success){
                       $("#viewPaymentModal .modal-body").html(resp.view);
                       $("#viewPaymentModal").modal("show");
                    }
                    else {

                    }
                },
                error: function(resp) {

                }
            });
    });
</script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    $( function() {
        $( ".to_date, .from_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });
    });

</script>

@include("admin/common/_view_user")