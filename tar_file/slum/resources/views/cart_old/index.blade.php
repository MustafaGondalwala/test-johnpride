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

      <div class="rightBar mobile_padding">
        <div class="tableArea container-custom">
          <div class="panel panel-default">
            <div class="topHeading panel-heading"> <span></span><span></span><span></span>{{trans('custom.shopping_cart')}} </div>


            <div class="welcomeContent panel-body noPaddings cartpage">
              <div class="col-md-12">

                <?php
                $gst = config('custom.gst_default');
                $shipping_fee = config('custom.shipping_fee');

                $total_qty = 0;
                $total_gst = 0;
                $total_amount = 0;
                //$total_amount_gst = $shipping_fee;
                $total_amount_gst = 0;

                if(count($cartItems) > 0){
                  ?>

                  @include('cart.cart_list')
                  
                  <div class="bbtndiv">
                    <?php
                    /*
                    <button class="btn removeBtn "> Remove</button>
                    <button class="btn btn-default"> Review and Order</button>
                    */
                    ?>

                    <?php /* ?>

                    <form method="POST" action="{{url('order/checkoutSubmit')}}" onsubmit="return validate_place_order()">
                      {{ csrf_field() }}
                      <?php
                      <!--
                      <label class="caseon"><input type="radio" name="payment[method]" value="cash" class="" checked> Case on delivery</label>
                      
                      --> 
                      <button type="submit" class="btn btn-default">Checkout</button>
                    </form>
                     <?php */ ?>

                     <form method="POST" action="{{url('cart/billingaddress')}}" name="submit_cart">
                      {{ csrf_field() }}
                      
                      <button type="submit" class="btn btn-default cart_next pull-right checkout_btn">{{trans('custom.checkout')}}</button>
                    </form>
                    
                  </div>
                  <?php
                }
                else{
                  ?>
                  <p>{{trans('custom.your_cart_is_empty')}}</p>
                  <p><a href="{{url('products/feeds')}}">{{trans('custom.click_here_to_shop')}}</a></p>
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
      $(".checkout_btn").click(function(){
        var curr_sel = $(this);
        loadSpinner(curr_sel);
      });

      $(document).on("click", ".deletebtn", function(){

        loadSpinner($(this));
        var conf = confirm('Are you sure to remove this item from your Cart?');

        if(conf){
          $("form[name='itemDeleteForm']").submit();
        }
        else{
          removeSpinner($(this));          
        }        
        
      });

      /*$(document).on("click", ".cart_next", function(){
        if($("input[name='payment_method']").is(":checked")){
          payment_method = $("input[name='payment_method']:checked").val();
          if(payment_method == "credit"){
            $("form[name='submit_cart']").submit();
          }
        }
      });*/
    </script>

  </body>
  </html>