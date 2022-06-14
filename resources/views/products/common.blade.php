<div class="show_filter"><i class="icon_filter"></i> Filter Price</div>
<div class="fixed_filter">
   <div class="hide_filter">Hide Filter</div>
    <form name="priceFilterForm" class="filter_form">
        <div class="row">
          <div class="col-sm-6 col-xs-6">
             <div class="form-group">
            <input type="number" name="min_price" value="{{request('min_price')}}" min="0" class="form-control" placeholder="Min Price">
         </div>

          </div>

           <div class="col-sm-6 col-xs-6">
            <div class="form-group">
            <input type="number" name="max_price" value="{{request('max_price')}}" min="0" class="form-control" placeholder="Max Price">
         </div>
            
          </div>
        </div>
         
          
        <div class="text-center">
            <button class="btn btn-primary price_filter_btn">Apply Filter</button>
        </div>
        
    </form>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
  $(document).on("click", function(){  
    $('.fixed_filter').animate({bottom: '-100%'});
  });

  $(document).on("click", ".hide_filter", function (e) {  
    $('.fixed_filter').animate({bottom: '-100%'});
  });

  $(document).on("click", ".show_filter", function (e) {
    e.stopPropagation(e);
    $('.fixed_filter').animate({bottom: '0px'});
  });
/*$(".show_filter").click(function (e) {
    e.stopPropagation();
  });*/

  $(document).on("click", ".filter_form", function (e) {
    e.stopPropagation(e);
    $('.fixed_filter').animate({bottom: '0px'});
  });

  $(document).on("click", ".price_filter_btn", function(e){
    e.preventDefault();

    var priceFilterForm = $("form[name='priceFilterForm']");

    var min_price = priceFilterForm.find("input[name='min_price']").val();
    min_price = parseInt(min_price);

    var max_price = priceFilterForm.find("input[name='max_price']").val();
    max_price = parseInt(max_price);

    if(min_price > max_price){
      alert("Max Price should be greater or equal to Min price!");
    }
    else{
      priceFilterForm.submit();
    }

  });
</script>

<script type="text/javascript">
	$(document).on("click", ".buy_now_btn", function(){
		loadSpinner($(this));
	});
</script>