<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="_token" content="{{ $_token or '' }}" />
    <title>{{ $title or config('app.name') . ' - Admin Panel' }}</title>

    <link rel="stylesheet" href="{{url('assets')}}/css/chat.css">
    <!-- <link rel="stylesheet" href="{{url('public/assets')}}/css/chat-style.css"> -->

    <?php
    /*
    <link href="favicon.ico" type="image/x-icon" rel="icon" />
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" />

    <link href="{{url('public')}}/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    */
    ?>
    
    <link href="{{url('/')}}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <link href="{{url('/')}}/css/site.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/css/jquery.mCustomScrollbar.css" />
     <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900" rel="stylesheet" /> 
    <!--Header block-->
    {{ $headerBlock or '' }}
</head>

<body>
<div class="fullwidth mainwrapper">
<div class="container">
    <!-- Site Top -->
    
    <!-- Menu -->
    @include('admin.layouts.nav')
    
    
     <div class="rightsec">
     <!-- Header -->
     <div class="headersec">
     	<div class="menuicon menu_on"><span></span></div>
		 @include('admin.layouts.top')
		 <div class="logo">

			<img src="{{url('/')}}/images/logo01.png" alt="Johnpride" />
			
			<!-- <img class="logo1" src="" alt="Johnpride" /> -->
		 </div>
     </div>
   

    

    <!-- Body -->
    <div class="centersec">
    {{ $slot }}
    </div>
    
    <div class="copyright text-center noPrint">
		All Rights Reserved &copy;  <?php echo date('Y'); ?>
	</div>
    
     </div>

    

</div>
</div>

<!-- Placed at the end of the document so the pages load faster -->

<?php
/*
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{url('public/js/jquery.min.js')}}"></script>
*/
?>



<script type="text/javascript" src="{{url('js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{url('js/bootstrap.min.js')}}"></script>


	<style>
		.leftsec1{height: calc(100% - 70px); position: absolute; top: 0; margin-top: 70px;}
	</style>
	<script>
		(function($){
			

			$(document).click(function(){
				$("body").removeClass("nav_active");
			});
			$(".menu_on,.leftsec1").click(function(e){
				e.stopPropagation();
				$("body").addClass("nav_active");
			});
		})(jQuery);
	</script>
	
<!--Bottom block-->
{{ $bottomBlock or '' }}
	
	

<script>
/*$(".subtab").click(function(){
	
		$('li').removeClass('active');
	
	  if($(this).parent().hasClass("active")) {
			$(this).parent().removeClass('active');
		} else {
			$('.subtab').removeClass('active');
			$(this).parent().addClass('active');
			
			
		}
  });*/
	$('.dropul').click(function(){
		$(".dropul").parent('li').not($(this).parent('li')).removeClass( "active" );
		$(this).parent('li').toggleClass( "active" );
		//$(this).parent() .toggleClass( "active" );
	} );

	$('.child_dropul').click(function(){
		$(".child_dropul").siblings('ul').not($(this).siblings('ul')).hide();

		if($(this).find('i').hasClass("fa-angle-right")){
			$(this).find('i').removeClass("fa-angle-right");
			$(this).find('i').addClass("fa-angle-down");
		}
		else{
			$(this).find('i').addClass("fa-angle-right");
			$(this).find('i').removeClass("fa-angle-down");
		}
		
		$(this).siblings('ul').toggle();
		//$(this).parent() .toggleClass( "active" );
	} );
</script>

<!--
<script>
$(".subnav_link").click(function(){
		if($(this).hasClass("active")){
			$(".catsubnav_full").slideUp(300);
			$(".subnav_link").removeClass("active");
		} else {
			$(".catsubnav_full").hide();
			$(".subnav_link").removeClass("active");
	    $(this).closest("li").find(".catsubnav_full").slideToggle(300);
	    $(this).toggleClass("active"); 
		}
	}); 
	</script>-->

	<script type="text/javascript">
		$(".updateInventory").click(function(){
			//alert("updateInventory");	

			$.ajax({
				url:"{{url('processjobs/getInventory')}}",
				type:"GET",
				success:function(resp){
					if(resp.success){
						alert("Inventory from Unicommerce has been updated successfully.");
					}
					else{
						alert("Nothing updated!");
					}
				}
			});
		});

		$(".updateOrderStatus").click(function(){
			//alert("updateOrderStatus");	

			$.ajax({
				url:"{{url('processjobs/update_order_status')}}",
				type:"GET",
				success:function(resp){
					if(resp.success){
						alert("Status from Unicommerce has been updated successfully.");
						window.location.reload(true);
					}
					else{
						alert("Nothing updated!");
					}

				}
			});
		});
	</script>
	
</body>
</html>