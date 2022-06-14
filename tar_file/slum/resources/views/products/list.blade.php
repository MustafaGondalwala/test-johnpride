<!DOCTYPE html>
<html>
<head>  

@include('common.head')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="{{url('public/css/owl.carousel.min.css')}}" />
</head>
<body>

@include('common.header')


<?php
$storage = Storage::disk('public');

$priceFrom = (isset($priceFrom) && is_numeric($priceFrom))?$priceFrom:0;
$priceTo = (isset($priceTo) && is_numeric($priceTo))?$priceTo:1000;
?>


<section class="breadcrumbs fullwidth">
  <div class="container"> 
    <a href="{{url('')}}">Home</a>
    <?php
    if(isset($parentCategory->name) && !empty($parentCategory->name) ){
      ?>
      <a href="javascript:void(0)">{{$parentCategory->name}}</a>
      <?php
    }
    ?>
  </div>
</section>

<section class="fullwidth innerlist">
  <div class="container">
    
  
@include('common.left_nav')
    
    <?php if(!empty($products) && count($products) > 0) { ?>
    <div class="rightcontent">
		<div class="shortlist fullwidth">
			<div class="founditem">6,360 Items Found</div>
			<div class="gridlist"><span class="threelist"><small></small><small></small><small></small></span> <span class="fivelist"><small></small><small></small><small></small><small></small><small></small></span></div>

      <?php
      if(isset($keyword) && !empty($keyword)){
        ?>
        <div class="founditem">{{count($products)}} Item(s) Found</div>
        <?php
      }
      ?>

      <?php
      $products_sort_by_arr = config('custom.products_sort_by_arr');
      ?>
			
			<select class="shortby listShortBy">
				<option>Sort by :</option>
        <?php
        if(!empty($products_sort_by_arr) && count($products_sort_by_arr) > 0){
          foreach($products_sort_by_arr as $psaKey=>$psa){
            $selected = '';
            if($psaKey == $sort_by){
              $selected = 'selected';
            }
            ?>
            <option value="{{$psaKey}}" {{$selected}} >{{$psa}}</option>
            <?php
          }
        }
        ?>
								
			</select>
			</div>
		
    <ul class="listpro">
      
      <?php
      foreach ($products as $product){

        $product_image = (isset($product->defaultImage))?$product->defaultImage:'';
        $reverse_image = (isset($product->reverseImage))?$product->reverseImage:'';

        $mainPrice = $product->price;

        $price = $product->price;
        $salePrice = $product->sale_price;

        $productPrice = $mainPrice;
        if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
          $productPrice = $product->sale_price;
        }
        else{
          $salePrice = $product->price;
        }

        ?>
        <li>
          <a href="<?php echo url('products/details/'.$product->slug); ?>" class="product">
            <?php
            if(!empty($product_image) && count($product_image) > 0){

              $img_path = 'products/';
              if(!empty($product_image->image) && $storage->exists($img_path.$product_image->image)){
               ?>      
               <div class="flip-inner">
                <img src="{{url('public')}}/images/blank.png" alt="{{$product->name}}"/>

                <div class="flip-front">
                  <img src="{{url('public/storage/'.$img_path.$product_image->image)}}" alt="{{$product->name}}" />
                </div>  

                <?php
                if(!empty($reverse_image->image) && $storage->exists($img_path.$reverse_image->image)){
                  ?>
                  <div class="flip-back">
                    <img src="{{url('public/storage/'.$img_path.$reverse_image->image)}}" alt="{{$product->name}}" />
                  </div>  
                  <?php
                }
                ?>   

              </div>
              <?php
            }
          }
          ?>

          <div class="procont">
            <p><span> {{$product->name}} </span></p>
            <p> {{$product->name}} </p>
            <p><strong>Just</strong><small>&#x20B9;{{ $productPrice }}</small> 
              <?php
              if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
                ?>
                <del>&#x20B9;{{$price}}</del> 
                <?php
              }
              ?>
            </p>
          </div>        
        </a>
      </li>
      <?php
    }
       ?>
       
    </ul>
    </div>

     
    <?php
  } 
  else{
    echo "No Product Found.";
  }
  ?>


  </div>
</section>



@include('common.footer')

<script type="text/javascript" src="{{url('public')}}/js/owl.carousel.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 

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
	
 $('.child-category').click(function(){
        $("#filterForm").submit();
    });
	
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


$(document).on("change", ".listShortBy", function(){
  var sortByVal = $(this).val();

  if(sortByVal && sortByVal != ""){
    $("form[name=filterForm]").find("input[name=sort_by]").val(sortByVal);
    $("form[name=filterForm]").submit();
  }
});


$(document).on("click", ".viewMore", function(){
  $(this).siblings(".moreItem").slideToggle();
  $(this).toggleClass("shownMore");

  if($(this).hasClass("shownMore")){
    $(this).text("View less");
  }
  else{
    $(this).text("View more");
  }
});

var priceFrom = parseInt("{{$priceFrom}}");
var priceTo = parseInt("{{$priceTo}}");
	

  $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 5000,
      values: [ priceFrom, priceTo ],
      slide: function( event, ui ) {
        $( "#amount" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
      },
      change: function( event, ui ){
        $("#filterForm").find("input[name=price_range]").val(ui.values);
        $("#filterForm").submit();
      }
    });
    $( "#amount" ).val( $( "#slider-range" ).slider( "values", 0 ) +
      " - " + $( "#slider-range" ).slider( "values", 1 ) );


</script>

</body>
</html>