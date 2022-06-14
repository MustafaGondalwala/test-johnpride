
<div class="topright">
   <ul class="top-nav">
    @if (auth()->check())
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-user"></i>&nbsp; {{ auth()->user()->first_name }} <i aria-hidden="true" class="fa fa-angle-down"></i>
            </a>
            <?php
            $back_url = CustomHelper::BackUrl();
            ?>
            <ul class="dropdown-menu dropdown-menu-right">
                <?php
                /*
                <li><a href="{{ route('account') }}">Account Settings</a></li>
                <li><a href="{{ route('orders') }}">My Orders</a></li>
                <li class="divider"></li>
                */
                ?>
                @if (auth()->user()->type == 'admin')
                    <li><a href="{{ route('admin.home') }}" title="Admin Panel"><i class="fa fa-desktop"></i> Admin</a></li>
                @endif

                <li><a href="{{ route('admin.change_password', ['back_url'=>$back_url]) }}" title="Change Password"><i class="fa fa-key"></i> Change Password</a></li>

                <li>

                    <?php
                    if(auth()->user()->isImpersonating()){
                        ?>            
                        <a href="{{ url('stop_impersonate') }}">
                            <i class="fa fa-power-off"></i> Stop Impersonate
                        </a>
                        <?php
                    }
                    else{
                        ?>
                        <a href="{{ url('admin/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i> Log out
                        </a>
                        <?php
                    }
                    ?>
                    

                    <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </li>
    @else
        <li><a href="login">Login</a></li>
        <!-- <li><a href="register">Register</a></li> -->
    @endif
</ul>
</div>