<script type="text/javascript">
	
	var state_id = '{{ $state }}';
	var city_id = '{{ $city }}';

	var formBox = $(".formBox")

	if(state_id && state_id != ""){
		load_cities(state_id, city_id, formBox);
	}

		$(document).on("keyup", "input[name=pincode]", function () {

			var currSelector = $(this);
			var pincode = currSelector.val();

			//console.log('pincode='+pincode);

			pincodeCityState(pincode, currSelector);
		} );

		function pincodeCityState(pincode, currSelector){

			if(pincode.length == 6){

				var _token = '{{csrf_token()}}';

				$.ajax({
					url: "{{url('common/get_pincode_city_state')}}",
					type: "POST",
					data: {pincode: pincode},
					dataType: "JSON",
					headers:{
						'X-CSRF-TOKEN': _token
					},
					cache: false,
					beforeSend:function(){},
					success:function(resp ){
						if(resp.success){

							var stateId = resp.state_id;

							if(stateId && stateId != ''){

								currSelector.parents("form").find("select[name='state']").find('option[value='+stateId+']').prop("selected", true);

								currSelector.parents("form").find("select[name='city']").html(resp.citiesOptions);
							}
						}
					}
				});

			}
		}

		$(document).on("change", "select[name='state']", function () {
			state_id = $(this).val();
			load_cities(state_id, city_id, $(this).parents("form"));
		} );

		function load_cities(state_id, city_id, currSelector){

			console.log(currSelector);

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('common/ajax_load_cities')}}",
				type: "POST",
				data: {state_id: state_id, city_id: city_id},
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend:function(){},
				success:function(resp ){
					if(resp.success){
						/*if(currSelector){
							currSelector.find("select[name='city']").html(resp.options);
						}
						else{
							$("select[name='city']").html(resp.options);
						}*/
						$("select[name='city']").html(resp.options);
					}
				}
			});
		}


		$(document).on("click", ".addrBtn", function(e){
			e.preventDefault();

			var addressId = $(this).data("id");

			//console.log("addressId="+addressId);

			getAddressForm( addressId );
		});

		function getAddressForm(addressId) {

			var addressModal = $("#addressModal");

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('users/get_address_form')}}",
				type: "POST",
				data: {addressId: addressId},
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function(){},
				success: function(resp){
					if(resp.success) {
						addressModal.find(".modal-title").html(resp.title);
						addressModal.find(".modal-body").html(resp.htmlData);

						state_id = resp.stateId;
						city_id = resp.cityId;
						var countryId = resp.countryId;

						if(state_id && state_id != ""){
							load_cities(state_id, city_id, addressModal);
						}

						addressModal.modal("show");
					}
				}
			});
		}

		$(document).on("click", ".saveAddrBtn", function(e){
			e.preventDefault();

			var addressModal = $("#addressModal");

			var addressForm = $("form[name=addressForm]");

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('users/save_address')}}",
				type: "POST",
				data: addressForm.serialize(),
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function(){
					addressForm.find( ".help-block" ).remove();
					addressForm.find( ".has-error" ).removeClass( "has-error" );
				},
				success: function(resp){
					if(resp.success) {
						window.location.reload();
					}
					else if(resp.errors){

						var errTag;
						var countErr = 1;

						$.each( resp.errors, function ( i, val ) {

							addressForm.find( "[name='" + i + "']" ).parent().addClass( "has-error" );
							addressForm.find( "[name='" + i + "']" ).parent().append( '<p class="help-block">' + val + '</p>' );

							if(countErr == 1){
								errTag = addressForm.find( "[name='" + i + "']" );
							}
							countErr++;

						});

						if(errTag){
							errTag.focus();
						}
					}
				}
			});
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