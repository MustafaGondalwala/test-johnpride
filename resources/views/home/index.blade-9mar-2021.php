<!DOCTYPE html>

<html>

<head>  
<style type="text/css">
  .pop_img {display: block;}
  .insta_images {    background-size: cover; background-position: center center;display: block;}
</style>


  @include('common.head')



  <link rel="stylesheet" type="text/css" href="{{url('css/owl.carousel.min.css')}}" />

</head>

<body class="home">
  @include('common.header')
  <?php
  $storage = Storage::disk('public');
  /*HOME_HEADING_2
HOME_HEADING_3
HOME_HEADING_4*/
$websiteSettingsNamesArr = ['HOME_VIDEO', 'HOME_HEADING_1', 'HOME_HEADING_2', 'HOME_HEADING_3', 'HOME_HEADING_4', 'HOME_HEADING_5', 'HOME_HEADING_6', 'HOME_HEADING_7','NEWSLETTER_BACKGROUND_IMAGE','HOME_IMAGE_1','SUB_TITLE_1','SUB_TITLE_2','SUB_TITLE_3'];
$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);
$HOME_HEADING_1 = (isset($websiteSettingsArr['HOME_HEADING_1']))?$websiteSettingsArr['HOME_HEADING_1']->value:'';
$HOME_HEADING_2 = (isset($websiteSettingsArr['HOME_HEADING_2']))?$websiteSettingsArr['HOME_HEADING_2']->value:'';
$HOME_HEADING_3 = (isset($websiteSettingsArr['HOME_HEADING_3']))?$websiteSettingsArr['HOME_HEADING_3']->value:'';
$HOME_HEADING_4 = (isset($websiteSettingsArr['HOME_HEADING_4']))?$websiteSettingsArr['HOME_HEADING_4']->value:'';
$HOME_HEADING_5 = (isset($websiteSettingsArr['HOME_HEADING_5']))?$websiteSettingsArr['HOME_HEADING_5']->value:'';
$HOME_HEADING_6 = (isset($websiteSettingsArr['HOME_HEADING_6']))?$websiteSettingsArr['HOME_HEADING_6']->value:'';
$HOME_HEADING_7 = (isset($websiteSettingsArr['HOME_HEADING_7']))?$websiteSettingsArr['HOME_HEADING_7']->value:'';
$NEWSLETTER_BACKGROUND_IMAGE = (isset($websiteSettingsArr['NEWSLETTER_BACKGROUND_IMAGE']))?$websiteSettingsArr['NEWSLETTER_BACKGROUND_IMAGE']->value:'';
$HOME_IMAGE_1 = (isset($websiteSettingsArr['HOME_IMAGE_1']))?$websiteSettingsArr['HOME_IMAGE_1']->value:'';

$SUB_TITLE_1 = (isset($websiteSettingsArr['SUB_TITLE_1']))?$websiteSettingsArr['SUB_TITLE_1']->value:'';
$SUB_TITLE_2 = (isset($websiteSettingsArr['SUB_TITLE_2']))?$websiteSettingsArr['SUB_TITLE_2']->value:'';
$SUB_TITLE_3 = (isset($websiteSettingsArr['SUB_TITLE_3']))?$websiteSettingsArr['SUB_TITLE_3']->value:'';

$image_path = config('custom.image_path');
?>

<?php if(!empty($banners) && count($banners) > 0){
//pr($banners);   ?> 
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
  <div style="background: url('{{url('storage/banners/'.$image->name)}}') center center no-repeat; background-size:cover;">
      <?php if(!empty($link)){ ?>
        <a href="{{$link}}" target="_blank">
      <?php } ?>
      <img src="{{url('images/blankimg.png')}}" alt="{{$banner->title}}" class="show_desktop_banner" />
       <img class="show_mobile_banner" src="{{url('images/blankimg-mobile.png')}}" alt="{{$banner->title}}" /> 
       <!-- <div class="bheading">Latest Sweatshirts</div> -->
       <?php if(!empty($link)){ ?>
        </a>
      <?php } ?>
    </div> 
    <?php }  } } } ?> 
