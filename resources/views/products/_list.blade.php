<?php

$storage = Storage::disk('public');

$image_path = config('custom.image_path');

$userWishlist = '';

if(auth()->check()){
  $userWishlist = auth()->user()->userWishlist->keyBy('product_id');

  //pr($userWishlist->toArray());
}

foreach ($products as $product){

  $product_image = (isset($product->defaultImage))?$product->defaultImage:'';
  $reverse_image = (isset($product->reverseImage))?$product->reverseImage:'';

  $mainPrice = $product->price;
  $stamp = $product->stamp;

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


$off = CustomHelper::calculateProductDiscount($mainPrice ,$salePrice);
$discount = number_format($off, 2);


  if(!empty($productBrand) && count($productBrand) > 0){

    //$productBrand = $productBrand->where('status',1)->all();
    //pr($productBrand);
    $brandName = isset($productBrand->name) ? $productBrand->name:'';
  }

  ?>
  <li>
<?php if(isset($userWishlist[$product->id])){ ?>
  <span class="wishlisticonsh"><i class="wishlisticon"></i></span>
  <?php } ?>
    <a href="<?php echo url('products/details/'.$product->slug); ?>" class="product">
      <?php if(!empty($product_image) && count($product_image) > 0){
        $img_path = 'products/';
        if(!empty($product_image->image)){
          //$mainimageUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $product_image->image);
          $mainimageUrl = $product_image->image;
          //echo "Stamp:". $stamp;
          ?>
         <div class="productimg">
          <img src="{{url('')}}/images/blank.png" alt="{{$product->name}}"/>
          <?php
          if(!empty($stamp)){

             $class = "stamp_btn";



             if($stamp == "premium" || $stamp == "PREMIUM" || $stamp == "Premium")
             {
               $class = "stamp_btn_p";
             }
             elseif($stamp == "eco" ||  $stamp == "ECO" || $stamp == "Eco")
             {
              
              $class = "stamp_btn_eco";

             }


            ?>
            <div class="{{$class}}">
              <?php echo (!empty($stamp))?$stamp:''; ?>
            </div>
            <?php
          }
          ?>

          <div class="pimg">
            <img src="{{$image_path.$mainimageUrl}}" alt="{{$product->name}}" />
          </div>  

          <?php /*
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
          */ ?>

        </div>
        <?php } } ?>

    <div class="procont">
      <!-- <p><span> {{$brandName}} </span></p> -->
      <div class="heading3"> {{$product->name}} </div>
      <p><small>&#x20B9;{{ number_format($productPrice) }}</small> 
        <?php
        if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
          ?>
          <del>&#x20B9;{{number_format($price)}}</del> 
          <p><small class="offpro">({{number_format($discount)}}% OFF)</small></p>
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