<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo (isset($meta_title))?$meta_title:'Johnpride'?></title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="index, follow"/>
    <meta name="robots" content="noodp, noydir"/>

    @include('common.head')

</head>

<body>

    @include('common.header')


	<?php
	
	$user = auth()->user();

	$userAddresses = $user->userAddresses;

	if(!empty($userAddresses) && count($userAddresses) > 0){
		//pr($userAddresses->toArray());
	}

	$userAddr = '';

	$addrId = (request('id'))?request()->id:0;

	if(is_numeric($addrId) && $addrId > 0){
		$userAddr = $userAddresses->where('id', $addrId)->first();
	}

	if(!empty($userAddr) && count($userAddr) > 0){
		//pr($userAddr->toArray());
	}


	$type = (isset($userAddr->type))?$userAddr->type:'';
	$name = (isset($userAddr->name))?$userAddr->name:'';
	$company_name = (isset($userAddr->company_name))?$userAddr->company_name:'';
	$phone = (isset($userAddr->phone))?$userAddr->phone:'';
	$address = (isset($userAddr->address))?$userAddr->address:'';

	$country = (isset($userAddr->country))?$userAddr->country:'';
	$state = (isset($userAddr->state))?$userAddr->state:'';
	$city = (isset($userAddr->city))?$userAddr->city:'';
	$pincode = (isset($userAddr->pincode))?$userAddr->pincode:'';

	?>
 

    <section class="fullwidth innerpage">
         <div class="container">

          @include('users.nav')

			<div class="rightcontent">
			 <div class="main_inner_box">
				<div class="heading2">Address <a href="JavaScript:Void(0)" class="addrBtn addnew">Add New +</a></div>
			

					@include('snippets.front.flash')

					<?php
					if(!empty($userAddresses) && count($userAddresses) > 0 && (empty($userAddr) || count($userAddr) == 0) ){

						?>
						<div class="selectadd">
							<ul>

								<?php
								foreach($userAddresses as $ua){

								//prd($ua->toArray());

									$addressArr = CustomHelper::formatUserAddress($ua);

									$name = $ua->name;

									$isDefault = ($ua->is_default == 1)?'(Default)':'';

									$checked = '';

									if($ua->is_default == '1'){
										$checked = 'checked';
									}
									?>
									<li>
										<div class="addselectbox">

											<div class="addlist">
												<a href="{{url('users/address/'.$ua->id)}}" data-id="{{$ua->id}}" class="edit-link addrBtn"><i class="editicon"></i></a>
												<h3 class="title3">Shipping Detail</h3>
												<p>
													<!-- <input type="radio" name="shppingAddr" value="{{$ua->id}}" {{$checked}}> -->
													<strong>{{$name}} {{$isDefault}}</strong>
												</p>
												<p>
													<?php
													if(!empty($addressArr) && count($addressArr) > 0){
														echo implode(', ', $addressArr); 
													}
													?>
												</p>

												<p>Mobile. {{$ua->phone}}</p>
											</div>

											<?php
										/*
										<div class="cashondelivery">
											Cash on delivery available Return pick up available
										</div>
										*/
										?>
									</div>
								</li>
								<?php
							}
							?>

							
						</ul>
					</div>
					<?php

				}
				else{
					?>

					<div class="formBox">
						@include('common._address_form')
					</div>

					<?php
				}
				?>



			</div>
			 </div>


		</div>
</section>

@include('common.footer')

@include('common._address_form_js')


	<!-- Address Modal -->
	<div id="addressModal" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				</div>
				<div class="modal-body">
					<p>Some text in the modal.</p>
				</div>
				 
			</div>

		</div>
	</div>

	<!-- End - Address Modal -->


	<script type="text/javascript" src="{{url('public')}}/js/bootstrap.min.js"></script> 



	<script type="text/javascript">
		

		$(".placeOrderBtn").click(function(){
			var shppingAddr = $("input[name=shppingAddr]:checked").val();

			shppingAddr = parseInt(shppingAddr);
			//alert(isDefault);

			if(!isNaN(shppingAddr) && shppingAddr > 0){
				//return true;
			}
			else{
				alert("please add or select address for delivery.");
				return false;
			}
			$("form[name=placeOrderForm]").submit();
		});

		$(document).on("click", "input[name=type]", function(){
			if($(this).val() == 'office'){
				$(".deliveryTime").show();
			}
			else{
				$(".deliveryTime").hide();
			}
		});
	</script>

<!-- <script type="text/javascript">
    $(".hidePwdBox").click(function(){
        $(".change-pwd").hide();
        $(".change-pwd-link ").show();
    });
    $(".showPwdBox").click(function(){
        $('.change-pwd').show();
        $('.change-pwd-link').hide();
    });
</script> -->

</body>
</html>