(function($) {

	// Make the carousel accessible via pointer	
	$carousel = $('#carousel');
	// Prevent the carousel from auto playing
	$carousel.carousel({
		interval: 0,
	});
	
	initPager();
	
	// Get all direct navigation controls, exclude left and right arrows
	$goto = $('.pagination a').not(':first, :last');
	
	// Add click handler
	$goto.click(function(e){
		// Dont't do any default stuff
		e.preventDefault();
		// Get position in relation to all other selected elements
		var index = $goto.index(this);
		// Point the carousel to the element
		$carousel.carousel(index);
	});
	
	$carousel.on('slid', function(){
		initPager();
	});
	
	// autoclosing twitter bootstrap alerts
	function createAutoClosingAlert(selector, delay) {
		//var alert = $(selector).alert();
		//window.setTimeout(function() {alert.alert('close')}, delay);
	}
	
	createAutoClosingAlert(".alert.in", 3000);
	
	/*icon-switch for accordion-templates*/
	$(function(){
		$('.accordion')
			.on('hidden', function(e) {
				
				$(e.target).parent().find('.accordion-heading i').removeClass('icon-minus').addClass('icon-plus');						
			})
			.on('shown', function(e){
				
				$(e.target).parent().find('.accordion-heading i').removeClass('icon-plus').addClass('icon-minus');
		});
	});
	
	/*color toggle for attendance-buttons in stundenplan*/
	$(function(){
		
		//disabling already attended courses and painting them green	
		$('.attendant:disabled').addClass('btn-success').find('i').addClass('icon-white');
		
		//painting a button on click
		$('.attendant').click(function(){
			
			$(this).addClass('btn-success').find('i').addClass('icon-white');
		});
		
	});			
	
})(jQuery);

function initPager() {
	var activeIndex = $carousel.find('.item').index($carousel.find('.item.active'));
	$('.pagination li').not(':first, :last').removeClass('active').eq(activeIndex).addClass('active');
}
