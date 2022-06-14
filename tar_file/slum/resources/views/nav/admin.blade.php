<div class="leftsec">
	<div class="menuicon"><span></span> <small>Menu</small></div>
	<div class="fullwidth leftsec1">
	<ul class="main-nav clearfix adminleft">

		<li{!! strpos(Route::currentRouteName(), 'admin.home') === 0 ? ' class="active"' : '' !!}>
		<a href="{{url('admin')}}"><i class="dashboard-icon"></i> <span>Dashboard</span></a>
		</li>

		@permission('reports')
		<li>
			<a href="javascript:void(0)" class="dropul subtab"><i class="orders-icon"></i> <span>Reports</span></a>
			<i aria-hidden="true" class="fa fa-angle-down dropul"></i>

			<ul>
				<?php
				/*
				<li><a href="{{url('admin/reports/download')}}">Download</a></li>
				*/
				?>

				<li>
					<a href="javascript:void(0)" class="child_dropul">Products&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
					<ul style="display:none;">
						<li><a href="{{url('admin/products')}}"><i class="fa fa-angle-right"></i> Total Products</a></li>
						<li><a href="{{url('admin/products?sort_by=highest_viewed')}}"><i class="fa fa-angle-right"></i> Highest viewed Products</a></li>
						<li><a href="{{url('admin/products?sort_by=most_ordered')}}"><i class="fa fa-angle-right"></i> Most ordered Products</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0)" class="child_dropul">Orders&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
					<ul style="display:none;">
						<li><a href="{{url('admin/orders')}}"><i class="fa fa-angle-right"></i> Total Orders</a></li>
						<li><a href="{{url('admin/orders?order_status=approval_pending')}}"><i class="fa fa-angle-right"></i> Pending orders</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0)" class="child_dropul">Customer&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
					<ul style="display:none;">
						<li><a href="{{url('admin/customers')}}"><i class="fa fa-angle-right"></i> Total Customers</a></li>
					</ul>
				</li>

			</ul>
		</li>

		@endpermission

		<?php
		/*
		@permission('orders')
		<li{!! strpos(Route::currentRouteName(), 'admin.orders') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.orders.index') }}"><i class="orders-icon"></i> <span>Orders</span></a>
		</li>
		@endpermission
		*/
		?>

		@permission('orders')
		<li {!! (strpos(Route::currentRouteName(), 'admin.orders') === 0 || strpos(Route::currentRouteName(), 'admin.feedback') === 0) ? ' class="active"' : '' !!}>
		<?php
		$order_status = request()->order_status;
		/*
		{{ url('admin/orders?order_status=approval_pending') }}
		*/
		?>
			<a href="javascript:void(0)" class="dropul subtab"><i class="orders-icon"></i> <span>Orders</span> </a>
			<i aria-hidden="true" class="fa fa-angle-down dropul"></i>
			<ul >
				<li><a href="{{ url('admin/orders') }}" <?php echo (strpos(Route::currentRouteName(), 'admin.orders') === 0 && empty($order_status))? 'style="color:#00c4aa"' : '' ?> >All</a></li>

				<?php
				//$order_status_list = config('custom.order_status');
				$order_status_list = CustomHelper::OrderStatus();
				if(count($order_status_list) > 0){
					foreach($order_status_list as $osl){
						?>
						<li {{ ($osl->name == $order_status)?'class="active"':'' }} ><a href="{{ url('admin/orders?order_status='.$osl->name) }}" <?php echo ($osl->name == $order_status)? 'style="color:#00c4aa"' : '' ?> >{{ $osl->title }}</a></li>
						<?php
					}
				}
				//echo Route::currentRouteName();
				?>

				<li{!! strpos(Route::currentRouteName(), 'admin.feedback.index') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.feedback.index') }}" {!! strpos(Route::currentRouteName(), 'admin.feedback.index') === 0 ? ' style="color:#00c4aa"' : '' !!}><i class="dashboard-icon"></i> <span>Feedback</span></a>
			</li>
				
			</ul>
		</li>
		@endpermission

		@permission('users')
		<li{!! strpos(Route::currentRouteName(), 'admin.users') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.users.index') }}"><i class="users-icon"></i> <span>Admin Users</span></a>
		</li>
		@endpermission

		
		<li {!! ( strpos (Route::currentRouteName(), 'admin.customers') === 0 || strpos(Route::currentRouteName(), 'admin.customers') === 0) ? ' class="active"' : '' !!}>
			{{-- Route::currentRouteName() --}}
			<a  class="dropul subtab"><i class="users-icon"></i> <span>Customers</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
			<ul>
				@permission('customers')

				<?php
				$ic = (request()->has('ic'))?request()->ic:"";
				?>

				<li {!! strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' class="active"' : '' !!}>
					<a href="{{ url('admin/customers') }}" {!! (strpos(Route::currentRouteName(), 'admin.customers') === 0 && $ic == "") ? ' style="color: #00c4aa;"' : '' !!}><i class="users-icon"></i> <span>All Customers</span></a>
				</li>

				<li {!! strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' class="active"' : '' !!}>
					<a href="{{ url('admin/customers?ic=1') }}" {!! (strpos(Route::currentRouteName(), 'admin.customers') === 0 && $ic == 1) ? ' style="color: #00c4aa;"' : '' !!}><i class="users-icon"></i> <span>Complete</span></a>
				</li>

				<li {!! strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' class="active"' : '' !!}>
					<a href="{{ url('admin/customers?ic=0') }}" <?php echo (strpos(Route::currentRouteName(), 'admin.customers') === 0 && strlen($ic) > 0 && $ic == 0) ? ' style="color: #00c4aa;"' : '' ?>><i class="users-icon"></i> <span>Incomplete</span></a>
				</li>

				<li {!! strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' class="active"' : '' !!}>
					<a href="{{ url('admin/customers/push_notification') }}" {!! (strpos(Route::currentRouteName(), 'admin.customers.push_notification') === 0 && $ic == 1) ? ' style="color: #00c4aa;"' : '' !!}><i class="users-icon"></i> <span>Push Notification</span></a>
				</li>

				@endpermission
			</ul>
		</li>
		

		<?php
		/*
		@permission('users')
		<li{!! strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ url('admin/customers') }}"><i class="users-icon"></i> <span>Customers</span></a>
		</li>
		@endpermission
		*/
		$user_code = (isset(auth()->user()->role()->code))?auth()->user()->role()->code:'';
		?>

		@if($user_code != 'admin')
			@permission('chat')
			<li{!! strpos(Route::currentRouteName(), 'admin.chat') === 0 ? ' class="active"' : '' !!}>
			<a href="{{ url('admin/chat') }}"><i class="chat-icon"></i> <span>Chat</span></a>
			</li>
			@endpermission
		@endif

		@permission('products')
		<li{!! strpos(Route::currentRouteName(), 'admin.products') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.products.index') }}"><i class="products-icon"></i> <span>Products</span></a>
		</li>
		@endpermission

		@permission('categories')
		<li{!! strpos(Route::currentRouteName(), 'admin.categories') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.categories.index') }}"><i class="categories-icon"></i> <span>Categories</span></a>
		</li>
		@endpermission

		<li{!! strpos(Route::currentRouteName(), 'admin.payments') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.payments.index') }}"><i class="payment-icon"></i> <span>Payments</span></a>
		</li>

		<?php
		$setting_active = '';
		if(	strpos(Route::currentRouteName(), 'admin.permissions') === 0 ||
			strpos(Route::currentRouteName(), 'admin.paymentoptions') === 0 ||
			strpos(Route::currentRouteName(), 'admin.garmenttypes') === 0 ||
			strpos(Route::currentRouteName(), 'admin.sizecharts') === 0 ||
			strpos(Route::currentRouteName(), 'admin.cms') === 0 ||
			strpos(Route::currentRouteName(), 'admin.brands') === 0 ||
			strpos(Route::currentRouteName(), 'admin.deliveryterms') === 0 ||
			strpos(Route::currentRouteName(), 'admin.attributes') === 0 ||
			strpos(Route::currentRouteName(), 'admin.cities') === 0 ||
			strpos(Route::currentRouteName(), 'admin.shippingzones') === 0 ||
			strpos(Route::currentRouteName(), 'admin.shippingrates') === 0 ||
			strpos(Route::currentRouteName(), 'admin.templates') === 0 ||
			strpos(Route::currentRouteName(), 'admin.settings') === 0 ||
			strpos(Route::currentRouteName(), 'admin.usersactivity') === 0){

			$setting_active = ' class="active"';
		}
		?>

		@permission('admin.settings')

		<li <?php echo $setting_active;?> >
			<a  class="dropul subtab"><i class="settings-icon"></i> <span>Settings</span></a><i aria-hidden="true" class="fa fa-angle-down dropul"></i>
			<ul>

				@permission('permissions.manage')
				<li>
				<a href="{{ route('admin.permissions.index') }}" {!! strpos(Route::currentRouteName(), 'admin.permissions') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="permissions-icon"></i> <span>Permissions</span></a>
				</li>
				@endpermission

				@permission('paymentoptions')
				<li>
				<a href="{{ route('admin.paymentoptions.index') }}" {!! strpos(Route::currentRouteName(), 'admin.paymentoptions') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="permissions-icon"></i> <span>Payment Options</span></a>
				</li>
				@endpermission

				@permission('garmenttypes')
				<li>
				<a href="{{ route('admin.garmenttypes.index') }}" {!! strpos(Route::currentRouteName(), 'admin.garmenttypes') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="permissions-icon"></i> <span>Garment Types</span></a>
				</li>
				@endpermission

				@permission('sizecharts')
				<li>
				<a href="{{ route('admin.sizecharts.index') }}" {!! strpos(Route::currentRouteName(), 'admin.sizecharts') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="permissions-icon"></i> <span>Size Charts</span></a>
				</li>
				@endpermission

				@permission('cms')
				<li>
					<a href="{{ route('admin.cms.index') }}" {!! strpos(Route::currentRouteName(), 'admin.cms') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="permissions-icon"></i> <span>CMS Pages</span></a>
				</li>
				@endpermission

				@permission('brands')
				<li{!! strpos(Route::currentRouteName(), 'admin.brands') === 0 ? ' class="active"' : '' !!}>
					<a href="{{ route('admin.brands.index') }}" {!! strpos(Route::currentRouteName(), 'admin.brands') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="brands-icon"></i> <span>Brands</span></a>
				</li>
				@endpermission

				@permission('deliveryterms')
				<li{!! strpos(Route::currentRouteName(), 'admin.deliveryterms') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ url('admin/deliveryterms') }}" {!! strpos(Route::currentRouteName(), 'admin.deliveryterms') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="delivery-icon"></i> <span>Delivery Terms</span></a>
				</li>
				@endpermission

				@permission('attributes')
				<li{!! strpos(Route::currentRouteName(), 'admin.attributes') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.attributes.index') }}" {!! strpos(Route::currentRouteName(), 'admin.attributes') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="attributes-icon"></i> <span>Attributes</span></a>
				</li>
				@endpermission

				@permission('cities')
				<li{!! strpos(Route::currentRouteName(), 'admin.cities') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.cities.index') }}" {!! strpos(Route::currentRouteName(), 'admin.cities') === 0 ? ' style="color: #00c4aa;"' : '' !!}><i class="attributes-icon"></i> <span>Cities</span></a>
				</li>
				@endpermission

				@permission('shippingzones')
				<li{!! strpos(Route::currentRouteName(), 'admin.shippingzones') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ url('admin/shippingzones') }}" {!! strpos(Route::currentRouteName(), 'admin.shippingzones') === 0 ? ' style="color: #00c4aa;"' : '' !!}><span>Shipping Zones</span></a>
				</li>
				@endpermission

				@permission('shippingrates')
				<li{!! strpos(Route::currentRouteName(), 'admin.shippingrates') === 0 ? ' class="active"' : '' !!}>		
				<a href="{{ route('admin.shippingrates.index') }}" {!! strpos(Route::currentRouteName(), 'admin.shippingrates') === 0 ? ' style="color: #00c4aa;"' : '' !!}><span>Shipping Rates</span></a>
				</li>
				@endpermission

				
                <!--Email Templates-->
				@permission('templates')
				<li{!! strpos(Route::currentRouteName(), 'admin.templates') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ url('admin/templates') }}" {!! strpos(Route::currentRouteName(), 'admin.templates') === 0 ? ' style="color: #00c4aa;"' : '' !!} ><i class="settings-icon"></i> <span>Templates</span></a>
				</li>
				@endpermission
				<!--End-->

				@permission('settings')
				<li{!! strpos(Route::currentRouteName(), 'admin.settings') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.settings.index') }}" {!! strpos(Route::currentRouteName(), 'admin.settings') === 0 ? ' style="color: #00c4aa;"' : '' !!} ><i class="settings-icon"></i> <span>Website Settings</span></a>
				</li>
				@endpermission

				
				<li{!! strpos(Route::currentRouteName(), 'admin.products_colors') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ route('admin.products_colors.index') }}" {!! strpos(Route::currentRouteName(), 'admin.products_colors') === 0 ? ' style="color: #00c4aa;"' : '' !!} ><i class="settings-icon"></i> <span>Products Colors</span></a>
				</li>
				


				@permission('usersactivity')
				{{-- pr(Route::currentRouteName()) --}}
				<li{!! strpos(Route::currentRouteName(), 'admin.usersactivity') === 0 ? ' class="active"' : '' !!}>
				<a href="{{ url('admin/usersactivity') }}" {!! strpos(Route::currentRouteName(), 'admin.usersactivity') === 0 ? ' style="color: #00c4aa;"' : '' !!} ><i class="settings-icon"></i> <span>Users Activity</span></a>
				</li>
				@endpermission

			</ul>
		</li>
		@endpermission
		
		
		<?php
		/*
		<li{!! strpos(Route::currentRouteName(), 'admin.coupons') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.coupons.index') }}"><i class="coupons-icon"></i> <span>Coupons</span></a>
		</li>
		*/
		?>

		<?php
		/*
		<!-- <li{!! strpos(Route::currentRouteName(), 'admin.chat') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.chat.index') }}">Chat</a></li> -->
		*/
		?>


	</ul>
	</div>
	
</div>


