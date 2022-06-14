<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $cms['title'] }}</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')

<section class="fullwidth innerpage">
<div class="container">
	
	<div class="cmsleft">
		<ul>
          <li><a class="active" href="{{url('about')}}">About</a></li>
          <li><a href="{{url('returns')}}">Return &amp; Exchange</a></li>
          <li><a href="{{url('faq')}}">FAQ</a></li>
          <li><a href="{{url('contact')}}">Contact</a></li>
          <li><a href="{{url('terms')}}">Terms &amp; Conditions</a></li>
          <li><a href="{{url('privacy')}}">Privacy Policy</a></li>
        </ul> 
	</div>
	<div class="cmscontent">
	<h1 class="heading">{{$cms['title']}}</h1>
     <?php echo $cms['content']; ?>
	</div>
  </div>
</section>

@include('common.footer')

</body>
</html>