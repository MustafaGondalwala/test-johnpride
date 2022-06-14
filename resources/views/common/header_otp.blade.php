

<div class="fullwidth heighthead"></div>

<?php 

$websiteSettingsNamesArr = ['TOP_MENU_IMAGE','HEADER_TEXT'];

$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

$TOP_MENU_IMAGE = (isset($websiteSettingsArr['TOP_MENU_IMAGE']))?$websiteSettingsArr['TOP_MENU_IMAGE']->value:'';

$HEADER_TEXT = (isset($websiteSettingsArr['HEADER_TEXT']))?$websiteSettingsArr['HEADER_TEXT']->value:'';



$image_path = config('custom.image_path');

?>

<div class="nav_out"></div>

<div class="logo show_header" style="display: none">

	<img src="{{url('/')}}/images/logo.png" alt="Slumber Jill"/>

</div>

<header class="header fullwidth noPrint"> 

<div class="fullwidth topstrip"> <?php echo $HEADER_TEXT; ?></div>



	<div class="logo1">

		<a href="{{url('/')}}">

			<img src="{{url('/')}}/images/logo01.png" alt="JohnPride"  class="logocolor" />

			<img src="{{url('/')}}/images/logow1.png" alt="JohnPride" class="logowhite" />

		</a>

	</div>



	<div class="logo">

		<a href="{{url('/')}}">

			<img src="{{url('/')}}/images/logo.png" alt="JohnPride" class="logocolor" />

			<img src="{{url('/')}}/images/logow.png" alt="JohnPride" class="logowhite" />

		</a>

	</div>

