<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$title}}</title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="robots" content="index, follow"/>
        <meta name="robots" content="noodp, noydir"/>
       @include('common.head')
       <link rel="stylesheet" href="{{url('public/assets')}}/css/owl.carousel.min.css">
       <script src="{{url('public/assets')}}/js/owl.carousel.js"></script>
        
    </head>

    <?php
    $back_url = '';
    if(request()->has('back')){
        $back_url = url(request()->back);
    }

    if(empty($back_url) || $back_url == ''){
        $back_url = url('products/collection');
    }

    $currency_symb = '<i class="fa fa-inr" aria-hidden="true"></i>';
    ?>

    <body>
         @include('common.user_header')
        <section>
            <div class="contentArea">

                @include('common.left_menu')

                <div class="rightBar">

                    <div class="tabletArea container-custom">

                    <div id="cart_msg"></div>

                        <div class="proDetails col-md-12 col-sm-12 col-xs-12 noPaddings">

                         <form method="POST" id="add-to-cart-form" action="{{url('cart/add')}}" accept-charset="UTF-8" role="form" onsubmit="return false;">

                         {{ csrf_field() }}

                            <?php
                            //pr($product->toArray());
                            //pr($product->photos->toArray());
                            $photos = $product->photos;

                            $brand = $product->brand;
                            //pr($brand);

                            $brand_id = (isset($brand->id))?$brand->id:$product->brand_id;
                            $brand_name = (isset($brand->name))?$brand->name:'';
                            $brand_logo = (isset($brand->logo))?$brand->logo:'';
                            $brand_logo_path = (isset($brand->logo_path))?$brand->logo_path:'';

                            $defaultPhoto = $product->defaultPhoto;
                            $photoPath = (isset($defaultPhoto->path))?$defaultPhoto->path:'';

                            $storage = Storage::disk('public');

                            $TaxRate = "";

                            if($product->tax_rate_id > 0){
                                $TaxRate = CustomHelper::GetTaxRate($product->tax_rate_id, $col_name='rate');
                            }

                            $gst = (!empty($TaxRate))?$TaxRate:config('custom.gst_default');
                            //echo 'gst='.$gst;

                            $min_qty = $product->min_qty;
                            $product_price = $product->price;
                            $cross_price = 0;

                            if(isset($UsersCategory->price_type_id) && $UsersCategory->price_type_id > 0){

                                $price_type_id = $UsersCategory->price_type_id;

                                $PriceType = $product->PriceType($product->id, $price_type_id);

                                if(count($PriceType)){
                                    if(!empty($PriceType->price) && $price_type_id == $PriceType->price_type_id){
                                        $cross_price = $product_price;
                                        $product_price = $PriceType->price;
                                    }
                                }
                            }

                            //pr($product);

                            $product_gst_price = $product_price*(100+$gst)/100;

                            $price_per_set = $product_price*$product->piece_per_set;

                            /*echo 'gst='.$gst.'<br>';
                            echo 'product_price='.$product_price.'<br>';
                            echo 'product_gst_price='.$product_gst_price;*/

                            //$price_per_set_js = number_format($price_per_set, 2, '.', '');

                            //number_format(1000.5, 2, '.', '')

                            //echo 'price_per_set='.$product_price; die;

                            //$UsersCategory = UsersCategory::where('users_id', $user_id);
                            //pr($UsersCategory->price_type_id);
                            //$inventoryItems = $product->inventoryItems->all();

                            //$availableOptions = $product->availableOptions();
                            $availableInventory = $product->availableInventory($product->id);
                            //prd($availableInventory);

                            $productPriceFromCart = 0;

                            $cart_btn = 'Add to Cart';
                            $disable_cart = 'disabled="disabled"';

                            //echo "productCartId=".$productCartId;

                            $sub_total_price = 0;
                            $sub_total_gst = 0;

                            if($productCartId > 0){
                                $productPriceFromCart = number_format($product->piece_per_set*$productCartQty*$product_price*(($gst+100)/100), 2);

                                $sub_total_price = number_format($product->piece_per_set*$productCartQty*$product_price, 2);
                                $sub_total_gst = number_format($product->piece_per_set*$productCartQty*$product_price*(($gst)/100), 2);


                                $cart_btn = 'Update Cart';
                                $disable_cart = '';
                            }

                            $delivery_terms_id = $product->delivery_terms_id;

                            if(is_numeric($delivery_terms_id) && $delivery_terms_id > 0){

                                $DeliveryTerms = CustomHelper::GetDeliveryTerms($delivery_terms_id);
                            }

                            $delivery_terms = (isset($DeliveryTerms->contents))?$DeliveryTerms->contents:'';
                            ?>

                            <input type="hidden" name="id" value="{{ $product->id }}" />
                            <input type="hidden" name="cart_id" value="{{ $cart_id }}" />
                <input type="hidden" name="quantity" value="{{ $productCartQty }}" />
                

                            <div class="mediaNew media">
                                <div class="media-left">
                                    <div class="detailslider owl-carousel">

                                    <?php

                                    if(!empty($photos) && count($photos) > 0){
                                        foreach($photos as $photo){
                                            if (!empty($photo->name) && $storage->exists($photo->path . $photo->name)) {
                                                ?>
                                                <div><img class="media-object" src="{{url('public/storage/'.$photo->path . $photo->name)}}" alt="{{$product->name}}" /></div>
                                                <?php
                                            }

                                        }
                                    }

                                    /*if (!empty($defaultPhoto) && $storage->exists($photoPath . $defaultPhoto->name)) {
                                        ?>

                                        <div><img class="media-object" src="{{url('public/storage/'.$photoPath . $defaultPhoto->name)}}" alt="{{$product->name}}" /></div>
                                        <div><img class="media-object" src="{{url('public/storage/'.$photoPath . $defaultPhoto->name)}}" alt="{{$product->name}}" /></div>
                                        <div><img class="media-object" src="{{url('public/storage/'.$photoPath . $defaultPhoto->name)}}" alt="{{$product->name}}" /></div>
                                        <?php
                                    }*/
                                    ?>
									</div>
                              
                                 <script>
                                    $(document).ready(function() {
                                      $('.detailslider').owlCarousel({
                                        items: 1,
                                        margin: 0,
                                        nav:true,

                                    });
                                  })
                              </script>

                                </div>
                                <a href="{{$back_url}}" class="btn btn-default shopping_btn21 pull-right" onclick="/*window.history.back();*/">Continue Shopping</a>
                                <div class="media-body-new media-body">
                                    <h3>{{$product->name}}</h3>
                                    <span class="spans"> <img src="{{url('public/assets/img/qr.png')}}"/>Item Code : <strong>{{$product->code}}</strong> </span> <span class="spans"> <img src="{{url('public/assets/img/tag.png')}}"/>Brand : <strong>{{$brand_name}}</strong> </span>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12 noPaddings itemPrice">
                                            <p>
                                                <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($product_price, 2) }}/pc + GST @ {{$gst}}%
                                                <?php
                                                if($cross_price > 0){
                                                  ?>
                                                  <br><span style="text-decoration: line-through; color: #747289;">{{ $cross_price }}</span>
                                                  <?php
                                              }
                                              ?>
                                          </p>
                                          <p class="gstPrice"><i class="fa fa-inr" aria-hidden="true"></i> {{number_format($product_gst_price, 2)}}/pc</p>
                                          <p>Min.qty. {{$product->min_qty}} Set <small>({{$product->piece_per_set}}pc/Set)</small></p>
                                      </div>
                                  </div>

                                    <p>{{$product->description}}</p>
                                    <hr>

                                    <?php                                    
                                    //prd($availableInventory);

                                    if(count($availableInventory) > 0){
                                       foreach($availableInventory as $ai){

                                        $inventory_id_val = 0;
                                        $min_disabled = 'disabled="disabled"';
                                        $inventory_id_input = 'disabled="disabled"';

                                        if(isset($productCartInventory[$ai->id])){

                                           // pr($productCartInventory);

                                            $inventory_id_val = $productCartInventory[$ai->id];

                                            if($inventory_id_val > 0){
                                                $min_disabled = '';
                                                $inventory_id_input = '';
                                            }

                                        }
                                        ?>
                                        <span class="sizes">
											<div class="sizediv">
                                       		<span>Select Qty in set:</span> <span class="sizeSelect active" id="sizeSelect_{{$ai->inventory_id}}">{{$ai->name}}</span>
                                       		</div>
                                        <div class="input-group">
											<span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-number btn-number-minus" {{$min_disabled}} data-type="minus" data-field="quant[{{$ai->inventory_id}}]" data-inventoryid="{{$ai->inventory_id}}"> <span class="glyphicon glyphicon-minus"></span> </button>
                                            </span>

                                            <input type="text" name="quant[{{$ai->inventory_id}}]" class="form-control input-number" value="{{$inventory_id_val}}" min="0" max="{{$ai->stock}}" data-inventoryid="{{$ai->inventory_id}}">

                                            <input type="hidden" name="inventory_id[{{$ai->inventory_id}}]" value="{{$inventory_id_val}}" {{ $inventory_id_input }}/>

                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-number btn-number-plus" data-type="plus" data-field="quant[{{$ai->inventory_id}}]" data-inventoryid="{{$ai->inventory_id}}"> <span class="glyphicon glyphicon-plus"></span> </button>
                                            </span>
                                            </div>

                                            <?php
                                            /*
                                            <div class="stocks">Available Stock: {{$ai->stock}}</div>
                                            */
                                            ?>

                                            

                                    </span>


                                        <?php
                                       }

                                       $total_price_detail_disp = 'display:none;';

                                       if($productPriceFromCart > 0){
                                        $total_price_detail_disp = '';
                                       }

                                       ?>
                                       <div class="row">
                                         <div class="col-md-12 col-sm-12 col-xs-12 well">
                                            <div class="borderSec col-sm-6 col-xs-6 col-md-6">
                                              <p>Price Per Set:</p>
                                              <strong><img src="{{url('public/assets/img/rs.png')}}" alt="rs" style="width: 13px;position: relative;bottom: 3px;"/> {{ number_format($price_per_set, 2) }}</strong> 
                                              <p>+ GST @ {{$gst}}%</p>
                                          </div>
                                          <div class=" mobile-view2 col-sm-6 col-xs-6 col-md-6">
                                            <p>Total:</p>
                                            <strong><img src="{{url('public/assets/img/rs.png')}}" alt="rs" style="width: 13px;position: relative;bottom: 3px;"/> <span class="totalPrice">{{ $productPriceFromCart }}</span></strong>
                                            
                                            <p class="total_price_detail" style="font-size:12px; {{$total_price_detail_disp}}">
                                                (
                                                <?php echo $currency_symb;?>
                                                <span class="sub_total_price"><?php echo $sub_total_price;?></span>
                                                +
                                                <?php echo $currency_symb;?>
                                                <span class="sub_total_gst"><?php echo $sub_total_gst;?></span> GST
                                                )
                                            </p>

                                        </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="noPaddings col-xs-12 col-sm-12 col-md-2">
                                            <button type="button" class="addCart btn btn-default" {{-- $disable_cart --}} onclick="addToCart()"> <i class="fa fa-shopping-cart" aria-hidden="true"></i>{{ $cart_btn }} </button>
                                        </div>
                                    </div>
                                    
                                       <?php
                                    }
                                    else{
                                        ?>

                                        <div class="noPaddings col-xs-12 col-sm-12 col-md-2">
                                        <button type="button" class="btn btn-default" disabled="disabled"> Out of stock </button>
                                    </div>

                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if(!empty($delivery_terms) && $delivery_terms != ''){
                                        ?>
                                        <div class="col-md-12">
                                            <p>
                                                <strong>Delivery Terms:</strong><br>
                                                <?php
                                                echo $delivery_terms;
                                                ?>
                                            </p>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    

                                </div>

                            </div>



                            </form>


                            </div>
                            </div>
                </div>
            </div>
        </section>

        @include('common.footer')
        
        <script src="{{url('public/assets')}}/js/function.js"></script>

        <script type="text/javascript">

            function scrollToTop(){
                $('html,body').animate({
                    scrollTop: $("#cart_msg").offset().top},
                    'slow');                
            }


        function addToCart(){

            err_msg = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>Minimum Order Quantity for this product is {{ $min_qty }}!</div>';

            if($("[name='quantity']").val() < 1){
                $("#cart_msg").html(err_msg);
                $('html,body').animate({
                            scrollTop: $("#cart_msg").offset().top},
                            'slow');
            }
            else{

            add_to_cart_form = $("#add-to-cart-form").serialize();
            test = 'test';

            _token = '{{csrf_token()}}';
            
            $.ajax({
                url: "{{ url('cart/add') }}",
                method: 'POST',
                data:add_to_cart_form,
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){},
                success: function(resp) {
                    if(resp.success == true){
                        $("#cart_msg").html(resp.message);
                        $("input[name='cart_id']").val(resp.cart_id);
                        $("#headCartCount").text(resp.cart_count);
                        
                        //$(document).scrollTop();
                        scrollToTop();
                    }
                    else{
                        $("#cart_msg").html(resp.message);
                        scrollToTop();
                    }
                },
                error: function(resp) {

                }
            });
        }

        }

            $('.btn-number').click(function(e){

                fieldName   = $(this).attr('data-field');
                type        = $(this).attr('data-type');

                inventoryid = $(this).attr('data-inventoryid');

                validate_qty_price(fieldName, inventoryid);
            });


            $(".input-number").on("keyup", function(){
                fieldName = $(this).attr('name');
                inventoryid = $(this).attr('data-inventoryid');
                validate_qty_price(fieldName, inventoryid);
            });

            function validate_qty_price(fieldName, inventoryid){

                var input = $("input[name='"+fieldName+"']");

                minValue =  parseInt(input.attr('min'));
                maxValue =  parseInt(input.attr('max'));
                valueCurrent = parseInt(input.val());

                if(valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='"+fieldName+"']").removeAttr('disabled');
                }
                else{
                    //alert('You have exceeded the available stock.');
                    input.val(input.data('oldValue'));
                }

                if(valueCurrent <= maxValue) {
                    $(".btn-number[data-type='plus'][data-field='"+fieldName+"']").removeAttr('disabled');
                }
                else{
                    //alert('You have exceeded the available stock.');
                    err_msg = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>You have exceeded the available stock!</div>';
                    
                    $("#cart_msg").html(err_msg);
                    $("#cart_msg").effect( "bounce", "slow" );
                    scrollToTop();
                    input.val(input.data('oldValue'));
                }
                
                var currentVal = parseInt(input.val());
                max_qty = parseInt(input.attr('max'));
                //console.log('max_qyt='+max_qty);

                if (!isNaN(currentVal)) {
                    if(currentVal > 0){
                        $("#sizeSelect_"+inventoryid).css("background", "#01D0B6");
                        $("#sizeSelect_"+inventoryid).css("color", "#fff");

                        $("input[name='inventory_id["+inventoryid+"]']").attr('disabled', false);
                    }
                    else{
                        $("#sizeSelect_"+inventoryid).css("background", "#fff");
                        $("#sizeSelect_"+inventoryid).css("color", "#333");

                         $("input[name='inventory_id["+inventoryid+"]']").attr('disabled', true);
                    }

                    $("input[name='inventory_id["+inventoryid+"]']").val(currentVal);

                }

                min_qty = parseInt('{{$min_qty}}');
                //price_per_set = parseInt('{{ $price_per_set }}');
                price_per_set = parseFloat('{{ $price_per_set }}');
                gst = parseInt('{{$gst}}');

                //console.log(price_per_set);

                //console.log('min_qty='+min_qty);

                totalQty = 0;
                totalPrice = 0;
                totalPrice = 0;

                if(currentVal == max_qty){
                   // $(this).attr('disabled', true);
                }

                $(".input-number").each(function(i, input){
                    totalQty = parseInt(totalQty) + parseInt(input.value);
                });
                //console.log('totalQty='+totalQty);

                if(totalQty < min_qty){
                    //$(".btn-number").attr('disabled', false);
                }
                else{
                    //$(".btn-number-plus").attr('disabled', true);
                }

                totalPrice = totalQty*price_per_set;
                //totalPriceGst = Math.round((totalQty*price_per_set)*(gst+100)/100);
                totalPriceGst = ((totalQty*price_per_set)*(gst+100)/100).toFixed(2);

                sub_total_gst = ((totalQty*price_per_set)*(gst)/100).toFixed(2);

                if(totalPriceGst > 0){
                    //$(".addCart").attr("disabled", false);
                }
                else{
                    //$(".addCart").attr("disabled", true);
                }

                $("input[name=quantity]").val(totalQty);

                $(".totalPrice").text(totalPriceGst);

                $(".sub_total_price").text(totalPrice.toFixed(2));
                $(".sub_total_gst").text(sub_total_gst);
                $(".total_price_detail").show();
            }

            $(".addCart").click(function(){
                //alert($(this).val());
            });

            function validateForm(){
                quantity  = $("input[name='quantity']").val();

                min_qty = parseInt('{{$min_qty}}');

                if(quantity < min_qty){
                    alert('Total selected Qty should be '+min_qty);
                    return false;
                }
                return true;
            }


        </script>

    </body>
</html>
