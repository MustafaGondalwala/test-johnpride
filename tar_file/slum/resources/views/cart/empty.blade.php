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
  
<section class="fullwidth tabcart">
  <div class="container">
     <ul>
	  	<li class="active"><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
		 <li><span><i class="addressicon"></i></span><strong>Address</strong></li>
		 <li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
	  </ul>
  </div>
</section>  
	
<section class="fullwidth innerpage"> 
  <div class="container">
	  <div class="sectionleft">
		  <div class="offersec">
			  <strong>Offers</strong>
		  		<ul>
			  	<li>Free Delivery on order above Rs.1000/-</li>
			  </ul>
		  </div>
	<div class="freedelivery"><i class="detailicon1"></i> Yay!  <strong>Free Delivery</strong> on this order. </div>
	  <div class="title3">My Shopping Bag ({{$cartCount}} Items) <div class="secures"><i class="secureimg"></i> <span>100% <br><small>Secure</small></span></div></div>
	  <ul class="cartlist">
      <?php

      $totalDiscount = 0;
      $totalShipping = 0;

      if(!empty($cartContent) && $cartContent->count() > 0){

        $storage = Storage::disk('public');
        $img_path = 'products/';
        $thumb_path = $img_path.'thumb/';

        foreach($cartContent as $cart){

          $cartId = $cart->id;

          $cartIdArr = explode('_', $cartId);

          $product_id = $cartIdArr[0];

          $product = $productModel->find($product_id);

          $attributes = $cart->attributes;

          $qty = $cart->quantity;

          $sizeName = $attributes->size_name;
          $clrName = $attributes->color_name;

          //prd($product->toArray());

          $price = $product->price;
          $sale_price = $product->sale_price;

          $defaultImage = $product->defaultImage;
          $productImages = $product->productImages;

          $imgUrl = '';

          if(!empty($defaultImage) && count($defaultImage) > 0){
            if(!empty($defaultImage->image) && $storage->exists($thumb_path.$defaultImage->image) ){
              $imgUrl = url('public/storage/'.$thumb_path.$defaultImage->image);
            }
          }

          if(empty($imgUrl)){
            if(!empty($productImages) && count($productImages) > 0){
              $productImg = $productImages[0];
              if(!empty($productImg->image) && $storage->exists($thumb_path.$productImg->image) ){
                $imgUrl = url('public/storage/'.$thumb_path.$productImg->image);
              }
            }
          }

          //$price
          ?>
          <li>
            <div class="cartimg">
              <?php
              if(!empty($imgUrl)){
                ?>
                <img src="{{$imgUrl}}" alt="{{$product->name}}">
                <?php
              }
              ?>
              
            </div>
            <div class="procont">
              <div class="titles">
                <p><span>Slumber Jill</span></p>
                <p><a href="{{url('products/details/'.$product->slug)}}" target="_blank">{{$cart->name}}</a></p>
              </div>
              <div class="cartprice">
                <?php
                if($sale_price < $price){
                  $discount = CustomHelper::calculateProductDiscount($price, $sale_price);

                  $totalDiscount = $totalDiscount + $discount;
                  ?>
                  <span>₹{{$sale_price}} <del>₹{{$price}}</del></span>
                  <small>Saving: <cite>{{number_format($discount, 2)}} %off</cite></small>
                  <?php
                }
                else{
                  ?>
                  <span>₹{{$price}} </span>
                  <?php
                }
                ?>
                
              </div>

              <div class="sizeqty">
                <div class="size">SIZE : {{$sizeName}}</div>
                <div class="qty">Qty : {{$qty}}</div>
              </div>

              <div class="removeandwish">
                <div class="remove"><i class="deleteicon"></i> <span><a href="javascript:void(0)" data-cartid="{{$cart->id}}" class="removeCartItem">Remove</a></span></div>
                <div class="wishlistmove"><i class="wishlistpink"></i> <span>Move To Wishlist</span></div>
              </div>
            </li>
          <?php
        }
      }
      ?>

	  </ul>

	  </div>

    <?php
    $cartTotal = Cart::getTotal();

    $total = $cartTotal - $totalDiscount + $totalShipping;
    ?>
	  
	  <div class="sectionright">
		  <div class="pricedetail">
		  <h3 class="title3">Price Detail</h3>
		  <ul>
		  	<li><span>Total MRP</span> <strong>₹{{$cartTotal}}</strong></li>
			  <li><span>Bag Discount</span> <strong>-₹{{number_format($totalDiscount, 2)}}</strong></li>
			  <li><span>Delivery Charges</span> <strong>₹{{$totalShipping}}</strong></li>  
			  <li class="totals"><span>Order Total</span> <strong>₹{{number_format($total, 2)}}</strong></li>
		  </ul>
			<div class="placebtn"><a href="{{url('cart/address')}}">Place Order</a></div>
		  </div>
		  
		  
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