</section>
<?php } ?>


<section class="collectionsec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
    <h2 class="headings2">{!!$HOME_HEADING_1!!}</h2>
  </div>

    <div class="fullwidth collectionslid owl-carousel">
      <?php 
      if(!empty($collections) && count($collections) > 0){ 
        $path = 'brands/';
        foreach($collections as $collection){

          if(!empty($collection->image) && $storage->exists($path.$collection->image))
          {
            ?>
            <a href="<?php echo url('products?collection='.$collection->slug); ?>" class="cimg">
              <img src="{{url('')}}/images/blank.png" alt=" "/>
              <div class="pimg">
                 <img src="{{ url('storage/'.$path.''.$collection->image) }}" alt="" />
              </div>
             
              <div class="title2">{{$collection->name}}</div>
            </a>

         <?php  }
        }
      }

      ?>

        <?php /* ?>
        <div class="cimg">
          <img src="{{url('images/collection1.jpg')}}" alt="" />
          <div class="title2">Shop Collection 1</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/collection2.jpg')}}" alt="" />
          <div class="title2">Shop Collection 2</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/collection3.jpg')}}" alt="" />
          <div class="title2">Shop Collection 3</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/collection1.jpg')}}" alt="" />
          <div class="title2">Shop Collection 4</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/collection2.jpg')}}" alt="" />
          <div class="title2">Shop Collection 5</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/collection3.jpg')}}" alt="" />
          <div class="title2">Shop Collection 6</div>
        </div>
        <?php */ ?>

         
    </div>
  </div>
</section>


<section class="celebratesec fullwidth">
  <div class="bgimg1" style="background:url({{$image_path.$HOME_IMAGE_1}}) center center no-repeat; background-size:cover;"></div>
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_2!!}</h2>
    <p><?php echo $SUB_TITLE_1;?></p>
  </div>
    <div class="fullwidth celebratslid owl-carousel">

      <?php 
      if(!empty($categories) && count($categories) > 0){ 
        $path = 'categories/';
        foreach($categories as $category){
          

          $categoryImages = (isset($category->defaultImage))?$category->defaultImage->image:'';
          $parentCategorySlug = (isset($category->parent))?$category->parent->slug:'';

          $url = route('products.list', ['p2cat'=>$category->slug]);

          if($parentCategorySlug != '')
          {
            $url = route('products.list', ['pcat'=>$parentCategorySlug, 'p2cat'=>$category->slug]);
          }



          if(!empty($categoryImages) && $storage->exists($path.$categoryImages))
          {
            ?>
            <a href="{{$url}}" class="cimg <?php echo 'parentCategorySlug=> '.$parentCategorySlug; ?>">
              <img src="{{url('')}}/images/blank.png" alt="{{$category->name}}"/>
              <div class="pimg">
                <img src="{{ url('storage/'.$path.''.$categoryImages) }}" alt="" />
              </div>
              
              <div class="title2">{{$category->name}}</div>
            </a>



         <?php  }
        }
      }

      ?>

      <?php /* ?>
        <div class="cimg">
          <img src="{{url('images/celebrate1.jpg')}}" alt="" />
          <div class="title2">Men Party Suit</div> 
        </div>
        <div class="cimg">
          <img src="{{url('images/celebrate2.jpg')}}" alt="" /> 
          <div class="title2">T-Shirt</div> 
        </div>
        <div class="cimg">
          <img src="{{url('images/celebrate3.jpg')}}" alt="" /> 
          <div class="title2">Men Party Suit</div> 
        </div>
        <div class="cimg">
          <img src="{{url('images/celebrate1.jpg')}}" alt="" /> 
          <div class="title2">T-Shirt</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/celebrate2.jpg')}}" alt="" /> 
          <div class="title2">T-Shirt</div>
        </div>
        <div class="cimg">
          <img src="{{url('images/celebrate3.jpg')}}" alt="" /> 
          <div class="title2">T-Shirt</div>
        </div>

        <?php */ ?>
        
         
    </div>
  </div>
