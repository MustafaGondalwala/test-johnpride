<?php
$parentCategories = CustomHelper::getCategories();

/*$FOOTER_CONTACT_DETAILS = CustomHelper::WebsiteSettings('FOOTER_CONTACT_DETAILS');
$FOOTER_TEXT = CustomHelper::WebsiteSettings('FOOTER_TEXT');
$FOOTER_BOTTOM = CustomHelper::WebsiteSettings('FOOTER_BOTTOM');*/

$websiteSettingsNamesArr = ['FOOTER_CONTACT_DETAILS', 'FOOTER_TEXT', 'FOOTER_BOTTOM'];

$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

$FOOTER_CONTACT_DETAILS = (isset($websiteSettingsArr['FOOTER_CONTACT_DETAILS']))?$websiteSettingsArr['FOOTER_CONTACT_DETAILS']->value:'';
$FOOTER_TEXT = (isset($websiteSettingsArr['FOOTER_TEXT']))?$websiteSettingsArr['FOOTER_TEXT']->value:'';
$FOOTER_BOTTOM = (isset($websiteSettingsArr['FOOTER_BOTTOM']))?$websiteSettingsArr['FOOTER_BOTTOM']->value:'';


?>


<!-- Footer Alert Modal -->
<div id="footerAlertModal" class="modal fade" role="dialog" style="z-index: 9999;">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-success">
					<strong>Success!</strong> Indicates a successful or positive action.
				</div>
			</div>
		</div>

	</div>
</div>

<!-- Footer Alert Modal -->

<section class="fullwidth bottomsec">
	<div class="container">
		<ul> 
		  <li>
		  	<div class="icons">
		  		<img src="{{url('/')}}/images/clothicon.png" alt="icon" />
		  		<span>Genuine Quality <br>Products</span>
		  	</div>
		  </li>
		 <li>
		  	<div class="icons">
		  		<img src="{{url('/')}}/images/serviceicon.png" alt="icon" />
		  		<span>Customer <br>Service</span>
		  	</div>
		  </li>
		  <li>
		  	<div class="icons">
		  		<img src="{{url('/')}}/images/changeicon.png" alt="icon" />
		  		<span>Free Returns and <br>Easy Exchange</span>
		  	</div>
		  </li>
		  <li>
		  	<div class="icons">
		  		<img src="{{url('/')}}/images/shippingicon.png" alt="icon" />
		  		<span>Reliable <br>Shipping</span>
		  	</div>
		  </li> 
		  </ul>
	</div>
</section>

<footer class="fullwidth footerBox noPrint"> 
	<div class="fullwidth footer1"> 
		<div class="container">
			<div id="topscroll" style="display: block;"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
			<div class="fbox flogo">
				<p><a href="{{url('/')}}"><img src="{{url('/')}}/images/logo01.png" alt="JohnPride" /></a></p>
				<?php
				echo $FOOTER_TEXT;
				?>
				<p></p>

			</div>
			<div class="fbox">
				<h4>ONLINE SHOPPING</h4>
				<ul>
					<?php
					if(!empty($parentCategories) && count($parentCategories) > 0){
						foreach($parentCategories as $pCat){
							?>
							<li><a href="{{url('products?pcat='.$pCat->slug)}}">{{$pCat->name}}</a></li>
							<?php
						}
					}
					?>
				</ul> 
			</div> 
			<div class="fbox">
				<h4>USEFUL LINKS</h4>
				<ul>
					<li><a href="{{url('about')}}">About</a></li>
					<li><a href="{{url('returns')}}">Return &amp; Exchange</a></li>
					<li><a href="{{url('faq')}}">FAQ</a></li>
					<li><a href="{{url('contact')}}">Contact</a></li>
					<li><a href="{{url('terms')}}">Terms &amp; Conditions</a></li>
					<li><a href="{{url('privacy')}}">Privacy Policy</a></li>
					<li><a href="{{url('blogs')}}">Blogs</a></li>
				</ul> 
			</div>


			<?php
	  /*
	  <div class="fbox">
		<h4>My Account</h4> 
		<ul>
		  <li><a href="{{url('users/orders')}}">Order History</a></li>
		  <li><a href="{{url('users/profile')}}">Account</a></li>
		  <li><a href="{{url('users/wishlist')}}">Wishlist</a></li>
		  <li><a href="{{url('users/wallet')}}">Wallet</a></li>

		  <?php
		  if(auth()->check()){
			?>
			<li><a href="{{url('logout')}}">Logout</a></li>
			<?php
		  }
		  else{
			?>
			<li><a href="{{url('account/login')}}">Login</a></li>
			<?php
		  }
		  ?>

		  <li><a href="{{url('cart')}}">Shopping Bag</a></li> 
		</ul> 
	  </div>
	  */
	  ?>

	  <div class="fbox faddress">
	  	<h4>For any query</h4>
	  	<?php echo $FOOTER_CONTACT_DETAILS; ?>


	  	<!-- <ul>
	  		<li><a href="https://www.facebook.com/johnpride/" target="_blank"><i class="facebookicon"></i></a></li>
	  		<li><a href="https://twitter.com/johnpride" target="_blank"><i class="twittericon"></i></a></li>
	  		<li><a href="#" target="_blank"><i class="linkedinicon"></i></a></li>
	  		<li><a href="https://www.instagram.com/johnpride/" target="_blank"><i class="instragramicon"></i></a></li>
	  	</ul> -->

	  </div>  
	  <div class="clearfix"></div>
	  <!-- <div class="row">
	  	<div class="col-md-3">
	  		<div class="footer_suscribe">
	  			<span>Subscribe Now</span>
	  			@include('common._subscribe_form') 
	  		</div>
	  	</div> 
	  </div> -->
	</div> 

