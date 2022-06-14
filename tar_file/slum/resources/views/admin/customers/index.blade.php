@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Customers - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = app('request')->input('name');
    $old_email = app('request')->input('email');
    $old_phone = app('request')->input('phone');
    $old_wallet = app('request')->input('old_wallet');
    $old_status = app('request')->input('status');

    $old_discount = app('request')->input('discount');
    $discount_scope = app('request')->input('discount_scope');

    $old_from = app('request')->input('from');
    $old_to = app('request')->input('to');

    $compare_scope = config('custom.compare_scope');

    $back_url = (request()->has('back_url'))?request('back_url'):'';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Customers ({{count($customers)}})</h1>

            <a href="{{ route('admin.customers.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Customer</a>
            

            <?php
            if(!empty($back_url)){
                ?>
                <a href="{{ url($back_url) }}" class="btn btn-sm btn-success pull-right">Back</a>
                <?php
            }
            ?>
            </div>
        </div>
   </div>



   <div class="row">

    <div class="col-md-12">
        <div class="bgcolor topsearch1">

            <div class="table-responsive">

                <form class="form-inline" method="GET">
                    <div class="col-md-2">
                        <label class="control-label">Name:</label><br/>
                        <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                    </div>

                    <div class="col-md-2">
                        <label>Email:</label><br/>
                        <input type="email" name="email" class="form-control admin_input1" value="{{$old_email}}">
                    </div>

                    <div class="col-md-2">
                        <label>Phone:</label><br/>
                        <input type="text" name="phone" class="form-control admin_input1" value="{{$old_phone}}">
                    </div>


                    <div class="col-md-2">
                        <label>Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                            <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                        </select>
                    </div>


                    <div class="col-md-2">
                        <label>From Date:</label><br/>
                        <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label>To Date:</label><br/>
                        <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}" autocomplete="off">
                    </div>

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/customers')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>




<div class="row">
    <div class="col-md-12">

        @include('snippets.errors')
        @include('snippets.flash')        

        <?php
        if(!empty($customers) && $customers->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $customers->appends(request()->query())->links() }}

                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Wallet</th>
                        <th>Status</th>
                        <th>Added on</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($customers as $customer){
                        $first_name = (isset($customer->first_name))?$customer->first_name:'';
                        $last_name = (isset($customer->last_name))?$customer->last_name:'';
                        $customer_name = trim($first_name.' '.$last_name);

                        $status = ($customer->status == '1')?'Active':'Inactive';
                        $wallet = ($customer->is_wallet == '1')?'Active':'Inactive';

                        $customer_id = $customer['id'];

                        $added_on = CustomHelper::DateFormat($customer->created_at, 'd F y');

                        $user_type = ucwords($customer->type);
                        ?>

                        <tr>
                            <td>{{$customer_name}}</td>
                            <td>{{$customer->email}}</td>
                            <td>{{$customer->phone}}</td>
                            <td>{{$wallet}}</td>
                            <td>{{$status}}</td>
                            <td>{{$added_on}}</td>

                            <td>
                                <a href="{{ route('admin.customers.edit', [$customer_id, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>

                                <a href="{{ route('admin.customers.wallet', [$customer_id, 'back_url'=>$BackUrl]) }}" title="Wallet" ><i aria-hidden="true" class="fa fa-credit-card"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                {{ $customers->appends(request()->query())->links() }}
            </div>
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no customers at the present.</div>
            <?php
        }
        ?>

    

<br /><br />

</div>
</div>

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>



<script>

      $( function() {
        $( ".to_date, .from_date" ).datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth:true,
            changeYear: true,
            yearRange:"1950:0+"
        });
    });



	 $(document).on("click", ".searchbtn", function(){
        $('.searchshow').fadeToggle();
    });
</script>