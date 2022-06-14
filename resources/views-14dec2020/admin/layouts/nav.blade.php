<style type="text/css">
	.sub_active a{ color:#e41881 !important; }
</style>
<div class="leftsec noPrint">
	<div class="menuicon"><span></span> <small>Menu</small></div>
	<div class="fullwidth leftsec1">

		<ul class="main-nav clearfix adminleft">

			<?php
			//echo Route::currentRouteName();

			$type = (isset(request()->type))?request()->type:'';
			?>

			<li {!! strpos(Route::currentRouteName(), 'admin.home') === 0 ? ' class="active"' : '' !!}>
				<a href="{{url('admin')}}"><i class="dashboard-icon"></i> <span>Dashboard</span></a>
			</li>

			<?php
			$master_active = (
				strpos(Route::currentRouteName(), 'admin.colors') === 0 ||
				strpos(Route::currentRouteName(), 'admin.size_charts') === 0 ||
				strpos(Route::currentRouteName(), 'admin.sizes') === 0 ||
				strpos(Route::currentRouteName(), 'admin.brands') === 0 ||
				strpos(Route::currentRouteName(), 'admin.pincodes') === 0 ||
				strpos(Route::currentRouteName(), 'admin.loyaltypoints') === 0
			)? 'class="active"':'';

			?>

			<li <?php echo $master_active; ?> >

				<a class="dropul subtab"> <span> Manage Masters</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.colors') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.colors.index') }}" > <span>Colors</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.size_charts') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.size_charts.index') }}" > <span>Size Charts</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.sizes') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.sizes.index') }}" > <span>Sizes</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.brands') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.brands.index') }}" > <span>Collections</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.pincodes') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.pincodes.index') }}" > <span>Pincodes</span></a>
					</li>
					
					<li {!! strpos(Route::currentRouteName(), 'admin.loyaltypoints') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.loyaltypoints.index') }}" > <span>Loyalty Points</span></a>
					</li>					

				</ul>

			</li>

			<li {!! strpos(Route::currentRouteName(), 'admin.banners') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Banners</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.banners.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.banners.index') }}" > <span>Banners List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.banners.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.banners.add') }}" > <span>Add Banner</span></a>
					</li>

				</ul>
			</li>

			<li {!! strpos(Route::currentRouteName(), 'admin.customer-picture') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Customer Picture</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.customer-picture.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.customer-picture.index') }}" > <span>Customer Picture List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.customer-picture.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.customer-picture.add') }}" > <span>Add Customer Picture</span></a>
					</li>

				</ul>
			</li>

			<li {!! strpos(Route::currentRouteName(), 'admin.look-book') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Look Book</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.look-book.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.look-book.index') }}" > <span>Look Book List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.look-book.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.look-book.add') }}" > <span>Add Look Book</span></a>
					</li>

				</ul>
			</li>

			<li {!! strpos(Route::currentRouteName(), 'admin.home_images') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Home Images</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.home_images.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.home_images.index') }}" > <span>Home Image List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.home_images.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.home_images.add') }}" > <span>Add Home Image</span></a>
					</li>

				</ul>
			</li>



			<?php
			$category_active = (
				strpos(Route::currentRouteName(), 'admin.categories') === 0 ||
				strpos(Route::currentRouteName(), 'admin.categories.add') === 0 ||
				strpos(Route::currentRouteName(), 'admin.categories.edit') === 0
			)? 'class="active"':'';
			?>

			<li <?php echo $category_active; ?> >

				<a class="dropul subtab"> <span> Manage Categories</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! !empty($category_active) ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.categories.index') }}" > <span>Categories List</span></a>
					</li>

				</ul>

			</li>



			<?php
			//pr(Route::currentRouteName());
			//pr(strpos(Route::currentRouteName(), 'admin.products.add'));
			$product_active = (
				strpos(Route::currentRouteName(), 'admin.products') === 0 ||
				strpos(Route::currentRouteName(), 'admin.products.add') === 0 ||
				strpos(Route::currentRouteName(), 'admin.products.edit') === 0 ||
				strpos(Route::currentRouteName(), 'admin.products.inventory_list') === 0 ||
				strpos(Route::currentRouteName(), 'admin.products.inventory_upload') === 0
			)? 'class="active"':'';
			?>

			<li <?php echo $product_active; ?> >

				<a class="dropul subtab"> <span> Manage Products</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.products.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.products.index') }}" > <span>Products List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.products.upload') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.products.upload') }}" > <span>Products Upload</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.products.inventory_list') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.products.inventory_list') }}" > <span>Inventory List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.products.inventory_upload') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.products.inventory_upload') }}" > <span>Inventory Upload</span></a>
					</li>

				</ul>

			</li>

			


			

			

			<?php
			/*
			<li {!! strpos(Route::currentRouteName(), 'admin.colors') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Colors</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.colors.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.colors.index') }}" > <span>Colors List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.colors.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.colors.add') }}" > <span>Add Color</span></a>
					</li>

				</ul>
			</li>
<li {!! strpos(Route::currentRouteName(), 'admin.size_charts') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Size Chart</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.size_charts.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.size_charts.index') }}" > <span>Size Chart List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.size_charts.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.size_charts.add') }}" > <span>Add Size Chart</span></a>
					</li>

				</ul>
			</li>

			<li {!! strpos(Route::currentRouteName(), 'admin.sizes') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.sizes.index') }}" > <span> Size</span></a>
			</li>

			<li {!! strpos(Route::currentRouteName(), 'admin.brands') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Brands </span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.brands.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.brands.index') }}" > <span> Brand List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.brands.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.brands.add') }}" > <span>Add Brand</span></a>
					</li>

				</ul>
			</li>
			*/
			?>


			

			

			<?php
			$customer_active = (
				strpos(Route::currentRouteName(), 'admin.customers') === 0 ||
				strpos(Route::currentRouteName(), 'admin.categories.index') === 0 ||
				strpos(Route::currentRouteName(), 'admin.categories.add') === 0 ||
				strpos(Route::currentRouteName(), 'admin.reviews') === 0 ||
				strpos(Route::currentRouteName(), 'admin.cart') === 0
			)? 'class="active"':'';
			?>

			<li <?php echo $customer_active; ?> >
				<a class="dropul subtab"> <span> Customers</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.customers.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.customers.index') }}" > <span>Customers List</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.customers.add') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.customers.add') }}" > <span>Add Customer</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.reviews') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.reviews.index') }}" > <span>Customer Reviews</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.cart') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.cart.index') }}" > <span>Customers Cart</span></a>
					</li>

				</ul>
			</li>

			

			<li {!! strpos(Route::currentRouteName(), 'admin.orders') === 0 ? ' class="active"' : '' !!}>
				<a class="dropul subtab"> <span> Orders</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.orders.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.orders.index') }}" > <span>Orders List</span></a>
					</li>					

				</ul>
			</li>

			

			<?php
			$shipping_active = (strpos(Route::currentRouteName(), 'admin.shippingzones') === 0 || strpos(Route::currentRouteName(), 'admin.shippingrates') === 0 )? 'class="active"':'';
			?>

			<li <?php echo $shipping_active;?> >
				<a  class="dropul subtab"> <span> Shipping</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.shippingzones.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.shippingzones.index') }}" > <span>Shipping Zone</span></a>
					</li>


					<li {!! strpos(Route::currentRouteName(), 'admin.shippingrates.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.shippingrates.index') }}" > <span>Shipping Rate</span></a>
					</li>

				</ul>
			</li>

			<?php
			$blog_active = (strpos(Route::currentRouteName(), 'admin.blogs_categories') === 0 || strpos(Route::currentRouteName(), 'admin.blogs') === 0 )? 'class="active"':'';
			?>

		
			<li <?php echo $blog_active; ?> >
				<a  class="dropul subtab"> <span> Blogs</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.blogs_categories.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.blogs_categories.index') }}" > <span>Blog Category List</span></a>
					</li>



					<li {!! strpos(Route::currentRouteName(), 'admin.blogs.index') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.blogs.index') }}" > <span>Blog List</span></a>
					</li>


				</ul>
			</li>
		

             <!-- Country,state, city-->

             <?php
			$country_active = (strpos(Route::currentRouteName(), 'admin.countries') === 0 || strpos(Route::currentRouteName(), 'admin.states') === 0  ||  strpos(Route::currentRouteName(), 'admin.cities') === 0 )? 'class="active"':'';
			?>

			<li <?php echo $country_active; ?> >
				<a  class="dropul subtab"> <span>Country, State, City</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
				<ul>

					<li {!! strpos(Route::currentRouteName(), 'admin.countries') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.countries.index') }}" > <span>Manage Countries</span></a>
					</li>


					<li {!! strpos(Route::currentRouteName(), 'admin.states') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.states.index') }}" > <span>Manage States</span></a>
					</li>

					<li {!! strpos(Route::currentRouteName(), 'admin.cities') === 0 ? ' class="sub_active"' : '' !!}>
						<a href="{{ route('admin.cities.index') }}" > <span>Manage Cities</span></a>
					</li>

				</ul>
			</li>



			<!-- For Coupon-->
			<li {!! strpos(Route::currentRouteName(), 'admin.coupons') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.coupons.index') }}" > <span> Coupons</span></a>
			</li>

			<!-- For Newletter Subscriber-->

			<li {!! strpos(Route::currentRouteName(), 'admin.newsletter') === 0 ? ' class="active"' : '' !!}>
				<a href="{{url('admin/newsletter')}}" > <span> Newsletter Subscriber</span></a>
			</li>




			<li {!! strpos(Route::currentRouteName(), 'admin.cms') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.cms.index') }}" > <span> CMS Pages</span></a>
			</li>

			<li>
				<a href="javascript:void(0)" class="updateInventory" > <span> Update Inventory (Unicommerce)</span></a>
			</li>

			<li>
				<a href="javascript:void(0)" class="updateOrderStatus" > <span> Update Order Status (Unicommerce)</span></a>
			</li>

		</ul>
</div>

</div>