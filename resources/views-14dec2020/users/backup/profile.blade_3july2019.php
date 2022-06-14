<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo (isset($meta_title))?$meta_title:'SlumberJill'?></title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="index, follow"/>
    <meta name="robots" content="noodp, noydir"/>

    @include('common.head')

</head>

<body>

    @include('common.header')

    <?php
    ///$name = trim($user->first_name.' '.$user->last_name);
    $name = $user->name;
    $phone = $user->phone;
    $email = $user->email;

    $addressArr = CustomHelper::formatUserAddress($user);

    $userAddresses = $user->userAddresses;

    $deliveryAddress = '';

    if(!empty($userAddresses) && count($userAddresses) > 0){
        $deliveryAddress = $userAddresses->where('is_default', 1)->first();
    }

    $deliveryAddrArr = CustomHelper::formatUserAddress($deliveryAddress);

    //pr(count($deliveryAddrArr));

    $pwdBoxDisp = '';
    $changePwdLinkDisp = '';

    if($errors->count() > 0){
        $pwdBoxDisp = 'display:block;';
        $changePwdLinkDisp = 'display:none;';
    }

    ?>

    <section class="fullwidth innerpage">
      <div class="container">
          @include('users.nav')
          
          <div class="rightcontent">
              <div class="heading2">Profile <a class="edit-link" href="{{url('users/update')}}"><i class="editicon"></i></a></div>

              @include('snippets.front.flash')

              <div class="accountinner">
                <p><span>NAME:</span> <small> {{$name}}</small> </p>

                <p>
                    <span>BILLING ADDRESS:</span>
                    <?php
                    if(!empty($addressArr) && count($addressArr) > 0){
                        ?>
                        <small><?php echo implode(', ', $addressArr); ?></small>
                        <?php
                    }
                    ?>
                </p>

                <p>
                    <span>DELIVERY ADDRESS:</span>
                    <?php
                    if(!empty($deliveryAddrArr) && count($deliveryAddrArr) > 0){
                        ?>
                        <small><?php echo implode(', ', $deliveryAddrArr); ?></small>
                        <?php
                    }
                    ?>
                </p>

                <p><span>PHONE NUMBER:</span>  <small>{{$phone}}</small></p>
                <p><span>EMAIL:</span>  <small>{{$email}}</small></p>
                
                <p class="change-pwd-link " style="{{$changePwdLinkDisp}}"><span>PASSWORD:</span>  <small><a href="javascript:void(0)" class="showPwdBox" >Change password</a></small></p>
                
                <div class="change-pwd" style="{{$pwdBoxDisp}}" >

                    <form name="changePassForm" method="POST" id="changePWD">
                        {{csrf_field()}}

                        <p class="success"></p>
                        <p class="error"></p>

                        <?php
                        if(!empty($user->password)){
                            ?>
                            <p>
                                <span>Current Password:</span>
                                <small><input type="password" name="current_password" class="inputfild" style="max-width:200px"></small>
                                @include('snippets.front.errors_first', ['param' => 'current_password'])
                            </p>
                            <?php
                        }
                        ?>                        

                        <p>
                            <span>New Password:</span>
                            <small><input type="password" name="new_password" class="inputfild" style="max-width:200px"></small>
                            @include('snippets.front.errors_first', ['param' => 'new_password'])
                        </p>
                        <p>
                            <span>Confirm Password:</span>
                            <small><input type="password" name="confirm_password" class="inputfild" style="max-width:200px"></small>
                            @include('snippets.front.errors_first', ['param' => 'confirm_password'])
                        </p>

                        <p>
                            <span> </span>
                            <small>
                                <button type="submit" id="change-pwd-btn" name="savePwd" class="savebtn">Save</button>
                                <button type="button" class="cancelbtn btn-default hidePwdBox">Cancel</button>
                            </small>
                        </p>

                    </form> 
                </div>       
            </div>
        </div>
    </div>
</section>

@include('common.footer')

<script type="text/javascript">
    $(".hidePwdBox").click(function(){
        $(".change-pwd").hide();
        $(".change-pwd-link ").show();
    });
    $(".showPwdBox").click(function(){
        $('.change-pwd').show();
        $('.change-pwd-link').hide();
    });
</script>

</body>
</html>