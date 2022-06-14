<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{trans('custom.terms_conditions')}}</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')


<section>
  <div class="contentArea">
      
    <div class="rightBar no_login_full_width">
      <div class="container-custom">

      @include('pages._terms')

      </div>
    </div>
  </div>
</section>

@include('common.footer')

</body>
</html>
