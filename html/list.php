<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Slumber Jill</title>
<meta name="description" content="" />
<meta name="keywords" content=" " />
<?php include('head.php'); ?>
<link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">

<body>
<?php include('header.php'); ?>
<section class="breadcrumbs fullwidth">
	<div class="container">	
		<a href="/">Home</a> <a href="/">Women</a>  Western Wear
	</div>
</section>
<section class="fullwidth innerlist">
	<div class="container">
		
		<div class="sidebarsec">
			<div class="filtertitle fullwidth">
		<span class="filtermobile">Filters <small></small></span>
			
		</div> 
			<div class="sideinner">
			<div class="sidetitle">Categories <span><i class="searchicon"></i></span></div>
			<div class="boxs catlist">
			<ul>
				<li class="active"><span>Western Wear</span>
				<ul>
					<li><label><input type="checkbox"> <span>T-Shirts & Tops</span></label></li>
					<li><label><input type="checkbox"> <span>Tunics</span></label></li>
					<li><label><input type="checkbox"><span>Dresses & Jumpsuits</span></label></li>
					<li><label><input type="checkbox"><span>Shorts & Skirts</span></label></li>
					<li><label><input type="checkbox"><span>Trousers & Capris</span></label></li>
					<li><label><input type="checkbox"><span>Shrugs</span></label></li>
					<li><label><input type="checkbox"><span>Sweaters & Sweatshirts</span></label></li>
					<li><label><input type="checkbox"><span>Jackets</span></label></li>
				</ul>
				</li>
				<li><span>Active Wear</span>
				<ul> 
					<li><label><input type="checkbox"> <span>Tunics</span></label></li>
					<li><label><input type="checkbox"><span>Dresses & Jumpsuits</span></label></li>
					<li><label><input type="checkbox"><span>Shorts & Skirts</span></label></li>
					<li><label><input type="checkbox"><span>Trousers & Capris</span></label></li>
					<li><label><input type="checkbox"><span>Shrugs</span></label></li>
					<li><label><input type="checkbox"><span>Sweaters & Sweatshirts</span></label></li> 
				</ul>
				</li>
				<li><span>Fusion Wear</span>
				<ul> 
					<li><label><input type="checkbox"><span>Dresses & Jumpsuits</span></label></li>
					<li><label><input type="checkbox"><span>Shorts & Skirts</span></label></li>
					<li><label><input type="checkbox"><span>Trousers & Capris</span></label></li>
					<li><label><input type="checkbox"><span>Shrugs</span></label></li>
					<li><label><input type="checkbox"><span>Sweaters & Sweatshirts</span></label></li>
					<li><label><input type="checkbox"><span>Jackets</span></label></li>
				</ul>
				</li>
				<li><span>Nightwear & Loungewear</span>
				<ul>  
					<li><label><input type="checkbox"><span>Trousers & Capris</span></label></li>
					<li><label><input type="checkbox"><span>Shrugs</span></label></li>
					<li><label><input type="checkbox"><span>Sweaters & Sweatshirts</span></label></li>
					<li><label><input type="checkbox"><span>Jackets</span></label></li>
				</ul>
				</li>
			</ul>
			</div>
			
			<div class="boxs">
			<ul>
				<li><span>Brand</span>
				<ul>
					<li><label><input type="checkbox"> <span>Slumber Jill</span></label></li>
					<li><label><input type="checkbox"> <span>Slumber Jill (Fashion  Street)</span></label></li>
					<li><label><input type="checkbox"><span>Litivo</span></label></li>
					<li><label><input type="checkbox"><span>Querida</span></label></li>
					<li><label><input type="checkbox"><span>Neemrana Collections</span></label></li>
					<li><label><input type="checkbox"><span>Odaka</span></label></li> 
				</ul>
				</li>
			</ul>
			</div>
			<div class="boxs colors">
			<ul>
				<li><span>Color</span>
				<ul>
					<li><label><input type="checkbox"> <span ><small style="background-color:#f5f1de;"></small> Beige</span></label></li>
					<li><label><input type="checkbox"> <span><small style="background-color:#000;"></small> Black</span></label></li>
					<li><label><input type="checkbox"><span><small style="background-color:#0060ff;"></small> Blue</span></label></li>
					<li><label><input type="checkbox"><span><small style="background-color:#333;"></small> Gold</span></label></li>
					<li><label><input type="checkbox"><span><small style="background-color:#444;"></small> Grey</span></label></li> 
				</ul>
				</li>
			</ul>
			</div>
			<div class="boxs filtersize">
			<ul>
				<li><span>Size</span>
				<ul>
					<li><label><input type="checkbox"> <span>XS</span></label></li>
					<li><label><input type="checkbox"> <span>S</span></label></li>
					<li><label><input type="checkbox"><span>M</span></label></li>
					<li><label><input type="checkbox"><span>L</span></label></li>
					<li><label><input type="checkbox"><span>XL</span></label></li>
					<li><label><input type="checkbox"><span>2XL</span></label></li> 
				</ul>
				</li>
			</ul> 
			</div>
			
			<div class="sidetitle">Price</div> 
				<div class="price_range">
					<span id="price_range"></span>
					<div class="price_filter" id="price_filter"></div>
					<span class="price_range_view">Min &#x20B9;<span id="min_price_range"> </span></span>
					<span class="price_range_view">Max &#x20B9;<span id="max_price_range"> </span></span>
					<input type="hidden" id="price_range_hidden">  
				</div>
			</div>
		</div>
		
		
		<div class="rightcontent">
			<div class="shortlist fullwidth">
			<div class="gridlist"><span class="threelist"><small></small><small></small><small></small></span> <span class="fivelist"><small></small><small></small><small></small><small></small><small></small></span></div>
			
			<div class="founditem">6,360 Items Found</div>
			<select class="shortby">
				<option>Sort by :</option>
				<option>Price: High to Low</option>
				<option>Price: Low to High </option>
				<option>What's new</option>
				<option>Popularity</option>
				<option>Discount</option>				
			</select>
			</div>
		<ul class="listpro">
			<li>
			<a href="detail.php" class="product">
				<div class="flip-inner">
					<img src="images/blank.png" alt="img"/>
					<div class="flip-front"><img src="images/product1.jpg" alt="img" /></div>
					<div class="flip-back"><img src="images/product-hover2.jpg" alt="img" /></div>				
				</div>
				<div class="procont">
					<p><span>Slumber Jill</span></p>
					<p>Printed Cotton Nightdress</p>
					<p><strong>Just</strong><small>&#x20B9;789</small> <del>&#x20B9;1499</del> </p>
				</div>				
			</a>
			</li>
			
			<li>
			<a href="detail.php" class="product">
				<div class="flip-inner">
					<img src="images/blank.png" alt="img"/>
					<div class="flip-front"><img src="images/listpro1.jpg" alt="img" /></div>
					<div class="flip-back"><img src="images/product-hover2.jpg" alt="img" /></div>				
				</div>
				<div class="procont">
					<p><span>Slumber Jill</span></p>
					<p>Printed Cotton Nightdress</p>
					<p><strong>Just</strong><small>&#x20B9;789</small> <del>&#x20B9;1499</del> </p>
				</div>	
			</a>
			</li>
			
			<li>
			<a href="detail.php" class="product">
				<div class="flip-inner">
					<img src="images/blank.png" alt="img"/>
					<div class="flip-front"><img src="images/listpro2.jpg" alt="img" /></div>
					<div class="flip-back"><img src="images/product-hover1.jpg" alt="img" /></div>				
				</div>
				 
				<div class="procont">
					<p><span>Querida</span></p>
					<p>Printed Cotton Nightdress</p>
					<p><strong>Just</strong><small>&#x20B9;789</small> <del>&#x20B9;1499</del> </p>
				</div>	
			</a>
			</li>
			
			<li>
			<a href="detail.php" class="product">
				<div class="flip-inner">
					<img src="images/blank.png" alt="img"/>
					<div class="flip-front"><img src="images/listpro3.jpg" alt="img" /></div>
					<div class="flip-back"><img src="images/product-hover1.jpg" alt="img" /></div>				
				</div> 
				<div class="procont">
					<p><span>Typographic Print Lounge Set</span></p>
					<p>Printed Cotton Nightdress</p>
					<p><strong>Just</strong><small>&#x20B9;789</small> <del>&#x20B9;1499</del> </p>
				</div>
			</a>
			</li>
			<li>
			<a href="detail.php" class="product">
				<div class="flip-inner">
					<img src="images/blank.png" alt="img"/>
					<div class="flip-front"><img src="images/listpro4.jpg" alt="img" /></div>
					<div class="flip-back"><img src="images/product-hover2.jpg" alt="img" /></div>				
				</div>
				<div class="procont">
					<p><span>Typographic Print Lounge Set</span></p>
					<p>Printed Cotton Nightdress</p>
					<p><strong>Just</strong><small>&#x20B9;789</small> <del>&#x20B9;1499</del> </p>
				</div>
			</a>
			</li>
			 
		</ul>
		</div>
	</div>
