$(document).ready(function() {
    
    $("#hitMe").click(function(){
        
        $(".fa-bars").toggleClass("fa-window-close animated pulse");
        $(".listing li").toggleClass("block animated slideInLeft");
        
    })
    
    $('.welcomeContent .smartWear').click(function(){
    var $this = $(this);
    $this.siblings().removeClass('active');
     if($this.hasClass('active')){
       $this.removeClass('active').addClass('inactive');
     }else{
       $this.removeClass('inactive').addClass('active');
     }

    })
    $("#otp").click(function(){
    $(".verify").show(""); 
    $(".signUp").hide("");
    })
    
    $("#verify").click(function(){
    $(".genPass").show(""); 
    $(".verify").hide("");
    })
    
    $("#generate").click(function(){
    $(".containerCustom").hide(""); 
    $(".welcome").show("");
    })
    
    $("#slide").click(function(){
    $(".offsetMini").toggleClass("firstChild");
    $(".listing").toggleClass("minWidths"); 
    $(".colSm").toggleClass("col-md-4"); 
    $(".listing li .linkData").toggleClass("noData");
    $(".container-custom").toggleClass("marginMini");
    $(".welcomeContent .col-md-4").toggleClass("col-md-3");
    $(".rightBar").toggleClass("widthFull");
        if($(".fa-arrow-right").hasClass("fa-arrow-right")) {
            $(".slide i").addClass("fa-arrow-left");
            $(".slide i").removeClass("fa-arrow-right");
        }
        else  if($(".fa-arrow-left").hasClass("fa-arrow-left")) {
            $(".slide i").addClass("fa-arrow-right");
            $(".slide i").removeClass("fa-arrow-left");
        }
    
    })
    
    $(".mobileView a").click(function(){
        $(".links").toggleClass("showMe slideInLeft");
        $(".one").toggleClass("oneNew");
        $(".three").toggleClass("threeNew");
        $(".two").toggleClass("twoNew");
	})
  
})
//plugin bootstrap minus and plus
//http://jsfiddle.net/laelitenetwork/puJ6G/
$('.btn-number').click(function(e){
    e.preventDefault();
    
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(0);
    }
});
$('.input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {
    
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    
    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    
    
});
$(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
