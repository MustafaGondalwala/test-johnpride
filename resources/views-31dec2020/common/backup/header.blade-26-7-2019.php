<header class="header fullwidth"> 
	<div class="logo">
		<a href="{{url('/')}}"><img src="{{url('public')}}/images/logo.png" alt="Slumber Jill" border="0" /></a>
	</div>

	<?php
	$parentCategories = CustomHelper::getCategories();

	if(!empty($parentCategories) && count($parentCategories) > 0) {
		?>
		<div class="navicon"><span></span></div>
		<div class="topmenu">
			<ul>
				<?php
				foreach ($parentCategories as $category) {
					$childCategories = (!empty($category->children))?$category->children:'';

					?>
					<li><a href="<?php echo url('products/?pcat='.$category->slug); ?>"> {{$category->name}} </a>
						<ul >
							<?php
							if(!empty($childCategories) && count($childCategories) > 0){
    		//pr($childCategories->toArray());

								foreach($childCategories as $childCat){

									$childChildCategories = (!empty($childCat->children))?$childCat->children:'';
									?>
									<li>
										<div class="menutitle"><a href="{{route('products.list', ['pcat'=>$category->slug, 'p2cat'=>$childCat->slug])}}">{{$childCat->name}}</a></div>

										<?php
										if(!empty($childChildCategories) && count($childChildCategories) > 0){
											?>
											<ul>
												<?php
												foreach ($childChildCategories as $childChildCat) {
													?>
													<li><a href="{{route('products.list', ['pcat'=>$category->slug, 'cat[]'=>$childChildCat->slug])}}">{{$childChildCat->name}}</a></li>
													<?php
												}
												?>
											</ul>
											<?php
										}
										?>
									</li>
									<?php
								}
							}
							?>
						</ul>
					</li>
					<?php
				}
				?>
			</ul> 
		</div>
		<?php
	}
	?>

	<?php
	$authCheck = auth()->check();

	$cartCollection = Cart::getContent();
	$cartCount = $cartCollection->count();
	 ?>

	<div class="topright">
		<ul>
			<?php
			if($authCheck){
				?>
				<li><a href="{{url('users')}}"><i class="profileicon"></i><span>Profile</span></a>
					<div class="dropdownsec">
						<ul>
							<li><a href="{{url('users/profile')}}"><strong>Hello</strong><br>{{ auth()->user()->email }} </a></li>
							<li><a href="{{url('users/orders')}}">Orders History</a></li>
							<li><a href="{{url('users/profile')}}">Profile</a></li> 
							<li><a href="{{url('users/update')}}">Edit Profile</a></li>
							<li><a href="{{url('users/wishlist')}}">Wishlist</a></li> 
							<li><a href="{{url('users/wallet')}}">Wallet</a></li> 
							<li><a href="{{url('logout')}}">Log Out</a></li>
						</ul>	
					</div>
				</li> 
				<?php
			}
			else{

				$BackUrl = CustomHelper::BackUrl();
				$referer = (request()->has('referer'))?request()->referer:'';

				$login_url = url('account/login');

				$strposLoginUrl = strpos($BackUrl,"account/login");

				//echo 'strpos='.$strposLoginUrl;

				if( strlen($strposLoginUrl) > 0 && $strposLoginUrl >= 0){
					$login_url = url('account/login?referer='.$referer);
				}

				?>
				<li><a href="{{$login_url}}"><i class="profileicon"></i><span>Login</span></a></li>
				<?php
			}
			if($authCheck){
				?>
				<li><a href="{{url('users/wishlist')}}"><i class="wishlisticon"></i><span>Wishlist</span></a></li>
				<?php
			}
			else{
				?>
				<li><a href="{{url('account/login?referer=users/wishlist')}}"><i class="wishlisticon"></i><span>Wishlist</span></a></li>
				<?php
			}
			?>
			 
			<li><a href="{{url('cart')}}"><i class="carticon"></i><small><span id="cart_count">{{$cartCount}}</span></small><span>Bag</span></a></li>
		</ul>
	</div>

	<?php
	$keyword = (request()->has('keyword'))?request()->keyword:'';
	?>

	<div class="searchform" style="position:relative;">
		<form name="searchForm" action="{{url('products')}}" class="search_form" onsubmit="return submit_search_form();">
			<input type="text" name="keyword" value="{{$keyword}}" placeholder="Search for products, brands and more" autocomplete="off" /><button><i class="searchicon"></i></button>
		</form>

		<div id="search_list" style="z-index: 99; position: absolute; top: 42px; /* bottom: 0; */ width: 100%;"></div>
	</div>


	<form name="searchForm2" action="{{url('products')}}"></form>

</header>