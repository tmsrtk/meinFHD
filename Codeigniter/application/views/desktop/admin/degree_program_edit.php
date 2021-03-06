<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang bearbeiten<?php endblock(); ?>

<?php
	// general form setup
	$degree_program_filter[0] = 'Bitte auswählen';

	// prepare dropdown options
	foreach($all_degree_programs as $dp ){ 
		$degree_program_filter[$dp->StudiengangID] = $dp->StudiengangAbkuerzung . ' ' . $dp->Pruefungsordnung . ' ' . $dp->StudiengangName; 
	}
	// setup js for dropdown
	$params = 'class="input-xxxlarge" id="admin-degree-program-filter"';
?>


<?php startblock('content'); # additional markup before content ?>
	<div class="row-fluid">
	    <div class="span8"><h2>Studiengangverwaltung</h2></div>
	    <div class="span4">
		    <h5>Filter</h5>
		    <?php echo form_dropdown('degree_program_dropdown', $degree_program_filter, '', $params); ?>
	    </div>
	</div>
	<hr>
	<?php  echo validation_errors(); ?>
	<div class="row-fluid">
	    <div id="degree-program-list">
		    <!-- this div is dynamically filled after user chose an option from dropdown -->
	    </div>
	<div id="confirmation-dialog-container"></div>

	</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
	
    // update degree-program-view according to chosen field in dropdown
    $('#admin-degree-program-filter').change(function() {
	    $("#degree-program-list").html('suche...');
	    // ajax
	    if($(this).val() != 0) {
		    $.get(
			    "<?php echo site_url();?>admin/ajax_show_courses_of_degree_program/",
			    'degree_program_id='+$(this).val(),
			    function(response) {
					// returns view into div
					$('#degree-program-list').html(response);
					bindFixedHeader(); // provides fixed-header functionality
			    });
	    } else {
		    $("#degree-program-list").html('');
	    }

    });
	    

	// getting id from php
	var degreeProgramId = '';
    var degreeProgramId = "<?php echo $degree_program_id_automatic_reload; ?>";
	
    // autoreload after submission AND validation-errors
	if(degreeProgramId != '0'){
		reloadDegreeProgram(degreeProgramId);
	}
	
	// reloads a degree program that has been selected via dropdown before
    function reloadDegreeProgram(dpId){

		//console.log('reload view:id  != 0');
		// auto_load_data_for_id($(this));
		$('#degree-program-list').html('suche...');

		// reload view
	    $.get(
			"<?php echo site_url();?>admin/ajax_show_courses_of_degree_program/",
			// send degree-program-id AND a flag to signalize that default-values for input-fields should be empty
			{degree_program_id: dpId},
			function(response) {
				$('#degree-program-list').html(response);
				bindFixedHeader(); // must call - lost during request
				
				// set correct dropdown-value
				$('#admin-degree-program-filter').val(dpId);
				
			});
	}
    
    // ##################### delete degree-program-course dialog

    // show delete dialog
    $('#degree-program-list').on('click', 'button.delete-degree-program-btn', function(){
		var courseId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createConfirmationDialog('Kurs löschen', 'Soll dieser Kurs gelöscht werden?');
		$('#confirmation-dialog-container').html(dialog);

		// function of dialog
		$('#confirmation-dialog').modal({
			keyboard: false,
			backdrop: 'static'
		// !! important part: on 'show' set data-id= courseId (the one to delete)
		}).on('show', function(){
			$('#conf-dialog-confirm').data('id', courseId);
			$('#conf-dialog-confirm').data('delete', 1);
		// on hide hide ^^
		}).on('hide', function(){
			//console.log('hidden');
		}).modal('show');

		return false;
	});
	
	
    // show save dialog
    $('#degree-program-list').on('click', '#degree-program-course-create', function(){
		var degreeProgramId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createConfirmationDialog('Kurs erstellen', 'Soll der Kurs erstellt werden?');
		$('#confirmation-dialog-container').html(dialog);

		// function of dialog
		$('#confirmation-dialog').modal({
			keyboard: false,
			backdrop: 'static'
		// !! important part: on 'show' set data-id= degreeProgramId (the one to delete)
		}).on('show', function(){
			$('#conf-dialog-confirm').data('id', degreeProgramId);
			$('#conf-dialog-confirm').data('delete', 0);
		// on hide hide ^^
		}).on('hide', function(){
			//console.log('hidden');
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
		var deleteId = ($(this).data('delete'));
		var id = ($(this).data('id')); // delete delivers course-id; create delivers po
		var callMethod = '';
		var submitData = '';
		//console.log(deleteId);

		// delete button was clicked
		if(deleteId != 0){
			$('#confirmation-dialog-container .modal-body').html('Kurs wird gelöscht.');
			callMethod = "<?php echo site_url();?>admin/ajax_delete_single_course_from_degree_program/";
			submitData = {course_data : id};
		
		// add button was clicked
		} else {
			$('#confirmation-dialog-container .modal-body').html('Kurs wird erstellt.');
			callMethod = "<?php echo site_url();?>admin/ajax_create_new_course_in_degree_program/";
			
			// fill createData with first-row-data
			var createData = new Array(
				id+'-StudiengangID',
				$('#new-course-coursename').val()+'-Kursname',
				$('#new-course-coursename-short').val()+'-kurs_kurz',
				$('#new-course-cp').val()+'-Creditpoints',
				$('#new-course-sws-vorl').val()+'-SWS_Vorlesung',
				$('#new-course-sws-ueb').val()+'-SWS_Uebung',
				$('#new-course-sws-prakt').val()+'-SWS_Praktikum',
				$('#new-course-sws-pro').val()+'-SWS_Projekt',
				$('#new-course-sws-seminar').val()+'-SWS_Seminar',
				$('#new-course-sws-seminar-u').val()+'-SWS_SeminarUnterricht',
				$('#new-course-semester').val()+'-Semester',
				$('#new-course-ext-1').attr('checked')+'-ext_1',
				$('#new-course-ext-2').attr('checked')+'-ext_2',
				$('#new-course-ext-3').attr('checked')+'-ext_3',
				$('#new-course-ext-4').attr('checked')+'-ext_4',
				$('#new-course-ext-5').attr('checked')+'-ext_5',
				$('#new-course-ext-6').attr('checked')+'-ext_6',
				$('#new-course-ext-7').attr('checked')+'-ext_7',
				$('#new-course-ext-8').attr('checked')+'-ext_8',
				$('#new-course-description').val()+'-Beschreibung'
			);
			submitData = {course_data : createData};
		}
		
		// hide action-buttons on dialog
		$('#confirmation-dialog-container .modal-footer').hide();

		// pass data to admin-controller to delete course - AJAX
		// AND reload view with updated data
		$.ajax({
			type: 'POST',
			url: callMethod,	// makes this peace of code reusable
			dataType: 'html',
			data: submitData,
			success: function (data){
				$('#degree-program-list').html(data);
				$('#confirmation-dialog').modal().hide();
				$('.modal-backdrop').hide();
				bindFixedHeader(); // has to be called cause functionality lost during update
			}
		});


		return false;

    });
	
	// makes table-header fixed
	// for functionality have a look at resources/bootstrap/js/table-fixed-header.js
	function bindFixedHeader(){
		var dpTable = $('.table-fixed-header');
		
		// add fixed header to table
		dpTable.fixedHeader();
	}
    
        
    
<?php endblock(); ?>

<?php end_extend(); ?>