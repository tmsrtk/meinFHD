(function($) {
	// custom js functionality goes here
			        		      			
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
	
	
	$(function(){
		
		//setting the first element
		//$('.carousel').find('item:eq(1)').addClass('active');
		
		
		
		//pagination
		$('.pagination').find('.slide-montag').click(function(){	
			$('.carousel').carousel(0);
		});
		
		$('.pagination').find('.slide-dienstag').click(function(){	
			$('.carousel').carousel(1);
		});
		
		$('.pagination').find('.slide-mittwoch').click(function(){	
			$('.carousel').carousel(2);
		});
		
		$('.pagination').find('.slide-donnerstag').click(function(){	
			$('.carousel').carousel(3);
		});
		
		$('.pagination').find('.slide-freitag').click(function(){	
			$('.carousel').carousel(4);
		});
		
	});
			
	
})(jQuery);