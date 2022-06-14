@component('admin.layouts.main')
<?php //prd($coupons); ?>
    @slot('title')
        Admin - Manage Coupons - {{ config('app.name') }}
    @endslot
    <?php $BackUrl = CustomHelper::BackUrl(); ?>
    <div class="row">

        <div class="col-md-12">

            <h1>Coupons <a href="{{ route('admin.coupons.add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add new Coupon</a></h1>

            @include('snippets.errors')
            @include('snippets.flash')

            <?php
            if(!empty($coupons) && count($coupons) > 0){
                ?>
                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Discount</th>
                        
                        <th>Max Discount</th>
                        <th>Order Amout</th>
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
                                {{ $coupon['max_discount'] }}
                            </td>
                            

                            <td>
                                <?php echo $coupon['order_amount'];?>
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

@endcomponent

<script type="text/javascript">

    $(document).on("click", ".sbmtDelForm", function(e){
        e.preventDefault();

        $(this).siblings("form.delForm").submit();                
    });
</script>