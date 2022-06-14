<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{$meta_title}}</title>
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="robots" content="index, follow"/>
  <meta name="robots" content="noodp, noydir"/>
  @include('common.head')

</head>
<body>
  @include('common.user_header')
  <section>
    <div class="contentArea">

      @include('common.left_menu')

      <div class="rightBar">
        <div class="tableArea container-custom">
          <div class="panel panel-default">
            <div class="topHeading panel-heading"> <span></span><span></span><span></span>{{trans('custom.shipping_address')}} </div>    

            <div class="welcomeContent panel-body noPaddings cartpage">
				<div class="paddingbox">
              <?php if(!empty($delivery_address)) { ?>
              <div class="row">
              <div class="col-md-12">
                    <h2>{{trans('custom.your_saved_address')}}</h2>
              </div>
			</div>
				<div class="row">  
            
              <?php foreach($delivery_address as $del_add) 
              {
                //pr($del_add);
                 
                ?>
               <div class=" col-xs-12 col-sm-6 col-md-6 col-lg-4">
			  <div class="shipping_address_part">
              <?php 
                if(!empty($del_add->company_name))
                {
                   echo '<strong>'.$del_add->company_name.'</strong>'."<br>";
                }
                if(!empty($del_add->is_gst_aadhar))
                {
                  if($del_add->is_gst_aadhar == 'gst' && !empty($del_add->gst)){
                     echo "GST: ".$del_add->gst."<br>";
                  }
                  elseif($del_add->is_gst_aadhar == 'aadhar_number' && !empty($del_add->aadhar_number)){
                    echo "Aadhaar: ".$del_add->aadhar_number."<br>";
                  }                   
                  
                }
                if(!empty($del_add->name))
                {
                   echo $del_add->name."<br>"; 
                }  
                 if(!empty($del_add->address_1))
                {
                   echo $del_add->address_1.",<br>"; 
                }  
                 if(!empty($del_add->address_2))
                {
                   echo $del_add->address_2."<br>"; 
                }  
                if(!empty($del_add->city))
                {
                    echo get_by_id($id= $del_add->city, $id_name='id', $table='cities', $field='name').",";  ;

                }  
                if(!empty($del_add->state))
                {
                    echo get_by_id($id= $del_add->state, $id_name='id', $table='states', $field='name') ; 
                }  
                
                if(!empty($del_add->zipcode))
                {
                   echo '-'.$del_add->zipcode."<br>"; 
                }

                 if(!empty($del_add->phone))
                {
                   echo 'Phone: '.$del_add->phone."<br>"; 
                }
                ?>
				<br/>
                <button onclick="copy_address(<?php echo $del_add->id;?>)" type="button" class="btn btn-primary deliver_here">{{trans('custom.deliver_here')}}</button>
                <button onclick="edit_address(<?php echo $del_add->id;?>)" type="button" class="btn btn-default">{{trans('custom.edit')}}</button>          
				</div>
              </div>
              <?php } ?>

              
               </div>
               <?php } ?>

              
                <?php 
                if(count($cartItems) > 0){
                  ?>
                  
					
                    <?php
                    /*
                    <button class="btn removeBtn "> Remove</button>
                    <button class="btn btn-default"> Review and Order</button>
                    */
                    ?>



                      

                    <form method="POST" name="shipping_address_form" id="shipping_address_form" action="{{url('cart/checkout')}}" onsubmit="return validate_shipping_address()">
                      {{ csrf_field() }}

                      <?php
                     
                      //pr($default_address);

                      $gst= (!empty($default_address->gst))?$default_address->gst:'';
                      $aadhar_number= (!empty($default_address->aadhar_number))?$default_address->aadhar_number:'';
                      $company_name= (!empty($default_address->company_name))?$default_address->company_name:'';

                      $name= (!empty($default_address->name))?$default_address->name:'';
                      $phone= (!empty($default_address->phone))?$default_address->phone:'';
                      $address_1= (!empty($default_address->address_1))?$default_address->address_1:'';
                      $address_2= (!empty($default_address->address_2))?$default_address->address_2:'';

                      $d_city= (!empty($default_address->city))?$default_address->city:'';
                      $d_state= (!empty($default_address->state))?$default_address->state:'';
                      $zipcode= (!empty($default_address->zipcode))?$default_address->zipcode:'';

                     /* $gst_selected = '';
                      $aadhaar_selected = '';

                      if(!empty($gst)){
                        $gst_selected = 'selected';
                      }
                      elseif(!empty($aadhar_number)){
                        $aadhaar_selected = 'selected';
                      }*/

                      $is_gst_aadhar = (isset($default_address->is_gst_aadhar ))?$default_address->is_gst_aadhar :'';

                      $gst_aadhar_num = (!empty($default_address->{$is_gst_aadhar}))?$default_address->{$is_gst_aadhar}:'';
                      $gst_aadhar_type = $is_gst_aadhar;

                      $gst_aadhar_num = (!empty($is_gst_aadhar))?$default_address->{$is_gst_aadhar}:'';

                      $gst_aadhar_type = old('gst_aadhar_type', $gst_aadhar_type);

                      ?>

                      <div class="row">

                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6 padding-0">

                        <div class="checkout-box">

                        
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="delivery-name" class="control-label">{{trans('custom.gst_aadhaar')}}*:</label>

                        <?php
                        /*
                        <select name="delivery_type" class="form-control">
                          <option value="">--Select--</option>
                          <option value="billing_gst" {{ $gst_selected }} >GST</option>
                          <option value="billing_aadhar_number" {{ $aadhaar_selected }} >Aadhaar</option>
                        </select>
                        */
                        ?>

                        

                        <select name="gst_aadhar_type" class="form-control  form-control-input">
                          <option value="">--{{trans('custom.select')}}--</option>
                          <option value="gst" <?php echo ($gst_aadhar_type == 'gst')?'selected':'selected';?> >{{trans('custom.gst')}}</option>
                          <option value="aadhar_number" <?php echo ($gst_aadhar_type == 'aadhar_number')?'selected':'';?> >{{trans('custom.aadhaar')}}</option>
                        </select>

                                <?php
                                /*
                                <input id="delivery_gst" class="form-control" name="delivery_gst"  maxlength="255" value="<?php echo $gst; ?>" type="text">
                                */
                                ?>
                        </div>

                       <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="gst_aadhar_num" class="control-label">{{trans('custom.gst_aadhaar_no')}}*:</label>

                        <input id="gst_aadhar_num" class="form-control  form-control-input" name="gst_aadhar_num" value="" maxlength="255" type="text">

                        <?php
                                /*
                                <input id="delivery_aadhar_number" class="form-control" name="delivery_aadhar_number"  maxlength="255" value="<?php echo $aadhar_number; ?>" type="text">
                                */
                                ?>

                                
                        </div>
					 
						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="delivery-name" class="control-label required">{{trans('custom.shipping_name')}}:</label>

                                <input id="delivery_company_name" class="form-control  form-control-input" name="delivery_company_name"  maxlength="255" value=""  type="text">
                        </div>

						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="delivery-name" class="control-label required">{{trans('custom.contact_name')}}*:</label>

                                <input id="delivery_name" class="form-control  form-control-input" name="delivery_name"  maxlength="255" value=""  type="text">
                        </div>

                      


						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="delivery-address-1" class="control-label required">{{trans('custom.address_line_1')}}*:</label>

							<input id="delivery_address_1" class="form-control  form-control-input" name="delivery_address_1" value="" maxlength="255" type="text">
						</div>

						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="delivery-address-2" class="control-label ">{{trans('custom.address_line_2')}}*:</label>

							<input id="delivery_address_2" class="form-control  form-control-input" name="delivery_address_2" value="" maxlength="255" type="text">

							<?php
							/*
							<p class="help-block">Apt. / Ste.</p>
							*/
							?>
						</div>
							
						
						 
						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="delivery-state" class="control-label required1">{{trans('custom.state')}}*:</label>

							<select id="delivery_state" class="state form-control delivery_state  form-control-input" name="delivery_state" data-type="delivery" >
							<option value="" selected="">--{{trans('custom.select')}}--</option>
									<?php
							if(count($states) > 0)
											{
													foreach($states as $state){
													  //$state_sel = '';

													  ?>
													  <option value="{{$state->id}}" >{{$state->name}}</option>
													  <?php
													}
											 }
									?>
							</select>

						  </div>

						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="delivery-city" class="control-label required">{{trans('custom.city')}}*:</label>

							<select id="delivery_city" name="delivery_city" class="delivery_city form-control  form-control-input">
							<option value="">--{{trans('custom.select')}}--</option>
							 <?php
								/*if(count($cities) > 0){
								  foreach($cities as $city){
									$city_sel = '';

									?>
									<option value="{{$city->id}}" > 
									{{$city->name}}</option>
									<?php
								  }
								}*/
							  ?>

							</select>                                

						  </div>
						 
						
						 
						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="delivery-zipcode" class="control-label required">{{trans('custom.postal_code')}}:</label>

							<input id="delivery_zipcode" class="form-control  form-control-input" name="delivery_zipcode" value="" maxlength="20" type="text">

						 </div>

						<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="delivery-phone" class="control-label">{{trans('custom.phone_number')}}*:</label>

							<input id="delivery_phone" class="form-control  form-control-input" name="delivery_phone" value="" maxlength="20" type="text">

						</div>
						 

                        </div>

                    </div>

                    

                </div>

                 <div class="bbtndiv">

                 <input type="hidden" name="is_new_shipping_addr" value="1">

                     <button type="submit" class="btn btn-default deliver_new_addr">{{trans('custom.deliver_here')}}</button>
                  </div>  

                    </form>
                     


                  <?php
                }
                else{
                  ?>
					<div class="col-md-12">
                  <p>{{trans('custom.your_cart_is_empty')}}</p>
                  <p><a href="{{url('products/feeds')}}">{{trans('custom.click_here_to_shop')}}</a></p>
						 </div>
                  <?php
                }
                ?>


             
            </div>
				</div>
            <!-- <a href="index.php" class="btnNext btn btn-default" ><i class="whites fa fa-angle-right" aria-hidden="true"></i> </a> </div> -->
          </div>
        </div>
      </div>
    </section>

    @include('common.footer')

    <script src="{{url('public/assets')}}/js/function.js"></script>
    <script src="{{url('public/assets')}}/js/load_spinner.js"></script>

    <script type="text/javascript">
      function validate_place_order()
      {
        conf = confirm('Are you sure to place this Order?');
        if(conf)
        {

        }
      }
    </script>

    <script type="text/javascript">
    /*state_id = '{{$d_state}}';
    city_id = '0';*/

    city_id = 0;
    
    $(".delivery_state").on("change", function()
    {
      state_id = $(this).val();
      //alert(state_id);
      load_cities(state_id, city_id);
    });


    function load_cities(state_id, city_id)
    {
      _token = '{{csrf_token()}}';
       $.ajax({
        url: "{{url('common/ajax_load_cities')}}",
        type: "POST",
        data: {state_id, city_id},
        dataType:"JSON",
        headers:{'X-CSRF-TOKEN': _token},
        cache: false,
        async: false,
        beforeSend:function(){},
        success: function(resp){
          if(resp.success){
            $(".delivery_city").html(resp.options);
          }
        }
      });
    }


    function copy_address(address_id)
    {
        _token = '{{csrf_token()}}';
        $.ajax({
        url: "{{url('common/get_address')}}",
        type: "POST",
        data: {address_id, address_id},
        //dataType:"JSON",
        headers:{'X-CSRF-TOKEN': _token},
        cache: false,
        aysnc: false,
        beforeSend:function(){
          loadSpinner($(".deliver_here"));
        },
        success: function(resp)
        {
            var res=JSON.parse(resp);
            if(res.status==1)
            {
              removeSpinner($(".deliver_here"));

                //alert(res.res.name); 
                $('#delivery_gst').val(res.res.gst);
                $('#delivery_aadhar_number').val(res.res.aadhar_number);
                $('#delivery_company_name').val(res.res.company_name);
                $('#delivery_name').val(res.res.name);
                $('#delivery_address_1').val(res.res.address_1); 
                $('#delivery_address_2').val(res.res.address_2);
                $('#delivery_state').val(res.res.state); 
                var state=res.res.state;
                var city= res.res.city;
                load_cities(state, city);
                $('#delivery_zipcode').val(res.res.zipcode); 
                $('#delivery_phone').val(res.res.phone);

                is_gst_aadhar = res.res.is_gst_aadhar;

                if(is_gst_aadhar != ''){
                  $("[name='gst_aadhar_type']").find("[value='"+is_gst_aadhar+"']").prop('selected', true);
                   $("[name='gst_aadhar_num']").val(res.res[is_gst_aadhar]);
                }

                $("[name='is_new_shipping_addr']").val("0");

               /* if(res.res.gst.trim() != ''){
                  $("[name='delivery_type']").find("[value='billing_gst']").prop('selected', true);
                  $("[name='delivery_type_num']").val(res.res.gst);
                }
                else if(res.res.is_gst_aadhar.trim() != ''){
                  $("[name='gst_aadhar_type']").find("[value='"+is_gst_aadhar+"']").prop('selected', true);
                   $("[name='delivery_type_num']").val(res.res.aadhar_number);
                }
                else{
                  $("[name='delivery_type']").find("option").prop('selected', false);
                   $("[name='delivery_type_num']").val('');
                }*/

                $("#shipping_address_form").submit();
                
            }
        }
      });


    }

    /*$(".deliver_new_addr").click(function(){
      $("input[name='is_new_shipping_addr']").val('1');
    });*/


    function validate_shipping_address(){

      is_submit = false;

      _token = '{{csrf_token()}}';
      $.ajax({
        url: "{{ url('cart/validate_shipping_address') }}",
        type: "POST",
        data: $("#shipping_address_form").serialize(),
        dataType:"JSON",
        headers:{'X-CSRF-TOKEN': _token},
        cache: false,
        aysnc: false,
        beforeSend:function(){
          loadSpinner($(".deliver_new_addr"));

          $("#shipping_address_form").find(".help-block").remove();
          $(".form-group").removeClass("has-error");
          //$("input[name='is_new_shipping_addr']").val('0');
        },
        success: function(resp){
          if(resp.success){
            document.shipping_address_form.submit();
          }
          else if(resp.errors){
            var errTag;
            var countErr = 1;
            $.each(resp.errors, function(i, val){

              removeSpinner($(".deliver_new_addr"));

              $("#shipping_address_form").find("[name='"+i+"']").parent(".form-group").addClass("has-error");
              $("#shipping_address_form").find("[name='"+i+"']").parent(".form-group").append('<p class="help-block">'+val+'</p>');

              if(countErr == 1){
                errTag = $( "#shipping_address_form" ).find( "[name='" + i + "']" );
              }
              countErr++;
            });

            if(errTag){
              errTag.focus();
            }

            //$("input[name='is_new_shipping_addr']").val('1');
          }
        }
      });

      return is_submit;

    }

    function edit_address(address_id){
      window.location = "{{ url('addresses/edit') }}/"+address_id+"?back_url=cart/shippingaddress";
    }


   </script>
  </body>
  </html>
