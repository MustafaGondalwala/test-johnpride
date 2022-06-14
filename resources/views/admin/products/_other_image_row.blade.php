<?php
$countheading = (isset($countheading))?$countheading:1;

$image = (isset($image))?$image:'';

$showRemoveBtn = (isset($showRemoveBtn))?$showRemoveBtn:false;
?>
<tr class="oi_row">


	<td>
		<input type="text" name="images[]" value="{{$image}}" class="form-control" >
	</td>

	<td>
		<input type="radio" name="is_default[]">
	</td>
	<td>
		<input type="radio" name="is_reverse[]">
	</td>

	<td>

		<?php
		if($countheading == 1 && !$showRemoveBtn){
			?>
			<a href="javascript:void(0)" class="add_oi_row">Add</a>
			<?php
		}
		else{
			?>
			<a href="javascript:void(0)" class="remove_oi_row">Remove</a>
			<?php
		}
		?>
	</td>
	
	
</tr>