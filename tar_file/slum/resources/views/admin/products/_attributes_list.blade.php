<?php
if(!empty($attributes) && count($attributes) > 0){

	$attrArr = (isset($productAttributesArr))?$productAttributesArr:'';
	
	?>
	<div class="col-md-12">

		<h4>Attributes</h4>

		<?php
		foreach($attributes as $attr){

			$attrLabel = trim($attr->label);
			if(!empty($attrLabel)){

				$attrVal = '';

				if(isset($attrArr[$attrLabel])){
					$attrVal = $attrArr[$attrLabel]->value;
				}
				?>

				<div class="row attribute">
					<div class="col-md-3">
						<div class="form-group">
							<label>{{$attrLabel}}</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<input type="text" name="attr[{{$attrLabel}}]" value="{{$attrVal}}" class="form-control">
						</div>
					</div>
				</div>
				<?php
			}
		}

		?>
	</div>
	<?php
}

?>