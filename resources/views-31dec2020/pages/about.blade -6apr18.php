<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php $cms = CustomHelper::GetCMSPage("about"); ?>
<title>{{ $cms['title'] }}</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.user_header')

<section>
  <div class="contentArea">

      @include('common.left_menu')
    <div class="rightBar">
      <div class="container-custom">
        <div class="panel panel-default"> 
          <div class="topHeading panel-heading"><span></span><span></span><span></span>{{ $cms['title']}}</div>
          <div class="panel-body">
            <div class="col-md-12">
            <?php echo $cms['content']; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('common.footer')

</body>
</html>