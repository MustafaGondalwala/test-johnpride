<?php 
$colors = '';
$collections = '';
$sizes = '';
$subCategories = '';

$colors = CustomHelper::getData('colors_master', '', ['status'=>1]);
$sizes = CustomHelper::getData('sizes', '', ['status'=>1]);
$collections = CustomHelper::getData('brands', '', ['status'=>1]);

$parent_id = 0;

$pcat = (request()->has('pcat'))?request()->pcat:'';
$p2cat = (request()->has('p2cat'))?request()->p2cat:'';
$catArr = (request()->has('cat'))?request()->cat:[];
$genderArr = (request()->has('gender'))?request()->gender:[];
$collectionArr = (request()->has('collection'))?request()->collection:[];
$colorArr = (request()->has('color'))?request()->color:[];
$sizeArr = (request()->has('size'))?request()->size:[];

$keyword = (request()->has('keyword'))?request()->keyword:'';
$price_range = (request()->has('price_range'))?request()->price_range:'';
$br = (request()->has('br'))?request()->br:'';

if(!empty($br)){
	$collectionArr[] = $br;
}

//pr($collectionArr);
if(!empty($pcat)){
	$parentCategory = CustomHelper::getCategories($pcat);
	//prd($parentCategory);

	//$parentCategory = $parentCategory->where('status',1)

	if(isset($parentCategory->id) && $parentCategory->id > 0){

		//$parentCategory = $parentCategory->where('status',1);

		$parent_id = $parentCategory->id;
	} 
}

if(is_numeric($parent_id) && $parent_id > 0){
	$subCategoriesBySlug = CustomHelper::getCategories('', $parent_id)->keyBy('slug');

	if(!empty($p2cat)){
		$subCategoriesBySlug = $subCategoriesBySlug->where('slug',$p2cat);
	}

	$subCategories = $subCategoriesBySlug;

	//$subCategories = $subCategories->where('status',1);
}

?>



