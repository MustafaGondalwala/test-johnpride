<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
  
<section class="fullwidth tabcart">
  <div class="container">
     <ul>
      <li><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
     <li><span><i class="addressicon"></i></span><strong>Address</strong></li>
     <li class="active"><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
    </ul>
  </div>
</section>  
  
<section class="fullwidth innerpage">
	<div class="container">
	  <div class="sectionleft">		  
		  <div class="selectadd">
		  	<ul>
			  <li>
				<div class="addselectbox">
					
					<div class="addlist">
						<a class="edit-link" href="{{url('cart/address')}}"><i class="editicon"></i></a>
						<h3 class="title3">Shipping Detail</h3>
						<p><strong>Siddique (Default)</strong></p>
						<p>114 noida,<br> Noida H.O,<br> Gautam Buddha Nagar- 201301 <br>Uttar Pradesh</p>
						<p>Mobile. 999999999</p>
					</div>
					
					<div class="cashondelivery">
					Cash on delivery available Return pick up available
					</div>
				  </div>
				</li>
				
				<li>
				 
					
					<a class="addaddresslink" href="{{url('cart/address')}}"><i class="addaddressicon"></i> <span>Add new address</span></a>
				 
				</li>
			  </ul>
		  </div>
		  
		  
		</div>
	 <div class="sectionright">
		  <div class="pricedetail">
			  <div class="itembox">
			  <div class="cartimg"><img src="http://johnpride.ii71.com/public/storage/products/190619065344--473Wx593H-460279802-black-MODEL4.jpg" alt=""></div>
			  <div class="titles">
					<p><span>Slumber Jill</span></p>
					<p>Printed Cotton Nightdress</p>
				</div>
			  </div>
		  <h3 class="title3">Price Detail</h3>
		  <ul>
		  	<li><span>Total MRP</span> <strong>₹1499</strong></li>
			  <li><span>Bag Discount</span> <strong>-₹99</strong></li>
			  <li><span>Delivery Charges</span> <strong>-₹00</strong></li>  
			  <li class="totals"><span>Order Total</span> <strong>₹1400</strong></li>
		  </ul>
			<div class="placebtn"><a href="#">Continue</a></div>
		  </div>
		  
		  
	  </div>
	 
  </div>
</section>
	
@include('common.footer')
</body>
</html>