<div class="menuwidth1">

	<?php

	//$collections = CustomHelper::getData('brands', '', ['status'=>1, 'featured'=>1]);

	$collections = CustomHelper::getData('brands', '', ['status'=>1]);

	$BackUrl = CustomHelper::BackUrl();

	$referer = (request()->has('referer'))?request()->referer:'';

	$parentCategories = CustomHelper::getCategories();

	$parentCategories = $parentCategories->where('status', 1);

	if(!empty($parentCategories) && count($parentCategories) > 0) { ?>

		<div class="navicon"><span></span></div>

		 		 



		<div class="topmenu">
	

			<ul>

				<li class="mobilelink"><a class="link_click" href="#">Featured</a>

					<ul class="sub-menu">

						<!-- <li><a href="{{route('products.list', ['new_arrival'=>1])}}">New Arrivals</a></li> -->

						<!-- <li><a href="<?php //echo url('products/new-arrival') ?>">New Arrivals</a></li>

						<li><a href="{{route('products.list', ['trending'=>1])}}">Trending</a></li>

						<li><a href="{{route('products.list', ['popularity'=>1])}}">Popular</a></li>

						<li><a href="{{route('products.list', ['eco'=>1])}}">ECO</a></li>

						<li><a href="{{route('products.list', ['premium'=>1])}}">Premium</a></li> -->


						<li><a href="<?php echo url('products/new-arrival') ?>">New Arrivals</a></li>
						
                       <li><a href="<?php echo url('products/trending') ?>">Trending</a></li>
                        
                        <li><a href="<?php echo url('products/popularity') ?>">Popular</a></li>

                         
                         <li><a href="<?php echo url('products/ECO') ?>">ECO</a></li>

                        <li><a href="<?php echo url('products/PREMIUM') ?>">Premium</a></li>






					</ul>

				</li>

				<li><a class="link_click"  href="javaScript:Void(0)"><span class="desktopshow">Shop</span>

				 <span class="mobileshow">Categories</span></a>



				<?php

				if(!empty($parentCategories) && count($parentCategories)>0) { ?>	
					<ul class="sub-menu">
					<div class="container">
					<li class="col_sec categories-block"><a class="link_click link_click_menu" href="javaScript:Void(0)"> Categories </a>
						
						
							<ul class="sub-sub-menu">

			<?php

				foreach ($parentCategories as $category) {

					$childCategories = (!empty($category->children))?$category->children:''; 
					$childCategories = $childCategories->where('status', 1)->sortBy('sort_order');
					?>

						<li><a href="javaScript:Void(0)" class="sub-sub-menu-head link_click"> {{$category->name}}  </a>
						
					<ul class="sub-sub-menu-child sub-sub-menu">

							<?php

							foreach($childCategories as $childCat)
							{

							$childChildCategories = (!empty($childCat->children))?$childCat->children:''; 

							$childChildCategories = $childChildCategories->where('status', 1)->sortBy('sort_order');

							?>

						


							<li><a href="<?php echo url('products/'.$childCat->slug); ?>">{{$childCat->name}}</a> </li>

							<?php 
							}
							?>			
								</ul>  
							</li>

						<?php } ?>

							</ul>

						 </li>

				<?php } ?>

					<li class="col_sec"><a class="link_click link_click_menu" href="javaScript:Void(0)"> Featured </a>
						
						<ul class="sub-sub-menu">
							<li><a href="<?php echo url('products/new-arrival') ?>">New Arrivals</a></li>

							<!-- <li><a href="{{route('products.list', ['trending'=>1])}}">Trending</a></li> -->

							<li><a href="<?php echo url('products/trending') ?>">Trending</a></li>

							<!-- <li><a href="{{route('products.list', ['popularity'=>1])}}">Popular</a></li> -->

							<li><a href="<?php echo url('products/popularity') ?>">Popular</a></li>

							<!-- <li><a href="{{route('products.list', ['eco'=>1])}}">ECO</a></li> -->
							<li><a href="<?php echo url('products/ECO') ?>">ECO</a></li>

						<li><a href="<?php echo url('products/PREMIUM') ?>">Premium</a></li>
						</ul>
					</li>

					<?php

				  if(!empty($collections) && count($collections) > 0) 
				  {

				   ?>

						 <li class="col_sec"><a class="link_click link_click_menu" href="javaScript:Void(0)"> Collections </a>
						
						<ul class="sub-sub-menu">
							 <?php

				       foreach ($collections as $collection) 
				       {
				       	?>
					
							<li><a href="<?php echo url('collections/'.$collection->slug); ?>"><?php echo $collection->name; ?></a> </li>
							
							<?php
						}
						?>
						</ul>
						 </li>	
						 <?php
					}	
		
				?>				 


						 <li class="desktopshow col_sec">

						<img src="https://jpnewstatic.ehostinguk.com/background-image/top-menu-image.jpg" alt="JohnPride"> 
					</li>

						
					</div>

					</ul>


				</li>

				   

				<?php

				  if(!empty($collections) && count($collections) > 0) {

				   ?>

				     <li class="mobilelink"><a class="link_click" href="javaScript:Void(0">Collections</a>

				      <ul class="sub-menu">

				       <?php

				       foreach ($collections as $collection) {

				        ?>


				        <li><a href="<?php echo url('collections/'.$collection->slug); ?>"> <span><?php echo $collection->name; ?></span></a></li>

				        <?php

				       }

				       ?>

				      </ul>

				     </li>

				    

				   

				   <?php

				  }

				  ?>



				<li><a href="{{url('track-order')}}">Track Your Order</a></li>



				<li class="mobilelink"><a href="{{url('about')}}">About Us</a></li>

				<!-- <li class="mobilelink"><a href="#">Careers</a></li> -->

				<li class="mobilelink"><a href="{{url('returns')}}">Free Shipping & Returns</a></li>

				<li class="mobilelink"><a href="{{url('terms')}}">Terms & Conditions</a></li>

				<li class="mobilelink"><a href="{{url('contact')}}">Contact Us</a></li> 




			</ul> 

		</div>

		<?php } ?> 

	<?php

	$authCheck = auth()->check();

	$cartCollection = Cart::getContent();

	$cartCount = $cartCollection->count();

	$userName = '';

	 ?>

	<div class="topright">

		<ul>

			<?php

			if($authCheck){

				$name = auth()->user()->name;

				$email = auth()->user()->email;

				if(!empty($name)){

					$userName = auth()->user()->name;

				}

				else{

					$userName = '<br>'.auth()->user()->email;

				}

				?>

				<!-- <li><a href="{{url('users')}}"> -->

					

				<li><a href="javascript:void(0);">

					<?php ?>

					<!-- <span><?php //if(!empty($name)){ echo 'Hi '.CustomHelper::wordsLimit($name, $limit =20, $isStripTags=false, $allowTags=''); } else { echo "Profile";} ?></span> --> <i class="profileicon"></i>

					</a>

					<div class="dropdownsec">

						<ul>

							<li><a href="{{url('users/profile')}}"><strong>Hi <?php echo $userName;?></strong></a></li>

							<li><a href="{{url('users/orders')}}">Orders History</a></li>

							<li><a href="{{url('users/profile')}}">Profile</a></li> 

							<li><a href="{{url('users/update')}}">Edit Profile</a></li>

							<li><a href="{{url('users/wishlist')}}">Wishlist</a></li> 

							<li><a href="{{url('users/wallet')}}">Wallet</a></li> 

							<li><a href="{{url('users/loyalty-points')}}">Loyalty Points</a></li> 
							<?php 
							 if(Auth::user()->isImpersonating())
							 {
							 	?>
							 	<li><a href="{{url('users/stop')}}">Log Out</a></li> 
							 	<?php
							 }
							 else
							 {
							 	?>
							 			<li><a href="{{url('logout')}}">Log Out</a></li>

							 	<?php
							 }

					         ?> 

						

						</ul>

					</div>

				</li> 

				<?php } else{

				$login_url = url('account/login');

				$strposLoginUrl = strpos($BackUrl,"account/login");

				//echo 'strpos='.$strposLoginUrl;

				if( strlen($strposLoginUrl) > 0 && $strposLoginUrl >= 0){

					$login_url = url('account/login?referer='.$referer); } ?>

			<!-- 	<li><a href="{{$login_url}}"><i class="profileicon"></i><span>Login</span></a></li> -->

			

				<li class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><i class="profileicon"></i><!-- <span>Login</span> --></a></li>

				<?php } if($authCheck){ ?>

				<!-- <li><a href="{{url('users/wishlist')}}"><i class="wishlisticon"></i> <span>Wishlist</span> </a></li> -->

				<?php } else{ ?>

				<!-- <li class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><i class="wishlisticon"></i><span>Wishlist</span></a></li> -->

				<?php } ?>

				<li><a href="{{url('cart')}}"><i class="carticon"></i>

					<?php if(!empty($cartCount)){?>

						<small><span id="cart_count">{{$cartCount}}</span></small>

					<?php }

					else{

						?>

						<small><span id="cart_count">0</span></small>

						<?php

					} ?>

				</a></li>