<div class="sidebarsec">
	<form method="get" name="filterForm" id="filterForm">

		<input type="hidden" name="pcat" value="{{$pcat}}">
		<input type="hidden" name="keyword" value="{{$keyword}}">
		<input type="hidden" name="sort_by" value="{{$sort_by}}">
		<input type="hidden" name="price_range" value="{{$price_range}}">

		<?php
		if(!empty($p2cat)){
			?>
			<input type="hidden" name="p2cat" value="{{$p2cat}}">
			<?php
		}
		?>
		
		<div class="filtertitle fullwidth">
			<span class="filtermobile">Filters <small></small></span>
			
		</div> 
		<div class="sideinner">
			

			<?php
			if(!empty($subCategories) && count($subCategories) > 0) {
				?>
				<div class="sidetitle">Categories <span><i class="searchicon"></i></span></div>
				<div class="boxs catlist">
					<ul>
						<?php 
						$i = 0;
						foreach ($subCategories as $subCategory){
							$i++;
							$class = '';
							$childCategories = $subCategory->children;

							$childCategories = $childCategories->where('status',1);

							$childCategoriesBySlug = $childCategories->keyBy('slug');

						//pr($childCategoriesBySlug->toArray());

							if(!empty($catArr) && count($catArr) > 0){
								foreach($catArr as $srCat){
									if(isset($childCategoriesBySlug[$srCat])){
										$class = 'class="active"';
										break;
									}
								}
							}
							elseif($i == 1){
								$class = 'class="active"';
							}
							?>
							<li <?php echo $class;?>>

								<span> <a href="{{route('products.list', ['pcat'=>$pcat, 'p2cat'=>$subCategory->slug])}}">{{$subCategory->name}}</a> </span>

								<?php
								if(!empty($childCategories) && count($childCategories) > 0) {
									?> 
									<ul>
										<?php
										foreach ($childCategories as $childCategory) {
											$checked = '';
											if(in_array($childCategory->slug, $catArr)){
												$checked = 'checked';
											}
											?>
											<li>
												<label>
													<input type="checkbox" name="cat[]" value="{{$childCategory->slug}}" class="filterItem" {{$checked}}> <span> {{ $childCategory->name }}</span>
												</label>
											</li>
											<?php
										}
										?>  
									</ul>
									<?php
								}
								?>

							</li>
							<?php
						}
						?>

					</ul>
				</div>
				<?php
			}
			?>
			
			<?php
			if(empty($pcat) && (empty($catArr) || !count($catArr) > 0) ){
				?>        

				<div class="boxs">
					<ul>
						<li><span>Gender</span>
							<ul>
								<li>
									<label>
										<input type="checkbox" name="gender[]" value="f" class="filterItem" {{(in_array('f',$genderArr))?'checked':''}}> <span>Female</span>
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" name="gender[]" value="m" class="filterItem" {{(in_array('m',$genderArr))?'checked':''}}> <span>Male</span>
									</label>
								</li>
							</ul>
						</li>
					</ul>
				</div>
				<?php

			}
			?>


			<?php
			if(!empty($collections) && count($collections) > 0) {
				?>
				<div class="boxs">
					<ul>
						<li><span>Collection</span>
							<ul>
								<?php
								foreach ($collections as $collection) {
									$checked = '';
									if(in_array($collection->slug, $collectionArr)){
										$checked = 'checked';
									}
									?>
									<li><label><input type="checkbox" name="collection[]" value="{{$collection->slug}}" class="filterItem" {{$checked}}> <span><?php echo $collection->name; ?></span></label></li>
									<?php
								}
								?>
							</ul>
						</li>
					</ul>
				</div>
				<?php
			}
			?>

			<?php
			if(!empty($colors) && count($colors) > 0){
				?>
				<div class="boxs colors">
					<ul>
						<li><span>Color</span>
							<ul>
								<?php
								$countClr = 0;

								$isViewMoreBtn = false;
								foreach ($colors as $color) {

									$checked = '';
									if(in_array($color->slug, $colorArr)){
										$checked = 'checked';
									}

									$displayNone = '';

									$moreItemClass = '';

									if($countClr >= 5){
										$displayNone = "display:none;";
										$moreItemClass = 'moreItem';

										$isViewMoreBtn = true;
									}
									?>
									<li style="<?php echo $displayNone; ?>" class="<?php echo $moreItemClass; ?>" >
										<label><input type="checkbox" name="color[]" value="{{$color->slug}}" class="filterItem" {{$checked}}><span><small style="background-color:<?php echo $color->code;?>; " ></small>{{$color->name}}</span></label>
									</li>
									<?php
									$countClr++;
								}
								if($isViewMoreBtn){
									?>
									<a href="javascript:void(0)" class="viewMore">+ More</a>
									<?php
								}
								?>
							</ul>
						</li>
					</ul>
				</div>
				<?php
			}
			?>

			<?php
			if(!empty($sizes) && count($sizes) > 0){
				$sizes = $sizes->sortBy('sort_order');
				?>
				<div class="boxs">
					<ul>
						<li><span>Size</span>
							<ul>
								<?php foreach ($sizes as $size){

									$checked = '';
									if(in_array($size->name, $sizeArr)){
										$checked = 'checked';
									}
									?>
									<li><label><input type="checkbox" name="size[]" value="{{$size->name}}" class="filterItem" {{$checked}}> <span> <?php echo $size->name; ?> </span></label></li>
									<?php
								}
								?>

							</ul>
						</li>
					</ul> 
				</div>
				<?php
			}
			?>

			<div class="boxs priceing">
				<div class="sidetitle">
					Price 
				</div>
				
				<div class="pricerang">
					<div id="slider-range"></div>
					<p> <label for="amount"> &#x20B9;</label> <input type="text" id="amount" readonly style="border:0;  "></p>
				</div>
			</div>
		</div>	  
	</form>
</div>