</section>

<section class="upperwear fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_3!!}</h2> 
  </div>

  <?php 
  if(!empty($productsBestSeller) && count($productsBestSeller) > 0){ ?>
    <div class="fullwidth upperslid owl-carousel">
      <?php 
      $path = 'categories/';
      foreach($productsBestSeller as $productPopular){

        $product_image = (isset($productPopular->defaultImage))?$productPopular->defaultImage->image:'';

        $product_name= $productPopular->name;

        $mainPrice = $productPopular->price;
        $price = $productPopular->price;
        $salePrice = $productPopular->sale_price;

        $productPrice = $mainPrice;
        if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
          $productPrice = $salePrice;
        }
        else{
          $salePrice = $productPopular->price;
        }


       
        ?>


        <div class="probox">
          <div class="cimg">
             <img src="{{url('')}}/images/blank.png" alt="{{$product_name}}"/>  
            <?php if(!empty($product_image)){ ?>
              <div class="pimg">
             <a href="<?php echo url('products/details/'.$productPopular->slug); ?>"> <img src="{{$image_path.$product_image}}" alt="{{$product_name}}" /> </a>
             </div>
            <?php } ?>
          </div>
          <div class="procont">
            <div class="heading3"><a href="<?php echo url('products/details/'.$productPopular->slug); ?>">{{$product_name}}</a></div>
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
            <a class="shopbtn" href="<?php echo url('products/details/'.$productPopular->slug); ?>">Shop Now</a>
          </div>
        </div>



        <?php  
      } ?>
    </div>
  <?php } ?>



      <?php /* ?>
    <div class="fullwidth upperslid owl-carousel">
        <div class="probox">
          <div class="cimg">
            <img src="{{url('images/img5.jpg')}}" alt="" /> 
          </div>
          <div class="procont">
            <div class="heading3">Upper Wear</div>
            <p>Jeans Joggers</p>
            <a class="shopbtn" href="#">Shop Now</a>
          </div>
        </div>
        <div class="probox">
          <div class="cimg">
            <img src="{{url('images/img6.jpg')}}" alt="" /> 
          </div>
          <div class="procont">
            <div class="heading3">King Size Clothing</div>
            <p>Jeans Joggers</p>
            <a class="shopbtn" href="#">Shop Now</a>
          </div>
        </div>
        <div class="probox">
          <div class="cimg">
            <img src="{{url('images/img7.jpg')}}" alt="" /> 
          </div>
          <div class="procont">
            <div class="heading3">King Size Clothing</div>
            <p>Jeans Joggers</p>
            <a class="shopbtn" href="#">Shop Now</a>
          </div>
        </div>

    </div>
        <?php */ ?>
        
         
  </div>
</section>


<section class="newtosec fullwidth showpopup" style="background: url({{$image_path.$NEWSLETTER_BACKGROUND_IMAGE}}) center center no-repeat; background-size:cover;">
  <img src="{{url('images/blankimg.png')}}" alt="{{$banner->title}}" class="blanks" />
  <!-- <img src="{{url('images/newto.jpg')}}" class="newtoimg" alt="" /> -->
<!--   <div class="container">
    <div class="newtobox">
        <div class="heading2"><span>New to</span> John Pride?</div>
        <p>Here's a 10% welcome benefit to get you started.</p>
        <a class="explorbtn showpopup">Explore Now</a>
      </div>
  </div> -->
</section>



