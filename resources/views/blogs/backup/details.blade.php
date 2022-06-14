<!DOCTYPE html>
<html>
<head>

@include('common.head')

	<link rel="stylesheet" type="text/css" href="{{url('public')}}/css/owl.carousel.min.css" />
</head>
<body>

@include('common.header')

<?php
//pr($blog->toArray());

$keyword = (request()->has('keyword'))?request()->keyword:'';

$storage = Storage::disk('public');

$blog_date = CustomHelper::DateFormat($blog->created_at, 'M d, Y');

$blog_title = $blog->title;
$blog_content = $blog->content;

$blog_images = (isset($blog->Images))?$blog->Images:'';
?>

<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">Blogs</h1>
		<p><a href="{{url('/')}}">Home</a> Blogs</p>
	</div>	
</section>
<section class="fullwidth innerpage">	
	<div class="container">


		@include('blogs._sidebar')

		
		<div class="blogpage">			
			<div class="bdetail">        
				<div class="dtbg">
					<?php
					if(!empty($blog_images) && count($blog_images)){
						foreach($blog_images as $bimg){
							if(!empty($bimg->image) && $storage->exists('blogs/'.$bimg->image)){
								?>
								<img src="{{url('public/storage/blogs/'.$bimg->image)}}" alt="{{$blog_title}}">
								<?php
								break;
							}
						}
					}
					?>
				</div>
				<div class="btext">
					<p><cite>{{$blog_date}}</cite></p>
					<h4>{{$blog_title}}</h4>

					<?php
					echo $blog_content;
					?>

				</div>
 
			</div>
		</div>
		 
	</div>
</section>
 
@include('common.footer')
	
</body>
</html>