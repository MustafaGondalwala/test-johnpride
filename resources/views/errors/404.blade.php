<!DOCTYPE html>
<html>
    <head>
        <title>Be right back.</title>
        @include('common.head')
       
    </head>

    <body>
		@include('common.header')
        <section class="fullwidth innerpage page404 text-center">
			<div class="container"> 
				<h1  class="title404">404</h1>
				<p>Oops, Sorry We Can't Find That Page!</p>
				<p><small>Either something went wrong or the page dosen’t exist anymore.</small></p>
				<a class="socialbtn" href="{{url('/')}}">Go Home</a>
			</div>
		</section>
		
		
		@include('common.footer')
    </body>

</html>
