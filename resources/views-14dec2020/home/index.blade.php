<!DOCTYPE html>

<html>

<head>  



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
$websiteSettingsNamesArr = ['HOME_VIDEO', 'HOME_HEADING_1', 'HOME_HEADING_2', 'HOME_HEADING_3', 'HOME_HEADING_4', 'HOME_HEADING_5', 'HOME_HEADING_6', 'HOME_HEADING_7'];
$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);
$HOME_HEADING_1 = (isset($websiteSettingsArr['HOME_HEADING_1']))?$websiteSettingsArr['HOME_HEADING_1']->value:'';
$HOME_HEADING_2 = (isset($websiteSettingsArr['HOME_HEADING_2']))?$websiteSettingsArr['HOME_HEADING_2']->value:'';
$HOME_HEADING_3 = (isset($websiteSettingsArr['HOME_HEADING_3']))?$websiteSettingsArr['HOME_HEADING_3']->value:'';
$HOME_HEADING_4 = (isset($websiteSettingsArr['HOME_HEADING_4']))?$websiteSettingsArr['HOME_HEADING_4']->value:'';
$HOME_HEADING_5 = (isset($websiteSettingsArr['HOME_HEADING_5']))?$websiteSettingsArr['HOME_HEADING_5']->value:'';
$HOME_HEADING_6 = (isset($websiteSettingsArr['HOME_HEADING_6']))?$websiteSettingsArr['HOME_HEADING_6']->value:'';
$HOME_HEADING_7 = (isset($websiteSettingsArr['HOME_HEADING_7']))?$websiteSettingsArr['HOME_HEADING_7']->value:'';
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
       <div class="bheading">Latest Sweatshirts</div>
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
            <div class="cimg">
              <img src="{{ url('storage/'.$path.''.$collection->image) }}" alt="" />
              <div class="title2">{{$collection->name}}</div>
            </div>

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
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_2!!}</h2>
    <p>Our top shopping recommendations for you</p>
  </div>
    <div class="fullwidth celebratslid owl-carousel">

      <?php 
      if(!empty($categories) && count($categories) > 0){ 
        $path = 'categories/';
        foreach($categories as $category){
          

          $categoryImages = (isset($category->defaultImage))?$category->defaultImage->image:'';

          if(!empty($categoryImages) && $storage->exists($path.$categoryImages))
          {
            ?>
            <div class="cimg">
              <img src="{{ url('storage/'.$path.''.$categoryImages) }}" alt="" />
              <div class="title2">{{$category->name}}</div>
            </div>



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
  if(!empty($productsPopular) && count($productsPopular) > 0){ ?>
    <div class="fullwidth upperslid owl-carousel">
      <?php 
      $path = 'categories/';
      foreach($productsPopular as $productPopular){

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
          $salePrice = $product->price;
        }


       
        ?>


        <div class="probox">
          <div class="cimg">
            <?php if(!empty($product_image)){ ?>
              <img src="{{$product_image}}" alt="{{$product_name}}" /> 
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
            <a class="shopbtn" href="#">Shop Now</a>
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


<section class="newtosec fullwidth" style="background: url({{url('images/newto.jpg')}}) center center no-repeat; background-size:cover;">
  <img src="{{url('images/blankimg.png')}}" alt="{{$banner->title}}" class="blanks" />
  <!-- <img src="{{url('images/newto.jpg')}}" class="newtoimg" alt="" /> -->
  <div class="container">
    <div class="newtobox">
        <div class="heading2"><span>New to</span> John Pride?</div>
        <p>Here's a 10% welcome benefit to get you started.</p>
        <a class="explorbtn showpopup">Explore Now</a>
      </div>
  </div>
</section>



<section class="scotchsec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
      <h2 class="headings2">{!!$HOME_HEADING_5!!}</h2> 
      <p>Lorem Ipsum is simply Dummy text of the printing and  </p>
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
           // pr($customerPicture->product);
            $product_image = '';
            $product_name = '';
            $productPrice = '';
            $mainPrice = '';
            $price = '';
            $salePrice = '';


            if($customerPicture->product){

              $product_image = ($customerPicture->product && isset($customerPicture->product->defaultImage))?$customerPicture->product->defaultImage->image:'';

              $product_name= ($customerPicture->product)?$customerPicture->product->name:'';

              $mainPrice = ($customerPicture->product)?$customerPicture->product->price:0;
              $price = ($customerPicture->product)?$customerPicture->product->price:0;
              $salePrice = ($customerPicture->product)?$customerPicture->product->sale_price:00;

              $productPrice = $mainPrice;
              if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
                $productPrice = $salePrice;
              }
              else{
                $salePrice = $product->price;
              }

            }
          ?>

          <li> 
              <a class="cimg">
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
                        <?php if($customerPicture->product){ ?>
                        <div class="probox">
                            <div class="cimg">
                              <?php if(!empty($product_image)){ ?>
                                <img src="{{$product_image}}" alt="{{$product_name}}" /> 
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
                              <a class="shopbtn" href="#">Buy this product</a>
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

    <div class="fullwidth showall"><a class="seeallbtn" href="#">See All</a></div>
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
          <div class="cimg">
            <a href="#">
              <?php if(!empty($blog_image)){ ?>
              <img src="{{url('storage/'.$path.''.$blog_image) }}" alt="{{$featuredBlog->title}}" /> 
            <?php } ?>
              
            </a>
          </div>
          <div class="feedtext">
            <div class="heading3"><a href="#">{{$featuredBlog->title}}</a></div>
            <p>{{CustomHelper::wordsLimit(strip_tags($featuredBlog->content), 100)}}</p>
            <p class="dates">{{$blog_date}}</p>
          </div>
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
      <p>Lorem Ipsum is simply dummy text of the printing and </p>
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

            $mainPrice = ($lookBook->product)?$lookBook->product->price:0;
            $price = ($lookBook->product)?$lookBook->product->price:0;
            $salePrice = ($lookBook->product)?$lookBook->product->sale_price:00;

            $productPrice = $mainPrice;
            if(is_numeric($salePrice) && $salePrice < $price && $salePrice > 0){
              $productPrice = $salePrice;
            }
            else{
              $salePrice = $product->price;
            }
          ?>

          <li> 
              <a class="cimg viewimg">                 
                <?php if(!empty($lookBook->image) && $storage->exists($path.$lookBook->image)){ ?>
                <img src="{{ url('storage/'.$path.'thumb/'.$lookBook->image) }}" alt="{{$lookBook->title}}" />
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
                              <?php if(!empty($product_image)){ ?>
                                <img src="{{$product_image}}" alt="{{$product_name}}" /> 
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
                              <a class="shopbtn" href="#">Buy this product</a>
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

<section class="instasec fullwidth">
  <div class="container">
    <div class="fullwidth headsec">
        <h2 class="headings2">{!!$HOME_HEADING_7!!}</h2> 
        <p>Mention us on @johnpride</p>
    </div>
    <div class="fullwidth instagramslid owl-carousel">
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
      </div>
    </div>
    <div class="fullwidth text-center"><span class="addinsta">+</span></div>

  </div>
</section>






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
    autoplayTimeout:3000,
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
  dots:true,
  nav:false,
  responsive:{
    0:{
      margin:10,
      items:1.3,
     loop:true,
     nav:false,
     dots:true,
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



</script>


</body>

</html>