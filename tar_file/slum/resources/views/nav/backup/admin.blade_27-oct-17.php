





<div class="leftsec">
	<div class="menuicon"><span></span> <small>Menu</small></div>
	<div class="fullwidth leftsec1">
	<ul class="main-nav clearfix">

		<li{!! strpos(Route::currentRouteName(), 'admin.home') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.home') }}"><i class="dashboard-icon"></i> <span>Dashboard</span></a>
		</li>

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
		<li {!! strpos(Route::currentRouteName(), 'admin.orders') === 0 ? ' class="active"' : '' !!}>
			<a href="{{ url('admin/orders') }}" class="subtab"><i class="orders-icon"></i> <span>Orders</span> <i aria-hidden="true" class="fa fa-angle-down"></i></a>
			<ul >
				<li {!! strpos(Route::currentRouteName(), 'admin.orders') === 0 ? ' class="active activelink"' : '' !!}><a href="{{ url('admin/orders') }}">All</a></li>

				<?php
				$order_status_list = config('custom.order_status');
                    if(count($order_status_list) > 0){
                        foreach($order_status_list as $osl_key=>$osl_val){
                            ?>
                            <li><a href="{{ url('admin/orders?order_status='.$osl_key) }}">{{$osl_val}}</a></li>
                            <?php
                        }
                    }
                    ?>
				
			</ul>
		</li>
		@endpermission


		@permission('permissions.manage')
		<li{!! strpos(Route::currentRouteName(), 'admin.permissions') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.permissions.index') }}"><i class="permissions-icon"></i> <span>Permissions</span></a>
		</li>
		@endpermission

		
		<li {!! ( strpos (Route::currentRouteName(), 'admin.users') === 0 || strpos(Route::currentRouteName(), 'admin.customers') === 0) ? ' class="active"' : '' !!}>
			{{-- Route::currentRouteName() --}}
			<a  class="subtab"><i class="users-icon"></i> <span>Users</span> <i aria-hidden="true" class="fa fa-angle-down"></i></a>
			<ul>
				@permission('users')
				<li {!! strpos(Route::currentRouteName(), 'admin.users') === 0 ? ' class="active activelink"' : '' !!}><a href="{{ route('admin.users.index') }}">Admin Users</a></li>
				@endpermission

				@permission('customers')
				<li {!! strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' class="active"' : '' !!}>
					<a href="{{ url('admin/customers') }}"><i class="users-icon"></i> <span>Customers</span></a>
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
		?>

		@permission('chat')
		<li{!! strpos(Route::currentRouteName(), 'admin.chat') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ url('admin/chat') }}"><i class="chat-icon"></i> <span>Chat</span></a>
		</li>
		@endpermission

		@permission('products')
		<li{!! strpos(Route::currentRouteName(), 'admin.products') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.products.index') }}"><i class="products-icon"></i> <span>Products</span></a>
		</li>
		@endpermission

		@permission('brands')
		<li{!! strpos(Route::currentRouteName(), 'admin.brands') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.brands.index') }}"><i class="brands-icon"></i> <span>Brands</span></a>
		</li>
		@endpermission

		@permission('categories')
		<li{!! strpos(Route::currentRouteName(), 'admin.categories') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.categories.index') }}"><i class="categories-icon"></i> <span>Categories</span></a>
		</li>
		@endpermission

		@permission('settings')
		<li{!! strpos(Route::currentRouteName(), 'admin.settings') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.settings.index') }}"><i class="settings-icon"></i> <span>Settings</span></a>
		</li>
		@endpermission


		@permission('attributes')
		<li{!! strpos(Route::currentRouteName(), 'admin.attributes') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ route('admin.attributes.index') }}"><i class="attributes-icon"></i> <span>Attributes</span></a>
		</li>
		@endpermission

		@permission('deliveryterms')
		<li{!! strpos(Route::currentRouteName(), 'admin.deliveryterms') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ url('admin/deliveryterms') }}"><i class="delivery-icon"></i> <span>Delivery Terms</span></a>
		</li>
		@endpermission


		@permission('shippingzones')
		<li{!! strpos(Route::currentRouteName(), 'admin.shippingzones') === 0 ? ' class="active"' : '' !!}>
		<a href="{{ url('admin/shippingzones') }}"><i class="fa fa-truck" aria-hidden="true"></i> <span>Shipping Zones</span></a>
		</li>
		@endpermission

		@permission('shippingrates')
		<li{!! strpos(Route::currentRouteName(), 'admin.shippingrates') === 0 ? ' class="active"' : '' !!}>		
		<a href="{{ route('admin.shippingrates.index') }}"><i class="fa fa-truck" aria-hidden="true"></i> <span>Shipping Rates</span></a>
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


