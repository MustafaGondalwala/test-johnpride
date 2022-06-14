<form name="sizeQtyForm" method="post">

	<input type="hidden" name="productId" value="{{$productId}}">
	<input type="hidden" name="cartId" value="{{$cartId}}">
	<input type="hidden" name="oldSizeId" value="{{$oldSizeId}}">
	<input type="hidden" name="oldQty" value="{{$oldQty}}">

	<div class="slider_wrap">
		<div class="owl-carousel owl-theme">
			<?php
			if(!empty($productSizesArr) && count($productSizesArr) > 0){
				foreach($productSizesArr as $psa){

					$sizeId = $psa->size_id;
					$stock = $psa->stock;
					$size_name = $psa->size_name;


					$boxBg = "";
					$sizeChecked = "";

					if($sizeId == $oldSizeId){
						$boxBg = "background:#a77736; border-color:#a77736; color:#fff;";
					}

					if($sizeId == $oldSizeId){
						$sizeChecked = "checked";
					}
					?>

					<?php
					/*
					<li><a href="javascript:void(0)" data-cid="{{cartId}}" data-pid="{{productId}}" data-sid="{{$sizeId}}" data-osid="{{sizeId}}" data-type="size" class="changeSize" >{{$size_name}}</a></li>
					*/
					?>


					<div class="item">

						<div class="size_box" data-cid="{{$cartId}}" data-pid="{{$productId}}" data-sid="{{$sizeId}}" data-osid="{{$oldSizeId}}" data-oqty="{{$oldQty}}" style="{{$boxBg}}" >

							<?php
							/*
							<a href="javascript:void(0)" data-cid="{{cartId}}" data-pid="{{productId}}" data-sid="{{$sizeId}}" data-osid="{{sizeId}}" data-type="size" class="changeSize" >{{$size_name}}</a>
							*/
							?>

							<input type="radio" name="sizeId" value="{{$sizeId}}" data-stock="{{$stock}}" {{$sizeChecked}} >

							<label>{{$size_name}}</label>

						</div>
					</div>
					<?php
				}
			}
			?>

		</div>
	</div>

	<div class="qtn_wrap">
		<h4 class="modal-title">Select quantity</h4>
		<button type="button" id="sub" class="sub qtn_btn">-</button>
		<input type="text" name="qty" value="{{$oldQty}}" >
		<button type="button" id="add" class="add qtn_btn"> +</button>

	</div>
	<button type="button" class="btn updateSizeQty">Update</button>

</form>
