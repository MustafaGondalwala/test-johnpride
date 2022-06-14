<?php
$countheading = (isset($countheading))?$countheading:1;
$label = (isset($label))?$label:'';
$attr_sort_order = (isset($attr_sort_order))?$attr_sort_order:'';

$showRemoveBtn = (isset($showRemoveBtn))?$showRemoveBtn:false;
?>
<div class="row attribute">
	<div class="col-md-3">
		<div class="form-group">
			<input type="text" class="form-control" name="attr_labels[]" value="{{ $label }}" placeholder="Label" />
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input type="number" class="form-control" name="attr_sort_order[]" value="{{ $attr_sort_order }}" placeholder="Sort order" />
		</div>
	</div>
	<?php
	if($countheading == 1 && !$showRemoveBtn){
		?>
		<a href="javascript:void(0)" class="add_head_row">Add</a>
		<?php
	}
	else{
		?>
		<a href="javascript:void(0)" class="remove_head_row">Remove</a>
		<?php
	}
	?>
</div>