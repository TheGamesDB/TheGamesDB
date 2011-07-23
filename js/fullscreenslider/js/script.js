/*
* Author:      Marco Kuiper (http://www.marcofolio.net/)
*/

// Speed of the animation
var animationSpeed = 1300;

// Type of easing to use; http://gsgd.co.uk/sandbox/jquery/easing/
var easing = "easeOutCubic";

// 0-based index to set which picture to show first
var activeIndex = 0;

$(function() {

	// Variable to store if the animation is playing or not
	var isAnimating = false;

	// Register keypress events on the whole document
	/*$(document).keypress(function(e) {
		
		// Keypress navigation
		// More info: http://stackoverflow.com/questions/302122/jquery-event-keypress-which-key-was-pressed
		if (!e.which && ((e.charCode || e.charCode === 0) ? e.charCode: e.keyCode)) {
		    e.which = e.charCode || e.keyCode;
		}
		
		var imageIndex = e.which - 49; // The number "1" returns the keycode 49. We need to retrieve the 0-based index.
		startAnimation(imageIndex);
	});*/

	// Add the navigation boxes
	$.template("navboxTemplate", "<div class='navbox ${cssclass}'><ul></ul><h2><a href='${url}' title='${title}'>${title}</a></h2><p>${text}</p></div>");
	$.tmpl("navboxTemplate", photos).appendTo("#navigationBoxes");

	// Add the navigation, based on the Photos
	// We can't use templating here, since we need the index + append events etc.
	var cache = [];
 	for(var i = 1; i < photos.length + 1; i++) {
		$("<a />")
			.html(i)
			.data("index", i-1)
			.attr("title", photos[i-1].title)
			.click(function() {
				clearTimeout(autoTimer);
				showImage($(this));
			})
			.appendTo(
				$("<li />")
					.appendTo(".navbox ul")
			);
			
		// Preload the images
		// More info: http://engineeredweb.com/blog/09/12/preloading-images-jquery-and-javascript
		var cacheImage = $("<img />").attr("src", photos[i-1]);
		cache.push(cacheImage);
	}
	
	// Set the correct "Active" classes to determine which navbox we're currently showing
	$(".navbox").each(function(index) {
		var parentIndex = index + 1;
		$("ul li a", this).each(function(index) {
			if(parentIndex == (index + 1)) {
				$(this).addClass("active");
			}
		});
	});
	
	// Hide all the navigation boxes, except the one from current index
	$(".navbox:not(:eq(" + activeIndex +"))").css('left', '-550px');
	
	// Set the proper background image, based on the active index
	$("<div />")
		.css({ 'background' : "url(" + photos[activeIndex].image + ") no-repeat center center" } )
		.prependTo("#pictureSlider");
	
	//
	// Shows an image and plays the animation
	//
	var showImage = function(docElem) {
		// Retrieve the index we need to use
		var imageIndex = docElem.data("index");
		
		startAnimation(imageIndex);
	};
	
	//
	// Starts the animation, based on the image index
	//
	var startAnimation = function(imageIndex) {
		// If the same number has been chosen, or the index is outside the
		// photos range, or we're already animating, do nothing
		if(activeIndex == imageIndex ||
			imageIndex > photos.length - 1 ||
			imageIndex < 0 ||
			isAnimating) {
			return;
		}
		
		isAnimating = true;
		animateNavigationBox(imageIndex);
		slideBackgroundPhoto(imageIndex);
		
		// Set the active index to the used image index
		activeIndex = imageIndex;		
	};
	
	//
	// Animate the navigation box
	//
	var animateNavigationBox = function(imageIndex) {
	
		// Hide the current navigation box
		$(".navbox").eq(activeIndex)
			.css({ 'z-index' : '998' }) // Push back
			.animate({ left : '-550px' }, animationSpeed, easing);
		
		// Show the accompanying navigation box
		$(".navbox").eq(imageIndex)
			.css({ 'z-index' : '999' }) // Push forward
			.animate({ left : '0px' }, animationSpeed, easing);
	};
	
	//
	// Slides the background photos
	//
	var slideBackgroundPhoto = function(imageIndex) {
		// Retrieve the accompanying photo based on the index
		var photo = photos[imageIndex];

		// Create a new div and apply the CSS
		$("<div />")
			.css(
				{ 	//'left' : '-1920px',
					'z-index' : '100',
					'display' : 'none',
					'background' : "url(" + photo.image + ") no-repeat center center" } )
			.addClass(photo.cssclass)
			.prependTo("#pictureSlider");

		// Slide all the pictures to the right
		$("#pictureSlider div:first").css({'z-index' : '99'}).fadeIn(animationSpeed);
		$("#pictureSlider div:first").queue( function () {
		$(this).css({'z-index' : '98'});
		$("#pictureSlider div:not(:first)").remove();
		});
			
		// Animation is complete
		isAnimating = false;
	};
	
	var autoTimer;
	var autoCount = 1;
	function go(autoTime) {
		autoTimer = setTimeout(function() {
			startAnimation(autoCount);
			if(autoCount != photos.length -1) {
				autoCount++;
			}
			else {
				autoCount = 0;
			}
			go(autoTime);
		}, autoTime);
	}
	
	go(10000);
	
});