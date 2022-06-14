var getIndex = function(el) {
	arr = el.parentNode.children;
	for(var i = 0, n = arr.length; i < n; i++) {
		if (el === arr[i]) {return i;}
	}
	return -1;
}

var setSlider = function(slider) {
	var sliderContent = slider.getElementsByClassName("content")[0]
		, sliderItems = slider.getElementsByClassName('slide-item')
		, sliderIndicators = slider.getElementsByClassName('indicators')[0]
		, left = slider.getElementsByClassName('left')[0]
		, right = slider.getElementsByClassName('right')[0]
		, i, n, el, changeActive;

	changeActive = function(prev, next) {
		sliderIndicators[prev].classList.remove("active-item");
		sliderIndicators[next].classList.add("active-item");
		if (sliderIndicators) {
			sliderItems[prev].classList.remove("active");
			sliderItems[next].classList.add("active");
		}
	}

	if (sliderIndicators) {
		for (i = 0, n = (sliderItems.length - 5); i<n; i++) {
			el = document.createElement('li');
			el.className = "item";
			sliderIndicators.appendChild(el);
		}
		el = getIndex(sliderContent.getElementsByClassName("active")[0]);
		sliderIndicators = document.getElementsByClassName('slider')[0].getElementsByClassName("item");
		sliderIndicators[el].classList.add('active-item');
		for (i = 0, n = sliderIndicators.length; i < n; i++) {
			sliderIndicators[i].addEventListener("click", function() {
				changeActive(getIndex(slider.getElementsByClassName("active-item")[0]), getIndex(this));
			});
		}
	}
	var leftChange = function() {
		var prevIndex = getIndex(sliderContent.getElementsByClassName("active")[0])
			, nextIndex = prevIndex - 1;
		if (nextIndex === -1 ) {nextIndex += sliderContent.getElementsByClassName('slide-item').length - 5}
		changeActive(prevIndex, nextIndex);
	};
	if (left) {
		left.addEventListener("click", leftChange);
	}
	
	var rightChange = function() {
		var prevIndex = getIndex(sliderContent.getElementsByClassName("active")[0])
				, nextIndex = prevIndex + 1;
		if (nextIndex >= sliderItems.length - 2 ) {nextIndex = 0}
		changeActive(prevIndex, nextIndex);
	};
	if (right) {
		right.addEventListener("click", rightChange);
	}
	var startX;
	sliderContent.addEventListener('touchstart', function(e) {
		startX = e.changedTouches[0].pageX;
	});
	sliderContent.addEventListener('touchend', function(e) {
		if (startX < e.changedTouches[0].pageX) {
			leftChange();
		} else if (startX > e.changedTouches[0].pageX) {
			rightChange();
		}
	});
}

window.addEventListener('load', function() {
	setSlider(document.getElementsByClassName('slider')[0])
});