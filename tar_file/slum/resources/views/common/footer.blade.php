<?php
$parentCategories = CustomHelper::getCategories();

$FOOTER_CONTACT_DETAILS = CustomHelper::WebsiteSettings('FOOTER_CONTACT_DETAILS');
$FOOTER_TEXT = CustomHelper::WebsiteSettings('FOOTER_TEXT');
$FOOTER_BOTTOM = CustomHelper::WebsiteSettings('FOOTER_BOTTOM');


?>

<footer class="fullwidth"> 
  <div class="fullwidth footer1"> 
    <div class="container">
		<div id="topscroll" style="display: block;"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
      <div class="fbox flogo">
        <p><a href="{{url('/')}}"><img src="{{url('public')}}/images/logo.png" alt="Slumber Jill" /></a></p>
        <?php
        echo $FOOTER_TEXT;
        ?>
        <p></p>
      </div>
      <div class="fbox">
        <h4>ONLINE SHOPPING</h4>
        <ul>
          <?php
          if(!empty($parentCategories) && count($parentCategories) > 0){
            foreach($parentCategories as $pCat){
              ?>
              <li><a href="{{url('products?pcat='.$pCat->slug)}}">{{$pCat->name}}</a></li>
              <?php
            }
          }
          ?>
        </ul> 
      </div> 
      <div class="fbox">
        <h4>USEFUL LINKS</h4>
        <ul>
          <li><a href="{{url('about')}}">About</a></li>
          <li><a href="{{url('returns')}}">Return &amp; Exchange</a></li>
          <li><a href="{{url('faq')}}">FAQ</a></li>
          <li><a href="{{url('contact')}}">Contact</a></li>
          <li><a href="{{url('terms')}}">Terms &amp; Conditions</a></li>
          <li><a href="{{url('privacy')}}">Privacy Policy</a></li>
        </ul> 
      </div>
      <div class="fbox">
        <h4>My Account</h4> 
        <ul>
          <li><a href="{{url('users/orders')}}">Order History</a></li>
          <li><a href="{{url('users/profile')}}">Account</a></li>
          <li><a href="{{url('users/wishlist')}}">Wishlist</a></li>

          <?php
          if(auth()->check()){
            ?>
            <li><a href="{{url('logout')}}">Logout</a></li>
            <?php
          }
          else{
            ?>
            <li><a href="{{url('account/login')}}">Login</a></li>
            <?php
          }
          ?>

          <li><a href="{{url('users/cart')}}">Shopping Cart</a></li> 
        </ul> 
      </div>
      <div class="fbox faddress">
        <h4>For any query</h4>
        <?php echo $FOOTER_CONTACT_DETAILS; ?>
		<ul>
		  <li><a href="#"><i class="facebookicon"></i></a></li>
			<li><a href="#"><i class="twittericon"></i></a></li>
			<li><a href="#"><i class="linkedinicon"></i></a></li>
		  </ul>
      </div>    
    </div> 
  </div> 
  
  <div class="fullwidth fbottom"> 

    <?php
    echo $FOOTER_BOTTOM;
    ?>

  </div>

</footer>

  <script type="text/javascript" src="{{url('public')}}/js/jquery.min.js"></script> 

<script>
$(window).scroll(function() {
     if ($(this).scrollTop() > 0){  
      $('header').addClass("sticky");
      }
      else{
      $('header').removeClass("sticky");
      }
    });

function submit_search_form(){
  var searchForm = $("form[name=searchForm]");
  var filterForm = $("form[name=filterForm]");

  var keyword = searchForm.find("input[name=keyword]").val();

  filterForm.find("input[name=keyword]").val(keyword);

  searchForm.submit();
  //$("form[name=filterForm]").submit();

  return false;
}

var searchForm = $("form[name=searchForm]");
var headerKeyword = searchForm.find("input[name=keyword]");

$("form[name=searchForm] input[name=keyword]").on('keyup click', function(){
  var searchKeyword = $(this).val();

  var keywordLen = searchKeyword.length;

  if(keywordLen >= 3){
    setTimeout(getSearchList(searchKeyword), 700);
  }
  else{
    $("#search_list").html("");
  }
});

function getSearchList(keyword){

    var _token = '{{ csrf_token() }}';

    $.ajax({
      url: "{{ route('products.ajax_get_list_by_search') }}",
      type: "POST",
      data: {keyword:keyword},
      dataType:"JSON",
      headers:{'X-CSRF-TOKEN': _token},
      cache: false,
      beforeSend:function(){

      },
      success: function(resp){
        if(resp.success){

          if(resp.searchListHtml){
            $("#search_list").html(resp.searchListHtml);

            $("#search_list").show();
          }

        }
      }
    });

}


$(document).on("click", ".sr_list_item", function(){
  var fieldName = $(this).data("field");
  var val = $(this).data("val");

  if( (fieldName && fieldName != "") && (val && val != "") ){

    var searchForm2 = $("form[name=searchForm2]");

    var newInp = '';
    
    newInp += '<input type="hidden" name="'+fieldName+'" value="'+val+'" />';

    if(fieldName == 'cat'){
      var p1CatSlug = $(this).data("pcat");
      newInp += '<input type="hidden" name="pcat" value="'+p1CatSlug+'" />';
    }

    searchForm2.append(newInp);

    searchForm2.submit();
  }
});


/*$("body").click(function(e){
    if(e.target.className !== "form_wrapper"){
      $("#search_list").hide();
    }
  });*/

$(document).mouseup(function (e){

  var container = $("#search_list");

  if (!container.is(e.target) && container.has(e.target).length === 0){
    container.hide();    
  }
});

$(document).on("click", ".alert .close", function(){
  $(this).parent(".alert").remove();
});
	
$(window).scroll(function() {
    if ($(this).scrollTop() >= 250) {
        $('#topscroll').fadeIn(200);
    } else {
        $('#topscroll').fadeOut(200);
    }
});
$('#topscroll').click(function() {
    $('body,html').animate({
        scrollTop : 0 
    }, 500);
});
</script>
