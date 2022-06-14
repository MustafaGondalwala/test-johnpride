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

$order_status = app('request')->input('order_status');

$old_from = app('request')->input('from');
$old_to = app('request')->input('to');

$compare_scope = config('custom.compare_scope');

$back_url = (request()->has('back_url'))?request('back_url'):'';

$orderStatusArr = config('custom.order_status_arr');
?>

<style>
	#ui-datepicker-div{ z-index: 99!important;}
	.detailbox {
	    padding: 0 30px;
	    display: none;
	}
	.detailbox .cartlist > li {
    padding: 15px 0 0;
    border: none;
        border-top-color: currentcolor;
        border-top-style: none;
        border-top-width: medium;
    border-top: 1px solid #ededef;
}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="titlehead">
			<h1 class="pull-left">Orders </h1>

			<?php
			if(!empty($back_url)){
				?>
				<a href="{{ url($back_url) }}" class="btn btn-sm btn-success pull-right">Back</a>
				<?php
			}
			?>

			<?php
            if( !empty($orders) && $orders->count() > 0){
                ?>
                <form name="exportForm" method="" action="" >
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
						<label>Order Status:</label><br/>

						


						<select name="order_status" class="form-control admin_input1">
							<option value="">Please Select</option>
							<?php
							if(!empty($orderStatusArr) && count($orderStatusArr) > 0){
								foreach($orderStatusArr as $osKey=>$osVal){

									if($osKey!='partially_cancelled'){

									$selected = '';
									if($order_status == $osKey){
										$selected = 'selected';
									}
									?>
									<option value="{{$osKey}}" {{$selected}} > {{$osVal}} </option>

									<?php 
								}
							}

							}
							?>


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

					<div  class="col-md-2">
						<label>Order No.:</label><br/>
						<input type="text" name="order_no" class="form-control admin_input1" value="" autocomplete="off">
					</div>

					<div class="clearfix"></div>

					<div class="col-md-6">
						<button type="submit" class="btn btn-success btn1search">Search</button>
						<a href="{{url('admin/orders')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
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
		if(!empty($orders) && $orders->count() > 0){
			?>

			<div class="table-responsive">

				{{ $orders->appends(request()->query())->links() }}

				<table class="table table-striped">
					<tr>
						<th>Order Id</th>
						<th>Customer</th>

						<th>Total (Rs)</th>
						<th>Order Status</th>
						<th>Added on</th>
						<th>Action</th>
					</tr>
					<?php
					$payment_id = 0;
					//prd($orders->toArray());
					foreach($orders as $order){
						//prd($order);

						$customerDetails = isset($order->customerDetails) ? $order->customerDetails:'';

						$customerName = (isset($customerDetails->name))?$customerDetails->name:'';

						$billing_country_name = $shipping_country_name = '';
						$billing_state_name = $shipping_state_name = '';
						$billing_city_name = $shipping_city_name = '';

						$added_on = CustomHelper::DateFormat($order->created_at, 'd F y');

						$orderStatus = isset($order->order_status) ? $order->order_status:'';

						$orderStatusName = (!empty($orderStatus) && isset($orderStatusArr[$orderStatus]))?$orderStatusArr[$orderStatus]:'';

						$orderItems = isset($order->orderItems) ? $order->orderItems:'';

						?>
						<tr>

							<td>{{$order->order_no}}</td>
							<td>
								{{$customerName}}
							</td>

							<td>{{$order->total}}</td>

							<td>{{$orderStatusName}}</td>

							<td>{{$added_on}}</td>



							<td>
								<?php /* ?>
								<a href="{{url('admin/orders/view/'.$order->id)}}" title="View"><i class="fas fa-eye"></i></a>
								<?php */ ?>
								<a href="javascript:void(0)" class="orderdetail"><i class="fas fa-eye"></i></a>





								<?php
								/*
								&nbsp;
								<a href="{{ route('admin.designers.designs', [$designer_id, 'back_url'=>$BackUrl]) }}" title="Manage Designs" ><i class="far fa-object-group"></i></a>
								
								&nbsp;
								<a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

								<form method="POST" action="{{ route('admin.designers.delete', $designer_id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this designer?');" class="delForm">
									{{ csrf_field() }}
								</form>
								*/
								?>
							</td>
						</tr>


						<tr class="detailbox">
							<td colspan="6">
								<table class="table table-striped">
												

													<?php 
													$storage = Storage::disk('public');
													$img_path = 'products/';
													$thumb_path = $img_path.'thumb/';

													foreach ($orderItems as $item) {

														//pr($item);

														$product = isset($item->productDetail) ? $item->productDetail:'';

														$defaultImage = isset($product->defaultImage) ? $product->defaultImage:'';
														$productBrand = isset($product->productBrand) ? $product->productBrand:'';
														$sale_price = isset($product->sale_price) ? $product->sale_price:'';
														$mainPrice = isset($product->price) ? $product->price:'';
														$productPrice = $mainPrice;
														if(is_numeric($sale_price) && $sale_price < $mainPrice && $sale_price > 0){
															$productPrice = isset($product->sale_price) ? $product->sale_price:'';
														}
														else{
															$productPrice = $mainPrice;
														}
														

														$brandName = '';
														if(!empty($productBrand) && count($productBrand) > 0){
															$brandName = $productBrand->name;
														}


														if(!empty($defaultImage) && count($defaultImage) > 0){
															if(!empty($defaultImage->image) && $storage->exists($thumb_path.$defaultImage->image) ){
																$imgUrl = url('public/storage/'.$thumb_path.$defaultImage->image);
															}
														}

														$subOrderStatusDetails = isset($item->subOrderStatusDetails) ? $item->subOrderStatusDetails:'';
														//prd($subOrderStatusDetails);
														$orderStatusName = (isset($subOrderStatusDetails->name))?$subOrderStatusDetails->name:'';

														?>
														<tr>
															<td colspan="2">
															<p>
																<span><strong>{{$orderStatusName}}
																</strong>
															</span>
															</p>
															<p>
															<span>
																Sub Order No: {{isset($item->sub_order_no) ? $item->sub_order_no:''}}</span>
															</p>
															<p>
																Total: Rs {{number_format($item->total)}}  
															</p>
															</td>
															<td colspan="3">
															<?php
															if(!empty($imgUrl)){
																?>
																<div class="cartimg">
																	<img src="{{$imgUrl}}">
																</div>
																<?php
															}
															?>

															<div class="procont">
																<div class="titles">
																	<p><span>{{$brandName}}</span></p>
																	<p>{{isset($product->name) ? $product->name:''}}</p>
																</div>
																<div class="cartprice"><span>Rs <?php echo $productPrice; //number_format($productPrice);?></span></div>

																<div class="sizeqty">
																	<div class="size">SIZE : {{isset($item->size_name) ? $item->size_name:''}}</div>
																	<div class="qty">QTY : {{isset($item->qty) ? $item->qty:''}}</div>
																</div>

															</div>


															

															</td>

															<td>
																<div class="">
																<a href="{{url('admin/orders/view/'.$item->id)}}" title="View"><i class="fas fa-eye"></i></a>
															</div>
															</td>
														</li>
														<?php
													}
													?>

												
											</table>

							</td>

						</tr>



						<?php
					}
					?>
				</table>

				{{ $orders->appends(request()->query())->links() }}
			</div>
			<?php
		}
		else{
			?>
			<div class="alert alert-warning">There are no orders at the present.</div>
			<?php
		}
		?>



		<br /><br />

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

<script>

		$(".orderdetail").click(function(){	
			
			$(this).parent().parent().next().slideToggle();
		});
	</script>
@endslot

@endcomponent