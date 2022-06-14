<div class="fullwidth heighthead"></div>

<div class="nav_out"></div>
<div class="logo show_header" style="display: none">
	<img src="{{url('/')}}/images/logo.png" alt="Slumber Jill" border="0" />
</div>
<header class="header fullwidth noPrint"> 
<div class="fullwidth topstrip"><span class="showpopup">Explore</span> New November Styles</div>

	<div class="logo1">
		<a href="{{url('/')}}">
			<img src="{{url('/')}}/images/logo01.png" alt="JohnPride" border="0" class="logocolor" />
			<img src="{{url('/')}}/images/logow1.png" alt="JohnPride" border="0" class="logowhite" />
		</a>
	</div>

	<div class="logo">
		<a href="{{url('/')}}">
			<img src="{{url('/')}}/images/logo.png" alt="JohnPride" border="0" class="logocolor" />
			<img src="{{url('/')}}/images/logow.png" alt="JohnPride" border="0" class="logowhite" />
		</a>
	</div>

	<?php
	$BackUrl = CustomHelper::BackUrl();
	$referer = (request()->has('referer'))?request()->referer:'';
	$parentCategories = CustomHelper::getCategories();
	if(!empty($parentCategories) && count($parentCategories) > 0) { ?>
		<div class="navicon"><span></span></div>
		<div class="topmenu ">
			<ul>
				<?php
				foreach ($parentCategories as $category) {
					$childCategories = (!empty($category->children))?$category->children:''; ?>
					<li><a href="<?php echo url('products/?pcat='.$category->slug); ?>"> {{$category->name}} </a>
						<ul>
							<?php if(!empty($childCategories) && count($childCategories) > 0){
							//pr($childCategories->toArray());
							foreach($childCategories as $childCat){
							$childChildCategories = (!empty($childCat->children))?$childCat->children:''; ?>
							<li>
							<div class="menutitle"><a href="{{route('products.list', ['pcat'=>$category->slug, 'p2cat'=>$childCat->slug])}}">{{$childCat->name}}</a></div>
							<?php
							if(!empty($childChildCategories) && count($childChildCategories) > 0){ ?>
							<ul>
							<?php foreach ($childChildCategories as $childChildCat) { ?>
							<li><a href="{{route('products.list', ['pcat'=>$category->slug, 'cat[]'=>$childChildCat->slug])}}">{{$childChildCat->name}}</a></li>
							<?php } ?>
							</ul>
							<?php } ?>
							</li>
							<?php } } ?>
						</ul>
					</li>
					<?php } ?>
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
					<i class="profileicon"></i>
					<span><?php if(!empty($name)){ echo 'Hi '.CustomHelper::wordsLimit($name, $limit =20, $isStripTags=false, $allowTags=''); } else { echo "Profile";} ?></span></a>
					<div class="dropdownsec">
						<ul>
							<li><a href="{{url('users/profile')}}"><strong>Hi <?php echo $userName;?></strong></a></li>
							<li><a href="{{url('users/orders')}}">Orders History</a></li>
							<li><a href="{{url('users/profile')}}">Profile</a></li> 
							<li><a href="{{url('users/update')}}">Edit Profile</a></li>
							<li><a href="{{url('users/wishlist')}}">Wishlist</a></li> 
							<li><a href="{{url('users/wallet')}}">Wallet</a></li> 
							<li><a href="{{url('users/loyalty-points')}}">Loyalty Points</a></li> 
							<li><a href="{{url('logout')}}">Log Out</a></li>
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
			<li class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><i class="searchicon"></i><!-- <span>Wishlist</span> --></a></li>
				<li class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><i class="profileicon"></i><!-- <span>Login</span> --></a></li>
				<?php } if($authCheck){ ?>
				<li><a href="{{url('users/wishlist')}}"><i class="wishlisticon"></i><span>Wishlist</span></a></li>
				<?php } else{ ?>
				<!-- <li class="open_slide"><a href="javascript:void(0)" class="mainLoginBtn"><i class="wishlisticon"></i><span>Wishlist</span></a></li> -->
				<?php } ?>
				<li><a href="{{url('cart')}}"><i class="carticon"></i><small><span id="cart_count">{{$cartCount}}</span></small><!-- <span>Bag</span> --></a></li>

		</ul>
	</div>


	<?php $keyword = (request()->has('keyword'))?request()->keyword:''; ?>
	<!-- <div class="searchform" style="position:relative;">
		<form name="searchForm" action="{{url('products')}}" class="search_form" onsubmit="return submit_search_form();">
			<input type="text" name="keyword" value="{{$keyword}}" placeholder="Search for products, brands and more" autocomplete="off" /><button><i class="searchicon"></i></button>
		</form>
		<div id="search_list" style="z-index: 99; position: absolute; top: 42px; /* bottom: 0; */ width: 100%;"></div>
	</div> -->
	<form name="searchForm2" action="{{url('products')}}"></form>
