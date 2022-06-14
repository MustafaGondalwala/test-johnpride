<?php
if(!empty($userCart) && $userCart->count() > 0){
	?>

	<table class="table table-bordered">
		<tr>
			<th>Product</th>

			<th>Size</th>
			<th>Qty</th>
			<th>Price</th>
			<th>Sale Price</th>
			<th>Added on</th>
		</tr>
		<?php
		foreach($userCart as $item){

			$user = $item->user;
			$product = $item->product;

			$productName = (isset($product->name))?$product->name:'';

			$customerName = (isset($user->name))?$user->name:'';

			$added_on = CustomHelper::DateFormat($item->created_at, 'd F y');

			?>
			<tr>
				<td>
					<a href="{{url('admin/products?name='.$productName)}}" target="_blank">{{$productName}}</a>
				</td>

				<td>{{$item->size_name}}</td>
				<td>{{$item->qty}}</td>
				<td>{{$item->price}}</td>
				<td>{{$item->sale_price}}</td>
				<td>{{$added_on}}</td>
			</tr>

			<?php
		}
		?>
	</table>

	<?php
}
?>