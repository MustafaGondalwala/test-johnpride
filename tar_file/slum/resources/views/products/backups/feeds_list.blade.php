<?php
$LikedProducts = CustomHelper::UsersLikedProducts($user_id);

if(!empty($feed_products) && count($feed_products) > 0){
    foreach($feed_products as $fp){
        //prd($fp);

        //$brand = $fp->brand;
        $brand = getProductBrand($fp->brand_id);
        //prd($brand);

        $brand_id = (isset($brand->id))?$brand->id:$fp->brand_id;
        $brand_name = (isset($brand->name))?$brand->name:'';
        $brand_logo = (isset($brand->logo))?$brand->logo:'';
        $brand_logo_path = (isset($brand->logo_path))?$brand->logo_path:'';

        //$defaultPhoto = $fp->defaultPhoto;
        $defaultPhoto = getProductPhotos($fp->id, $default=1);
        //prd($defaultPhoto);

        $storage = Storage::disk('public');

        $params['user_id'] = $user_id;
        //$params['subcategory_id'] = $fp->category_id;
        $params['brand_id'] = $brand_id;

        $FavouriteBrands = getFavouriteBrands($params, $result_type = 1);
        //pr($FavouriteBrands);

        $fav_icon_class = "fa-heart-o";
        $liked = "0";

        if(count($FavouriteBrands) > 0){
            if($FavouriteBrands->is_favourite == 1){
                //$fav_icon_class = "fa-heart";
            }
        }

        if(isset($LikedProducts[$fp->id])){
          $fav_icon_class = "fa-heart";
          $liked = "1";
        }

        $TaxRate = "";

        if($fp->tax_rate_id > 0){
          $TaxRate = CustomHelper::GetTaxRate($fp->tax_rate_id, $col_name='rate');
        }

        $gst = (!empty($TaxRate))?$TaxRate:config('custom.gst_default');
        //echo 'gst='.$gst;

        $product_price = number_format($fp->price, 2);

        //prd($fp);
        $price_type_id = 0;
        $cross_price = 0;

        if(isset($fp->price_type_id) && $fp->price_type_id > 0){
          $price_type_id = $fp->price_type_id;


          $PriceType = $ProductModel->PriceType($fp->id, $price_type_id);
        //prd($PriceType);

          if(count($PriceType)){
            if(!empty($PriceType->price) && $price_type_id == $PriceType->price_type_id){
              $cross_price = $product_price;
              $product_price = number_format($PriceType->price, 2);
            }
          }
        }

        $product_gst_price = number_format($product_price*(100+$gst)/100, 2);
        //echo 'product_gst_price='.$product_gst_price

        //$popular_since = date_format2($fp->created_at, 'Y');
        //$popular_since = dateTimeInterval($fp->created_at);
        $popular_since = CustomHelper::DateTimeInterval($fp->created_at);

        // || strpos(strtolower($popular_since), 'month') == ''

        $strlen_popular_since = strlen(strpos(strtolower($popular_since), 'day'));

        if($strlen_popular_since > 0 && (strpos(strtolower($popular_since), 'second') >= 0 || strpos(strtolower($popular_since), 'day') >= 0)){
          $popular_since = '';
        }

        $availableInventory = $ProductModel->availableInventory($fp->id);

        //echo "availableInventory="; pr($availableInventory);
        ?>

        <li>
 <div class="col-md-12 noPaddings">

 <div class="topHeadImg">
    <?php
    if( !empty($brand_logo) && file_exists(public_path('storage/'.$brand_logo_path.$brand_logo)) ){
        $logo_img_src = url('public/storage/'.$brand_logo_path.$brand_logo);
        ?>
        
        <div class="logoimg"><img src="{{$logo_img_src}}" alt="{{$fp->name}}" /></div>
        <?php
    }
    else{
        ?>
        <div class="logoimg"><img src="{{url('public/assets/img/newUser.png')}}" alt="{{$fp->name}}" /></div>
        <?php
    }
    ?>
      <div class="headImgTittle">
        <strong>
          {{$brand_name}}
          <?php
          /*
          <a href="javascript:void(0)" title="Like it" class="like_brand like_brand_{{$brand_id}}" style="color:#09D1B8;" data-categoryid="{{$fp->category_id}}" data-brandid="{{$brand_id}}"><i class="fa {{$fav_icon_class}}" aria-hidden="true"></i></a>
          */
          ?>
          <a href="javascript:void(0)" title="Like it" class="like_product like_product_{{$fp->id}}" data-id="{{$fp->id}}" data-liked="{{$liked}}" style="color:#09D1B8;"><i class="fa {{$fav_icon_class}}" aria-hidden="true"></i></a>
        </strong>

        <?php
        if(!empty($popular_since)){
          ?>
          <p class="fsMini"><img src="{{url('public/assets/img/mail1.png')}}" alt="mail1"/>  Popular Since : <strong>{{ $popular_since }}</strong></p>
          <?php
        }
        ?>

        
      </div>

    </div>                       

  </div>

  <div class="itemListing thumbnailNew thumbnail">

    <?php
    if (!empty($defaultPhoto) && $storage->exists($defaultPhoto->path . $defaultPhoto->name)) {
        ?>
        <a class="listimg" href="{{url('products/detail/'.$fp->uri)}}"><img src="{{url('public/storage/'.$defaultPhoto->path . $defaultPhoto->name)}}" alt="{{$fp->name}}"></a>
        <?php
    }
    ?>

    <div class="caption captionNew">
      <div class="tittleArea"> <span class="tittle"><a href="{{url('products/detail/'.$fp->uri)}}">{{$fp->name}}</a></span> </div>
      <div class="col-md-7 col-sm-6 col-xs-6 noPaddings priceLeft">
        <p>
        <i class="fa fa-inr" aria-hidden="true"></i>{{ $product_price }}/pc + GST @ {{$gst}}%
        <?php
        if($cross_price > 0){
          ?>
          <span style="text-decoration: line-through; color: #747289;">{{ $cross_price }}</span>
          <?php
        }
        ?>
        </p>
        <p class="gstPrice"><i class="fa fa-inr" aria-hidden="true"></i>{{$product_gst_price}}/pc</p>
      </div>
      <div class="col-md-5 col-sm-6 col-xs-6 noPaddings priceRight priceRightNew">
        
      <?php
      /*
      <button class="btn buyNow btn-default"> BUY NOW </button>
      */
      ?>

      <?php
      if(count($availableInventory) > 0){
        ?>
        <a href="{{url('products/detail/'.$fp->uri)}}" class="btn buyNow btn-default">BUY NOW </a>
        <?php
      }
      else{
        ?>
        <a href="{{url('products/detail/'.$fp->uri)}}" class="btn buyNow out_of_stock">Out of Stock </a>
        <?php
      }
      ?>

        <p>Min. qty. {{$fp->min_qty}} Set({{$fp->piece_per_set}} pc/set)</p>
      </div>
    </div>
  </div>
</li>

        <?php
    }
}
?>