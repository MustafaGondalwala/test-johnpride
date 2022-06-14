<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')

<section class="fullwidth innerpage prideonme_pages">
<div class="container">	
	<div class="fullwidth scochlist">
	   <h1 class="heading">{{$heading}}</h1>
     
     <?php
     $image_path = config('custom.image_path');
     $storage = Storage::disk('public');
        if(!empty($customerPictures) && count($customerPictures) > 0){
        ?>
         <ul>
          <?php
          $path = 'customer_picture/';
          $products_img_path = 'products/';
          foreach($customerPictures as $customerPicture){
           //pr($customerPicture->product);
            $product_image = '';
            $product_name = '';
            $productPrice = '';
            $mainPrice = '';
            $price = '';
            $salePrice = '';
            $link = '#';

            $customerPro = isset($customerPicture->product) ? $customerPicture->product:'';

            if($customerPro){


              $product_image = ($customerPro && isset($customerPro->defaultImage))?$customerPro->defaultImage->image:'';

              $product_name= ($customerPro)?$customerPro->name:'';
              $product_slug= ($customerPro)?$customerPro->slug:'';

              $mainPrice = ($customerPro)?$customerPro->price:0;
              $price = ($customerPro)?$customerPro->price:0;
              $salePrice = ($customerPro)?$customerPro->sale_price:00;

              $productPrice = $mainPrice;
              if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
                $productPrice = $salePrice;
              }
              else{
                $salePrice = $price;
              }

            }

           
          if(!empty($customerPicture->url))
          {
            //$link = $customerPicture->url;
          }
          ?>

          <li> 
              <a class="cimg" href="{{$link}}">
                  <div class="content_text">
                <img src="{{url('images/instagramicon-white.png')}}" alt="" /> 
                <span>{{$customerPicture->title}}</span></div>
                
                <img src="{{url('images/blankscotch.png')}}" alt="" />
                <?php if(!empty($customerPicture->image) && $storage->exists($path.$customerPicture->image)){ ?>
                <img src="{{ url('storage/'.$path.'thumb/'.$customerPicture->image) }}" class="scotchpic" alt="{{$customerPicture->title}}" />
              <?php } ?>
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <?php if(!empty($customerPicture->image) && $storage->exists($path.$customerPicture->image)){ ?>
                      <img src="{{ url('storage/'.$path.''.$customerPicture->image) }}" class="scotchpic" alt="{{$customerPicture->title}}" />
                    <?php } ?>

                    
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2">
                          <img src="{{url('images/instagramicon.png')}}" alt="" /> 

                          <span>{{$customerPicture->title}}</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <?php if($customerPro){ ?>
                        <div class="probox">
                            <div class="cimg">
                              <?php if(!empty($product_image)){ ?>
                                <img src="{{$image_path.$product_image}}" alt="{{$product_name}}" /> 
                              <?php } ?>
                             
                            </div>
                            <div class="procont">
                              <div class="heading3">{{$product_name}}</div>
                              <p>
                                <small>&#x20B9;{{ number_format($productPrice) }}</small> 
                                <?php
                                if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
                                  ?>
                                  <del>&#x20B9;{{number_format($price)}}</del> 
                                  <?php
                                }
                                ?>
                              </p>
                              <a class="shopbtn shopbtn-click" href="<?php echo url('products/details/'.$product_slug); ?>">Buy this product</a>
                            </div>
                          </div>

                        <?php } ?>
                      </div>
                  </div>
                </div>
            </div>
        </li>


          <?php } ?>


         </ul>

        <?php } ?>
	</div>
  </div>
</section>

@include('common.footer')

<script>
$(".shopbtn-click").on('click', function(event){
    
    var url = ($(this).attr('href'))?$(this).attr('href'):window.location.href;
    //console.log('href=>', $(this).attr('href'))
    // similar behavior as clicking on a link
    window.location.href = url;
});
</script>
</body>
</html>