<li class="search_icon">

				<a href="javascript:void(0)" class="mainLoginBtn"><i class="searchicon"></i><!-- <span>Wishlist</span> --></a>

			</li>

		</ul>

	</div>

</div>



<?php $keyword = (request()->has('keyword'))?request()->keyword:''; ?>



<div class="search_box_warp" style="display: none;">

	<div class="cross_icon"></div>

	<div class="search_box">

		<!-- <input type="email" name="email" value="" class="form-control" placeholder="Search Here..."> -->



		<form name="searchForm" action="{{url('products')}}" class="search_form" onsubmit="return submit_search_form();">

			<input type="text" name="keyword" value="{{$keyword}}" class="form-control" placeholder="Search Here..." autocomplete="off" /> <!-- <button><i class="searchicon"></i></button>  -->

		</form>



		<div id="search_list" style="z-index: 99; position: absolute; top: 42px; /* bottom: 0; */ width: 100%;"></div>



		<form name="searchForm2" action="{{url('products')}}"></form>

	</div>

</div>



	<!-- <div class="searchform" style="position:relative;">

		<form name="searchForm" action="{{url('products')}}" class="search_form" onsubmit="return submit_search_form();">

			<input type="text" name="keyword" value="{{$keyword}}" placeholder="Search for products, brands and more" autocomplete="off" /><button><i class="searchicon"></i></button>

		</form>

		<div id="search_list" style="z-index: 99; position: absolute; top: 42px; /* bottom: 0; */ width: 100%;"></div>

	</div> -->

	<!-- <form name="searchForm2" action="{{url('products')}}"></form> -->

</header>



<div class="slide_login">

	<div class="login_head">

		<div class="btn_top loginBack"><img src="{{url('/')}}/images/left-arrow.png" alt=""></div> 

		<div class="title">Login Account</div>

		<div class="btn_top cross_icon"><img src="{{url('/')}}/images/cross.png" alt=""></div> 

	</div>



<!-- Login -->

<div class="login_body loginBox">

	<div class="logbox">

		<div class="font_md">Login to Johnpride</div>

		<!-- <div class="logdiv">

			<p>Use Your Social Media Account</p>

			<?php ?>

			<a href="{{ route('account.fbLogin', ['referer'=>$BackUrl]) }}" class="signfacebook" ><i class="facebooklogin"></i>  Facebook</a>

			<a href="{{ route('account.gLogin', ['referer'=>$BackUrl]) }}" class="signgoogle" ><i class="googlelogin"></i>  Google</a>

		</div> -->



		<div class="new_to_box">

			<div class="font_md">New To johnpride</div>

			<button type="button" class="reg_btn show_reg">Register</button>

		</div>



		<div class="or"><span>OR</span></div>



		<div class="loginform">

			<form name="loginForm" method="post">

				{{csrf_field()}}

				<div class="form-group">

					<input type="text" name="email" id="email_phone" value="{{old('email')}}" placeholder="Your Email ID" />

					@include('snippets.front.errors_first', ['param' => 'email'])

				</div>



				<div id="password_div" class="form-group">
					<div class="input_warap">
						<input type="password" name="password" placeholder="Enter Password" class="inpPass" />

						<a href="javascript:void(0)" class="passEye"></a>
					</div>

					@include('snippets.front.errors_first', ['param' => 'password'])
				</div>


				<div style="display: none;" id="otp_div" class="form-group">
					<div class="input_warap">
						<input type="text" name="otp" placeholder="Enter OTP" class="inpPass" />
						
					</div>

					@include('snippets.front.errors_first', ['param' => 'otp'])
				</div>


			<input type="hidden" id="is_request_otp" value="">	

				<div class="otp_link_div">
					<p><a href="javascript:;" class="request_otp" id="request_otp_id">Login with OTP</a></p>
				</div>

				<div style="display: none;" class="forgot_otp_link_div">
					<p><a href="javascript:;" class="forgot_request_otp" id="forgot_request_otp">Resend OTP</a></p>
				</div>


				<div class="keep_me">

					<input type="checkbox" name="remember" value="1"> Keep me signed in<br>

				</div>

				<span id="login_btn"><input type="submit" value="Login" class="submitbtn sbmtLogin" /></span>

			</form>

		</div>



		<div class="formbot">

			<a href="javascript:void(0)" class="forgot_btn_show">Recover password</a> <span>New to Johnpride ? <a class="show_reg" href="javascript:void(0)">Register</a></span>

		</div>

	</div>

