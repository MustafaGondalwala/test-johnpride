<?php
$keyword = (request()->has('keyword'))?request()->keyword:'';
$cat_slug = (request()->has('cat'))?request()->cat:'';
?>

<div class="sidesec">
	<div class="sidebox">
		<div class="sidehead">Search</div>
		<div class="searchbox">
			<form name="searchForm" action="{{url('blogs')}}">
				<?php
				/*
				<input type="hidden" name="cat" value="{{$cat_slug}}">
				*/
				?>
				<input type="text" name="keyword" value="{{$keyword}}" placeholder="Search for posts" class="searchinput" />
				<button class="searchbtn"><i class="searchicon"></i></button>
			</form>
		</div>
	</div>
	
	<?php
	if(!empty($BlogCategories) && count($BlogCategories) > 0){
		?>

		<div class="sidebox">
			<div class="sidehead">Category</div>
			<div class="category">
				<ul>
					<?php
					foreach($BlogCategories as $bcat){
								//#203351
						$cat_clr = '';
						if($bcat->slug == $cat_slug){
							$cat_clr = 'color:#203351;';
						}
						?>
						<li><a href="{{url('blogs?cat='.$bcat->slug)}}" style="{{$cat_clr}}">{{$bcat->name}}</a></li>
						<?php
					}
					?>
				</ul> 
			</div>
		</div>

		<?php
	}
	?>
	
	<?php
	if(!empty($recent_blogs) && count($recent_blogs) > 0){
		?>
		<div class="sidebox">
			<div class="sidehead">Recent Post</div>
			<div class="recentpost">
				<ul class="bloglist">

					<?php
					foreach($recent_blogs as $rblog){

						$rblog_date = CustomHelper::DateFormat($rblog->created_at, 'M d, Y');

						$rblog_images = (isset($rblog->Images))?$rblog->Images:'';
						?>
						<li>
							<a href="{{url('blogs/details/'.$rblog->slug)}}" class="blogbox">
								<div class="imghover">
									<?php
									if(!empty($rblog_images) && count($rblog_images)){
										foreach($rblog_images as $rbimg){
											if(!empty($rbimg->image) && $storage->exists('blogs/thumb/'.$rbimg->image)){
												?>
												<img src="{{url('public/storage/blogs/thumb/'.$rbimg->image)}}" alt="{{$blog->title}}">
												<?php
												break;
											}
										}
									}
									?>

								</div>

								<div class="btext">
									<p><cite>{{$rblog_date}}</cite></p>
									<p>{{$rblog->title}}</p>
								</div>
							</a>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}
	?>

	
</div>