<section class="scotchsec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_5!!}</h2> 
      <p><?php echo $SUB_TITLE_2;?></p>
  </div>
    <div class="fullwidth scochlist">
       <?php
        if(!empty($customerPictures) && count($customerPictures) > 0){
        ?>
         <ul>
          <?php
          $path = 'customer_picture/';
          $products_img_path = 'products/';
          foreach($customerPictures as $customerPicture){
           
            $product_image = '';
            $product_name = '';
            $productPrice = '';
            $mainPrice = '';
            $price = '';
            $salePrice = '';
            $link = '#';

            $pictureProduct = isset($customerPicture->product) ? $customerPicture->product:'';

            //pr($pictureProduct);

            if(!empty($pictureProduct) && count($pictureProduct) > 0){
              //echo 'hi'; die;

              $product_image = ($pictureProduct && isset($pictureProduct->defaultImage))?$pictureProduct->defaultImage->image:'';

              $product_name= ($pictureProduct)?$pictureProduct->name:'';
              $product_slug= ($pictureProduct)?$pictureProduct->slug:'';

              $mainPrice = ($pictureProduct)?$pictureProduct->price:0;
              $price = ($pictureProduct)?$pictureProduct->price:0;
              $salePrice = ($pictureProduct)?$pictureProduct->sale_price:00;

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
          $link = '';//url('products/details/'.$product_slug);
          ?>

          <li> 
              <a class="cimg" href="{{$link}}">
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
                        <?php if(!empty($customerPicture->product) && count($customerPicture->product) > 0){ ?>
                        <a class="probox shopbtn-click" href="<?php echo url('products/details/'.$product_slug); ?>">
                            <div class="cimg">
                         
                              <?php
                              //pr($image_path);
                              if(!empty($product_image)){ ?>
                               
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
                              <div class="shopbtn shopbtn-click">Buy this product</div>
                            </div>
                          </a>

                        <?php } ?>
                      </div>
                  </div>
                </div>
            </div>
        </li>


          <?php } ?>


         </ul>

        <?php } ?>





       <?php /* ?> 
      <ul>
        <li> 
              <a class="cimg">
                <img src="{{url('images/blankscotch.png')}}" alt="" />
                <img src="{{url('images/img11.jpg')}}" class="scotchpic" alt="" />
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img11.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img11.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

        <li> 
              <a class="cimg">
                <img src="{{url('images/blankscotch.png')}}" alt="" />
                <img src="{{url('images/img6.jpg')}}" class="scotchpic" alt="" />
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img6.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img6.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

        <li> 
              <a class="cimg">
                <img src="{{url('images/blankscotch.png')}}" alt="" />
                <img src="{{url('images/img7.jpg')}}" class="scotchpic" alt="" />
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img7.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img7.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

        <li> 
              <a class="cimg">
                <img src="{{url('images/blankscotch.png')}}" alt="" />
                <img src="{{url('images/img8.jpg')}}" class="scotchpic" alt="" />
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img8.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img8.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

      </ul> 

       <?php */ ?>
         
    </div>

    <div class="fullwidth showall"><a class="seeallbtn" href="<?php echo url('prideonme'); ?>">See All</a></div>
  </div>
</section>


<section class="feedsec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_4!!}</h2> 
  </div>

  <?php 
  if(!empty($featuredBlogs) && count($featuredBlogs) > 0){ ?>
    <div class="fullwidth upperslid owl-carousel">
      <?php 
      $path = 'blogs/';
      foreach($featuredBlogs as $featuredBlog){

        $blog_image = (isset($featuredBlog->Images) && !empty($featuredBlog->Images))?$featuredBlog->Images[0]->image:'';
        $blog_date = CustomHelper::DateFormat($featuredBlog->blog_date, 'M d, Y');
       
        ?>
        <div class="feedbox">
          <div class="cimg blogimgbg">
            <a href="<?php echo url('blogs/'.$featuredBlog->slug); ?>">
              <img src="{{url('')}}/images/blog-blank.png" alt="{{$featuredBlog->title}}"/>
              <?php if(!empty($blog_image)){ ?>
                <div class="pimg">
              <img src="{{url('storage/'.$path.''.$blog_image) }}" alt="{{$featuredBlog->title}}" /> 
            </div>
            <?php } ?>
              
            </a>
          </div>
          <a class="feedtext" href="<?php echo url('blogs/'.$featuredBlog->slug); ?>">
            <div class="heading3">{{$featuredBlog->title}}</div>
            <p>{{CustomHelper::wordsLimit(strip_tags($featuredBlog->content), 100)}}</p>
            <p class="dates">{{$blog_date}}</p>
          </a>
        </div> 
        <?php  
      } ?>
    </div>
  <?php } ?>



  <?php /* ?>
    <div class="fullwidth upperslid owl-carousel">
        <div class="feedbox">
          <div class="cimg">
            <a href="#"><img src="{{url('images/img8.jpg')}}" alt="" /> </a>
          </div>
          <div class="feedtext">
            <div class="heading3"><a href="#">King Size Clothing</a></div>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            <p class="dates">24 November 2020</p>
          </div>
        </div> 
        <div class="feedbox">
          <div class="cimg">
            <a href="#"><img src="{{url('images/img9.jpg')}}" alt="" /> </a>
          </div>
          <div class="feedtext">
            <div class="heading3"><a href="#">King Size Clothing</a></div>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the  </p>
            <p class="dates">24 November 2020</p>
          </div>
        </div> 
        <div class="feedbox">
          <div class="cimg">
            <a href="#"><img src="{{url('images/img10.jpg')}}" alt="" /> </a>
          </div>
          <div class="feedtext">
            <div class="heading3"><a href="#">King Size Clothing</a></div>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and  </p>
            <p class="dates">24 November 2020</p>
          </div>
        </div> 
        <div class="feedbox">
          <div class="cimg">
            <a href="#"><img src="{{url('images/img9.jpg')}}" alt="" /> </a>
          </div>
          <div class="feedtext">
            <div class="heading3"><a href="#">King Size Clothing</a></div>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the  </p>
            <p class="dates">24 November 2020</p>
          </div>
        </div> 
         
    </div>
    <?php */ ?>

  </div>
</section>

<section class="scotchsec lookbooksec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_6!!}</h2> 
      <p><?php echo $SUB_TITLE_3;?></p>
  </div>
    <div class="fullwidth scochlist">

      <?php
        if(!empty($lookBooks) && count($lookBooks) > 0){
        ?>
         <ul>
          <?php
          $path = 'look_book/';
          $products_img_path = 'products/';
          foreach($lookBooks as $lookBook){
           // pr($lookBook->product);

            $product_image = ($lookBook->product && isset($lookBook->product->defaultImage))?$lookBook->product->defaultImage->image:'';

            $product_name= ($lookBook->product)?$lookBook->product->name:'';
            $product_slug= ($lookBook->product)?$lookBook->product->slug:'';

            $mainPrice = ($lookBook->product)?$lookBook->product->price:0;
            $price = ($lookBook->product)?$lookBook->product->price:0;
            $salePrice = ($lookBook->product)?$lookBook->product->sale_price:00;

            $productPrice = $mainPrice;
            if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
              $productPrice = $salePrice;
            }
            else{
              $salePrice = $price;
            }
          ?>

          <li> 
              <a class="cimg viewimg dd">   
                <img src="{{url('')}}/images/blank.png" alt="{{$lookBook->title}}"/>
                <?php if(!empty($lookBook->image) && $storage->exists($path.$lookBook->image)){ ?>
                  <div class="pimg">
                <img src="{{ url('storage/'.$path.'thumb/'.$lookBook->image) }}" alt="{{$lookBook->title}}" />
              </div>
              <?php } ?>
                <span>View Product</span>
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                   
                    <?php if(!empty($lookBook->image) && $storage->exists($path.$lookBook->image)){ ?>
                      <img src="{{ url('storage/'.$path.''.$lookBook->image) }}" class="scotchpic" alt="{{$lookBook->title}}" />
                    <?php } ?>
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>{{$lookBook->title}}</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                          <div class="cimg">
                            
                          <img src="{{url('')}}/images/blank.png" alt="{{$product_name}}"/>
                           <?php if(!empty($product_image)){ ?>
                            <div class="pimg">
                                <img src="{{$image_path.$product_image}}" alt="{{$product_name}}" /> 
                                </div>
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
                              <a class="shopbtn shopbtn-click" href="<?php echo url('products/details/'.$product_slug); ?>" target="_blank">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>



        <?php } ?>
      </ul>
        <?php } ?>


      <?php /* ?>
      <ul>
        <li> 
              <a class="cimg viewimg">                 
                <img src="{{url('images/img11.jpg')}}" alt="" />
                <span>View Product</span>
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img11.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img11.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

        <li> 
              <a class="cimg viewimg">                 
                <img src="{{url('images/img6.jpg')}}" alt="" />
                <span>View Product</span>
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img6.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img6.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

        <li> 
              <a class="cimg viewimg">                 
                <img src="{{url('images/img7.jpg')}}" alt="" />
                <span>View Product</span>
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img7.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img7.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

        <li> 
              <a class="cimg viewimg">                 
                <img src="{{url('images/img8.jpg')}}" alt="" />
                <span>View Product</span>
              </a>
              <div class="popupmain">
                <div class="popupbg1"></div>
                <div class="scotchpopup">
                  <div class="crossbtn closebtn">X</div>
                  <div class="scotchimg">
                    <img src="{{url('images/blankscotch.png')}}" alt="" class="blanksc" />
                    <img src="{{url('images/img8.jpg')}}" alt="" class="scotchpic" />
                  </div>
                  <div class="scotchproduct">
                      <div class="scotchtitle">
                        <div class="heading2"><img src="{{url('images/instagramicon.png')}}" alt="" /> <span>Ser_di</span></div>
                      </div>
                      <div class="shoplook">
                        <div class="heading3">Shop The Look</div>
                        <div class="probox">
                            <div class="cimg">
                              <img src="{{url('images/img8.jpg')}}" alt="" /> 
                            </div>
                            <div class="procont">
                              <div class="heading3">King Size Clothing</div>
                              <p>Jeans Joggers</p>
                              <a class="shopbtn" href="#">Buy this product</a>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </li>

      </ul>
      <?php */ ?>
       
         
         
    </div>
  </div>
</section>

<?php
if(!empty($instaMedia) && count($instaMedia) > 0){

  //pr($instaMedia);
  if(!empty($instaMedia->data) && count($instaMedia->data) > 0){
?>

<section class="instasec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
        <h2 class="headings2">{!!$HOME_HEADING_7!!}</h2> 
        <p>Mention us on @johnpride</p>
    </div>
    <div class="fullwidth instagramslid owl-carousel">

      <?php
      $token = config('custom.intagram_token');

      foreach ($instaMedia->data as $vm){

        //pr($vm);
        $media_id = $vm->id;
        //$media_content_by_id = CustomHelper::getMediaContent($media_id,$token);

        $img = '';

          $media_type = isset($vm->media_type) ? $vm->media_type:'';

          if(!empty($media_type) && $media_type == 'CAROUSEL_ALBUM' || $media_type == 'IMAGE') {
            $img = isset($vm->media_url) ? $vm->media_url:'';
            ?>
            <div class="instaimg">
              <a class="insta_images" href="{{$vm->permalink}}" target="_blank" style="background-image: url({{$img}});">
                <img target="_blank" src="{{url('/')}}/images/blank-insta.png" alt="Instagram" />
              </a> 
            </div>
            <?php
          }
      }
      ?>
    </div>
    <!-- <div class="fullwidth text-center"><span class="addinsta">+</span></div> -->

  </div>
</section>
<?php
}
}
?>

<!-- <div class="instaimg">
        <img src="{{url('images/instaimg2.jpg')}}" alt="" /> 
      </div>
      <div class="instaimg">
        <img src="{{url('images/instaimg3.jpg')}}" alt="" /> 
      </div>
      <div class="instaimg">
        <img src="{{url('images/instaimg4.jpg')}}" alt="" /> 
      </div>
      <div class="instaimg">
        <img src="{{url('images/instaimg1.jpg')}}" alt="" /> 
      </div>
      <div class="instaimg">
        <img src="{{url('images/instaimg2.jpg')}}" alt="" /> 
      </div>
      <div class="instaimg">
        <img src="{{url('images/instaimg3.jpg')}}" alt="" /> 
      </div>
      <div class="instaimg">
        <img src="{{url('images/instaimg4.jpg')}}" alt="" /> 
      </div> -->
<?php
/*

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

          <li><a href="https://www.facebook.com/johnpride/" target="_blank"><i class="facebookicon"></i></a></li>

          <li><a href="https://twitter.com/johnpride" target="_blank"><i class="twittericon"></i></a></li>

          <li><a href="#" target="_blank"><i class="linkedinicon"></i></a></li>

      <li><a href="https://www.instagram.com/johnpride/" target="_blank"><i class="instragramicon"></i></a></li>

        </ul>

      </div>

  

    </div>

  </section>

  <?php

}
*/
?>





@include('common.footer')


<script type="text/javascript" src="{{url('/')}}/js/owl.carousel.min.js"></script> 



<script>

  $('.banner').owlCarousel({
    loop:true,
    margin:0,
      autoplay:true,
    autoplayTimeout:6000,
    smartSpeed:1000,
    items:1,
    dots:false,
    nav:false,   
	 autoHeight:false,
  });

  $('.collectionslid').owlCarousel({
    loop:true,
    margin:20,
    items:3,
    dots:false,
    nav:true,
    responsive:{
      0:{
        items:1,
       loop:true,
       nav:false,
      },

      600:{
        items:2,
       nav:false,
      },
      768:{
        items:2,
      },
      1000:{
        items:3
      }
    }

  });

$('.celebratslid').owlCarousel({
    loop:true,
    margin:20,
    items:2,
    dots:true,
    nav:false,
    responsive:{
      0:{
        items:1,
       loop:true,
       
      },

      600:{
        items:2, 

      },
      768:{
        items:2,
      },
      1000:{
        items:2
      }
    }

  });

$('.upperslid').owlCarousel({
    loop:true,
    margin:20,
    items:3,
    dots:true,
    nav:false,
    responsive:{
      0:{
        items:1,
       loop:true,
       
      },

      600:{
        items:2, 

      },
      768:{
        items:3,
      },
      1000:{
        items:3
      }
    }

  });


$('.lookbookslide').owlCarousel({
  
  loop:true,
  margin:20,
  items:4,
  dots:true,
  nav:false,
  responsive:{
    0:{
      margin:10,
      items:1.3,  
    },
    600:{
      margin:10,
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



$('.instagramslid').owlCarousel({
  
  loop:false,
  margin:20,
  items:4,
  dots:false,
  nav:false,
  responsive:{
    0:{
      margin:10,
      items:1.3,
     loop:true,
     nav:false,
     dots:false,
     center: true,
    },
    600:{
      margin:10,
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
    autoplay:true,
    responsive:{
      0:{
        items:2,
		  margin:10,
		   nav:false,
      },
      600:{
        items:3,
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




$(".shopbtn-click").on('click', function(event){
    
    var url = ($(this).attr('href'))?$(this).attr('href'):window.location.href;
    //console.log('href=>', $(this).attr('href'))
    // similar behavior as clicking on a link
    window.location.href = url;
});
</script>


</body>

</html>