</div>

<!-- End - Login -->



<!-- Register -->

<div class="login_body registerBox">

	<div class="">

		<div class="logbox">

			<div class="alertMsg"></div>

			<div class="font_md">Sign up to John Pride</div>

			<!-- <div class="logdiv">

				<a href="{{ route('account.fbLogin') }}" class="signfacebook" ><i class="facebooklogin"></i> Facebook</a>

				<a href="{{ route('account.gLogin') }}" class="signgoogle" ><i class="googlelogin"></i> Google</a>

			</div> -->



			<div class="or"><span>OR</span></div>

			<div class="loginform">

				<form name="registerForm" method="POST">

					{{csrf_field()}}


					<div class="form-group">

						<input type="name" name="name" value="{{old('name')}}" placeholder="Your Name" />

						@include('snippets.front.errors_first', ['param' => 'name'])

					</div>


					<div class="form-group">

						<input type="email" name="email" value="{{old('email')}}" placeholder="Your Email ID" />

						@include('snippets.front.errors_first', ['param' => 'email'])

					</div>



					<div class="form-group">

						<div class="input_warap">

							<input type="password" name="password" placeholder="Choose Password" class="inpPass" />

							<a href="javascript:void(0)" class="passEye"></a>

						</div>

						@include('snippets.front.errors_first', ['param' => 'password'])

					</div>



					<div class="form-group">

						<input type="text" name="phone" value="{{old('phone')}}" placeholder="Mobile Number" />

						@include('snippets.front.errors_first', ['param' => 'mobile'])

					</div>



					<?php $oldGender = old('gender'); ?>

					<div style="display: none;" class="form-group text-left">

						<div class="label_area">

							<input type="radio"  name="gender" value="M" <?php echo ($oldGender == 'M')?'checked':'';?> /> Male

							<input type="radio"  name="gender" value="F" <?php echo ($oldGender == 'F')?'checked':'';?> /> Female

							<span class="error"></span>

				</div>

					</div>



					<div class="form-group otpBox" style="display:none;">

						<input type="text" name="otp" value="" placeholder="OTP" />

						@include('snippets.front.errors_first', ['param' => 'otp'])

					</div> 

					<?php if(CustomHelper::isSmsGateway()){ ?>



						<input type="button" name="send_otp" value="Send OTP" class="submitbtn sendOtp" />

						<div class="confirmBox" style="display:none;">

							<!-- <input type="button" name="resend_otp" value="Resend OTP" class="submitbtn sendOtp" /> -->

							<a href="javascript:;" class="submitbtn sendOtp">Resend OTP</a>

							<input type="submit" name="register" value="Register" class="submitbtn sbmtRegister" />

						</div>

						<?php } else{ ?>

						<input type="submit" name="register" value="Register" class="submitbtn sbmtRegister" />

						<?php } ?>

				</form>

			</div>



			<div class="formbot">

				<p>Already have an account? <a href="javascript:void(0)" class="loginBtn">Login!</a> </p>

			</div>

		</div>

	</div>

</div>

<!-- End - Register -->



<!-- Forgot password -->



<div class="login_body">

<div class="logbox forgotBox">

	  <div class="font_md">Forgot password!</div>

		<div class="loginform">

			<div class="alertMsg"></div>

            <form name="forgotForm" method="post">

            	{{csrf_field()}}

            	<div class="form-group">

            		<input type="email" name="email" placeholder="Your Email ID" />

            	</div>

            	<input type="submit" value="Submit" class="submitbtn sbmtForgot" />

            </form>

		</div>

		<div class="formbot">

			 <p><a href="javascript:void(0)" class="loginBtn">Login here!</a> </p>

		</div>

	  </div>

	</div>

	<!-- End - Forgot password -->

</div>