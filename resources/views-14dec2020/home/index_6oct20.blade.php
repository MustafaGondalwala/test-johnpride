<!DOCTYPE html>
<html>
<head>  

  @include('common.head')

  <link rel="stylesheet" type="text/css" href="{{url('public/css/owl.carousel.min.css')}}" />
</head>
<body class="home">

  @include('common.header')

  <?php
  $storage = Storage::disk('public');

  /*HOME_HEADING_2
HOME_HEADING_3
HOME_HEADING_4*/

$websiteSettingsNamesArr = ['HOME_VIDEO', 'HOME_HEADING_1', 'HOME_HEADING_2', 'HOME_HEADING_3', 'HOME_HEADING_4'];

$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

$HOME_HEADING_1 = (isset($websiteSettingsArr['HOME_HEADING_1']))?$websiteSettingsArr['HOME_HEADING_1']->value:'';
$HOME_HEADING_2 = (isset($websiteSettingsArr['HOME_HEADING_2']))?$websiteSettingsArr['HOME_HEADING_2']->value:'';
$HOME_HEADING_3 = (isset($websiteSettingsArr['HOME_HEADING_3']))?$websiteSettingsArr['HOME_HEADING_3']->value:'';
$HOME_HEADING_4 = (isset($websiteSettingsArr['HOME_HEADING_4']))?$websiteSettingsArr['HOME_HEADING_4']->value:'';


  ?>
  <?php
  if(!empty($banners) && count($banners) > 0){
    //pr($banners);
    
    ?>
    <section class="banner owl-carousel fullwidth">

      <?php
      $path = 'banners/';
      foreach($banners as $banner){
        $images = (isset($banner->Images))?$banner->Images:'';
        $link = '';
              if(!empty($banner->link))
              {
                $link = $banner->link;
              }

        if(!empty($images) && count($images) > 0){
          foreach($images as $image){
            if(!empty($image->name) && $storage->exists($path.$image->name)){
              


              ?>

              

              <div style="background: url('{{url('public/storage/banners/'.$image->name)}}') center center no-repeat; background-size:cover;">   
              <?php if(!empty($link)){ ?>
                <a href="{{$link}}" target="_blank">
              <?php } ?>           
              <img src="{{url('public/images/blankimg.png')}}" alt="{{$banner->title}}" class="show_desktop_banner" />
               <img class="show_mobile_banner" src="{{url('public/images/blankimg-mobile.png')}}" alt="{{$banner->title}}" /> 
               <?php if(!empty($link)){ ?>
                </a>
              <?php } ?>
            </div>

            

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
      <h1 class="heading">{{$HOME_HEADING_1}} <!-- <small><a href="#">View more</a></small> --></h1>

      <?php
      if(!empty($HomeImages) && count($HomeImages) > 0) {
        ?>
        <ul class="brandli">

          <?php
          $img_path = 'home_images/';
          foreach($HomeImages as $hi){
            $hi_image = (isset($hi->image))?$hi->image:'';
            if(!empty($hi_image) && $storage->exists($img_path.$hi_image)){

              $link = '#';
             // pr($HomeImages);
              

              if(!empty($hi->link)){
                $link = $hi->link;
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
      <h2 class="heading">{{$HOME_HEADING_2}} <small><a href="{{url('products')}}">View more</a></small></h2>
      <div class="sellerslider owl-carousel">

        <?php
        foreach($productsBestSeller as $product){ 
          $product_image = (isset($product->defaultImage))?$product->defaultImage:'';
          $reverse_image = (isset($product->reverseImage))?$product->reverseImage:'';

          //pr($product_image);
          ?>

          <a href="<?php echo url('products/details/'.$product->slug); ?>" class="product">

            <?php
            if(!empty($product_image) && count($product_image) > 0){

              $img_path = 'products/';

              if(!empty($product_image->image)){ 
                $mainImageUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $product_image->image);
              ?>
                <div class="flip-inner">
                  <img src="{{url('public')}}/images/blank.png" alt="{{$product->name}}"/>

                  <div class="flip-front"><img src="{{$mainImageUrl}}" alt="{{$product->name}}" /></div>

                  <?php
                  if(!empty($reverse_image->image)){
                    $revImageUrl = str_replace("https://www.dropbox.com/", "https://www.dl.dropboxusercontent.com/", $reverse_image->image);
                    ?>
                    <div class="flip-back">
                      <img src="{{$revImageUrl}}" alt="{{$product->name}}" />
                    </div>  
                    <?php
                  }
                  ?> 

                </div>
                <?php
              }
            }
            ?>

            <span>{{$product->name}}</span>
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
      <h2 class="heading">{{$HOME_HEADING_3}} <small>View more</small></h2>
      <ul class="trandingli">
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
//$homeVideo = CustomHelper::WebsiteSettings('HOME_VIDEO');
$homeVideo = (isset($websiteSettingsArr['HOME_VIDEO']))?$websiteSettingsArr['HOME_VIDEO']->value:'';

if(!empty($homeVideo) && count($homeVideo) > 0){
  ?>
  <section class="videoimg fullwidth">
   <?php //echo $homeVideo; ?>
	  <?php /*?><iframe width="560" height="315" src="https://www.youtube.com/embed/X-gO9fWSKQQ?autoplay=1&mute=1" name="youtube embed" allow="autoplay; encrypted-media" allowfullscreen></iframe><?php */?>
 </section>
 <?php
}

?>

<?php
if(!empty($instaMedia) && count($instaMedia) > 0 && array_key_exists('data', $instaMedia)){
  ?>
  <section class="followsec fullwidth">
    <div class="container">
      <h2 class="heading">{{$HOME_HEADING_4}} <small>/ Social Media</small></h2>
      <div class="followslider owl-carousel">

        <?php
        foreach($instaMedia['data'] as $insta){

          //pr($insta);

          $pic_text=$insta['caption']['text'];
          $pic_link=$insta['link'];
          $pic_like_count=$insta['likes']['count'];
          $pic_comment_count=$insta['comments']['count'];
          $pic_src=str_replace("http://", "https://", $insta['images']['standard_resolution']['url']);
          $pic_created_time=date("F j, Y", $insta['caption']['created_time']);
          $pic_created_time=date("F j, Y", strtotime($pic_created_time . " +1 days"));

          ?>
          <div class="followbox">
            <a href='{{$pic_link}}' target='_blank'>
              <img src='{{$pic_src}}' alt='{{$pic_text}}'>
            </a>
          </div>
          <?php
        }
        ?>

      </div>
      <div class="clearfix"></div>
      <div class="pages_social">
      <ul>
          <li><a href="https://www.facebook.com/SlumberJill/" target="_blank"><i class="facebookicon"></i></a></li>
          <li><a href="https://twitter.com/slumberjill" target="_blank"><i class="twittericon"></i></a></li>
          <li><a href="#" target="_blank"><i class="linkedinicon"></i></a></li>
      <li><a href="https://www.instagram.com/slumberjill_women/" target="_blank"><i class="instragramicon"></i></a></li>
        </ul>
      </div>
      <!-- <a href="https://www.instagram.com/slumberjill_women/" target="_blank" class="socialbtn">Let’s Get Social</a>  -->
    </div>
  </section>
  <?php
}
?>


@include('common.footer')

<script type="text/javascript" src="{{url('public')}}/js/owl.carousel.min.js"></script> 

<script>
 	$( ".dgsgs" ).click(function(e) {
    e.stopPropagation();
    if($(window).width() < 1023)
    {		
		$(this).next().fadeToggle();		 
    }
  });

  $('.banner').owlCarousel({
    loop:true,
    margin:0,
      autoplay:true,
    autoplayTimeout:3000,
    smartSpeed:1000,
    items:1,
    dots:false,
    nav:false,   
	 autoHeight:false,
  });
  
  $('.sellerslider').owlCarousel({
    loop:false,
    margin:20,
    items:4,
    dots:false,
    nav:true,
    responsive:{
      0:{
        items:1.5,
		   loop:true,
		   nav:false,
      },
      600:{
        items:2,
		   nav:false,
      },
      768:{
        items:3,
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
        items:1.5,
		   nav:false,
      },
      600:{
        items:3,
		  nav:false,
      },
      1200:{
        items:4
      }
    }
  });
	
	 if($(window).width() < 767)
    {		
		$(".trandingli").addClass('trandingslider owl-carousel');	
		$(".brandli").addClass('brandslider owl-carousel');	
    }
	
	$('.trandingslider').owlCarousel({
    loop:true,
    margin:20,
    items:2,
    dots:false,
    nav:true,
    responsive:{
      0:{
        items:1.5,
		  margin:10,
		   nav:false,
      },
      600:{
        items:2,
		   nav:false,
      } 
    }
  });

  $('.brandslider').owlCarousel({
    loop:true,
    margin:10,
    items:2,
    dots:false,
    nav:true,
    responsive:{
      0:{
        items:1.5,
		  margin:10,
		   nav:false,
      },
      600:{
        items:2,
		   nav:false,
      } 
    }
  });	

</script>
<script>
        var tv;
        var vid='{{$homeVideo}}';
        function onYouTubePlayerAPIReady() {
          tv = new YT.Player('tv', {
            autoplay : 1,
            videoId: vid,
            width: 1920,          
            height: 720,
            playerVars : {
              'autoplay' : 1,
              'rel' : 0,
              'showinfo' : 0,
              'showsearch' : 0,
              'controls' : 0,
              'loop' : 1,
              'enablejsapi' : 1,
              'iv_load_policy': 3,
              'cc_load_policy': 0,
              'playlist': vid,
              'vq':'hd720'
            },
            events: {
              "onReady": onPlayerReady,
              'onStateChange': onPlayerStateChange
            }
          });
        }
        function onPlayerReady(event) {
          event.target.setVolume(0);
          event.target.playVideo();
        }
        function onPlayerStateChange(event) {        
          var id = vid;
            if(event.data === YT.PlayerState.ENDED){
                player.loadVideoById(id);
            }
        }
        var video = $('<div />', {
          class: 'video-wrapper',
        });
        var videosrc = $('<div />', {
            id: 'tv',
            class: 'screen',
        }).appendTo(video);
        var videoover = $('<div />', {
            class: 'cover',
        }).appendTo(video);
        video.appendTo($('.videoimg'));
        $(".video-wrapper").show();
        $.getScript("https://www.youtube.com/iframe_api");
</script>
<style>
  .videoimg{
    overflow: hidden;
    position: relative;
  }
  .video-wrapper .cover {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 1;
}
</style>
</body>
</html>