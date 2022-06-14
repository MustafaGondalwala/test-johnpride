@component('admin.layouts.main')

	@slot('title')
		Admin - Manage Inventory - {{ config('app.name') }}
	@endslot

	<?php

	$BackUrl = CustomHelper::BackUrl();
	$id = 0;
	//$productId = (request('product_id'))?(request('product_id')):0;

	$id = (isset($inventory->id))?$inventory->id:0;
	$sku = (isset($inventory->sku))?$inventory->sku:'';
	$stock = (isset($inventory->stock))?$inventory->stock:'';
	$size_id = (isset($inventory->size_id))?$inventory->size_id:'';
 
	?>


	<div class="row">

	<div class="col-md-12">
			<h1>Inventory</h1>

			@include('snippets.errors')
			@include('snippets.flash')
			</div>

				<div class="col-md-12">
				<div class="topsearch">

				<h4>{{$form_heading}}</h4>
				<br>
					<form method="POST" action="" accept-charset="UTF-8" role="form" class="form-inline heightform">
						{{ csrf_field() }}

						<input type="hidden" name="back_url" value="{{$BackUrl}}">

                        <div class="form-group {{ $errors->has('sku') ? ' has-error' : '' }} ">
                            <label for="sku" class="control-label required">SKU:</label>

                            <input type="text" name="sku" value="{{ old('sku', $sku) }}" class="form-control" maxlength="50" />

                           @include('snippets.errors_first', ['param' => 'sku'])
                        </div>

						<div class="form-group{{ $errors->has('size_id') ? ' has-error' : '' }}">
							<label class="control-label">Size:</label>

							<select name="size_id" class="form-control">
								<option value="">--Select--</option>
								<?php
								if(!empty($sizes) && count($sizes) > 0) {
									foreach ($sizes as $size){
										
										$selected = '';
										if($size->id == $size_id){
											$selected = 'selected';
										}
										?>
										<option value="{{$size->id}}" {{$selected}}>{{$size->name}}</option>
										<?php
									}
								}
								?>
							</select>

							@include('snippets.errors_first', ['param' => 'size_id'])
						</div>

						<div class="form-group {{ $errors->has('stock') ? ' has-error' : '' }} ">
							<label for="stock" class="control-label required">Stock:</label>

							<input type="number" name="stock" class="form-control" value="{{ old('stock', $stock) }}" maxlength="255" />

						   @include('snippets.errors_first', ['param' => 'stock'])
						</div>
 
						<input type="hidden" name="id" value="{{ $id }}">

						<button class="btn btn-success btn1search"><i class="fa fa-save"></i> Save</button>

						<?php
						if($id > 0){
							?>
							<a href="{{ url('admin/products/'.$productId.'/inventory') }}" class="btn resetbtn btn-primary btn1search" title="Cancel">Cancel</a>
							<?php
						}
						?>

					</form>

				</div>
			</div>


		<div class="col-md-12">

			
			<?php
			if(!empty($inventories) && count($inventories) > 0){
			?>
				<table class="table table-striped">
					<tr>
						<th>Size Name</th>
						<th>SKU</th>
						<th>Stock</th>                      
						<th>Action</th>
					</tr>
					<?php
					//pr($inventories);
					foreach($inventories as $inventory){
						//$countProducts = $inventory->countProducts();
						?>

						<tr>
							<td>{{$inventory->size_name}}</td>
							<td>{{$inventory->sku}}</td>
							<td>{{$inventory->stock}}</td>
							<td>
								<a href="{{ url('admin/products/'.$productId.'/inventory', $inventory->id) }}" class=""><i class="fas fa-edit"></i></a>

								<a href="javascript:void(0)" class="sbmtDelForm"  id="{{$inventory->id}}"><i class="fas fa-trash-alt"></i></i></a>

								<form method="POST" action="{{ url('admin/products/'.$productId.'/inventory/'.$inventory->id.'/delete') }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Inventory?');" id="delete-form-{{$inventory->id}}">
									{{ csrf_field() }}
									<input type="hidden" name="id" value="<?php echo $inventory->id; ?>">
								</form>

							</td>
						</tr>
						<?php
					}
					?>
				</table>
				{{ $inventories->appends(request()->query())->links() }}
				 
				 <?php
			 }
			 else{
				?>
				<div class="alert alert-warning">There are no Inventory at the present.</div>
				<?php
			 }
				 ?>
		</div>

	</div>

	@slot('bottomBlock')

	<script type="text/javaScript">
		$('.sbmtDelForm').click(function(){
			var id = $(this).attr('id');
			$("#delete-form-"+id).submit();
		});
	</script>

	@endslot

@endcomponent