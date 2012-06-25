(function($) {
	// custom js functionality goes here
			        		      			
	/*icon-switch for accordion-templates*/
	$(function(){									
		$('#accordion')
			.on('hidden', function(e) {
				
				$(e.target).parent().find('.accordion-heading i').removeClass('icon-minus').addClass('icon-plus');						
			})
			.on('shown', function(e){
				
				$(e.target).parent().find('.accordion-heading i').removeClass('icon-plus').addClass('icon-minus');
		});
	});
			
	
})(jQuery);