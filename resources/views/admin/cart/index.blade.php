@component('admin.layouts.main')

@slot('title')
Admin - Manage Customers - {{ config('app.name') }}
@endslot


<?php

$BackUrl = CustomHelper::BackUrl();

$old_customer = app('request')->input('customer');
$old_phone = app('request')->input('phone');
$old_product = app('request')->input('product');

$old_from = app('request')->input('from');
$old_to = app('request')->input('to');

$back_url = (request()->has('back_url'))?request('back_url'):'';
?>

<style>
	#ui-datepicker-div{ z-index: 99!important;}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="titlehead">
			<h1 class="pull-left">Cart({{count($cart)}}) </h1>

				<form name="exportForm" method="" action="" >
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
						<label class="control-label">Customer Name:</label><br/>
						<input type="text" name="customer" class="form-control admin_input1" value="{{$old_customer}}">
					</div>


					<div class="col-md-2">
						<label class="control-label">Customer Phone:</label><br/>
						<input type="text" name="phone" class="form-control admin_input1" value="{{$old_phone}}">
					</div>

					<div class="col-md-2">
						<label class="control-label">Product Name:</label><br/>
						<input type="text" name="product" class="form-control admin_input1" value="{{$old_product}}">
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
						<a href="{{url('admin/cart')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
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
		if(!empty($cart) && $cart->count() > 0){
			?>

			<div class="table-responsive">

				{{ $cart->appends(request()->query())->links() }}

				<table class="table table-striped">
					<tr>
						<!-- <th><input type="checkbox" name="select_all"></th> -->
						<th>Customer</th>
						<th>Phone</th>
						<!-- <th>Product</th>

						<th>Size</th>
						<th>Qty</th>
						<th>Price</th>
						<th>Sale Price</th>
						<th>Added on</th> -->
						<th>Action</th>
					</tr>
					<?php
					$payment_id = 0;
					foreach($cart as $item){

						//prd($item->toArray());

						$user = $item->user;

						$customerName = (isset($user->name))?$user->name:'N/A';
						$customerEmail = (isset($user->email))?$user->email:'';
						$customerPhone = (isset($user->phone))?$user->phone:'';

						$added_on = CustomHelper::DateFormat($item->created_at, 'd F y');

						$customerUrl = 'javascript:void(0)';

						if(!empty($customerName)){
							$customerUrl = url('admin/customers?phone='.$customerPhone);
						}
						
						// if(empty($customerName) && !empty($customerEmail)){
						// 	$customerUrl = url('admin/customers?email='.$customerEmail);
						// }

						if(empty($customerName) && !empty($customerPhone)){
							$customerUrl = url('admin/customers?phone='.$customerPhone);
						}

						?>
						<tr>
							<!-- <td><input type="checkbox" name="cart_id" value="{{$item->id}}"></td> -->

							<td>
								<a href="{{$customerUrl}}" target="_blank">{{$customerName}}({{($item->count_items)}})</a>
							</td>

							<td>
								<a href="{{$customerUrl}}" target="_blank">{{$customerPhone}}</a>
							</td>

							<td>
								<a href="javascript:void(0)" data-uid="{{$item->user_id}}" class="viewItems" title="View"><i class="fas fa-eye"></i></a>

								<?php
								/*
								<a href="{{url('admin/orders/view/'.$item->id)}}" title="View"><i class="fas fa-eye"></i></a>
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

						<tr class="detailRow{{$item->user_id}}" style="display:none;">
							<td colspan="2">
								<div class="detailBox" style="display:none;">
									
								</div>
							</td>
						</tr>

						<?php
					}
					?>
				</table>

				{{ $cart->appends(request()->query())->links() }}
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

	$(document).on("click", ".viewItems", function(){

		var userId = $(this).data("uid");

		var detailRow = $(".detailRow"+userId);

		var detailBoxChildrenCount = detailRow.find(".detailBox").children().length;

		if(detailBoxChildrenCount > 0){
			toggleDetailsRow(userId);
			return true;
		}

		var _token = '{{ csrf_token() }}';

        $.ajax({
            url: "{{ url('admin/cart/ajax_get_items') }}",
            type: "POST",
            data: {userId:userId},
            dataType:"JSON",
            headers:{'X-CSRF-TOKEN': _token},
            cache: false,
            beforeSend:function(){
                //$("#viewModal .modal-body").html('');
            },
            success: function(resp){
                if(resp.success){
                    if(resp.rowsHtml){
                        $(".detailRow"+userId).find(".detailBox").html(resp.rowsHtml);

                        toggleDetailsRow(userId);
                    }
                }
            }
        });
	});

	function toggleDetailsRow(userId){

		var detailRow = $(".detailRow"+userId);

		if(detailRow.is(":visible")){
			$(".detailRow"+userId).find(".detailBox").slideToggle(function(){
				$(".detailRow"+userId).toggle();			
			});
		}
		else{
			$(".detailRow"+userId).toggle();
			$(".detailRow"+userId).find(".detailBox").slideToggle();
		}
	}
</script>

@endslot

@endcomponent




























