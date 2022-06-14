<?php
$countheading++;

$iid = (isset($iid))?$iid:0;
$image = (isset($image))?$image:'';

$is_default_id = (isset($is_default_id))?$is_default_id:0;
$is_reverse_id = (isset($is_reverse_id))?$is_reverse_id:0;

$showRemoveBtn = (isset($showRemoveBtn))?$showRemoveBtn:false;
?>
<tr class="img_row">

	<td>
		<input type="text" name="images[]" value="{{$image}}" class="form-control" >
		<input type="hidden" name="image_ids[]" value="{{$iid}}">
	</td>

	<td>
		<?php
		if(is_numeric($iid) && $iid > 0){
			?>
			<input type="radio" name="is_default" value="{{$iid}}" {{($is_default_id == $iid)?'checked':''}} >
			<?php
		}
		else
		{
			?>
			<input type="radio" name="is_default" value="{{$iid}}" >

		<?php }
		?>
		
	</td>
	<td>
		<?php
		if(is_numeric($iid) && $iid > 0){
			?>
			<input type="radio" name="is_reverse" value="{{$iid}}" {{($is_reverse_id == $iid)?'checked':''}} >
			<?php
		}
		else
		{
			?>
			<input type="radio" name="is_reverse" value="{{$iid}}">

		<?php }
		?>
		
	</td>

	<td>
		<a href="javascript:void(0)" data-iid="{{$iid}}" class="remove_img_row">Remove</a>
	</td>
	
	
</tr>