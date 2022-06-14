<?php

$storage = Storage::disk('public');

$userWishlist = '';

if(auth()->check()){
  $userWishlist = auth()->user()->userWishlist->keyBy('product_id');

  //pr($userWishlist->toArray());
}

foreach ($products as $product){

  $product_image = (isset($product->defaultImage))?$product->defaultImage:'';
  $reverse_image = (isset($product->reverseImage))?$product->reverseImage:'';

  $mainPrice = $product->price;

  $price = $product->price;
  $salePrice = $product->sale_price;

  $productPrice = $mainPrice;
  if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
    $productPrice = $product->sale_price;
  }
  else{
    $salePrice = $product->price;
  }

  $productBrand = isset($product->productBrandStatus) ? $product->productBrandStatus:'';

  $brandName = '';

  if(!empty($productBrand) && count($productBrand) > 0){

    //$productBrand = $productBrand->where('status',1)->all();
    //pr($productBrand);
    $brandName = isset($productBrand->name) ? $productBrand->name:'';
  }

  ?>
  <li>
    <?php
    if(isset($userWishlist[$product->id])){
      ?>
      <span><i class="wishlisticon"></i></span>
      <?php
    }
    ?>
    <a href="<?php echo url('products/details/'.$product->slug); ?>" class="product">
      <?php
      if(!empty($product_image) && count($product_image) > 0){

        $img_path = 'products/';
        if(!empty($product_image->image)){
          $mainimageUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $product_image->image);
          ?>
         <div class="flip-inner">
          <img src="{{url('public')}}/images/blank.png" alt="{{$product->name}}"/>

          <div class="flip-front">
            <img src="{{$mainimageUrl}}" alt="{{$product->name}}" />
          </div>  

          <div class="flip-back">
            <?php
            if(!empty($reverse_image->image)){
              $revimageUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $reverse_image->image);
              ?>
              <img src="{{$revimageUrl}}" alt="{{$product->name}}" />
              <?php
            }
            else{
              ?>
              <img src="{{$mainimageUrl}}" alt="{{$product->name}}" />
              <?php
            }
            ?>   
          </div>

        </div>
        <?php
      }
    }
    ?>

    <div class="procont">
      <p><span> {{$brandName}} </span></p>
      <p> {{$product->name}} </p>
      <p><strong>Just</strong><small>&#x20B9;{{ number_format($productPrice) }}</small> 
        <?php
        if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
          ?>
          <del>&#x20B9;{{number_format($price)}}</del> 
          <?php
        }
        ?>
      </p>
    </div>
  </a>
</li>

<?php
}
?>

<?php
$hasMore = true;
if($totalCount != $viewCount){
  ?>
  <li class="loadMoreBox">
    <form name="loadMoreForm" method="post">

      <input type="hidden" name="total_count" value="{{$totalCount}}">
      <input type="hidden" name="view_count" value="{{$viewCount}}">

      <?php
      /*
      <a href="javascript:void(0)" class="loadMoreBtn">Load more</a>
      */
      ?>
    </form>
  </li>
  <?php
}
?>