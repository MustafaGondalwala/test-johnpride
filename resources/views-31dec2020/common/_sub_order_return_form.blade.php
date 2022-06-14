
<form name="orderReturnForm" method="POST">
	{{csrf_field()}}

	<input type="hidden" name="order_id" value="{{$order_id}}">

	<div class="addaddress formbox">
		<ul>

			<li class="fullwidth">

				<?php
				$reasonData = [];
				if($order->payment_method == 'Wallet'){
					$reasonData = ['Wallet'];
				}
				elseif($order->payment_method == 'ccavenue'){
					$reasonData = ['Refund in Wallet','Original mode'];
				}
				elseif ($order->payment_method == 'cod') {
					$reasonData = ['Refund in Wallet','Bank Account'];
				}
				
				$reasonsArr = config('custom.reason_order_return_arr');
				//pr($reasonData);
				?>

				<li class="fullwidth fulladd">
					<span class="form-group">Reason<cite>*</cite></span>
					<span class="form-group">
						<select class="inputfild" name="reason" id="reason">
							<option value="">Select</option>
							<?php
							foreach ($reasonsArr as $key => $value) {
								?>
								<option value="{{$key}}">{{$value}}</option>
								<?php 
							}
							?>
							
						</select>
					</span>
				</li>
				
				<li class="fullwidth fulladd">
					<span >Refund Mode<cite>*</cite></span>
					<span class="form-group">
					<select class="inputfild" name="refund_mode" id="return_refund_mode">
						<option value="">Select</option>
						<?php
						foreach ($reasonData as $reason) {
							?>
							<option value="{{$reason}}">{{$reason}}</option>
							<?php 
						}
						?>
						
					</select>
				</span>
				</li>

		

			<li class="fullwidth fulladd" id="comment_show" style="display: none;">
				<span>Remark<cite></cite></span>
				<span class="form-group">
					<textarea name="reason_comment" class="inputfild">{{old('reason_comment')}}</textarea>
				</span>
			</li>

			<li class="fullwidth fulladd" id="return_refund_mode_show" style="display: none;">
				<span>Bank Detail<cite>*</cite></span>
				<span class="form-group">
					<textarea style="min-height: 100px;" name="bank_details" class="inputfild" placeholder="">Beneficiary Name: 
						Bank Name: 
						A/c No.: 
						IFSC: 
						{{old('bank_details')}}
					</textarea>
				</span>
				<span><cite>*</cite> All the details verified by you and that will be final.</span>
				<span><cite>*</cite> Amount will be transferred if you have already paid.</span>
			</li>
			
		
		</ul>


	</div>


	<div class="formbox">
		<button class="savebtn saveReturnOrderBtn">Save</button>
	</div>

</form>

<script type="text/javascript">

	$('#reason').on('change',function(){
			var reason = $(this).val();
			//alert(reason);
			comment_show_hide(reason);
		});

	function comment_show_hide(reason){
		
		if(reason == 'remark'){
			$('#comment_show').show('slow');
		}

		else{
			$('#comment_show').hide('hide');
		}
	}

	$('#return_refund_mode').on('change',function(){
		var return_refund_mode = $(this).val();
			//alert(reason);
			return_refund_mode_show_hide(return_refund_mode);
		});

	function return_refund_mode_show_hide(return_refund_mode){
		
		if(return_refund_mode == 'Bank Account'){
			$('#return_refund_mode_show').show('slow');
		}

		else{
			$('#return_refund_mode_show').hide('hide');
		}
	}
	
</script>

