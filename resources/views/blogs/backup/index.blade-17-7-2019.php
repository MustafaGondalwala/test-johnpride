<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')

<?php
$storage = Storage::disk('public');

$keyword = (request()->has('keyword'))?request()->keyword:'';
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
			<?php
			if(!empty($blogs) && count($blogs) > 0){
				?>
				<ul class="bloglist">

					<?php
					foreach($blogs as $blog){

						//pr($blog->toArray());

						$blog_date = CustomHelper::DateFormat($blog->created_at, 'M d, Y');

						$blog_images = (isset($blog->Images))?$blog->Images:'';
						?>
						<li>
							<a href="{{url('blogs/details/'.$blog->slug)}}" class="blogbox">
								<div class="imghover">
									<?php
									if(!empty($blog_images) && count($blog_images)){
										foreach($blog_images as $bimg){
											if(!empty($bimg->image) && $storage->exists('blogs/thumb/'.$bimg->image)){
												?>
												<img src="{{url('public/storage/blogs/thumb/'.$bimg->image)}}" alt="{{$blog->title}}">
												<?php
												break;
											}
										}
									}
									?>
									
								</div>

								<div class="btext">
									<p><cite>{{$blog_date}}</cite></p>
									<p>{{$blog->title}}</p>
								</div>
							</a>
						</li>
						<?php
					}
					?>
				

			</ul>
				<?php
			}
			?>



		</div>

		{{$blogs->appends(request()->query())->links()}}

		
		 
	</div>
</section>
 
@include('common.footer')
	
</body>
</html>