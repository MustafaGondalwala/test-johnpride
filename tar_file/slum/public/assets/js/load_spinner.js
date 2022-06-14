function loadSpinner(selector){
	selector.append('<span class="click_spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i></span>');
}

function removeSpinner(selector){
	selector.find(".click_spinner").remove();
}