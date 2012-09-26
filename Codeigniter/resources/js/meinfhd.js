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


/**
 * Creates the needed HTML Markup for the desired Bootstrap Modal.
 * @author Konstantin Voth
 * @param  {String} title  Title of the modal.
 * @param  {String} text   Message in the modal.
 * @param  {bool} withOK Should there be an OK button?
 * @return {jQuery Object}
 * @private
 */
function _createModalDialog(title, text, withOK) {
	myModalDialog =
		$('<div class="modal hide" id="myModal"></div>')
		.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
		.append('<div class="modal-body"><p>'+text+'</p></div>')
		.append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Schließen</a>');
		if (withOK) myModalDialog.find('.modal-footer').append('<a href="" class="btn btn-primary" data-accept="modal">OK</a></div>');
		// <a href="" class="btn btn-primary" data-accept="modal">OK</a></div>
	return myModalDialog;
}

/**
 * Shows the Bootstrap Modal. There have to be additional HTML markup in your view file to
 * show the modal correctly. Place <div id="modalcontent"></div> at the bottom of your view file.
 * @author Konstantin Voth
 * @param   {String} title  Title of the dialog.
 * @param   {[type]} text   Message in the modal.
 * @param   {[type]} withOK Should there be an OK button?
 * @private
 */
function _showModal(title, text, withOK) {
	mm = _createModalDialog(title, text, withOK);
	$('#modalcontent').html(mm);

	$('#myModal').modal({
		keyboard: false
	}).on('hide', function () {
		$("input[type=submit][data-clicked=true]").removeAttr("data-clicked");
	}).modal('show');

	if (withOK) {
		// if there are any click listener, remove them
		$('#modalcontent').off('click');
		// add new
		$("#modalcontent").on( 'click', 'button, a', function(event) {
			event.preventDefault();

			if ( $(this).attr("data-accept") === 'modal' ) {
				console.log("accept");

				$(event.target).parent().parent().find("div.modal-body").html("Bitte warten, der Befehl wird ausgeführt");
				$(event.target).parent().parent().find("div.modal-footer").hide();

				// get the form name dynamically, to be able to use this in every form
				form_id = $("input[type=submit][data-clicked=true]").parents("form").attr("id");
				// console.log(form_id);

				$("input[type=submit][data-clicked=true]").parents("form#"+form_id).submit();

			} else {
				console.log("cancel");
			}

		});
	}
}