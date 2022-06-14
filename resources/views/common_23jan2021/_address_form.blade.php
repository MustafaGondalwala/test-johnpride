<?php
$addrId = (isset($userAddress->id))?$userAddress->id:0;
$type = (isset($userAddress->type))?$userAddress->type:'';
$name = (isset($userAddress->name))?$userAddress->name:'';
$phone = (isset($userAddress->phone))?$userAddress->phone:'';
$pincode = (isset($userAddress->pincode))?$userAddress->pincode:'';
$address = (isset($userAddress->address))?$userAddress->address:'';
$locality = (isset($userAddress->locality))?$userAddress->locality:'';
$state = (isset($userAddress->state))?$userAddress->state:'';
$city = (isset($userAddress->city))?$userAddress->city:'';

//$company_name = (isset($userAddress->company_name))?$userAddress->company_name:'';
//$last_name = (isset($userAddress->last_name))?$userAddress->last_name:'';
//$country = (isset($userAddress->country))?$userAddress->country:'';


?>
<form name="addressForm" method="POST">
	{{csrf_field()}}

	<input type="hidden" name="address_id" value="{{$addrId}}">

	<div class="addaddress formbox">
		<ul>

			<li class="fullwidth">
				<span>Name<cite>*</cite></span>
				<span>
					<input type="text" name="name" value="{{old('name', $name)}}" class="inputfild" >
				</span>
			</li> 

			<li>
				<span>Mobile<cite>*</cite></span>
				<span>
					<input type="text" name="phone" value="{{old('phone', $phone)}}" class="inputfild" >
				</span>
			</li>
			
			<li>
				<span>Pincode<cite>*</cite></span>
				<span>
					<input type="text" name="pincode" value="{{old('pincode', $pincode)}}" class="inputfild" />
				</span>
			</li>

			<li class="fullwidth fulladd">
				<span>Address (House No, Building, Street, Area) <cite>*</cite></span>
				<span>
					<textarea name="address" class="inputfild">{{old('address', $address)}}</textarea>
				</span>
			</li>
			
			<li class="threesec">
				<span>Landmark</span>
				<span>
					<input type="text" name="locality" value="{{old('locality', $locality)}}" class="inputfild" />
					 @include('snippets.front.errors_first', ['param' => 'locality'])
				</span>
			</li>


			<li class="threesec">
				<span>State<cite>*</cite></span>
				<span>
					<select name="state" class="inputfild">
						<option value="">--Select--</option>

						<?php

						if(!empty($states) && count($states) > 0){
							foreach($states as $st){
								$selected = '';
								if($st->id == $state){
									$selected = 'selected';
								}
								?>
								<option value="{{$st->id}}" {{$selected}} >{{$st->name}}</option>
								<?php
							}
						}
						?>
					</select>

				</span>
			</li>


			<li class="threesec">
				<span>City / District<cite>*</cite></span>
				<span>
					<select name="city" class="inputfild">
						<option value="">--Select--</option>
					</select>

				</span>
			</li>

			

			<?php /*?><li>
				<span>Country<cite>*</cite></span>
				<span>
					<select name="country" class="inputfild" >
						<option value="99">India</option>
					</select>

				</span>
			</li><?php */?>

			<input type="hidden" name="country" value="99">


			<li class="addresstype"><span>Type of Address<cite>*</cite></span>
				<span>
					<input type="radio" name="type" value="home" <?php echo ($type == 'home')?'checked':'';?> /> Home
					&nbsp;&nbsp;
					<input type="radio" name="type" value="office" <?php echo ($type == 'office')?'checked':'';?> /> Office/Commercial
				</span>
			</li>

			<li class="deliveryTime" style="display:none;">*Delivery will be 10 am to 6 Pm.</li>

		</ul>


	</div>

	<div class="formbox">
		<button class="savebtn saveAddrBtn">Save &amp; Continue</button>
		<button type="reset" class="resetbtn">Reset</button>
	</div>

</form>
