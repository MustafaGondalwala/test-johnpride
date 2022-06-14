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
$cat = (request()->has('category'))?request()->category:'';
?>
	
<div class="fullwidth banner_blog">
	<img src="{{url('public')}}/images/blog-slider.jpg" alt="Slumber Jill" />
</div>
<div class="blog_wrap">
<section class="fullwidth innerpage blog_page">	
	<div class="container">


		<?php 
		if(!empty($blogCategories) && count($blogCategories) > 0){
		foreach ($blogCategories as $category){
		
			$blogs = $category->Blogs;
	        //prd($blogs->toArray());

		?>
		<div class="blog_rows">
		<div class="strip_head">
			{{$category->name}}
		</div>
		<ul class="blog_list">

			<?php 
			foreach ($blogs as $blog){

				$blog_date = CustomHelper::DateFormat($blog->blog_date, 'M d, Y');

				$blog_images = (isset($blog->Images))?$blog->Images:'';
			?>
			<li>
				<a href="{{url('blogs/'.$blog->slug)}}" class="blog_box">

					<?php
					if(!empty($blog_images) && count($blog_images)){
						foreach($blog_images as $bimg){
							if(!empty($bimg->image) && $storage->exists('blogs/thumb/'.$bimg->image)){

								$backgroundImage = url('public/storage/blogs/thumb/'.$bimg->image);
								?>
								<div class="images" style="background: url('<?php echo $backgroundImage; ?>');">
									<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 

									<div class="content">
										<span>{{$blog->title}}</span>
									</div>
									<?php
									break; ?>
								</div>
								<?php }
							}
						}
					?>
					
				</a>
			</li>
			<?php } ?>

		</ul>
		<?php 
		if(empty($cat)){
		?>
		<div class="view_all">
			<a href="{{ url('blogs?category='.$category->slug) }}">View All</a>
		</div>
		<?php } ?>

	</div>
	<?php } 
}?>

	<!-- <div class="blog_rows">
		<div class="strip_head">
			Business Like
		</div>
		<ul class="blog_list">
			<li>
				<a href="#" class="blog_box">
					<div class="images" style="background-image: url({{url('public')}}/images/blog-img3.jpg);">
						<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 
						<div class="content">
							<span>The Nurturing, Vibrant Colour Of 2019: Living Coral</span>
						</div>
					</div>
				</a>
			</li>
			<li>
				<a href="#" class="blog_box">
						<div class="images" style="background-image: url({{url('public')}}/images/blog-img4.jpg);">
						<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 
						<div class="content">
							<span>HRX Pushes The Fitness Envelope Through
Its Active Innerwear Collection</span>
						</div>
					</div>
				</a>
			</li>
			<li>
				<a href="#" class="blog_box">
					<div class="images" style="background-image: url({{url('public')}}/images/blog-img3.jpg);">
						<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 
						<div class="content">
							<span>The Nurturing, Vibrant Colour Of 2019:
Living Coral
</span>
						</div>
					</div>
				</a>
			</li>
		</ul>
		<div class="view_all">
			<a href="#">View All</a>
		</div>
	</div>
		<div class="blog_rows">
		<div class="strip_head">
			Business Like
		</div>
		<ul class="blog_list">
			<li>
				<a href="{{url('blogs/details/sit-cumque-omnis-lab')}}" class="blog_box">
					<div class="images" style="background-image: url({{url('public')}}/images/blog-img1.jpg);">
						<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 
						<div class="content">
							<span>The Nurturing, Vibrant Colour Of 2019: Living Coral</span>
						</div>
					</div>
				</a>
			</li>
			<li>
				<a href="#" class="blog_box">
						<div class="images" style="background-image: url({{url('public')}}/images/blog-img2.jpg);">
						<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 
						<div class="content">
							<span>Are You Ready For The Biggest EORS Ever?</span>
						</div>
					</div>
				</a>
			</li>
			<li>
				<a href="#" class="blog_box">
						<div class="images" style="background-image: url({{url('public')}}/images/blog-img1.jpg);">
						<img src="{{url('public')}}/images/blog-blank.png" alt="Slumber Jill" /> 
						<div class="content">
							<span>The Nurturing, Vibrant Colour Of 2019: Living Coral</span>
						</div>
					</div>
				</a>
			</li>
		</ul>
		<div class="view_all">
			<a href="#">View All</a>
		</div>
	</div> -->

		
	</div>
</section>
</div>
 
@include('common.footer')
	
</body>
</html>