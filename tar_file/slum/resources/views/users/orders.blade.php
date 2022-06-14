<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo (isset($meta_title))?$meta_title:'SlumberJill'?></title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')
<section class="fullwidth innerpage">
 	<div class="container">
		@include('users.nav')
		
		<div class="rightcontent">
			<div class="heading2">My Orders</div>
			<div class="ordersec ">
			<ul>
				<li>
					<div class="orderlist fullwidth">
						<p><span><strong>Approved </strong>Order No: 524854855</span></p>
						<p>Placed on: June 02, 2019 / ₹599.00 / 2items </p>
						<div class="orderdetail">Detail</div>
					</div>
					<div class="detailbox fullwidth">
					<ul class="cartlist">
					<li>
						<div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt="190619065344--473Wx593H-460279802-black-MODEL4.jpg"></div>
						<div class="procont">
							<div class="titles">
								<p><span>Slumber Jill</span></p>
								<p>Printed Cotton Nightdress</p>
							</div>
							<div class="cartprice"><span>₹789</span></div>

							<div class="sizeqty">
								<div class="size">SIZE : M</div>
								<div class="qty">Qty : 1</div>
							</div>

							 
					  </div>
						</li>
						<li>
						<div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt="190619065344--473Wx593H-460279802-black-MODEL4.jpg"></div>
						<div class="procont">
							<div class="titles">
								<p><span>Slumber Jill</span></p>
								<p>Printed Cotton Nightdress</p>
							</div>
							<div class="cartprice"><span>₹789</span></div>

							<div class="sizeqty">
								<div class="size">SIZE : M</div>
								<div class="qty">Qty : 1</div>
							</div>

							 
					  </div></li>
				  </ul>
					</div>
				</li>
				<li>
					<div class="orderlist">
						<p><span><strong>Approved </strong>Order No: 524854855</span></p>
						<p>Placed on: June 02, 2019 / ₹599.00 / 2items </p>
						<div class="orderdetail">Detail</div>
					</div>
					<div class="detailbox fullwidth">
					<ul class="cartlist">
					<li>
						<div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt="190619065344--473Wx593H-460279802-black-MODEL4.jpg"></div>
						<div class="procont">
							<div class="titles">
								<p><span>Slumber Jill</span></p>
								<p>Printed Cotton Nightdress</p>
							</div>
							<div class="cartprice"><span>₹789</span></div>

							<div class="sizeqty">
								<div class="size">SIZE : M</div>
								<div class="qty">Qty : 1</div>
							</div>

							 
					  </div>
						</li>
						<li>
						<div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt="190619065344--473Wx593H-460279802-black-MODEL4.jpg"></div>
						<div class="procont">
							<div class="titles">
								<p><span>Slumber Jill</span></p>
								<p>Printed Cotton Nightdress</p>
							</div>
							<div class="cartprice"><span>₹789</span></div>

							<div class="sizeqty">
								<div class="size">SIZE : M</div>
								<div class="qty">Qty : 1</div>
							</div>

							 
					  </div></li>
				  </ul>
					</div>
				</li>
				<li>
					<div class="orderlist">
						<p><span><strong>Approved </strong>Order No: 524854855</span></p>
						<p>Placed on: June 02, 2019 / ₹599.00 / 2items </p>
						<div class="orderdetail">Detail</div>
					</div>
					<div class="detailbox fullwidth">
					<ul class="cartlist">
					<li>
						<div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt="190619065344--473Wx593H-460279802-black-MODEL4.jpg"></div>
						<div class="procont">
							<div class="titles">
								<p><span>Slumber Jill</span></p>
								<p>Printed Cotton Nightdress</p>
							</div>
							<div class="cartprice"><span>₹789</span></div>

							<div class="sizeqty">
								<div class="size">SIZE : M</div>
								<div class="qty">Qty : 1</div>
							</div>

							 
					  </div>
						</li>
						<li>
						<div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt="190619065344--473Wx593H-460279802-black-MODEL4.jpg"></div>
						<div class="procont">
							<div class="titles">
								<p><span>Johnpride</span></p>
								<p>Printed Cotton Nightdress</p>
							</div>
							<div class="cartprice"><span>₹789</span></div>

							<div class="sizeqty">
								<div class="size">SIZE : M</div>
								<div class="qty">Qty : 1</div>
							</div>

							 
					  </div></li>
				  </ul>
					</div>
				</li>
				<li>
					<div class="orderlist">
						<p><span><strong>Approved </strong>Order No: 524854855</span></p>
						<p>Placed on: June 02, 2019 / ₹599.00 / 2items </p>
					</div>
				</li>
			</ul>
		</div>
			 
		</div>
	</div>
</section>
 

@include('common.footer')
	
<script>

$(".orderdetail").click(function(){	
    $(this).parent().next().slideToggle();
  });
</script>

</body>
</html>