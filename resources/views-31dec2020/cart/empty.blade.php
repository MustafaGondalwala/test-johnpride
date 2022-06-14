<!DOCTYPE html>

<html>

<head>



@include('common.head')



</head>

<body>



@include('common.header')



<?php

$cartCollection = Cart::getContent();

$cartCount = $cartCollection->count();

?>

  

  

	

<section class="fullwidth innerpage text-center"> 

  <div class="container">

	  <div class="cartempt">

	  <p><span><i class="carticon"></i></span></p>

		  <p><strong> Add products to it.</strong></p>

<p>Your shopping cart is currently empty</p>

		  <p><a href="{{url('products')}}" class="socialbtn">Shop</a></p>

	  </div>

 

  </div>

</section>

 

@include('common.footer')



<script type="text/javascript">

$(document).on('click', '.delete_item', function()

{



   var p_id= $(this).attr('data-id');

   var token= '{{ csrf_token() }}';

   $.ajax({

          url: "{{url('cart/remove')}}", 

          type: 'post',

          cache: false,

          dataType: 'html',

          //data: $('#'+form_id).serialize(),

          data: {p_id:p_id, _token:token},

          crossDomain: true,

          beforeSend: function()

          {

            

          },

          success: function(response)

          {

              var  response_json=JSON.parse(response);

              $('#cart_items_data').html(response_json.cart_list_html); 

              $('#total_cart_count').html(response_json.cart_total_items)

             

          },

    }); 







});



$(document).on('change', '.qty', function()

{



    

    var product_id= $(this).attr('data-id');

    var qty= $(this).val();

    if(qty < 0)

    {

       qty=1;



    }

   var token= '{{ csrf_token() }}';

   $.ajax({

          url: "{{url('cart/swatchbooks_addtocart')}}", 

          type: 'post',

          cache: false,

          dataType: 'html',

          //data: $('#'+form_id).serialize(),

          data: {_token:token,product_id:product_id,qty:qty },

          crossDomain: true,

          beforeSend: function()

          {

            

          },

          success: function(response)

          {

              var  response_json=JSON.parse(response);

              $('#cart_items_data').html(response_json.cart_list_html); 

              $('#total_cart_count').html(response_json.cart_total_items)

             

          },

    }); 

    







});



$(document).on('click', '#apply_coupon_btn', function()

{

    var coupon_code= $('#coupon_code').val().trim(); 

    if(coupon_code=='')

    {

        alert('Please enter coupon code');

        return false;

    }



    var token= '{{ csrf_token() }}';

   $.ajax({

          url: "{{url('cart/apply_coupon')}}", 

          type: 'post',

          cache: false,

          dataType: 'html',

          //data: $('#'+form_id).serialize(),

          data: {_token:token,coupon_code:coupon_code },

          crossDomain: true,

          beforeSend: function()

          {

            

          },

          success: function(response)

          {

              

              var response_json= JSON.parse(response);

                  $('#cart_items_data').html(response_json.cart_list_html); 

              $('#total_cart_count').html(response_json.cart_total_items)



              if(response_json.status==0)

              {

                $('#couponcode_success_error_mess').html('<span style="color:red">'+response_json.message+'</span>'); 

              }

              if(response_json.status==1)

              {

                $('#couponcode_success_error_mess').html('<span style="color:green">'+response_json.message+'</span>'); 

              }



            



              

              

             

          },

    }); 





});





$(document).on('click', '#remove_coupon_btn', function()

{

    



    var token= '{{ csrf_token() }}';

   $.ajax({

          url: "{{url('cart/remove_coupon')}}", 

          type: 'post',

          cache: false,

          dataType: 'html',

          //data: $('#'+form_id).serialize(),

          data: {_token:token},

          crossDomain: true,

          beforeSend: function()

          {

            

          },

          success: function(response)

          {

              

              var response_json= JSON.parse(response);

              $('#cart_items_data').html(response_json.cart_list_html); 

              $('#total_cart_count').html(response_json.cart_total_items)



              if(response_json.status==0)

              {

                $('#couponcode_success_error_mess').show().html('<span style="color:red">'+response_json.message+'</span>').fadeOut(3000); 

              }

              if(response_json.status==1)

              {

                $('#couponcode_success_error_mess').show().html('<span style="color:green">'+response_json.message+'</span>').fadeOut(3000);; 

              }



            



              

              

             

          },

    }); 





});



$(".removeCartItem").on("click", function(){



    var conf = confirm("Are you sure you want to remove this item?");



    if(conf){

        

      var cartId = $(this).data("cartid");



        var _token = '{{ csrf_token() }}';



        $.ajax({

            url: "{{ url('cart/delete') }}",

            type: "POST",

            data: {cartId:cartId},

            dataType:"JSON",

            headers:{'X-CSRF-TOKEN': _token},

            cache: false,

            beforeSend:function(){

                //$(".ajax_msg").html("");

            },

            success: function(resp){

                if(resp.success){

                    window.location.reload();

                }



            }

        });



    }

    else{

        $(".sizeErr").text("Please select a size");

    }

});







</script>

</body>

</html>