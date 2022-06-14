<!DOCTYPE html>
<html><head>  

  @include('common.head')

  <link rel="stylesheet" type="text/css" href="{{url('public/css/owl.carousel.min.css')}}" />
</head>
<body class="home">

  @include('common.header')

  <?php $storage = Storage::disk('public'); ?>

  <?php
  if(!empty($banners) && count($banners) > 0){
    ?>
    <section class="banner owl-carousel fullwidth">

      <?php
      $path = 'banners/';
      foreach($banners as $banner){
        $images = (isset($banner->Images))?$banner->Images:'';

        if(!empty($images) && count($images) > 0){
          foreach($images as $image){
            if(!empty($image->name) && $storage->exists($path.$image->name)){
              ?>
              <img src="{{url('public/storage/banners/'.$image->name)}}" alt="{{$banner->title}}" />

              <?php
            } 
          }
        }
      }
      ?>
    </section>
    <?php
  }
  ?>

  <section class="tranindingsec fullwidth">
    <div class="container">
      <h1 class="heading">Trending Now <!-- <small><a href="#">View more</a></small> --></h1>

      <?php
      if(!empty($HomeImages) && count($HomeImages) > 0) {
        ?>
        <ul>

          <?php
          $img_path = 'home_images/';
          foreach($HomeImages as $hi){
            $hi_image = (isset($hi->image))?$hi->image:'';
            if(!empty($hi_image) && $storage->exists($img_path.$hi_image)){

              $link = '#';

              if(!empty($HomeImages->link)){
                $link = $HomeImages->link;
              }

              ?>
              <li>
                <a href="<?php echo $link; ?>" target="_blank" class="tbox">
                  <img src="{{url('public/storage/'.$img_path.$hi_image)}}" alt="{{$hi->title}}" />
                  <span>{{ $hi->title }}</span>
                </a>

              </li>
              <?php
            }
          }
          
          ?>
        </ul>
        <?php
      }
      ?>

 </div>
</section>

<?php if(!empty($productsBestSeller) && count($productsBestSeller) > 0) { ?>
  <section class="bestsellers fullwidth">
    <div class="container">
      <h2 class="heading">Best Sellers <small><a href="{{url('products')}}">View more</a></small></h2>
      <div class="sellerslider owl-carousel">

        <?php foreach($productsBestSeller as $product){ 
          $product_image = (isset($product->defaultImage))?$product->defaultImage:'';
          $reverse_image = (isset($product->reverseImage))?$product->reverseImage:'';
          ?>

          <a href="<?php echo url('products/details/'.$product->slug); ?>" class="product">

            <?php
            if(!empty($product_image) && count($product_image) > 0){

              $img_path = 'products/';

              if(!empty($product_image->image) && $storage->exists($img_path.$product_image->image)){ ?>
                <div class="flip-inner">
                  <img src="{{url('public')}}/images/blank.png" alt="img"/>

                  <div class="flip-front"><img src="{{url('public/storage/'.$img_path.$product_image->image)}}" alt="img" /></div>

                  <?php if(!empty($reverse_image->image) && $storage->exists($img_path.$reverse_image->image)){ ?>
                    <div class="flip-back">
                      <img src="{{url('public/storage/'.$img_path.$reverse_image->image)}}" alt="img" />
                    </div>  
                    <?php
                  }
                  ?> 

                </div>
              <?php }
            } ?>

            <span>{{$product->name}} &nbsp; Shorts Set </span>
          </a>
          <?php
        }
        ?>

      </div>
    </div>
  </section>
  <?php
}
?>


<?php
if(!empty($brands) && count($brands) > 0) { ?>
  <section class="brandsec fullwidth">
    <div class="container">
      <h2 class="heading">Brands in Focus <small>View more</small></h2>
      <ul>
        <?php foreach($brands as $brand){  ?>
          <li>
           <?php $img_path = 'brands/';
           $icon_path = 'brands/icon/'; ?>

           <a href="{{route('products.list', ['brand[]'=>$brand->slug])}}" class="brandbox">
             <?php
             if(!empty($brand->image) && $storage->exists($img_path.$brand->image)) {
              ?>
              <img src="{{url('public/storage/'.$img_path.$brand->image)}}" alt="img" />
              <?php
            }

            if(!empty($brand->icon) && $storage->exists($icon_path.$brand->icon)) {
              ?>
              <span><img src="{{url('public/storage/'.$icon_path.$brand->icon)}}" alt="img" /></span>
              <?php
            }
            ?>
          </a>

        </li>
        <?php
      }
      ?>

    </ul>
  </div>
</section>
<?php
}
?>

<?php 
$homeVideo = CustomHelper::WebsiteSettings('HOME_VIDEO');

if(!empty($homeVideo) && count($homeVideo) > 0){
  ?>
  <section class="videoimg fullwidth">
   <?php echo $homeVideo; ?>
 </section>
 <?php
}
?>

<section class="followsec fullwidth">
  <div class="container">
    <h2 class="heading">Follow us <small>/ Social Media</small></h2>
    <div class="followslider owl-carousel">
      <div class="followbox">
        <img src="{{url('public')}}/images/follow1.jpg" alt="img" /> 
      </div>

      <div class="followbox">
        <img src="{{url('public')}}/images/follow2.jpg" alt="img" /> 
      </div>

      <div class="followbox">
        <img src="{{url('public')}}/images/follow3.jpg" alt="img" />
      </div>

      <div class="followbox">
        <img src="{{url('public')}}/images/follow4.jpg" alt="img" />
      </div>
      <div class="followbox">
        <img src="{{url('public')}}/images/follow3.jpg" alt="img" />
      </div>

      <div class="followbox">
        <img src="{{url('public')}}/images/follow4.jpg" alt="img" />
      </div>
    </div>
    <div class="clearfix"></div>
    <a class="socialbtn" href="#">Let’s Get Social</a> 
  </div>
</section>


@include('common.footer')

<script type="text/javascript" src="{{url('public')}}/js/owl.carousel.min.js"></script> 

<script>


  $('.banner').owlCarousel({
    loop:true,
    margin:20,
    items:1,
    dots:false,
    nav:false,     
  });
  
  $('.sellerslider').owlCarousel({
    loop:true,
    margin:20,
    items:4,
    dots:false,
    nav:true,
    responsive:{
      0:{
        items:1
      },
      600:{
        items:3
      },
      1000:{
        items:4
      }
    }
  });
  
  $('.followslider').owlCarousel({
    loop:true,
    margin:20,
    items:4,
    dots:false,
    nav:true,
    responsive:{
      0:{
        items:1
      },
      600:{
        items:3
      },
      1000:{
        items:4
      }
    }
  })
</script>


</body>
</html>