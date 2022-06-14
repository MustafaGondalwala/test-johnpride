<div class="row">
    <div class="col-md-12">
        <ul class="main-nav clearfix">
            <li{!! strpos(Route::currentRouteName(), 'admin.home') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.home') }}">Dashboard</a></li>
            <li{!! strpos(Route::currentRouteName(), 'admin.settings') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.settings.index') }}">Settings</a></li>
            <li{!! strpos(Route::currentRouteName(), 'admin.orders') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.orders.index') }}">Orders</a></li>
            <li{!! strpos(Route::currentRouteName(), 'admin.categories') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.categories.index') }}">Categories</a></li>
            <li{!! strpos(Route::currentRouteName(), 'admin.attributes') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.attributes.index') }}">Attributes</a></li>

            @permission('deliveryterms')
            <li{!! strpos(Route::currentRouteName(), 'admin.deliveryterms') === 0 ? ' class="active"' : '' !!}><a href="{{ url('admin/deliveryterms') }}">Delivery Terms</a></li>
            @endpermission

            <li{!! strpos(Route::currentRouteName(), 'admin.brands') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.brands.index') }}">Brands</a></li>
            <li{!! strpos(Route::currentRouteName(), 'admin.products') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.products.index') }}">Products</a></li>
            <li{!! strpos(Route::currentRouteName(), 'admin.coupons') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>

            @permission('users')
            <li{!! strpos(Route::currentRouteName(), 'admin.users') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.users.index') }}">Users</a></li>
            @endpermission

            @permission('chat')
            <li{!! strpos(Route::currentRouteName(), 'admin.users') === 0 ? ' class="active"' : '' !!}><a href="{{ url('admin/chat') }}">Chat</a></li>
            @endpermission

            @permission('permissions.manage')
            <li{!! strpos(Route::currentRouteName(), 'admin.permissions') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
            @endpermission

            <?php
            /*
            <!-- <li{!! strpos(Route::currentRouteName(), 'admin.chat') === 0 ? ' class="active"' : '' !!}><a href="{{ route('admin.chat.index') }}">Chat</a></li> -->
            */
            ?>


        </ul>
    </div>
</div>