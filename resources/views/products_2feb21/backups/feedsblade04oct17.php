<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Feeds</title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="robots" content="index, follow"/>
        <meta name="robots" content="noodp, noydir"/>
       @include('common.head')
        
    </head>
    <body ng-app="classApp">

        @include('common.user_header')
        <section>
            <div class="contentArea">
                @include('common.left_menu')
                <div class="rightBar">
                    <div class="tabletArea container-custom" ng-controller="classCtrl">

                    <?php
                    if(!empty($feed_products) && count($feed_products) > 0){
                    	foreach($feed_products as $fp){
                    		//prd($fp);

                            $brand = $fp->brand;
                            //prd($brand);

                            $brand_id = (isset($brand->id))?$brand->id:'';
                            $brand_name = (isset($brand->name))?$brand->name:'';
                            $brand_logo = (isset($brand->logo))?$brand->logo:'';
                            $brand_logo_path = (isset($brand->logo_path))?$brand->logo_path:'';

                    		$defaultPhoto = $fp->defaultPhoto;
                    		$photoPath = $fp->defaultPhoto['path'];

                    		$storage = Storage::disk('public');

                            $params['user_id'] = $user_id;
                            //$params['subcategory_id'] = $fp->category_id;
                            $params['brand_id'] = $brand_id;

                            $FavouriteBrands = getFavouriteBrands($params, $result_type = 1);
                            //pr($FavouriteBrands);

                            $fav_icon_class = "fa-heart-o";

                            if(count($FavouriteBrands) > 0){
                                if($FavouriteBrands->is_favourite == 1){
                                    $fav_icon_class = "fa-heart";
                                }
                            }
                    		?>

                    		<div class="col-md-6 colSm noPaddings">
                            <div class="col-md-12">
                                <div class="brandData"> 
                                    <?php

                                    if( !empty($brand_logo) && file_exists(public_path('storage/'.$brand_logo_path.$brand_logo)) ){
                                        $logo_img = url('public/storage/'.$brand_logo_path.$brand_logo);
                                        ?>
                                        <a href="#"> <img class="img-responsive img-circle borderMini" src="{{$logo_img}}" alt="bgNew"> </a>
                                        <?php
                                    }
                                    ?> 
                                <div class="">
                                    <h4 class="media-heading">{{$brand_name}}</h4>
                                </div>
                                &nbsp;&nbsp;<a href="javascript:void(0)" title="Like it" class="like_brand" style="color:#09D1B8;" data-categoryid="{{$fp->category_id}}" data-brandid="{{$brand_id}}"><i class="fa {{$fav_icon_class}}" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div class="mediaNew col-md-12 proDetails">

                            <?php
                            if (!empty($defaultPhoto) && $storage->exists($photoPath . $defaultPhoto['name'])) {
                            	?>
                            	<div class=""> <a href="#"> <img class="img-responsive" src="{{url('public/storage/'.$photoPath . $defaultPhoto['name'])}}" alt="newUser" class="img-responsive" /> </a> </div>
                            	<?php
                            }
                            /*
                            <div class=""> <a href="#"> <img class="img-responsive" src="{{url('public/assets')}}/img/bgNew.jpg" alt="newUser" class="img-responsive" /> </a> </div>
                            */
                            ?>
                                
                                <div class="paddingSideNew">
                                    <h3 class="textBold">{{$fp->name}}</h3>
                                    <div class="flexi col-md-12 col-sm-12 col-xs-12 well paddingSide">
                                        <div class="borderSec col-sm-6 col-xs-6 col-md-6 ">
                                            <span>Price Per Set:</span></div>
                                        <div class="borderSec col-sm-6 col-xs-6 col-md-6 text-right">
                                            <strong><img src="{{url('public/assets')}}/img/rs.png" alt="rs" style="width: 11px;position: relative;bottom: 2px;">{{number_format($fp->price, 0)}}</strong> </div>
                                    </div>
                                    <button class="btn btn-default"> BUY NOW </button>
                                </div>
                            </div>
                        </div>

                    		<?php
                    	}
                    }
                    ?>

                    <!-- <a href="price.php" style="top: 10px;" class="btnNext btn btn-default" ><i class="whites fa fa-angle-right" aria-hidden="true"></i> </a> </div> -->
                    
                </div>
            </div>
        </section>
        <script src="{{url('public/assets')}}/js/function.js"></script>
        <script src="{{url('public/assets')}}/js/angularjs.js"></script>

        <script type="text/javascript">
            $(document).on("click", ".like_brand", function(){
                curr_sel = $(this);

                user_id = '{{$user_id}}';
                category_id = curr_sel.data('categoryid');
                brand_id = curr_sel.data('brandid');

                console.log('brand_id='+brand_id+' category_id='+category_id+' user_id='+user_id);

                _token = '{{csrf_token()}}';

                $.ajax({
                    url: "{{url('products/save_favourite_brand')}}",
                    method: 'POST',
                    data:{user_id, category_id, brand_id},
                    dataType:"JSON",
                    headers:{'X-CSRF-TOKEN': _token},
                    beforeSend:function(){},
                    success: function(resp) {
                        if(resp.is_favourite == 1){
                            curr_sel.children("i").removeClass("fa-heart-o");
                            curr_sel.children("i").addClass("fa-heart");
                        }
                        else if(resp.is_favourite == 0){                         
                            curr_sel.children("i").removeClass("fa-heart");
                            curr_sel.children("i").addClass("fa-heart-o");
                        }
                    },
                    error: function(resp) {

                    }
                });
            });
        </script>

    </body>
</html>