</div> 

<!-- <div class="fullwidth fbottom"> 

	<?php
	//echo $FOOTER_BOTTOM;
	?>

</div>
 -->
</footer>

<div class="support" id="fab1" title="Support">
  <!-- <i class="fa fa-comments" id="fabIcon"></i> -->
  <img src="{{url('/')}}/images/support.png" class="img-fluid" id="fabIcon" alt="Support" title="Support">
</div>
<div class="inner-fabs">
  <a href="mailto:support@johnpride.in" title="Send Enquiry">
    <svg class="svg-inline--fa fa-envelope fa-w-16" aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path></svg><!-- <i class="far fa-envelope"></i> -->
  </a>
  <a href="https://wa.me/919599969498" target="_blank" title="Whatsapp Now">
    <svg class="svg-inline--fa fa-whatsapp fa-w-14" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="whatsapp" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path></svg><!-- <i class="fab fa-whatsapp"></i> -->
  </a>
  <a href="tel:09599969498" title="Call Now">
    <svg class="svg-inline--fa fa-phone fa-w-16" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="phone" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg><!-- <i class="fa fa-phone"></i> -->
  </a> 

  <!-- Scroll to top -->
  <!-- <a href="javascript:void(0);" title="Move to top" class="returntotop" id="top" style="">
    <i class="fa fa-angle-double-up"></i>
  </a> -->
</div>




<div class="popupbg"></div> 
<div class="popupbox">
  <div class="popboxbg">
  <span class="crossbtn closebtn">X</span> 
  <div class="fullwidth logoimg"><img src="{{url('/')}}/images/logo.png" alt="JohnPride" border="0" /></div>
  <div class="fullwidth popcont">
    <h3>GET A 10% BENEFIT</h3>
    <p>Dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</p>  
    @include('common._newsletter_form') 
    <!-- <input type="text" placeholder="Enter your name" />  
    <input type="email" placeholder="Enter your email" />     
    <button class="submitbtn">Get Started</button>
    <p>Dummy text of the printing and typesetting industry.</p>  -->
    <a href="#">No Thanks</a>
  </div>

</div>
</div>  



