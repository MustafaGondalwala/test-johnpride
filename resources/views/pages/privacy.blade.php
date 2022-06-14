<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{trans('custom.privacy_policy')}}</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')

<?php
/*
@include('common.user_header')
*/
?>

<section>
  <div class="contentArea">

      
      
    <div class="rightBar no_login_full_width">
      <div class="container-custom">

      @include('pages._privacy')


         </div>
      </div>
    </div>
  </div>
</section>

@include('common.footer')

</body>
</html>
