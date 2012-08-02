(function($) {
	// custom js functionality goes here

	
	// autoclosing twitter bootstrap alerts
	function createAutoClosingAlert(selector, delay) {
		var alert = $(selector).alert();
		window.setTimeout(function() {alert.alert('close')}, delay);
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
	
	$(function(){
		
		$('.carousel').carousel('pause');
		
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