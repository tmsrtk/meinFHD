$(function(){

	// Add a pointer to the carousel
	$carousel = $('#carousel');
	// Enable swiping on carousel
	$carousel.on('swipe', function(e){
		// Swiping left will cause the carousel to go to next item
		if (e.direction == 'left') {
			$carousel.carousel('next');
		}
		// Swiping left will cause the carousel to go to previous item
		if (e.direction == 'right') {
			$carousel.carousel('prev');
		}
	});
	
});

// Scrolling to Top, hiding adressbar on iOS devices
window.addEventListener('load',function() {
	setTimeout(function(){
		// Hide the address bar!
		window.scrollTo(0, 1);
	}, 0);
});