</header>

<div class="slide_login">
	<div class="login_head">
		<div class="btn_top loginBack"><img src="{{url('/')}}/images/left-arrow.png"></div> 
		<div class="title">Login Account</div>
		<div class="btn_top cross_icon"><img src="{{url('/')}}/images/cross.png"></div> 
	</div>

<!-- Login -->
<div class="login_body loginBox">
	<div class="logbox">
		<div class="font_md">Login to Johnpride</div>
		<div class="logdiv">
			<p>Use Your Social Media Account</p>
			<?php ?>
			<a href="{{ route('account.fbLogin', ['referer'=>$BackUrl]) }}" class="signfacebook" ><i class="facebooklogin"></i>  Facebook</a>
			<a href="{{ route('account.gLogin', ['referer'=>$BackUrl]) }}" class="signgoogle" ><i class="googlelogin"></i>  Google</a>
		</div>

		<div class="new_to_box">
			<div class="font_md">New To johnpride</div>
			<button type="button" class="reg_btn show_reg">Register</button>
		</div>

		<div class="or"><span>OR</span></div>

		<div class="loginform">
			<form name="loginForm" method="post">
				{{csrf_field()}}
				<div class="form-group">
					<input type="email" name="email" value="{{old('email')}}" placeholder="Your Email ID" />
					@include('snippets.front.errors_first', ['param' => 'email'])
				</div>

				<div class="form-group">
					<div class="input_warap">
						<input type="password" name="password" placeholder="Enter Password" class="inpPass" />
						<a href="javascript:void(0)" class="passEye"></a>
					</div>
					@include('snippets.front.errors_first', ['param' => 'password'])

				</div>

				<div class="keep_me">
					<input type="checkbox" name="remember" value="1"> Keep me signed in<br>
				</div>
				<input type="submit" value="Login" class="submitbtn sbmtLogin" />
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
			<div class="font_md">Sign up to SlumberJill</div>
			<div class="logdiv">
				<a href="{{ route('account.fbLogin') }}" class="signfacebook" ><i class="facebooklogin"></i> Facebook</a>
				<a href="{{ route('account.gLogin') }}" class="signgoogle" ><i class="googlelogin"></i> Google</a>
			</div>

			<div class="or"><span>OR</span></div>
			<div class="loginform">
				<form name="registerForm" method="POST">
					{{csrf_field()}}
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
					<div class="form-group text-left">
						<label>
							<input type="radio" name="gender" value="M" <?php echo ($oldGender == 'M')?'checked':'';?> /> Male
							<input type="radio" name="gender" value="F" <?php echo ($oldGender == 'F')?'checked':'';?> /> Female
							<span class="error"></span>
						</label>
					</div>

					<div class="form-group otpBox" style="display:none;">
						<input type="text" name="otp" value="" placeholder="OTP" />
						@include('snippets.front.errors_first', ['param' => 'otp'])
					</div> 
					<?php if(CustomHelper::isSmsGateway()){ ?>

						<input type="button" name="send_otp" value="Send OTP" class="submitbtn sendOtp" />
						<div class="confirmBox" style="display:none;">
							<input type="button" name="resend_otp" value="Resend OTP" class="submitbtn sendOtp" />
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