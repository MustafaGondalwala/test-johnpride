@component('admin.layouts.main')

    @slot('title')
        Admin Panel - {{ config('app.name') }}
    @endslot


    <?php
        $todayDate = CustomHelper::DateFormat($todayDate, 'd/m/Y', 'Y-m-d');
        $weekStartDate = CustomHelper::DateFormat($weekStartDate, 'd/m/Y', 'Y-m-d');
        $weekEndDate = CustomHelper::DateFormat($weekEndDate, 'd/m/Y', 'Y-m-d');
        $monthStartDate = CustomHelper::DateFormat($monthStartDate, 'd/m/Y', 'Y-m-d');
        $monthEndDate = CustomHelper::DateFormat($monthEndDate, 'd/m/Y', 'Y-m-d');

        $old_from = app('request')->input('from');
        $old_to = app('request')->input('to');

        $websiteSettingsNamesArr = ['LAST_UPDATED_TIME_UNICOMMERCE_INVENTORY'];

        $websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

        $lastInventoryActivity = (isset($websiteSettingsArr['LAST_UPDATED_TIME_UNICOMMERCE_INVENTORY']))?$websiteSettingsArr['LAST_UPDATED_TIME_UNICOMMERCE_INVENTORY']->value:'';

        $lastInventoryActivity = CustomHelper::dateFormat($lastInventoryActivity, $toFormat='d-m-Y H:i:s', $fromFormat='Y-m-d H:i:s');
    ?>


