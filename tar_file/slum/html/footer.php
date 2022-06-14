<footer class="fullwidth"> 
	<div class="fullwidth footer1"> 
		<div class="container">
			<div class="fbox flogo">
				<p><a href="index.php"><img src="images/logo.png" alt="Slumber Jill" /></a></p>
				<p>Slumber Jill is a lounge wear brand launched in August 2013 by Francis Wacziarg Fashion Private Limited, a part of the Francis Wacziarg Group.</p>
				<p></p>
			</div>
			<div class="fbox">
				<h4>ONLINE SHOPPING</h4>
				<ul>
					<li><a href="#">Women</a></li>
					<li><a href="#">Men</a></li>
					<li><a href="#">Home Furnishing</a></li>
				</ul> 
			</div> 
			<div class="fbox">
				<h4>USEFUL LINKS</h4>
				<ul>
					<li><a href="#">About</a></li>
					<li><a href="#">Return &amp; Exchange</a></li>
					<li><a href="#">FAQ</a></li>
					<li><a href="#">Contact</a></li>
					<li><a href="#">Terms &amp; Conditions</a></li>
					<li><a href="#">Privacy Policy</a></li>
				</ul> 
			</div>
			<div class="fbox">
				<h4>My Account</h4> 
				<ul>
					<li><a href="#">Order History</a></li>
					<li><a href="#">Account</a></li>
					<li><a href="#">Wishlist</a></li>
					<li><a href="#">Login</a></li>
					<li><a href="#">Shopping Cart</a></li> 
				</ul> 
			</div>
			<div class="fbox faddress">
				<h4>For any query</h4>
				<p><i class="mapicon"></i>FRANCIS WACZIARG FASHION PRIVATE LIMITED <br>
					No.7, KRG Thottam, Serangadu, Chandrapuram Main Road, <br>
					KNP Colony Post, Tirupur - 641 608, <br>
					Tamil Nadu, India 
				</p>
				<p><i class="phoneicon"></i> <a href="tel:94437 26891"><i class="fa fa-phone"></i> 91 421 4310900 / 94437 26891</a></p>	
				<p><i class="mailicon"></i> <a href="mailto:slumberjill@fwacziarg.com"><i class="fa fa-envelope-o"></i> slumberjill@fwacziarg.com</a></p>
			</div>		
		</div> 
	</div> 
	
	<div class="fullwidth fbottom"> 
		<div class="container">
			<div class="paysec">
				 <i class="payimg"></i>
			</div> 
			<div class="sslsec">
				 <i class="sslicon"></i>
			</div> 
			<div class="ficons">
				 <ul>
					<li><i class="originalicon"></i> <span>100% ORIGINAL <small>products</small></span></li>
					 <li><i class="returenicon"></i> <span>Return within 15 days <small>of receiving your order</small></span></li>
					 <li><i class="deliveryicon"></i> <span>Get free delivery <small>for every order above Rs.1000/-</small></span></li>
				</ul>
			</div> 
			
		</div> 
	</div>
</footer>


<script>
$(window).scroll(function() {
 if ($(this).scrollTop() > 0){  
	$('header').addClass("sticky");
  }
  else{
	$('header').removeClass("sticky");
  }
});	
	
$(document).ready(function () { 
  $( "body" ).click(function() {
    $('.navicon').removeClass('active');
    $('.topmenu').removeClass('showmenu');
  });
    $( ".navicon" ).click(function(e) {
      e.stopPropagation();
      $(this).toggleClass('active');
      $( ".topmenu" ).toggleClass('showmenu');
    }); 
  $(".topmenu").click(function(e){
    e.stopPropagation();
  }); 
      
});
</script>
