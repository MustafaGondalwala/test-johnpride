<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')


<?php
$state = old('state');
$city = old('city');
?>
			
	
<section class="fullwidth logpage">	
	<div class="container">
		<h2>Join</h2>

			@include('snippets.errors')
			@include('snippets.flash')
		<div class="regform">
			<form name="registerForm" method="POST" enctype="multipart/form-data" >

				{{csrf_field()}}
				

				<ul>

				<li class="{{ $errors->has('type') ? ' has-error' : '' }} fullwidth">
						<!--<label>Customer/Designer *</label>-->
						<?php
						$selected_type = old('type', 'customer');
						if(!empty($Roles) && count($Roles) > 0){
							foreach($Roles as $Role){
								$checked = '';
								if($selected_type == $Role->name){
									$checked = 'checked';
								}
								?>
								<label class="cuslabel">
									<input type="radio" name="type" value="{{$Role->name}}" <?php echo $checked; ?> >
									<strong>{{$Role->display_name}}</strong>
								</label>
								<?php
							}
						}


						/*
						<label class="cuslabel"><input type="radio" checked=""  name="type" value="customer"> <strong>Customer</strong></label>
						<label class="cuslabel"><input type="radio" name="type" value="designer"> <strong>Designer</strong></label>
						*/
						?>
						
						
					</li>
					
					

					<li class="{{ $errors->has('first_name') ? ' has-error' : '' }}">
						<label>First Name*</label>
						<input type="text" name="first_name" value="{{old('first_name')}}" class="inputfild"/>
						<!--@include('snippets.errors_first', ['param' => 'first_name'])-->
					</li>

					<li class="{{ $errors->has('last_name') ? ' has-error' : '' }}">
						<label>Last Name*</label>
						<input type="text" name="last_name" value="{{old('last_name')}}" class="inputfild"/>
						<!--@include('snippets.errors_first', ['param' => 'last_name'])-->
					</li>

					<li class="{{ $errors->has('email') ? ' has-error' : '' }}">
						<label>Email*</label>
						<input type="text" name="email" value="{{old('email')}}" class="inputfild"/>
						<!--@include('snippets.errors_first', ['param' => 'email'])-->
					</li>

					<li class="{{ $errors->has('phone') ? ' has-error' : '' }}">
						<label>Phone*</label>
						<input type="text" name="phone" value="{{old('phone')}}" class="inputfild"/>
						<!--@include('snippets.errors_first', ['param' => 'phone'])-->
					</li>

					<li class="{{ $errors->has('password') ? ' has-error' : '' }}">
						<label>Password*</label>
						<input type="password" name="password" value="" class="inputfild"/>
						<!--@include('snippets.errors_first', ['param' => 'password'])-->
					</li>

					<li class="{{ $errors->has('confirm_password') ? ' has-error' : '' }}">
						<label>Confirm Password*</label>
						<input type="password" name="confirm_password" value="" class="inputfild"/>
						<!--@include('snippets.errors_first', ['param' => 'confirm_password'])-->
					</li>

					<li>
						<label>Country</label>
						<select name="country" class="inputfild">
							<option value="99">India</option>
						</select>
					</li>
					<li>
						<label>State</label>

						<select name="state" id="state" class="inputfild">
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

					</li>

					<li>
						<label>City</label>

						<select name="city" class="inputfild">
							<option value="">--Select--</option>
						</select>
					</li>

					<li class="{{ $errors->has('student_id_proof') ? ' has-error' : '' }} student_id_box" style="display:none;">
						<label>Upload (Student ID Proof)</label>
						<input type="file" name="student_id_proof" class="inputfild">
					</li>

					<li class="captchadiv{{ $errors->has('password') ? ' has-error' : '' }}" >
						<label class="notext">&nbsp;</label>
						 
						<span class="captchainput">
							<small><img src="<?php echo captcha_src('custom'); ?>" > <a href="javascript:void(0)" class="change_captcha">&#128472;</a></small>
							<input type="text" name="captcha" class="inputfild enquiry-captchar " placeholder="Security code*">
						<!--@include('snippets.errors_first', ['param' => 'captcha'])-->
						</span>
					</li>
					
					<li class="fullsec"><input class="loginbtn" type="submit" value="Join" /></li>
				</ul>
			 
			</form>

		</div> 
	</div>
</section>

@include('common.footer')

<script type="text/javascript">
    state_id = '{{ $state }}';
    city_id = '{{ $city }}';

    if(state_id && state_id != ""){
        load_cities( state_id, city_id );
    }

    $(document).on("change", "select[name='state']", function () {
        state_id = $( this ).val();
        load_cities( state_id, city_id );
    } );

    function load_cities( state_id, city_id ) {

        var _token = '{{csrf_token()}}';

        $.ajax( {
            url: "{{url('common/ajax_load_cities')}}",
            type: "POST",
            data: {state_id: state_id, city_id: city_id},
            dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': _token
            },
            cache: false,
            beforeSend: function () {},
            success: function ( resp ) {
                if ( resp.success ) {
                    $("select[name='city']").html( resp.options );
                }
            }
        } );
    }

    $('.change_captcha').on('click', function(e){
    	e.preventDefault();

    	var anchor = $(this);
    	var captcha = anchor.prev('img');

    	var _token = '{{csrf_token()}}';

    	$.ajax( {
            url: "{{url('common/ajax_regenerate_captcha')}}",
            type: "POST",
            data: {},
            dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': _token
            },
            cache: false,
            beforeSend: function () {},
            success: function ( resp ) {
                if ( resp.success ) {
                	if(resp.captcha_src){
                		captcha.attr('src', resp.captcha_src);
                	}
                }
            }
        } );
    });

    $('input[type=radio][name=type]').change(function() {
    	checkIfStudent();
    });
    
    checkIfStudent();

    function checkIfStudent(){
    	var type = $('input[type=radio][name=type]:checked').val();
    	//console.log("type="+type);

    	if(type == 'student'){
    		$(".student_id_box").show();
    	}
    	else{
    		$(".student_id_box").hide();
    	}
    }
</script>
 
</body>
</html>