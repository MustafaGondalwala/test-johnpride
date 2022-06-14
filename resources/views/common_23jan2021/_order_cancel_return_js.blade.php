<script type="text/javascript">
	
	$(document).on("click", ".btnCancel", function(e){
			e.preventDefault();

			var order_id = $(this).attr('data-id');

			//console.log("addressId="+addressId);

			getOrderCancelForm(order_id);
		});

		function getOrderCancelForm(order_id) {

			var cancelOrderModal = $("#cancelOrderModal");

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('users/get_order_cancel_form')}}",
				type: "POST",
				data: {order_id: order_id},
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function(){},
				success: function(resp){
					if(resp.success) {
						//cancelOrderModal.find(".modal-title").html(resp.title);
						cancelOrderModal.find(".modal-body").html(resp.htmlData);

						cancelOrderModal.modal("show");
					}
				}
			});
		}

		$(document).on("click", ".saveCancelOrderBtn", function(e){
			e.preventDefault();

			var currSel = $(this);

			var cancelOrderModal = $("#cancelOrderModal");

			var orderCancelForm = $("form[name=orderCancelForm]");
			orderCancelForm.find(".form-group").removeClass( "has-error" );
			orderCancelForm.find(".help-block").remove();

			var _token = '{{ csrf_token() }}';

			$.ajax({
				url: "{{ url('users/ajax_cancel_order') }}",
				type: "POST",
				data: orderCancelForm.serialize(),
				dataType:"JSON",
				headers:{'X-CSRF-TOKEN': _token},
				cache: false,
				beforeSend:function(){
					orderCancelForm.find(".form-group").removeClass( "has-error" );
					orderCancelForm.find(".help-block").remove();
				},
				success: function(resp){
					if(resp.success) {
						//$("#sccMsg").html('<div class="alert alert-success"> Your Request has been submitted. </div>');

						document.orderCancelForm.reset();
						//cancelOrderModal.modal("close");
						window.location.reload();
					}
					else if(resp.errors){

						var errTag;
						var countErr = 1;

						$.each( resp.errors, function ( i, val ) {

							orderCancelForm.find( "[name='" + i + "']" ).parents(".form-group").addClass( "has-error" );
					orderCancelForm.find( "[name='" + i + "']" ).parents(".form-group").append( '<p class="help-block">' + val + '</p>' );

					if(countErr == 1){
						errTag = orderCancelForm.find( "[name='" + i + "']" );
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


$(document).on("click", ".btnReturn", function(e){
			e.preventDefault();

			var order_id = $(this).attr('data-id');

			//console.log("addressId="+addressId);

			getOrderReturnForm(order_id);
		});

		function getOrderReturnForm(order_id) {

			var returnOrderModal = $("#returnOrderModal");

			var _token = '{{csrf_token()}}';

			$.ajax({
				url: "{{url('users/get_order_return_form')}}",
				type: "POST",
				data: {order_id: order_id},
				dataType: "JSON",
				headers:{
					'X-CSRF-TOKEN': _token
				},
				cache: false,
				beforeSend: function(){},
				success: function(resp){
					if(resp.success) {
						//returnOrderModal.find(".modal-title").html(resp.title);
						returnOrderModal.find(".modal-body").html(resp.htmlData);

						returnOrderModal.modal("show");
					}
				}
			});
		}

		$(document).on("click", ".saveReturnOrderBtn", function(e){
			e.preventDefault();

			var currSel = $(this);

			var returnOrderModal = $("#returnOrderModal");

			var orderReturnForm = $("form[name=orderReturnForm]");
			orderReturnForm.find(".form-group").removeClass( "has-error" );
			orderReturnForm.find(".help-block").remove();

			var _token = '{{ csrf_token() }}';

			$.ajax({
				url: "{{ url('users/ajax_return_order') }}",
				type: "POST",
				data: orderReturnForm.serialize(),
				dataType:"JSON",
				headers:{'X-CSRF-TOKEN': _token},
				cache: false,
				beforeSend:function(){
					orderReturnForm.find(".form-group").removeClass( "has-error" );
					orderReturnForm.find(".help-block").remove();
				},
				success: function(resp){
					if(resp.success) {
						//$("#sccMsg").html('<div class="alert alert-success"> Your Request has been submitted. </div>');

						document.orderReturnForm.reset();
						//returnOrderModal.modal("close");
						window.location.reload();
						//window.location = "{{url('thankyou')}}";
					}
					else if(resp.errors){

						var errTag;
						var countErr = 1;

						$.each( resp.errors, function ( i, val ) {

							orderReturnForm.find( "[name='" + i + "']" ).parents(".form-group").addClass( "has-error" );
					orderReturnForm.find( "[name='" + i + "']" ).parents(".form-group").append( '<p class="help-block">' + val + '</p>' );

					if(countErr == 1){
						errTag = orderReturnForm.find( "[name='" + i + "']" );
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

</script>