<!-- <script type="text/javascript" src="{{url('public')}}/js/jquery.min.js"></script>
 -->
 <script type="text/javascript" src="{{url('/')}}/js/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script>
      $('.support').click(function(){
      let img_link = "{{url('/')}}/images/support.png";
      let img_cross_link = "{{url('/')}}/images/cross.png";
      $('.inner-fabs').toggleClass('show');
      let img = $('#fabIcon').attr('src');
      (img == img_link)?$('#fabIcon').attr('src',img_cross_link):$('#fabIcon').attr('src',img_link);
      // if($('.inner-fabs').hasClass('show')){
      //     setTimeout(function(){
      //       $('.inner-fabs').removeClass('show');
      //        (img == img_cross_link)?$('#fabIcon').attr('src',img_cross_link):$('#fabIcon').attr('src',img_link);
      //      },10000);
      // }
    });
    </script>
<script>
// 	$(window).scroll(function(){
//   var windowHeights = $(document).scrollTop();
//   if ($(window).width() > 767) {
//     if(windowHeights > 180)
//     {
//       $(".headerbg").addClass('fixed');
//       $(".heighthead").addClass('fixed');
//     }
//     else
//     {
//       $(".headerbg").removeClass('fixed');
//       $(".heighthead").removeClass('fixed');
//     }
//   }  
// });

$(".scochlist").find("a").on("click", function(e){
    e.preventDefault();
    $(this).parent().find(".popupmain").addClass("active");
     $(".popupbg1").fadeIn();

  });
  $(".closebtn").on("click", function(e){
    e.preventDefault();
    $(".popupbg1").fadeOut();
    $(".popupmain").removeClass("active");
  });

$( ".showpopup" ).click(function() {
  $('.popupbg').fadeToggle();
  $('.popupbox').toggleClass('active');
  //$('.popupbox').fadeToggle();
});

