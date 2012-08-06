<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan bearbeiten<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span12 well well-small">
<?php endblock(); ?>
	
<?php
# general form setup
	// peraparation of studiengan-dropdown
	$stdplanFilter[0] = 'Bitte auswählen';
	foreach($all_stdplan_filterdata as $spf){ 
		$stdplanFilter[
			$spf->StudiengangAbkuerzung.'_'
			.$spf->Semester.'_'
			.$spf->Pruefungsordnung] =
				$spf->StudiengangAbkuerzung.' '
				.$spf->Semester.' - '
				.$spf->Pruefungsordnung; 
	}
	$js = 'id="admin-stdplanfilter"';
	
?>

<?php startblock('content'); # additional markup before content ?>

		<div class="row-fluid">
			<div class="span8"><h2>Studiengang bearbeiten</h2></div>
			<div class="span4">
				<h5>Filter</h5>
				<!-- build array for Studiengang-Filter -->
				<?php echo form_dropdown('stdplanFilter', $stdplanFilter, '', $js); ?>
				
			</div>
		</div>
		<hr>
		
		<?php echo validation_errors(); ?>
		
		<div class="row-fluid">
			<div id="stdplan-change-view">
				<!-- load data dynamically via AJAX -->
			</div>

			<div id="confirmation-dialog-container"></div>

		</div>
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
		