<!-- weekly_orders -->

    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p>Last Updated Inventory : <?php echo $lastInventoryActivity; ?></p>
        </div>
    </div>

    <div class="row">

	<div class="col-md-12">
		<div class="bgcolor topsearch1">

			<div class="table-responsive">

				<form class="form-inline" method="GET">
					<div class="col-md-2">
						<label>From Date:</label><br/>
						<input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}" autocomplete="off">
					</div>

					<div class="col-md-2">
						<label>To Date:</label><br/>
						<input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}" autocomplete="off">
					</div>

					<div class="col-md-6">
                            <label>&nbsp;</label><br/>
						<button type="submit" class="btn btn-success btn1search">Search</button>
						<a href="{{url('admin')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>

    <div class="row">
       <div class="col-md-4">

        <table class="table table-bordered table-striped">
            <tr class="info">
                <th colspan="2" class="text-center">
                    <i class="fa fa-shopping-cart"></i> Orders
                    <a href="{{url('admin/orders')}}" class="pull-right" title="View all users"><small class="label label-info">View all &raquo;</small></a>
                </th>
            </tr>


            <?php
            $totalOrdersUrl = ($totalOrders > 0)?(url('admin/orders/')):'javascript:void(0)';
            ?>

            <tr>
                <td>Total Orders</td>
                <td class="text-right"> <a href="{{$totalOrdersUrl}}">{{$totalOrders}}</a> </td>
            </tr>

            <?php
            $todayOrdersUrl = ($todayOrders > 0)?(url('admin/orders/?from='.$todayDate.'&to='.$todayDate)):'javascript:void(0)';
            ?>

            <tr>
                <td>Today's Orders</td>
                <td class="text-right"> <a href="{{$todayOrdersUrl}}">{{$todayOrders}}</a> </td>
            </tr>

            <?php
            $weekOrdersUrl = ($weekOrders > 0)?(url('admin/orders/?from='.$weekStartDate.'&to='.$weekEndDate)):'javascript:void(0)';
            ?>

            <tr>
                <td>Week's Orders</td>
                <td class="text-right"> <a href="{{url('admin/orders/?from='.$weekStartDate.'&to='.$weekEndDate)}}">{{$weekOrders}}</a> </td>
            </tr>

            <?php
            $monthOrdersUrl = ($monthOrders > 0)?(url('admin/orders/?from='.$monthStartDate.'&to='.$monthEndDate)):'javascript:void(0)';
            ?>

            <tr>
                <td>Month's Orders</td>
                <td class="text-right"> <a href="{{$monthOrdersUrl}}">{{$monthOrders}}</a> </td>
            </tr>





            <tr class="info">
                <th colspan="2" class="text-center">
                    <i class="fa fa-shopping-cart"></i> Orders Revenue

                </th>
            </tr>


            <?php
            $totalOrdersRevenueUrl = ($totalOrdersRevenue > 0)?(url('admin/orders?order_status=confirmed&from='.$old_from.'&to='.$old_to)):'javascript:void(0)';
            ?>


            <tr>
                <td>Total revenue</td>
                <td class="text-right"> <a href="{{$totalOrdersRevenueUrl}}">({{$totalOrdersRevenueAmount}}) {{$totalOrdersRevenue}}</a> </td>
            </tr>



            <?php
            $todayOrdersRevenueUrl = ($todayOrdersRevenue > 0)?(url('admin/orders?order_status=confirmed&from='.$todayDate.'&to='.$todayDate.'&date_type=updated_at')):'javascript:void(0)';
            ?>

            <tr>
                <td>Today's revenue</td>
                <td class="text-right"> <a href="{{$todayOrdersRevenueUrl}}">{{$todayOrdersRevenue}}</a> </td>
            </tr>

            <?php
            $weekOrdersRevenueUrl = ($weekOrdersRevenue > 0)?(url('admin/orders?order_status=confirmed&from='.$weekStartDate.'&to='.$weekEndDate.'&date_type=updated_at')):'javascript:void(0)';
            ?>

            <tr>
                <td>Week's revenue</td>
                <td class="text-right"> <a href="{{$weekOrdersRevenueUrl}}">{{$weekOrdersRevenue}}</a> </td>
            </tr>

            <?php
            $monthOrdersRevenueUrl = ($monthOrdersRevenue > 0)?(url('admin/orders?order_status=confirmed&from='.$monthStartDate.'&to='.$monthEndDate.'&date_type=updated_at')):'javascript:void(0)';
            ?>

            <tr>
                <td>Month's revenue</td>
                <td class="text-right"> <a href="{{$monthOrdersRevenueUrl}}">{{$monthOrdersRevenue}}</a> </td>
            </tr>




            <tr class="info">
                <th colspan="2" class="text-center">
                    <i class="fa fa-shopping-cart"></i> Orders Return

                </th>
            </tr>

            <?php
            $totalOrdersReturnUrl = ($totalOrdersReturn > 0)?(url('admin/orders?order_status=return&from='.$old_from.'&to='.$old_to)):'javascript:void(0)';
            ?>


            <tr>
                <td>Total return</td>
                <td class="text-right"> <a href="{{$totalOrdersReturnUrl}}">({{$totalOrdersReturnAmount}}) {{$totalOrdersReturn}}</a> </td>
            </tr>

            <?php
            $todayOrdersReturnUrl = ($todayOrdersReturn > 0)?(url('admin/orders?order_status=return&from='.$todayDate.'&to='.$todayDate.'&date_type=updated_at')):'javascript:void(0)';
            ?>

            <tr>
                <td>Today's return</td>
                <td class="text-right"> <a href="{{$todayOrdersReturnUrl}}">{{$todayOrdersReturn}}</a> </td>
            </tr>

            <?php
            $weekOrdersReturnUrl = ($weekOrdersReturn > 0)?(url('admin/orders?order_status=return&from='.$weekStartDate.'&to='.$weekEndDate.'&date_type=updated_at')):'javascript:void(0)';
            ?>

            <tr>
                <td>Week's return</td>
                <td class="text-right"> <a href="{{$weekOrdersReturnUrl}}">{{$weekOrdersReturn}}</a> </td>
            </tr>

            <?php
            $monthOrdersReturnUrl = ($monthOrdersReturn > 0)?(url('admin/orders?order_status=return&from='.$monthStartDate.'&to='.$monthEndDate.'&date_type=updated_at')):'javascript:void(0)';
            ?>

            <tr>
                <td>Month's return</td>
                <td class="text-right"> <a href="{{$monthOrdersReturnUrl}}">{{$monthOrdersReturn}}</a> </td>
            </tr>


        </table>

    </div>

    <div class="col-md-4">

        <table class="table table-bordered table-striped">
            <tr class="info">
                <th colspan="2" class="text-center">
                    <i class="fas fa-boxes"></i> Top Selling Products
                    <?php
                    if(count($topSellingProducts) > 0){
                        ?>
                        <a href="{{url('admin/products?sortBy=top_selling')}}" class="pull-right" title="View all users"><small class="label label-info">View all &raquo;</small></a>
                        <?php
                    }
                    ?>
                </th>
            </tr>

            <tr class="info">
                <th>Product Name</th>
                <th>Ordered Qty</th>
            </tr>

            <?php
            if(!empty($topSellingProducts) && count($topSellingProducts) > 0){
                foreach($topSellingProducts as $topProd){
                    $productUrl = 'javascript:void(0)';
                    if($topProd->total_qty > 0){
                        $productUrl = url('admin/products?name='.$topProd->product_name);
                    }
                    ?>
                    <tr>
                        <td>
                            <a href="{{$productUrl}}">{{$topProd->product_name}}</a>
                        </td>
                        <td>{{$topProd->total_qty}}</td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>

    </div>

</div>

@slot('bottomBlock')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
	$( function() {
		$( ".to_date, .from_date" ).datepicker({
			'dateFormat':'dd/mm/yy'
		});
	});

	$(document).on("click", ".searchbtn", function(){
		$('.searchshow').fadeToggle();
	});
</script>


@endslot

@endcomponent