</section>
	
<link rel="stylesheet" href="css/jquery-ui.css" media="all" /> 
<script type="text/javascript" language="javascript" src="js/owl.carousel.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script> 
<?php include('footer.php'); ?>
<script>
$( function() 
    {
        var max_price =$('#prodct_max_price').text();
        $( "#price_filter" ).slider({
            range: true,
            min: 500,
            max: max_price,
            values: [1000,max_price],
            //slidechange: function( event, ui )
            slide: function( event, ui ) 
            {
                $("#price_range_hidden").val(ui.values[ 0 ] + "-" + ui.values[ 1 ] );
                $("#min_price_range").text(ui.values[ 0 ]);
                $("#max_price_range").text(ui.values[ 1 ]);
            }
         });
         $( "#price_range_hidden" ).val($( "#price_filter" ).slider( "values", 0 ) +
            "-" + $( "#price_filter" ).slider( "values", 1 ) );
         $("#min_price_range").text($( "#price_filter" ).slider( "values", 0 ));
         $("#max_price_range").text($( "#price_filter" ).slider( "values", 1 ));
        
         $("#price_filter").slider
         ({
             stop: function( event, ui ) 
             {
                load_search_product_result();
             }
         });
} );
  
</script>
<script> 
	
$('.catlist > ul > li.active ul').slideDown();
$( ".catlist > ul > li > span" ).click(function() {
	if ($( this ).parent().hasClass('active')){
    $('.catlist > ul > li').removeClass('active');
		$(this).next().slideUp();	
		
  } else {
    $(this).parent().addClass('active');
	  $(this).next().slideDown();
  }
  	//$('.catlist > ul > li.active ul').slideToggle();	
});	

/*$(".topmenu > ul > li > a").each(function(){ 
	if($(window).width() < 1023)
    {		
		$(this).after( '<span class="plusicon"></span>' );		 
    }

});
  */
	
$( ".topmenu > ul > li > a" ).click(function(e) {
    e.stopPropagation();
    if($(window).width() < 1023)
    {		
		$(this).next().fadeToggle();		 
    }
  });
	
$( ".filtermobile" ).click(function(e) {
    e.stopPropagation();
    if($(window).width() < 767)
    {
      if($('.sideinner').length)
      {
        $('.sideinner').fadeToggle();
      }
    }
  });
	
	
$( ".fivelist" ).click(function(e) {
    e.stopPropagation();
    if($(window).width() > 1199)
    {
		$('.threelist').removeClass('active');
		$(this).addClass('active');
      $('.listpro').addClass('fivelist');
    }
  });	
	
$( ".threelist" ).click(function(e) {
    e.stopPropagation();
    if($(window).width() > 1199)
    {
		$('.fivelist').removeClass('active');
		$(this).addClass('active');
      $('.listpro').removeClass('fivelist');
    }
  }); 
	


</script>
</body>
</html>
