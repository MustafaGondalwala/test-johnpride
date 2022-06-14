@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Coupons - {{ config('app.name') }}
    @endslot
    
    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = app('request')->input('name');
    $old_code = app('request')->input('code');

    $old_start = app('request')->input('start');
    $old_expiry = app('request')->input('expiry');
    
    $old_status = app('request')->input('status');

    $compare_scope = config('custom.compare_scope');

    $back_url = (request()->has('back_url'))?request('back_url'):'';
    ?>

    <div class="row">

        <div class="col-md-12">

            <h1>Coupons <a href="{{ route('admin.coupons.add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add new Coupon</a></h1>

            @include('snippets.errors')
            @include('snippets.flash')

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
                        <label>Code:</label><br/>
                        <input type="text" name="code" class="form-control admin_input1" value="{{$old_code}}">
                    </div>


                    <div class="col-md-2">
                        <label>Start Date(>=):</label><br/>
                        <input type="text" name="start" class="form-control admin_input1 pickDate" value="{{$old_start}}" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label>Expiry Date(<=):</label><br/>
                        <input type="text" name="expiry" class="form-control admin_input1 pickDate" value="{{$old_expiry}}" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label>Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                            <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/coupons')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>


            <?php
            if(!empty($coupons) && count($coupons) > 0){
                ?>
                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Discount</th>
                        
                        <th>Max Discount Amt</th>
                        <th>Min Order Amount</th>
                        <th>Use Limit</th>
                        <th>Start Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php 
                    foreach ($coupons as $coupon){
                        //$created_at = CustomHelper::DateFormat($coupon->created_at, 'd M Y');
                        $start_from = ($coupon->start_date) ? CustomHelper::DateFormat($coupon->start_date, 'd M Y'): '';
                        $expiry_at = CustomHelper::DateFormat($coupon->expiry_date, 'd M Y');
                    ?>
                        <tr>
                            <td>
                                {{ $coupon['name'] }}
                            </td>
                            <td>
                                {{ $coupon['code'] }}
                            </td>
                            <td>
                                {{ $coupon['discount'] }} {{ ($coupon['type']=="value") ? '':'%' }}
                            </td>

                            
                            <td>
                                {{ $coupon['max_discount_amt'] }}
                            </td>
                            

                            <td>
                                <?php echo $coupon['min_amount'];?>
                            </td>
                            <td>
                                <?php echo $coupon['use_limit'];?>
                            </td>
                            <td>
                                {{ $start_from }}
                            </td>

                            <td>
                                {{ $expiry_at }}
                            </td>

                            <td>
                                @if ($coupon['status'])
                                     Active
                                @else
                                     Inactive
                                @endif
                            </td>
                         
                            <td>
                                <a href="{{ route('admin.coupons.edit', [$coupon['id'],  'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>
                                &nbsp;
                                <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                <form method="POST" action="{{ route('admin.coupons.delete', $coupon['id']) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this coupon?');" class="delForm">
                                    {{ csrf_field() }}
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                {{ $coupons->appends(request()->query())->links() }}
           

            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no coupons at the present.</div>
            <?php
        }
            ?>

            <br /><br />

        </div>

    </div>

    @slot('bottomBlock')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">

        $( function() {
            $( ".pickDate" ).datepicker({
                dateFormat:'dd M yy',
                changeMonth:true,
                changeYear: true,
                yearRange:"1950:0+"
            });
        });

        $(document).on("click", ".sbmtDelForm", function(e){
            e.preventDefault();

            $(this).siblings("form.delForm").submit();                
        });
    </script>

    @endslot

@endcomponent

