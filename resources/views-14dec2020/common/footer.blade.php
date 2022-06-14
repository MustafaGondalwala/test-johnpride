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


<div class="popupbg"></div> 
<div class="popupbox">
  <div class="popboxbg">
  <span class="crossbtn closebtn">X</span> 
  <div class="fullwidth logoimg"><img src="{{url('/')}}/images/logo.png" alt="JohnPride" border="0" /></div>
  <div class="fullwidth popcont">
    <h3>GET A 10% BENEFIT</h3>
    <p>Dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</p>  
    <input type="text" placeholder="Enter your name" />  
    <input type="email" placeholder="Enter your email" />     
    <button class="submitbtn">Get Started</button>
    <p>Dummy text of the printing and typesetting industry.</p> 
    <a href="#">No Thanks</a>
  </div>

</div>
</div>  

<!-- <script type="text/javascript" src="{{url('public')}}/js/jquery.min.js"></script>
 -->
 <script type="text/javascript" src="{{url('/')}}/js/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

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


		$(".topmenu > ul > li > a").each(function(){ 
			if($(this).parent().children("ul").length){
				$(this).parent('li').addClass("sub-links");	
			} 
		});

		$(".sub-links > a").each(function(){	
			if($(window).width() < 1023){
				$(this).before( '<span class="plusicon"></span>' );
			}
		});

		$('.plusicon').click(function(e) {
			e.stopPropagation();
			if($(window).width() < 1023){
				var $el = $(this).parent().find("ul"),
				$parPlus = $(this).parent().find(".plusicon"); 

				$parPlus.stop(true, true).toggleClass("minus_icon");
				$(this).next().next().slideToggle();
			}
		}); 

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
   //alert(email); return false;

   if(email==''){

   	currSelector.siblings('.newsletter_messages').html('<span style="color:#e41881">Email is required.</span>');
   	y=false;
   } 
   if(email!=''){
   	if (!filter.test(email)){
   		currSelector.siblings('.newsletter_messages').html('<span style="color:#e41881">Invalid Email.</span>');
   		y=false;
   	}
   }
   
   if(y) {
   	var _token = '{{csrf_token()}}';
   	$.ajax({
   		url : '<?php echo url('common/newsletterSubscribe'); ?>',
   		type : 'POST',
   		data : {email:email},
   		dataType: 'JSON',
   		async : false,
   		headers:{'X-CSRF-TOKEN': _token},
   		success: function(resp){
   			if(resp.success){

   				if(resp.message){
   					currSelector.siblings('.newsletter_messages').html('<span>'+resp.message+'</span>');
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

