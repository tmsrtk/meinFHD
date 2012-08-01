<div class="well">

	<!-- build array for Studiengang-Filter -->
	<?php 
		echo validation_errors();
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
	
		// generates Dropdown with stdgngeFilter as Options
		$js = 'id="admin_stdplanfilter"';
		echo form_dropdown('stdplanFilter', $stdplanFilter, '', $js);
	?>
	
<!--	<pre>
	<?php //echo print_r($stdplanFilter); ?>
	</pre>-->

	<div id="stdplan-change-view">
		<!-- load data dynamically via AJAX -->
	</div>
	
	<div id="delete-dialog-container"></div>

</div>


<script>
	(function() {

		// update stdplan-view according to chosen field in dropdown

		$('#admin_stdplanfilter').change(function() {
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
		
	})(); // self envoked anonymous function
	
	    
		
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
			}
	    });
	    $('#admin_stdplanfilter').val(stdplan_ids);
	    stdplan_ids = '';
	}
	
	
	// show delete dialogs
    $('#stdplan-change-view').on('click', 'button.delete-stdpln-btn', function(){
		var spCourseId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createDeleteDialog('Veranstaltgung löschen', 'Soll diese Veranstaltung gelöscht werden?', spCourseId);
		$('#delete-dialog-container').html(dialog);

		// function of dialog
		$('#delete-dialog').modal({
			keyboard: false
		// !! important part: on 'show' set data-id= courseId (the one to delete)
		}).on('show', function(){
			$('#delete-dialog-delete').data('id', spCourseId);
		// on hide hide ^^
		}).on('hide', function(){
			console.log('hidden');
		}).modal('show');

		return false;
	});


	// show save dialog
	$('#stdplan-change-view').on('click', '#create-event-btn', function(){
		var spCourseId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createDeleteDialog('Veranstaltgung löschen', 'Soll diese Veranstaltung gelöscht werden?', spCourseId);
		$('#delete-dialog-container').html(dialog);

		// function of dialog
		$('#delete-dialog').modal({
			keyboard: false
		// !! important part: on 'show' set data-id= courseId (the one to delete)
		}).on('show', function(){
			$('#delete-dialog-delete').data('id', spCourseId);
		// on hide hide ^^
		}).on('hide', function(){
			console.log('hidden');
		}).modal('show');

		return false;
	});

	// create dialog element
	function createDeleteDialog(title, text, courseId) {
		var myDeleteDialog = 
			$('<div class="modal hide" id="delete-dialog"></div>')
			.html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
			.append('<div class="modal-body"><p>'+text+'</p></div>')
			.append('<div class="modal-footer"><a href="#" class="btn" id="delete-dialog-cancel" data-dismiss="modal">Abbrechen</a>\n\
			<a href="" class="btn btn-primary" data-id="0" id="delete-dialog-delete" data-accept="modal">OK</a></div>');

		return myDeleteDialog;
    };
	
	// behaviour of modal-buttons
    $('#delete-dialog-container').on('click', '#delete-dialog-delete', function(){
		var deleteId = ($(this).data('id'));
		console.log(deleteId);

		// hide action-buttons on dialog
		$('#delete-dialog-container .modal-body').html('Veranstaltung wird gelöscht.');
		$('#delete-dialog-container .modal-footer').hide();

		// pass data to admin-controller to delete course - AJAX
		// AND reload view with updated data
		$.ajax({
			type: 'POST',
			url: "<?php echo site_url();?>admin/ajax_delete_single_event_from_stdplan/",
			dataType: 'html',
			data: {delete_course_id : deleteId},
			success: function (data){
				$('#stdplan-change-view').html(data);
				$('#delete-dialog').modal().hide();
				$('.modal-backdrop').hide();
			}
		});


		return false;

    });

</script>