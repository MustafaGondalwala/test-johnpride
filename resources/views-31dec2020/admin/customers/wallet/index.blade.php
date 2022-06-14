@component('admin.layouts.main')

    @slot('title')
        Admin - User Wallet - {{ config('app.name') }}
    @endslot

<?php
$back_url = (request()->has('back_url'))?request()->input('back_url'):'';
$segment2 = request()->segment(2);

$user_first_name = (isset($user->first_name))?$user->first_name:'';
$user_last_name = (isset($user->last_name))?$user->last_name:'';

$user_full_name = trim($user_first_name.' '.$user_last_name);

$type = old('type');
?>
   
	<div class="row">
		<div class="col-md-12">
			<div class="titlehead">
				<h1 class="pull-left">Wallet - {{ $user_full_name }}</h1>
				<!-- <a href="{{url('admin/deliveryterms/add')}}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Delivery Term</a> -->
			</div>
		</div>
	</div>

    <div class="row">

        <div class="col-md-12">

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


            @include('snippets.errors')
            @include('snippets.flash')

            <?php
            //prd($wallet);
            $wallet_id = (isset($wallet['id']))?$wallet['id']:0;
            $amount = (isset($wallet['amount']))?$wallet['amount']:'';
            $description = (isset($wallet['description']))?$wallet['description']:'';
            

            if(is_numeric($wallet_id) && $wallet_id > 0){
                $action_url = url('admin/customers/wallet/'.$user_id.'/', $wallet_id);
            }
            else{
                $action_url = url('admin/customers/wallet/'.$user_id.'/');
            }            
            ?>

			</div>
	</div>         
            
    <div class="row">

        <div class="col-md-12">
			<div class="topsearch">
            <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" role="form" class="form-inline heightform">
                {{ csrf_field() }}
                <?php
            //echo 'user_id='.$user_id; die;
            if(!empty($wallet_id) && $wallet_id > 0){
               ?>
               {{method_field('PUT')}}
               <?php
            }
            ?>


                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }} ">
                    <label for="Type" class="control-label required">Type:</label>

                    <select name="type" id="type" class="form-control">
                        <option value="">--Select--</option>
                        <option value="credit_amount" <?php echo ($type == 'credit_amount')?'selected':''; ?> >Credit</option>
                        <option value="debit_amount" <?php echo ($type == 'debit_amount')?'selected':''; ?> >Debit</option>                        
                    </select>                    

                    <?php
                    /*@include('snippets.errors_first', ['param' => 'type'])*/
                    ?>
                </div>


                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} ">
                    <label for="amount" class="control-label required">Amount:</label>

                    <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $amount) }}" maxlength="255" />

                    <?php
                    /*@include('snippets.errors_first', ['param' => 'amount'])*/
                    ?>
                </div>


                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }} ">
                    <label for="description" class="control-label">Description:</label>

                    <textarea name="description" class="form-control" placeholder="Enter fulll description" id="description" maxlength="255" >{{ old('description', $description) }}</textarea>

                    

                    @include('snippets.errors_first', ['param' => 'description'])
                </div>

               

                <input type="hidden" name="wallet_id" value="{{ $wallet_id }}">
                <input type="hidden" name="back_url" value="{{ $back_url }}">
               

                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <?php
                    if($wallet_id > 0){
                        ?>
                        <a href="{{ url('admin/'.$segment2.'/'.$user_id) }}" class="btn resetbtn btn-primary" title="Cancel">Cancel</a>
                        <?php
                    }
                    if(empty($back_url)){
                        $back_url = 'admin/'.$segment2;
                    }
                    ?>
                    <a href="{{ url($back_url) }}" class="btn btn-primary" style="    padding: 9px 15px;    margin-top: -10px;">Back</a>
                    
               



            </form>

</div>
          </div>
	</div>         
            
    <div class="row">

        <div class="col-md-12">
            <div class="table-responsive">

            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Description</th>
                    <th>Credit (Rs)</th>
                    <th>Debit (Rs)</th>
                    <th>Date</th>
                </tr>
                <?php
                $credit_total = 0;
                $debit_total = 0;
                $balance = 0;
                if(!empty($wallet_list) && count($wallet_list) > 0){
                    foreach($wallet_list as $wl){

                        $credit_total = $credit_total + $wl->credit_amount;
                        $debit_total = $debit_total + $wl->debit_amount;
                       
                        ?>
                        <tr>
                            <td>{{ $wl->description }}</td>
                            <td>{{ $wl->credit_amount }}</td>
                            <td>{{ $wl->debit_amount }}</td>
                            <td>
                            <?php
                            echo CustomHelper::DateFormat($wl->created_at, 'd M Y');
                            ?>
                            </td>
                            
                        </tr>
                        
                        <?php
                    }
                }

                $balance = $credit_total - $debit_total;

                CustomHelper::UpdateUserWalletBal($user_id, $balance);

                ?>

                <tr>
                    <td colspan="5"><strong>Credit (Total) </strong>: <i class="fa fa-inr" aria-hidden="true"></i> {{ $credit_total }}</td>
                    
                </tr>
                <tr>
                    <td colspan="5"><strong>Debit (Total) </strong> : <i class="fa fa-inr" aria-hidden="true"></i> {{ $debit_total }}</td>
                </tr>
                <tr>
                    <td colspan="5"><strong>Balance </strong> : <i class="fa fa-inr" aria-hidden="true"></i> {{ $balance }}</td>
                </tr>
                
            </table>
            </div>
            <hr />


        </div>

    </div>

@endcomponent