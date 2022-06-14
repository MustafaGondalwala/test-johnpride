<?php
if(!empty($products) && count($products) > 0){
    foreach($products as $product){
        //prd($product);

        //$brand = $product->brand;
        $brand = getProductBrand($product->brand_id);
        //prd($brand);

        $brand_id = (isset($brand->id))?$brand->id:$product->brand_id;
        $brand_name = (isset($brand->name))?$brand->name:'';
        $brand_logo = (isset($brand->logo))?$brand->logo:'';
        $brand_logo_path = (isset($brand->logo_path))?$brand->logo_path:'';

        //$defaultPhoto = $product->defaultPhoto;
        $defaultPhoto = getProductPhotos($product->id, $default=1);
        //prd($defaultPhoto);
        
        //$photoPath = $product->defaultPhoto['path'];
        $photoPath = $defaultPhoto->path;

        $storage = Storage::disk('public');

        $params['user_id'] = $user_id;
        //$params['subcategory_id'] = $product->category_id;
        $params['brand_id'] = $brand_id;

        $FavouriteBrands = getFavouriteBrands($params, $result_type = 1);
        //pr($FavouriteBrands);

        $fav_icon_class = "fa-heart-o";

        if(count($FavouriteBrands) > 0){
            if($FavouriteBrands->is_favourite == 1){
                $fav_icon_class = "fa-heart";
            }
        }

        $gst = config('custom.gst_default');
        //echo 'gst='.$gst;

        $product_price = $product->price;

        //prd($product);
        $price_type_id = 0;

        if(isset($product->price_type_id) && $product->price_type_id > 0){
          $price_type_id = $product->price_type_id;


          $PriceType = $ProductModel->PriceType($product->id, $price_type_id);
        //prd($PriceType);

          if(count($PriceType)){
            if(!empty($PriceType->price) && $price_type_id == $PriceType->price_type_id){
              $product_price = $PriceType->price;
            }
          }
        }

        $product_gst_price = round($product_price*(100+$gst)/100);
        //echo 'product_gst_price='.$product_gst_price

        $popular_since = date_format2($product->created_at, 'Y');
        ?>

        <div class="col-md6 col-md-6 col-sm-6 col-xs-12">
 <div class="col-md-12 noPaddings">

 <div class="topHeadImg">
    <?php
    if( !empty($brand_logo) && file_exists(public_path('storage/'.$brand_logo_path.$brand_logo)) ){
        $logo_img_src = url('public/storage/'.$brand_logo_path.$brand_logo);
        ?>
        <img src="{{$logo_img_src}}" alt="{{$product->name}}">
        <?php
    }
    else{
        ?>
        <img src="{{url('public/assets/img/newUser.png')}}" style="height: 100px;" alt="{{$product->name}}">
        <?php
    }
    ?>
      <div class="headImgTittle">
        <strong>{{$brand_name}}</strong>

         <a href="javascript:void(0)" title="Like it" class="like_brand like_brand_{{$brand_id}}" style="color:#09D1B8;" data-categoryid="{{$product->category_id}}" data-brandid="{{$brand_id}}"><i class="fa {{$fav_icon_class}}" aria-hidden="true"></i></a>

        <p class="fsMini"><img src="{{url('public/assets/img/mail1.png')}}" alt="mail1"/>  Popular Since : <strong>{{$popular_since}}</strong></p>
      </div>

    </div>                       

  </div>

  <div class="itemListing thumbnailNew thumbnail">

    <?php
    if (!empty($defaultPhoto) && $storage->exists($photoPath . $defaultPhoto->name)) {
        ?>
        <img src="{{url('public/storage/'.$photoPath . $defaultPhoto->name)}}" alt="{{$product->name}}">
        <?php
    }
    ?>

    <div class="caption captionNew">
      <div class="tittleArea"> <span class="tittle">{{$product->name}}</span> </div>
      <div class="col-md-7 col-sm-6 col-xs-6 noPaddings priceLeft">
        <p><i class="fa fa-inr" aria-hidden="true"></i>{{number_format($product_price, 0)}}/pc + GST @5%</p>
        <p class="gstPrice"><i class="fa fa-inr" aria-hidden="true"></i>{{$product_gst_price}}/pc</p>
      </div>
      <div class="col-md-5 col-sm-6 col-xs-6 noPaddings priceRight priceRightNew">
        
      <?php
      /*
      <button class="btn buyNow btn-default"> BUY NOW </button>
      */
      ?>

        <a href="{{url('products/detail/'.$product->uri)}}" class="btn buyNow btn-default"> BUY NOW </a>

        <p>Min. qty. {{$product->min_qty}} Set({{$product->piece_per_set}} pc/set)</p>
      </div>
    </div>
  </div>
</div>

        <?php
    }
}
?>