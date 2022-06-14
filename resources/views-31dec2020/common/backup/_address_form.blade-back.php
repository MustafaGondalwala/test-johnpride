<?php
$addrId = (isset($userAddress->id))?$userAddress->id:0;
$type = (isset($userAddress->type))?$userAddress->type:'';
$first_name = (isset($userAddress->first_name))?$userAddress->first_name:'';
$last_name = (isset($userAddress->last_name))?$userAddress->last_name:'';
$company_name = (isset($userAddress->company_name))?$userAddress->company_name:'';
$phone = (isset($userAddress->phone))?$userAddress->phone:'';
$address = (isset($userAddress->address))?$userAddress->address:'';

$country = (isset($userAddress->country))?$userAddress->country:'';
$state = (isset($userAddress->state))?$userAddress->state:'';
$city = (isset($userAddress->city))?$userAddress->city:'';
$pincode = (isset($userAddress->pincode))?$userAddress->pincode:'';
?>
<form name="addressForm" method="POST">
	{{csrf_field()}}

	<input type="hidden" name="address_id" value="{{$addrId}}">

	<div class="addaddress formbox">
		<ul>

			<li>
				<span>First Name<cite>*</cite></span>
				<span>
					<input type="text" name="first_name" value="{{old('first_name', $first_name)}}" class="inputfild" >
				</span>
			</li>

			<li>
				<span>Last Name<cite>*</cite></span>
				<span>
					<input type="text" name="last_name" value="{{old('last_name', $last_name)}}" class="inputfild" >
				</span>
			</li>

			<li>
				<span>Business Name</span>
				<span>
					<input type="text" name="company_name" value="{{old('company_name', $company_name)}}" class="inputfild" >
				</span>
			</li>

			<li>
				<span>Telephone<cite>*</cite></span>
				<span>
					<input type="text" name="phone" value="{{old('phone', $phone)}}" class="inputfild" >
				</span>
			</li>

			<li class="fullwidth">
				<span>Address (House No, Building, Street, Area) <cite>*</cite></span>
				<span>
					<textarea name="address" class="inputfild">{{old('address', $address)}}</textarea>
				</span>
			</li>

			<li>
				<span>State / Province<cite>*</cite></span>
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


			<li>
				<span>City<cite>*</cite></span>
				<span>
					<select name="city" class="inputfild">
						<option value="">--Select--</option>
					</select>

				</span>
			</li>

			<li>
				<span>Pincode<cite>*</cite></span>
				<span>
					<input type="text" name="pincode" value="{{old('pincode', $pincode)}}" class="inputfild" />
				</span>
			</li>

			<li>
				<span>Country<cite>*</cite></span>
				<span>
					<select name="country" class="inputfild" >
						<option value="99">India</option>
					</select>

				</span>
			</li>


			<li class="addresstype"><span>Type of Address<cite>*</cite></span>
				<span>
					<input type="radio" name="type" value="home" <?php echo ($type == 'home')?'checked':'';?> /> Home
					&nbsp;&nbsp;
					<input type="radio" name="type" value="office" <?php echo ($type == 'office')?'checked':'';?> /> Office/Commercial
				</span>
			</li>

		</ul>


	</div>

	<div class="formbox"><button class="savebtn saveAddrBtn">Save</button> </div>

</form>
