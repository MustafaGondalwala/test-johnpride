<div class="row">

    <div class="col-md-12">

        <ul class="main-nav clearfix">
            <li{!! Route::currentRouteName() == 'home' ? ' class="active"' : '' !!}><a href="{{ route('home') }}" title="Homepage"><i class="fa fa-home"></i> Home</a></li>

            <li{!! Route::currentRouteName() == 'shop' ? ' class="active"' : '' !!}><a href="{{ route('shop') }}">Shop</a></li>

            @foreach ($categories['list'] as $category)
                @if ($category['menu'])
                    <li{!! Route::getCurrentRoute()->parameter('uri') == $category['uri'] ? ' class="active"' : '' !!}>
                        <a href="{{ route('shop.category', [$category['uri'], $category['id']]) }}">{!! $category['name'] !!}</a>
                    </li>
                @endif
            @endforeach

            <li{!! Route::currentRouteName() == 'page.services' ? ' class="active"' : '' !!}><a href="{{ route('page.services') }}">Services</a></li>
        </ul>

    </div>

</div>