$('.closebtn').click(function() { 
    $(".popupbg").fadeOut();
    $(".popupbox").removeClass('active');       
});



	$(window).scroll(function() {
		if ($(this).scrollTop() > 48){  
			$('header').addClass("sticky");
			$(".heighthead").addClass('fixed');
		}
		else{
			$('header').removeClass("sticky");
			$(".heighthead").removeClass('fixed');
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
 
		//$('.topmenu').find('li > ul').addClass('sub-menu');

		$(".sub-menu").before("<span class='ddclick'></span>");
		$(".sub-sub-menu").before("<span class='ddclick'></span>");

		  $( ".ddclick").click(function() {	
		  $(this).toggleClass('active');		  	 
		  	 // $('.ddclick').removeClass('active');
		  	 // $(this).addClass('active');		  	 
		  	 $(this).next().slideToggle(); 
		  	 //$('.sub-menu').not($(this).parent().parent('.sub-menu')).slideToggle( );
		  	 //
		  	 //$(this).parent().parent('.sub-menu').slideDown();
		  	//$(this).next().slideDown(); 
		    

		  });

		// $('.ddclick').click(function(e) {
		// 	e.stopPropagation();
		// 	if($(window).width() < 1023){
		// 		var $el = $(this).parent().find("ul"),
		// 		$parPlus = $(this).parent().find(".ddclick"); 

		// 		$parPlus.stop(true, true).toggleClass("minus_icon");
		// 		$(this).next().next().slideToggle();
		// 	}
		// }); 
		  

		// $(".sub-links > a").each(function(){	
		// 	if($(window).width() < 1023){
		// 		$(this).before( '<span class="plusicon"></span>' );
		// 	}
		// });

		

	});
	
	


	function submit_search_form(){
		var searchForm = $("form[name=searchForm]");
		var filterForm = $("form[name=filterForm]");

		var keyword = searchForm.find("input[name=keyword]").val();

		filterForm.find("input[name=keyword]").val(keyword);

		searchForm.submit();
  //$("form[name=filterForm]").submit();

  return false;
}

var searchForm = $("form[name=searchForm]");
var headerKeyword = searchForm.find("input[name=keyword]");

$("form[name=searchForm] input[name=keyword]").on('keyup click', function(){
	var searchKeyword = $(this).val();

	var keywordLen = searchKeyword.length;

	if(keywordLen >= 3){
		setTimeout(getSearchList(searchKeyword), 700);
	}
	else{
		$("#search_list").html("");
	}
});

function getSearchList(keyword){

	var _token = '{{ csrf_token() }}';

	$.ajax({
		url: "{{ route('products.ajax_get_list_by_search') }}",
		type: "POST",
		data: {keyword:keyword},
		dataType:"JSON",
		headers:{'X-CSRF-TOKEN': _token},
		cache: false,
		beforeSend:function(){

		},
		success: function(resp){
			if(resp.success){

				if(resp.searchListHtml){
					$("#search_list").html(resp.searchListHtml);

					$("#search_list").show();
				}

			}
		}
	});

}


$(document).on("click", ".sr_list_item", function(){
	var fieldName = $(this).data("field");
	var val = $(this).data("val");

	if( (fieldName && fieldName != "") && (val && val != "") ){

		var searchForm2 = $("form[name=searchForm2]");

		var newInp = '';

		newInp += '<input type="hidden" name="'+fieldName+'" value="'+val+'" />';

		if(fieldName == 'cat'){
			var p1CatSlug = $(this).data("pcat");
			newInp += '<input type="hidden" name="pcat" value="'+p1CatSlug+'" />';
		}

		searchForm2.append(newInp);

		searchForm2.submit();
	}
});


/*$("body").click(function(e){
	if(e.target.className !== "form_wrapper"){
	  $("#search_list").hide();
	}
});*/

$(document).mouseup(function (e){

	var container = $("#search_list");

	if (!container.is(e.target) && container.has(e.target).length === 0){
		container.hide();    
	}
});

$(document).on("click", ".alert .close", function(){
	$(this).parent(".alert").remove();
	//window.location.reload();
});

$(document).on("click", "#cart_popup .close", function(){
	
	window.location.reload();
});

$(window).scroll(function() {
	if ($(this).scrollTop() >= 250) {
		$('#topscroll').fadeIn(200);
	} else {
		$('#topscroll').fadeOut(200);
	}
});
$('#topscroll').click(function() {
	$('body,html').animate({
		scrollTop : 0 
	}, 500);
});

$('.form-control').click(function() {
	$(this).siblings('.newsletter_messages').html('');
});

$('.subscribeBtn').click(function(e)
{
	e.preventDefault();

	var currSelector = $(this);
	var y= true;
	var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/; 
	var email = currSelector.siblings("input[name='subscribe_email']").val().trim();
	var name = currSelector.siblings("input[name='subscribe_name']").val().trim();
   //alert(email); return false;

   if(name==''){

   	currSelector.siblings('.newsletter_messages').html('<span style="color:#e41881">Name is required.</span>');
   	y=false;
   } 

   if(email==''){

   	currSelector.siblings('.newsletter_messages').html('<span style="color:#a77736">Email is required.</span>');
   	y=false;
   } 
   if(email!=''){
   	if (!filter.test(email)){
   		currSelector.siblings('.newsletter_messages').html('<span style="color:#a77736">Invalid Email.</span>');
   		y=false;
   	}
   }
   
   if(y) {
   	var _token = '{{csrf_token()}}';
   	$.ajax({
   		url : '<?php echo url('common/newsletterSubscribe'); ?>',
   		type : 'POST',
   		data : {email:email, name:name},
   		dataType: 'JSON',
   		async : false,
   		headers:{'X-CSRF-TOKEN': _token},
   		success: function(resp){
   			if(resp.success){

   				if(resp.message){
   					currSelector.siblings('.newsletter_messages').html('<span>'+resp.message+'</span>');
   					currSelector.siblings(".subscribe_name").val('');
   					currSelector.siblings(".subscribe_email").val('');
   					currSelector.siblings(".newsletter_messages").addClass('succ_msg');
			//currSelector.siblings(".newsletter_messages").fadeOut(3000);

		}

	}
	else{
		if(resp.message){
			currSelector.siblings('.newsletter_messages').html('<span>'+resp.message+'</span>');
			currSelector.siblings(".subscribe_email").val('');
		}
	}
}

});
   }
   return false; 
});

</script>

<script type="text/javascript">


	var loginSeqArr = {};

	$(document).ready(function(){
		$(".open_slide").click(function(){
			$(".slide_login").animate({right: '0px'});
		});
		$(".cross_icon").click(function(){
			$(".slide_login").animate({right: '-100%'});
		});
		$(".nav_out").click(function(){
			$(".slide_login").animate({right: '-100%'});
		});
		$(".open_slide").click(function(){
			$(".nav_out").css({visibility: 'visible', opacity:'1'});
		});

		$(".nav_out").click(function(){
			$(".nav_out").css({visibility: 'hidden', opacity:'0'});
		});
		$(".cross_icon").click(function(){
			$(".nav_out").css({visibility: 'hidden', opacity:'0'});
		});
		$(".open_slide").click(function(){
			$("body").addClass("white_header");
		});
		$(".nav_out").click(function(){
			$("body").removeClass("white_header");
		});
		$(".cross_icon").click(function(){
			$("body").removeClass("white_header"); 
		});
   // $(".open_slide").click(function(){
   //      $(".home header").css({box-shadow: '0px 3px 7px 0px rgba(0, 0, 0, 0.08)', background:'#fff'});
   //  });

});


	$(".show_reg").click(function(){
		$(".registerBox").show();
		$(".loginBox").hide();
		$(".forgotBox").hide();

		resetRegisterForm();

	//setLoginSeq('show_reg');
});
	$(".forgot_btn_show").click(function(){
		$(".forgotBox").show();
		$(".loginBox").hide();
		$(".registerBox").hide();

	//setLoginSeq('forgot_btn_show');
});

	$(".loginBtn").click(function(){
		showLoginBox();

	//setLoginSeq('loginBtn');
});

	$(document).on("click", ".mainLoginBtn", function(){
		showLoginBox();
	});

	function showLoginBox(){
	//alert('showLoginBox');

	$(".loginBox").show();
	$(".forgotBox").hide();
	$(".registerBox").hide();

}


$(".loginBack").click(function(){
	backToLogin();
});





  //var loginSeqArr = {};


  /*function setLoginSeq(className){

	if(className && className != ''){
	  var arrLen = Object.keys(loginSeqArr).length;

	  if( !(arrLen >= 3) ){
		for(var i=1; i<=arrLen; i++){
		   console.log("loginSeqArr["+i+"]="+loginSeqArr[i]);
		}
		loginSeqArr[arrLen+1] = className;
	  }
	}

}*/

function backToLogin(){

	$(".loginBtn").click();

	/*var arrLen = Object.keys(loginSeqArr).length;

	if(arrLen >= 2){
	  console.log("arrLen="+arrLen);

	  var className = loginSeqArr[arrLen-1];
	  console.log("className="+className);

	  if(className && className != ''){
		delete loginSeqArr[arrLen-1];
		delete loginSeqArr[arrLen];
		$("."+className).click();

		if(className == 'loginBtn'){
		  loginSeqArr = {};
		}
	  }
	}
	else{
	  $(".loginBtn").click();
	}*/
}



$(document).on("click", ".sbmtLogin", function(e){
	e.preventDefault();

	var loginForm = $("form[name=loginForm]");

	var _token = '{{csrf_token()}}';

	$.ajax({
		url: "{{url('account/ajax_login')}}",
		type: "POST",
		data: loginForm.serialize(),
		dataType: "JSON",
		headers:{
			'X-CSRF-TOKEN': _token
		},
		cache: false,
		beforeSend: function(){
			loginForm.find( ".help-block" ).remove();
			loginForm.find( ".has-error" ).removeClass( "has-error" );
		},
		success: function(resp){
			if(resp.success) {
				window.location.reload();
			}
			else if(resp.errors){

				var errTag;
				var countErr = 1;

				$.each( resp.errors, function ( i, val ) {

					loginForm.find( "[name='" + i + "']" ).parent().addClass( "has-error" );
					loginForm.find( "[name='" + i + "']" ).parent().append( '<p class="help-block">' + val + '</p>' );

					if(countErr == 1){
						errTag = loginForm.find( "[name='" + i + "']" );
					}
					countErr++;

				});

				if(errTag){
					errTag.focus();
				}
			}
		}
	});
});

var sendingOtp = 0;

$(document).on("click", ".sbmtRegister, .sendOtp", function(e){
	e.preventDefault();

	var currSelector = $(this);

	var btn_name = $(this).attr('name');
	var btn_val = 1;

	var type = btn_name+'='+btn_val;

	submitRegisterForm(btn_name, currSelector);
});


function submitRegisterForm(type, currSelector){
	var registerForm = $("form[name=registerForm]");

	var _token = '{{csrf_token()}}';

	if(sendingOtp == 0){

		//sendingOtp = 1;

		$.ajax({
			url: "{{url('account/ajax_register')}}",
			type: "POST",
			data: registerForm.serialize()+'&'+type+'=1',
			dataType: "JSON",
			headers:{
				'X-CSRF-TOKEN': _token
			},
			cache: false,
			beforeSend: function(){
				registerForm.find( ".help-block" ).remove();
				registerForm.find( ".has-error" ).removeClass( "has-error" );
			},
			success: function(resp){
				if(resp.success) {
			//window.location.reload();
			if(resp.message){
				//$(".registerBox .alertMsg").html(resp.message);
			  //document.registerForm.reset();


			  /*setTimeout(function(){
			  	$(".registerBox .alertMsg .alert").slideUp();
			  }, 2000);*/

			  showFooterAlert(resp.message);
			}
			sendingOtp = 0;


			if(type == 'send_otp'){

				currSelector.hide();
				currSelector.siblings(".otpBox").show();
				currSelector.siblings(".confirmBox").show();

			}
			else if(type == 'register'){
				document.registerForm.reset();
				showLoginBox();
			}
		}
		else if(resp.errors){

			var errTag;
			var countErr = 1;

			$.each( resp.errors, function ( i, val ) {

				registerForm.find( "[name='" + i + "']" ).parent().addClass( "has-error" );

				if(i == 'gender'){
					registerForm.find( "[name='" + i + "']" ).siblings("span.error").html( '<p class="help-block">' + val + '</p>' );
				}
				else{
					registerForm.find( "[name='" + i + "']" ).parent().append( '<p class="help-block">' + val + '</p>' );
				}

				if(countErr == 1){
					errTag = registerForm.find( "[name='" + i + "']" );
				}
				countErr++;

			});

			if(errTag){
				errTag.focus();
			}
		}
	}
});
	}
}


function resetRegisterForm(){
	document.registerForm.reset();

	$(".otpBox").hide();
	$(".confirmBox").hide();
	$(".sendOtp").show();

	sendingOtp = 0;
}






$(document).on("click", ".sbmtForgot", function(e){
	e.preventDefault();

	var forgotForm = $("form[name=forgotForm]");

	var _token = '{{csrf_token()}}';

	$.ajax({
		url: "{{url('account/ajax_forgot')}}",
		type: "POST",
		data: forgotForm.serialize(),
		dataType: "JSON",
		headers:{
			'X-CSRF-TOKEN': _token
		},
		cache: false,
		beforeSend: function(){
			forgotForm.find( ".help-block" ).remove();
			forgotForm.find( ".has-error" ).removeClass( "has-error" );
		},
		success: function(resp){
			if(resp.success) {
				if(resp.message){
					$(".forgotBox .alertMsg").html(resp.message);
					document.forgotForm.reset();
					setTimeout(function(){
						$(".forgotBox .alertMsg .alert").slideUp();
					}, 2000);

				}
			}
			else if(resp.errors){

				var errTag;
				var countErr = 1;

				$.each( resp.errors, function ( i, val ) {

					forgotForm.find( "[name='" + i + "']" ).parent().addClass( "has-error" );
					forgotForm.find( "[name='" + i + "']" ).parent().append( '<p class="help-block">' + val + '</p>' );

					if(countErr == 1){
						errTag = forgotForm.find( "[name='" + i + "']" );
					}
					countErr++;

				});

				if(errTag){
					errTag.focus();
				}
			}
		}
	});
});

$(document).on("mouseover", ".passEye", function(){
	$(this).siblings(".inpPass").prop("type", "text");
});

$(document).on("mouseleave", ".passEye", function(){
	$(this).siblings(".inpPass").prop("type", "password");
});


function showFooterAlert($msg=''){

	if($msg && $msg != ''){    

		$("#footerAlertModal").find(".modal-body").html($msg);
		$("#footerAlertModal").modal("show");
		//showLoginBox();
	}

}



</script>