<?php endblock(); ?>
	
	
<?php startblock('customFooterJQueryCode');?>
		
		// update stdplan-view according to chosen field in dropdown
		$('#admin-stdplanfilter').change(function() {
			$("#stdplan-change-view").html('suche...');
			// ajax
			if($(this).val() != 0) {
				
				$.ajax({
				   type: "POST",
				   url: "<?php echo site_url();?>admin/ajax_show_events_of_stdplan/",
				   dataType: 'html',
				   data : {stdplan_ids : $(this).val()},
				   success: function (data){
				       $('#stdplan-change-view').html(data);
					   // get wpf-name fields and disable if checkbox unchecked
					   disableUncheckedWpf();
					   bindFixedHeader();
					   
				   }
				});
				
//				$.get(
//					"<?php echo site_url();?>admin/ajax_show_events_of_stdplan/",
//					'stdplan_id='+stdplanIds,
//					function(response) {
//						// 
//						$('#stdplan-change-view').html(response);
//					});
			} else {
				$("#stdplan-change-view").html('');
			}

		});
		
	
	    
		
	// autoreload after submission AND validation-errors
	var stdplan_ids = "<?php echo $stdplan_id_automatic_reload; ?>"
	if(stdplan_ids != '0'){
	    $.ajax({
			type: "POST",
			url: "<?php echo site_url();?>admin/ajax_show_events_of_stdplan/",
			dataType: 'html',
			data : {stdplan_ids : stdplan_ids},
			success: function (data){
				$('#stdplan-change-view').html(data);
				bindFixedHeader();
			}
	    });
	    $('#admin-stdplanfilter').val(stdplan_ids);
	    stdplan_ids = '';
	}
	
	
	// show delete dialogs
    $('#stdplan-change-view').on('click', 'button.delete-stdpln-btn', function(){
		var spCourseId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createConfirmationDialog('Veranstaltgung löschen', 'Soll diese Veranstaltung gelöscht werden?');
		$('#confirmation-dialog-container').html(dialog);

		// function of dialog
		$('#confirmation-dialog').modal({
			keyboard: false
		// !! important part: on 'show' set data-id= courseId (the one to delete)
		}).on('show', function(){
			$('#conf-dialog-confirm').data('id', spCourseId);
			$('#conf-dialog-confirm').data('delete', 1);
		// on hide hide ^^
		}).on('hide', function(){
			console.log('hidden');
		}).modal('show');

		return false;
	});


	// show save dialog
	$('#stdplan-change-view').on('click', '#create-btn-stdpln', function(){
		// fetching sp-id (abk, sem, po) stored in name of button)
		var spId = $(this).attr('name');
		
		// open dialog and set text to show
		var dialog = createConfirmationDialog('Veranstaltgung erstellen', 'Soll die Veranstaltung erzeugt werden?');
		$('#confirmation-dialog-container').html(dialog);

		// function of dialog
		$('#confirmation-dialog').modal({
			keyboard: false
		// !! important part: on 'show' set data-id= 0 (sign to create new course)
		}).on('show', function(){
			$('#conf-dialog-confirm').data('id', spId);
			$('#conf-dialog-confirm').data('delete', 0);
		// on hide hide ^^
		}).on('hide', function(){
			console.log('hidden');
		}).modal('show');

		return false;
	});

	// create dialog element
	function createConfirmationDialog(title, text) {
		var myDialog = 
			$('<div class="modal hide" id="confirmation-dialog"></div>')
			.html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
			.append('<div class="modal-body"><p>'+text+'</p></div>')
			.append('<div class="modal-footer"><a href="#" class="btn" id="conf-dialog-cancel" data-dismiss="modal">Abbrechen</a>\n\
			<a href="" class="btn btn-primary" data-id="0" data-delete="0" id="conf-dialog-confirm" data-accept="modal">OK</a></div>');

		return myDialog;
    };
	
	// behaviour of modal-buttons
    $('#confirmation-dialog-container').on('click', '#conf-dialog-confirm', function(){
		
		var spId = ($(this).data('id'));
		var deleteId = ($(this).data('delete'));
		console.log(deleteId);
		var callMethod = '';
		var submitData = '';
		
		// decide which method in controller should be called
		if(deleteId != 0){
			$('#confirmation-dialog-container .modal-body').html('Veranstaltung wird gelöscht.');
			callMethod = "<?php echo site_url();?>admin/ajax_delete_single_event_from_stdplan/";
			submitData = {course_data : spId};
		} else {
			$('#confirmation-dialog-container .modal-body').html('Veranstaltung wird erstellt.');
			callMethod = "<?php echo site_url();?>admin/ajax_create_new_event_in_stdplan/";

			// TODO fill createData with data in first row
			var createData = new Array(
				spId,
				$('#new-course-courses-dropdown').val()+'_KursID',
				$('#new-course-eventtype-dropdown').val()+'_VeranstaltungsformID',
				$('#new-course-stdplan-list-alternative').val()+'_VeranstaltungsformAlternative',
				$('#new-course-stdplan-list-room').val()+'_Raum',
				$('#new-course-profs-dropdown').val()+'_DozentID',
				$('#new-course-starttime-dropdown').val()+'_StartID',
				$('#new-course-endtime-dropdown').val()+'_EndeID',
				$('#new-course-days-dropdown').val()+'_TagID',
				$('#new-course-stdplan-list-wpfcheckbox').attr('checked')+'_isWPF',
				$('#new-course-stdplan-list-wpfname').val()+'_WPFName',
				$('#new-course-color-dropdown').val()+'_Farbe'
			);
			submitData = {course_data : createData};
		}
		
		// hide action-buttons on dialog
		$('#confirmation-dialog-container .modal-footer').hide();

		// pass data to admin-controller - AJAX
		// AND reload view with updated data
		$.ajax({
			type: 'POST',
			url: callMethod,
			dataType: 'html',
			data: submitData,
			success: function (data){
				$('#stdplan-change-view').html(data);
				$('#confirmation-dialog').modal().hide();
				$('.modal-backdrop').hide();
			}
		});

		return false;

    });
	
	$('#stdplan-change-view').on('click', '.stdplan-edit-wpfcheckbox', function(){
		var cbId = $(this).data('spcid');
		$('#'+cbId+'-wpfname').attr('disabled', !$(this).is(':checked'));
		
	});
	
	/**
	* Runs through all wpf-input-fields and disables them, if corresponding checkbox is not checked
	*/
	function disableUncheckedWpf(){
		$('.stdplan-edit-wpfcheckbox').each(function(){
			var cbId = $(this).data('spcid');
			$('#'+cbId+'-wpfname').attr('disabled', !$(this).is(':checked'));
		});
	}

	// make table-header fixed
	function bindFixedHeader(){
		var stdplanTable = $('.table-fixed-header');
		
		// add fixed header to table
		stdplanTable.fixedHeader();
	}
	
	
<?php endblock(); ?>
	
<?php end_extend(); ?>