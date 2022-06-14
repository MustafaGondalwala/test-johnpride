<?php
if(!empty($Brands) && count($Brands) > 0){
	?>
	<div class="list-group">
		<a href="javascript:void(0)" class="list-group-item disabled">Brands</a>
		<?php
		foreach($Brands as $brand){
			?>
			<a href="javascript:void(0)" data-field="brand[]" data-val="{{$brand->slug}}" class="list-group-item sr_list_item">{{$brand->name}}</a>
			<?php
		}
		?>
	</div>
	<?php
}


/*if(!empty($Categories) && count($Categories) > 0){
	?>
	<div class="list-group">
		<a href="javascript:void(0)" class="list-group-item disabled">Categories</a>
		<?php
		foreach($Categories as $cat){

			$p1CatSlug = '';

			if($cat->parent && count($cat->parent) > 0){
				$parentCat = $cat->parent;

				if($parentCat->parent && count($parentCat->parent) > 0){
					$pParentCat = $parentCat->parent;
					$p1CatSlug = (isset($pParentCat->slug))?$pParentCat->slug:'';
				}
			}
			?>
			<a href="javascript:void(0)" data-pcat="{{$p1CatSlug}}" data-field="cat[]" data-val="{{$cat->slug}}" class="list-group-item sr_list_item">{{$cat->name}}</a>
			<?php
		}
		?>
	</div>
	<?php
}*/


if(!empty($Products) && count($Products) > 0){
	?>
	<div class="list-group">
		<a href="javascript:void(0)" class="list-group-item disabled">All Others</a>
		<?php
		foreach($Products as $product){
			?>
			<a href="javascript:void(0)" data-field="keyword" data-val="{{$product->name}}" class="list-group-item sr_list_item">{{$product->name}}</a>
			<?php
		}
		?>
	</div>
	<?php
}
?>