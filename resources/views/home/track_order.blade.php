<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Track Order::Johnpride</title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index, follow"/>
	<meta name="robots" content="noodp, noydir"/>

	@include('common.head')

</head>

<body>

	@include('common.header')

	<?php
	$name = '';
	$email = '';
	$phone = '';

	//pr($orderHistory);
	?>

	<section class="fullwidth innerpage enquiry_form_page">
		<div class="container">
			<h1 class="heading">Track Order</h1>

			<div class="contactform formbox">

				@include('snippets.front.flash')


				<form name="trackForm" method="POST">
					
					{{csrf_field()}}

					<ul>

						<li>
							<span>Enter Order No or Tracking number<cite>*</cite></span>
							<span><input type="text" name="order_no" value="{{$order_no}}" class="inputfild"></span>
							@include('snippets.front.errors_first', ['param' => 'order_no'])
						</li><br>

						<li><button class="savebtn">Submit</button></li>

					</ul>

					

				</form>
			</div>
			<div class="clearfix"></div>

			
			<div class="track_process">
				<div class="row">
				<?php 
					if(isset($order_status) && $order_status == 1)
					{
						
						?>
					<div class="col-12  hh-grayBox">
						<div class="row justify-content-between">

					
								<div class="order-tracking completed">
								<?php
								foreach ($order_items_data as $key => $value) 
								{
									$updated_date = ($value->updated/1000);
									$convert_date = date('Y F d',$updated_date);

									?>
										<div class="order_items">
										<p><?php echo isset($value->itemName) ? $value->itemName : '' ?></p>
										<span class="is-complete"></span>
										<p><?php echo isset($value->statusCode) ? $value->statusCode : '' ?><br>
											<!-- <span>Tue, June 25</span> -->
											<span><?php echo $convert_date ?></span>
										</p>
									</div>
									

									<?php

								}
								?>
								</div>	
							

							

							


						</div>
					</div>

					<?php 
				}
				else
				{
					
					?>
					<p class="text text-danger"><?php echo $error_description ?></p>
					<?php
				}

					?>
				</div>
				<div>
				</div>
			</div>
		
		</div>
	</section>

@include('common.footer')

</body>
</html>