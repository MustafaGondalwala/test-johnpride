<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
  
<section class="fullwidth innerheading">
  <div class="container">
     <h1 class="heading">Checkout</h1>
    <p><a href="{{url('/')}}">Home</a>  <a href="{{url('/cart')}}">Cart</a>   Checkout  </p>
  </div>
</section>  
<section class="fullwidth innerpage"> 
  <div class="container">

  <div class="row">
    
    
    <div class="col-md-12">

    <form method="post" action="{{url('cart/checkout')}}" >

    {{ csrf_field() }}

    <?php 
         
          if(!Cart::isEmpty())
          
          {
               $cart_items=Cart::getContent();

            ?>
		<div class="row">
           
           <div class="col-md-12">

           Returning Customer or create account? Click here to <a href="{{url('login')}}">login</a> or <a href="{{url('register')}}">Signup</a>
          
          </div>



          <div class="col-md-12">
          Is shipping info same as billing info?  <input type="checkbox" name="billing_shipping_same" <?php if (session()->has('user_address')) 
        { echo 'checked'; }
 ?> id="billing_shipping_same" value="1">
          </div>

           <div class="col-md-6">
           


          <table class="table">

          <tr><th colspan="2">Billing Info</th></tr>


          <tr><td>Billing First Name <span class="text-red">*</span> </td>
          <td><input type="text" name="billing_first_name" id="billing_first_name"  class="form-control" value="{{old('billing_first_name', $user_address->billing_first_name)}}"  >

           <span style="color: red;" class="text-red" id="billing_first_name_error">
           <?php 
           if ($errors->has('billing_first_name')) 
           {
              echo $errors->first('billing_first_name');

           } 
           ?>
             
           </span>


          </td>
          </tr>

          <tr><td>Billing Last Name <span class="text-red">*</span></td>
          <td><input type="text" name="billing_last_name" id="billing_last_name" class="form-control" value="{{old('billing_last_name',$user_address->billing_last_name)}}" >

           <span style="color: red;" class="text-red" id="billing_last_name_error">
           <?php 
           if ($errors->has('billing_last_name')) 
           {
              echo $errors->first('billing_last_name');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Billing Email <span class="text-red">*</span></td>
          <td><input type="text" name="billing_email" id="billing_email" class="form-control" value="{{old('billing_email',$user_address->billing_email)}}" >

           <span style="color: red;" class="text-red" id="billing_email_error">
           <?php 
           if ($errors->has('billing_email')) 
           {
              echo $errors->first('billing_email');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Billing Phone <span class="text-red">*</span></td>
          <td><input type="text" name="billing_phone" id="billing_phone" class="form-control" value="{{old('billing_phone',$user_address->billing_phone)}}" >

           <span style="color: red;" class="text-red" id="billing_phone_error">
           <?php 
           if ($errors->has('billing_phone')) 
           {
              echo $errors->first('billing_phone');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Billing Address 1 <span class="text-red">*</span></td>
          <td><input type="text" name="billing_address1" id="billing_address1" class="form-control" value="{{old('billing_address1',$user_address->billing_address1)}}" >

           <span style="color: red;" class="text-red" id="billing_address1_error">
           <?php 
           if ($errors->has('billing_address1')) 
           {
              echo $errors->first('billing_address1');

           } 
           ?>
             
           </span>
          </td>
          </tr>

          <tr><td>Billing Address 2</td>
          <td><input type="text" name="billing_address2" id="billing_address2" class="form-control" value="{{old('billing_address2',$user_address->billing_address2)}}" >

           <span style="color: red;" class="text-red" id="billing_address2_error">
           <?php 
           if ($errors->has('billing_address2')) 
           {
              echo $errors->first('billing_address2');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Billing Pincode <span class="text-red">*</span></td>
          <td><input type="text" name="billing_pincode" id="billing_pincode" class="form-control" value="{{old('billing_pincode',$user_address->billing_pincode)}}" >

           <span style="color: red;" class="text-red" id="billing_pincode_error">
           <?php 
           if ($errors->has('billing_pincode')) 
           {
              echo $errors->first('billing_pincode');

           } 
           ?>
             
           </span>

          </td>
          </tr>
          <tr><td>Billing Country <span class="text-red">*</span></td>
          <td>
          <?php //pr($country); ?>

          <select class="form-control" name="billing_country" id="billing_country">
          
          <?php foreach($country as $co) { ?>
          <option <?php if($user_address->billing_country==$co->id)  { echo 'selected';   } ?> value="{{$co->id}}">{{$co->name}}</option>

          <?php } ?>
          </select>

           <span style="color: red;" class="text-red" id="billing_country_error">
           <?php 
           if ($errors->has('billing_country')) 
           {
              echo $errors->first('billing_country');

           } 
           ?>
             
           </span>



          </td>
          </tr>


         


          <tr><td>Billing State <span class="text-red">*</span></td>
          <td>

          <select class="form-control" name="billing_state" id="billing_state">
          <option value="">Please Select</option>
          <?php foreach($state as $so) { ?>
          <option <?php if($user_address->billing_state==$so->id)  { echo 'selected';   } ?> value="{{$so->id}}">{{$so->name}}</option>

          <?php } ?>
          </select>
           <span style="color: red;" class="text-red" id="billing_state_error">
           <?php 
           if ($errors->has('billing_state')) 
           {
              echo $errors->first('billing_state');

           } 
           ?>
             
           </span>

            

          </td>
          </tr>

           <tr><td>Billing City <span class="text-red">*</span></td>
          <td> 

          <?php //pr($billing_cities); ?>

          <select class="form-control" name="billing_city" id="billing_city">
          <option value="">Please Select</option>
          <?php if($billing_cities->count()) 
          { 
                 foreach($billing_cities as $bc)
                 {?>

                <option <?php if($user_address->billing_city==$bc->id) { echo 'selected';  } ?> value="{{$bc->id}}">{{$bc->name}}</option>

                 <?php 

                 }



            }  ?>

          </select>
           <span style="color: red;" class="text-red" id="billing_city_error">
           <?php 
           if ($errors->has('billing_city')) 
           {
              echo $errors->first('billing_city');

           } 
           ?>
             
           </span>
          </td>
          </tr>


          

          </table>

          </div>


          <div class="col-md-6">


          <table class="table">
          <tr><th colspan="2">Shipping Info</th></tr>


          <tr><td>Shipping First Name <span class="text-red">*</span></td>
          <td><input type="text" name="shipping_first_name" id="shipping_first_name"  class="form-control" value="{{old('shipping_first_name',$user_address->shipping_first_name)}}"  >
           <span style="color: red;" class="text-red" id="shipping_first_name_error">
           <?php 
           if ($errors->has('shipping_first_name')) 
           {
              echo $errors->first('shipping_first_name');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Shipping Last Name <span class="text-red">*</span></td>
          <td><input type="text" name="shipping_last_name" id="shipping_last_name" class="form-control" value="{{old('shipping_last_name',$user_address->shipping_last_name)}}" >
           <span style="color: red;" class="text-red" id="shipping_last_name_error">
           <?php 
           if ($errors->has('shipping_last_name')) 
           {
              echo $errors->first('shipping_last_name');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Shipping Email <span class="text-red">*</span></td>
          <td><input type="text" name="shipping_email" id="shipping_email" class="form-control" value="{{old('shipping_email',$user_address->shipping_email)}}" >

           <span style="color: red;" class="text-red" id="shipping_email_error">
           <?php 
           if ($errors->has('shipping_email')) 
           {
              echo $errors->first('shipping_email');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Shipping Phone <span class="text-red">*</span></td>
          <td><input type="text" name="shipping_phone" id="shipping_phone" class="form-control" value="{{old('shipping_phone',$user_address->shipping_phone)}}" >

           <span style="color: red;" class="text-red" id="shipping_phone_error">
           <?php 
           if ($errors->has('shipping_phone')) 
           {
              echo $errors->first('shipping_phone');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Shipping Address 1 <span class="text-red">*</span></td>
          <td><input type="text" value="{{old('shipping_address1',$user_address->shipping_address1)}}" name="shipping_address1" id="shipping_address1" class="form-control" >

           <span style="color: red;" class="text-red" id="shipping_address1_error">
           <?php 
           if ($errors->has('shipping_address1')) 
           {
              echo $errors->first('shipping_address1');

           } 
           ?>
             
           </span>

          </td>
          </tr>

          <tr><td>Shipping Address 2 <span class="text-red">*</span></td>
          <td><input type="text" name="shipping_address2" id="shipping_address2" class="form-control" value="{{old('shipping_address2',$user_address->shipping_address2)}}" >

           <span style="color: red;" class="text-red" id="shipping_address2_error">
           <?php 
           if ($errors->has('shipping_address2')) 
           {
              echo $errors->first('shipping_address2');

           } 
           ?>
             
           </span>


          </td>
          </tr>

          <tr><td>Shipping Pincode <span class="text-red">*</span></td>
          <td><input type="text" name="shipping_pincode" id="shipping_pincode" class="form-control" value="{{old('shipping_pincode',$user_address->shipping_pincode)}}" >

           <span style="color: red;" class="text-red" id="shipping_pincode_error">
           <?php 
           if ($errors->has('shipping_pincode')) 
           {
              echo $errors->first('shipping_pincode');

           } 
           ?>
             
           </span>

          </td>
          </tr>
          <tr><td>Shipping Country <span class="text-red">*</span></td>
          <td>

          <select class="form-control" name="shipping_country" id="shipping_country">
           <?php foreach($country as $co) { ?>
          <option <?php if($user_address->shipping_country==$co->id)  { echo 'selected';   } ?> value="{{$co->id}}">{{$co->name}}</option>

          <?php } ?>
          </select>

           <span style="color: red;" class="text-red" id="shipping_country_error">
           <?php 
           if ($errors->has('shipping_country')) 
           {
              echo $errors->first('shipping_country');

           } 
           ?>
             
           </span>



          </td>
          </tr>


         


          <tr><td>Shipping State <span class="text-red">*</span></td>
          <td>

          <select class="form-control" name="shipping_state" id="shipping_state">
          <option value="">Please Select</option>
          <?php foreach($state as $so) { ?>
          <option <?php if($user_address->shipping_state==$so->id)  { echo 'selected';   } ?> value="{{$so->id}}">{{$so->name}}</option>

          <?php } ?>
          </select>

           <span style="color: red;" class="text-red" id="shipping_state_error">
           <?php 
           if ($errors->has('shipping_state')) 
           {
              echo $errors->first('shipping_state');

           } 
           ?>
             
           </span>

            

          </td>
          </tr>

           <tr><td>Shipping City <span class="text-red">*</span></td>
          <td> <select class="form-control" name="shipping_city" id="shipping_city">
          <option value="">Please Select</option>
          <?php if($shipping_cities->count()) 
          { 
                 foreach($shipping_cities as $bc)
                 {?>

                <option <?php if($user_address->shipping_city==$bc->id) { echo 'selected';  } ?> value="{{$bc->id}}">{{$bc->name}}</option>

                 <?php 

                 }



            }  ?>
          </select>

           <span style="color: red;" class="text-red" id="shipping_city_error">
           <?php 
           if ($errors->has('shipping_city')) 
           {
              echo $errors->first('shipping_city');

           } 
           ?>
             
           </span>

          </td>
          </tr>


          

          </table>

          </div>

          <div class="row">
           <input type="submit" name="order_confirm" value="Submit">
          </div>

                  


          <?php
          }
          else
          {
             echo 'You have no items in your cart';

          }
          

      ?>


  
    

    

    </form>
    </div>

          <table class="table">
          <tr>
          <th>Product</th>
          
          <th>Price</th>
          <th>Total</th>
         

          </tr>
          <?php

          $total= 0; 
          $subtotal=0; 
          foreach($cart_items as $items) 
          { 
                 
                 $name= $items->name;
                 $item_price= $items->price;
                 $quantity=$items->quantity; 
                 $total_item_price= $items->price * $quantity;

                 $subtotal+=$total_item_price;
                 $attributes= $items->attributes;
                 $products_images = $attributes->products_images;

                  $preview_design_url=''; 

                  if(!empty($attributes)  && isset($attributes['fabric_generator']) && !empty($attributes['fabric_generator']['preview_design']))
                  {
                     $preview_design_url=$attributes['fabric_generator']['preview_design'];

                   
                  }
            ?>

            <tr>
            <td>
              <img src="{{$products_images}}" width="75" height="75">
            {{$name}}

            <?php if(!empty($preview_design_url)) { ?>
            <br>

            <a target="_blank"  href="{{$preview_design_url}}">Click to view design</a>
            <?php } ?>

            <?php if(!empty($attributes))
            {
                 if(isset($attributes['size']))
                 {
                   echo '<br>';
                   echo '<b>Size:</b>'.$attributes['size'];

                 }

                 if(isset($attributes['length']))
                 {
                   echo '<br>';
                   echo '<b>Length:</b>'.$attributes['length']. " meter ";

                 }

                 if(isset($attributes['fabric_generator']))
                 {


                   if(isset($attributes['fabric_generator']['layout']))
                   {
                     echo '<br>';
                     echo '<b>Layout:</b>'.$attributes['fabric_generator']['layout'];

                   }

                   if(isset($attributes['fabric_generator']['rotate']))
                   {
                     echo '<br>';
                     echo '<b>Rotate:</b>'.$attributes['fabric_generator']['rotate'];

                   }
                    if(isset($attributes['fabric_generator']['scale']))
                   {
                     echo '<br>';
                     echo '<b>Scale:</b>'.$attributes['fabric_generator']['scale'];

                   }

                 }
                  

            } ?>



            </td>
            
            <td>{{$item_price}}</td>
            <td>{{$total_item_price}}</td>

           


            </tr>



          <?php }

          $total=Cart::getTotal();



           ?>



          <tr><td></td><td></td><td>Subtotal (Rs)</td><td colspan="">{{Cart::getSubTotal()}}</td></tr> 
          <?php if(!empty($coupon_discount)) 
          {

            $total=$total-$coupon_discount;


           ?>

            <tr><td></td><td></td><td>Discount (Rs)</td><td colspan="">{{$coupon_discount}}</td></tr>
            <?php } ?> 



          <tr><td></td><td></td><td>Total(Rs)</td><td colspan="">{{$total}}</td></tr>



          </table>

          
     
  </div>
  </div>
</section>
 
@include('common.footer')



<script type="text/javascript">

function update_billing_city()
{

  var token= '{{ csrf_token() }}';
var state=$('#billing_state').val();
   $.ajax({
          url: "{{url('ajaxhit/getCityByStateDropdown')}}", 
          type: 'post',
          cache: false,
          dataType: 'html',
          //data: $('#'+form_id).serialize(),
          async:false,
          data: {_token:token,state:state },
          crossDomain: true,
          beforeSend: function()
          {
            
          },
          success: function(response)
          {
              $('#billing_city').html(response);
              
              
          },
    });


}

function update_shipping_city()
{

    var token= '{{ csrf_token() }}';
var state=$('#shipping_state').val();
   $.ajax({
          url: "{{url('ajaxhit/getCityByStateDropdown')}}", 
          type: 'post',
          cache: false,
          dataType: 'html',
          async:false,
          //data: $('#'+form_id).serialize(),
          data: {_token:token,state:state },
          crossDomain: true,
          beforeSend: function()
          {
            
          },
          success: function(response)
          {
              $('#shipping_city').html(response);
              
              
          },
    }); 

 


}



$('#billing_state').change(function()
{
    update_billing_city();

}); 




$('#shipping_state').change(function()
{

    update_shipping_city();



}); 

update_billing_shipping_info(); 


function update_billing_shipping_info()
{
   var is_checked=false;
   if($('#billing_shipping_same').prop('checked')==true)
   {
        is_checked=true;

   }
   var billing_first_name=$('#billing_first_name').val();
   var billing_last_name=$('#billing_last_name').val();
   var billing_email=$('#billing_email').val();
   var billing_phone=$('#billing_phone').val();
   var billing_address1=$('#billing_address1').val();
   var billing_address2=$('#billing_address2').val();
   var billing_pincode=$('#billing_pincode').val();

   var billing_country=$('#billing_country').val();
   var billing_state=$('#billing_state').val();
   var billing_city=$('#billing_city').val(); 


   var shipping_first_name=$('#shipping_first_name').val();
   var shipping_last_name=$('#shipping_last_name').val();
   var shipping_email=$('#shipping_email').val();
   var shipping_phone=$('#shipping_phone').val();
   var shipping_address1=$('#shipping_address1').val();
   var shipping_address2=$('#shipping_address2').val();
   var shipping_pincode=$('#shipping_pincode').val();

   var shipping_country=$('#shipping_country').val();
   var shipping_state=$('#shipping_state').val();
   var shipping_city=$('#shipping_city').val();

   if(is_checked)
   {
        
        $('#shipping_first_name').val(billing_first_name);
        $('#shipping_last_name').val(billing_last_name);
        $('#shipping_email').val(billing_email);
        $('#shipping_phone').val(billing_phone);
        $('#shipping_address1').val(billing_address1);
        $('#shipping_address2').val(billing_address2);
        $('#shipping_pincode').val(billing_pincode);
        $('#shipping_country').val(billing_country);
        $('#shipping_state').val(billing_state);
        update_shipping_city();
        


        $('#shipping_city').val(billing_city);
        




   }
   else
   {

        $('#shipping_first_name').val('');
        $('#shipping_last_name').val('');
        $('#shipping_email').val('');
        $('#shipping_phone').val('');
        $('#shipping_address1').val('');
        $('#shipping_address2').val('');
        $('#shipping_pincode').val('');

        $('#shipping_state').val('');
        $('#shipping_city').val('');
        


   }

   
   





   




}


$('#billing_shipping_same').click(function(){
 
 update_billing_shipping_info();

});






  


  

</script>





</body>
</html>