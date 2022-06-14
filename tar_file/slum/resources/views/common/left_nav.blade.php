<?php 
$colors = '';
$brands = '';
$sizes = '';
$subCategories = '';

$colors = CustomHelper::getData('colors_master', '', ['status'=>1]);
$sizes = CustomHelper::getData('sizes', '', ['status'=>1]);
$brands = CustomHelper::getData('brands', '', ['status'=>1]);

$parent_id = 0;

$pcat = (request()->has('pcat'))?request()->pcat:'';
$catArr = (request()->has('cat'))?request()->cat:[];
$brandArr = (request()->has('brand'))?request()->brand:[];
$colorArr = (request()->has('color'))?request()->color:[];
$sizeArr = (request()->has('size'))?request()->size:[];

$keyword = (request()->has('keyword'))?request()->keyword:'';
$price_range = (request()->has('price_range'))?request()->price_range:'';
$br = (request()->has('br'))?request()->br:'';

if(!empty($br)){
  $brandArr[] = $br;
}

//pr($brandArr);
if(!empty($pcat)){
  $parentCategory = CustomHelper::getCategories($pcat);
  //prd($parentCategory);

  if(isset($parentCategory->id) && $parentCategory->id > 0){
    $parent_id = $parentCategory->id;
  } 
}

if(is_numeric($parent_id) && $parent_id > 0){
  $subCategories = CustomHelper::getCategories('', $parent_id);
}

?>



  <div class="sidebarsec">
   <form method="get" name="filterForm" id="filterForm">

    <input type="hidden" name="pcat" value="{{$pcat}}">
    <input type="hidden" name="keyword" value="{{$keyword}}">
    <input type="hidden" name="sort_by" value="{{$sort_by}}">
    <input type="hidden" name="price_range" value="{{$price_range}}">
    
    <div class="filtertitle fullwidth">
      <span class="filtermobile">Filters <small></small></span>
      
    </div> 
    <div class="sideinner">
      <div class="sidetitle">Categories <span><i class="searchicon"></i></span></div>

      <?php
      if(!empty($subCategories) && count($subCategories) > 0) {
        ?>
        <div class="boxs catlist">
          <ul>
            <?php 
            $i = 0;
            foreach ($subCategories as $subCategory){
              $i++;
              $class = '';
              $childCategories = $subCategory->children;
              $childCategoriesBySlug = $childCategories->keyBy('slug');

            //pr($childCategoriesBySlug->toArray());

              if(!empty($catArr) && count($catArr) > 0){
                foreach($catArr as $srCat){
                  if(isset($childCategoriesBySlug[$srCat])){
                    $class = 'class="active"';
                    break;
                  }
                }
              }
              elseif($i == 1){
                $class = 'class="active"';
              }
              ?>
              <li <?php echo $class;?>><span> {{$subCategory->name}} </span>

                <?php
                if(!empty($childCategories) && count($childCategories) > 0) {
                  ?> 
                  <ul>
                    <?php
                    foreach ($childCategories as $childCategory) {
                      $checked = '';
                      if(in_array($childCategory->slug, $catArr)){
                        $checked = 'checked';
                      }
                      ?>
                      <li>
                        <label>
                          <input type="checkbox" name="cat[]" value="{{$childCategory->slug}}" class="child-category" {{$checked}}> <span> {{ $childCategory->name }}</span>
                        </label>
                      </li>
                      <?php
                    }
                    ?>  
                  </ul>
                  <?php
                }
                ?>

              </li>
              <?php
            }
            ?>

          </ul>
        </div>
        <?php
      }
      ?>


      <?php
      if(!empty($brands) && count($brands) > 0) {
       ?>
       <div class="boxs">
        <ul>
          <li><span>Brand</span>
            <ul>
              <?php
              foreach ($brands as $brand) {
                $checked = '';
                if(in_array($brand->slug, $brandArr)){
                  $checked = 'checked';
                }
                ?>
                <li><label><input type="checkbox" name="brand[]" value="{{$brand->slug}}" class="child-category" {{$checked}}> <span><?php echo $brand->name; ?></span></label></li>
                <?php
              }
              ?>
            </ul>
          </li>
        </ul>
      </div>
      <?php
    }
    ?>

    <?php
    if(!empty($colors) && count($colors) > 0){
      ?>
      <div class="boxs colors">
        <ul>
          <li><span>Color</span>
            <ul>
              <?php
              $countClr = 0;

              $isViewMoreBtn = false;
              foreach ($colors as $color) {

                $checked = '';
                if(in_array($color->slug, $colorArr)){
                  $checked = 'checked';
                }

                $displayNone = '';

                $moreItemClass = '';

                if($countClr >= 5){
                  $displayNone = "display:none;";
                  $moreItemClass = 'moreItem';

                  $isViewMoreBtn = true;
                }
                ?>
                <li style="<?php echo $displayNone; ?>" class="<?php echo $moreItemClass; ?>" >
                  <label><input type="checkbox" name="color[]" value="{{$color->slug}}" class="child-category" {{$checked}}><span><small style="background-color:<?php echo $color->code;?>; " ></small>{{$color->name}}</span></label>
                </li>
                <?php
                $countClr++;
              }
              if($isViewMoreBtn){
                ?>
                <a href="javascript:void(0)" class="viewMore">View more</a>
                <?php
              }
              ?>
            </ul>
          </li>
        </ul>
      </div>
      <?php
    }
    ?>

    <?php if(!empty($sizes) && count($sizes) > 0){ ?>
      <div class="boxs">
        <ul>
          <li><span>Size</span>
            <ul>
              <?php foreach ($sizes as $size){

                $checked = '';
                if(in_array($size->name, $sizeArr)){
                  $checked = 'checked';
                }
                ?>
                <li><label><input type="checkbox" name="size[]" value="{{$size->name}}" class="child-category" {{$checked}}> <span> <?php echo $size->name; ?> </span></label></li>
                <?php
              }
              ?>

            </ul>
          </li>
        </ul> 
      </div>
      <?php
    }
    ?>

		<div class="boxs priceing">
    <div class="sidetitle">
		Price 
    </div>
		
	<div class="pricerang">
		<div id="slider-range"></div>
		<p> <label for="amount"> (&#x20B9;):</label> <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;"></p>	
	</div>
			</div>
  </div>	  
</form>
</div>



