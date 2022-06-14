@component('admin.layouts.main')

    @slot('title')
        Admin - User Loyalty Points - {{ config('app.name') }}
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
				<h1 class="pull-left">User Loyalty Points - {{ $user_full_name }}</h1>

			</div>
		</div>
	</div>

    <?php if(!empty($loyaltyPointsDetails) && $loyaltyPointsDetails['haveCriteria'])
            {
               // pr($loyaltyPointsDetails);
                ?>
                <div class="orderlist">
                    <p><a href="{{url('users/loyalty-points')}}"><strong><?php echo $loyaltyPointsDetails['name']; ?></strong></a></p>
                    <?php if($loyaltyPointsDetails['freeShipping'] == true){ ?>
                        <p><span><strong>Free shipping</strong></span></p>
                        <p><span><strong>For Free shipping minimum order amount should be <?php echo ($loyaltyPointsDetails['shipping_free_min_order'])?$loyaltyPointsDetails['shipping_free_min_order']:0; ?></strong> </span></p>
                    <?php } ?>

                    


                    <p><span><strong><?php echo ($loyaltyPointsDetails['min_order_amount'] == 0)?'No minimum order amount':'minimum order amount should be '.$loyaltyPointsDetails['min_order_amount'].' '; ?></strong></span></p>

                    <?php if($loyaltyPointsDetails['discount'] > 0){ ?>
                        <p><span><strong>Discount: <?php echo $loyaltyPointsDetails['discount']; ?> <?php echo ($loyaltyPointsDetails['discount_type']=='percentage')?'%':'Rs'; ?> </strong> </span></p>
                    <?php } ?>


                    <?php if($loyaltyPointsDetails['instant_refund'] > 0){ ?>
                        <p><span><strong>Instant Refund </strong> </span></p>
                    <?php }elseif($loyaltyPointsDetails['instant_refund_after_shipped'] > 0){ ?>
                        <p><span><strong>Instant Refund after Product Pickup</strong> </span></p>
                    <?php } ?>

                </div>



                <?php 
            }
            ?>

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
            //prd($points);
            $id = (isset($points['id']))?$points['id']:0;
            $amount = (isset($points['amount']))?$points['amount']:'';
            $description = (isset($points['description']))?$points['description']:'';
            

            if(is_numeric($id) && $id > 0){
                $action_url = url('admin/customers/loyalty-points/'.$user_id.'/', $id);
            }
            else{
                $action_url = url('admin/customers/loyalty-points/'.$user_id.'/');
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
            if(!empty($id) && $id > 0){
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

                </div>


                <div class="form-group{{ $errors->has('points') ? ' has-error' : '' }} ">
                    <label for="points" class="control-label required">Points:</label>

                    <input type="number" name="points" id="points" class="form-control" value="{{ old('points', $points) }}" maxlength="25" />

                    <?php
                    /*@include('snippets.errors_first', ['param' => 'points'])*/
                    ?>
                </div>


                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }} ">
                    <label for="description" class="control-label">Description:</label>

                    <textarea name="description" class="form-control" placeholder="Enter fulll description" id="description" maxlength="255" >{{ old('description', $description) }}</textarea>

                    

                    @include('snippets.errors_first', ['param' => 'description'])
                </div>

               

                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="back_url" value="{{ $back_url }}">
               

                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <?php
                    if($id > 0){
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
                if(!empty($loyaltyPointsList) && count($loyaltyPointsList) > 0){
                    foreach($loyaltyPointsList as $lp){

                        $credit_total = $credit_total + $lp->credit_amount;
                        $debit_total = $debit_total + $lp->debit_amount;
                       
                        ?>
                        <tr>
                            <td>{{ $lp->description }}</td>
                            <td>{{ $lp->credit_amount }}</td>
                            <td>{{ $lp->debit_amount }}</td>
                            <td>
                            <?php
                            echo CustomHelper::DateFormat($lp->created_at, 'd M Y');
                            ?>
                            </td>
                            
                        </tr>
                        
                        <?php
                    }
                }

                $balance = $credit_total - $debit_total;

                ?>

                <tr>
                    <td colspan="5"><strong>Credit (Total) </strong>:  {{ $credit_total }}</td>
                    
                </tr>
                <tr>
                    <td colspan="5"><strong>Debit (Total) </strong> :  {{ $debit_total }}</td>
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