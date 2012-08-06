<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang bearbeiten<?php endblock(); ?>

<?php
# general form setup
$degree_program_filter[0] = 'Bitte auswählen';

# prepare dropdown options
foreach($all_stdgnge as $dp ){ 
	$degree_program_filter[$dp->StudiengangID] = $dp->StudiengangAbkuerzung . ' ' . $dp->Pruefungsordnung . ' ' . $dp->StudiengangName; 
}
# setup js for dropdown
$params = 'class="input-xxxlarge" id="admin-stdgngfilter"';
?>


<?php startblock('content'); # additional markup before content ?>
	<div class="row-fluid">
	    <div class="span8"><h2>Studiengangverwaltung</h2></div>
	    <div class="span4">
		    <h5>Filter</h5>
		    <?php echo form_dropdown('stdgnge_dropdown', $degree_program_filter, '', $params); ?>
	    </div>
	</div>
	<hr>
	<?php  echo validation_errors(); ?>
	<div class="row-fluid">
	    <div id="stdgng-list">
		    <!-- this div is dynamically filled after user chose an option from dropdown -->
	    </div>
	<div id="confirmation-dialog-container"></div>

	</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
				
    // update stdgnge-view according to chosen field in dropdown
    $('#admin-stdgngfilter').change(function() {
	    $("#stdgng-list").html('suche...');
	    // ajax
	    if($(this).val() != 0) {
		    $.get(
			    "<?php echo site_url();?>admin/ajax_show_courses_of_degree_program/",
			    'stdgng_id='+$(this).val(),
			    function(response) {
					// returns view into div
					$('#stdgng-list').html(response);
					bindFixedHeader();
			    });
	    } else {
		    $("#stdgng-list").html('');
	    }

    });
	    

//		$('#stdgng-save-button').submit(function() {
//		    console.log("submit");
//		    // aufruf abfrage auf id
//		    // reload wenn wert drin ist
//		    auto_load_data_for_id();
////		    return false;
//		});

	    
    // autoreload after submission AND validation-errors
    var stdgng_id = "<?php echo $stdgng_id_automatic_reload; ?>";
    console.log("dp-id from controller - preselect dropdown?: "+ stdgng_id);
    // TODO antoher flag that asks for save-button-click
    if(stdgng_id != "0"){
		console.log(" != 0");
		// auto_load_data_for_id($(this));
		$("#stdgng-list").html('suche...');
	    // reload view
	    $.get(
			"<?php echo site_url();?>admin/ajax_show_courses_of_degree_program/",
			// send stdgng_id AND a flag to signalize that default-values for input-fields should be empty
			{stdgng_id: stdgng_id},
			function(response) {
				// 
				$('#stdgng-list').html(response);
			});
	    // set correct dropdown-value
	    $("#admin-stdgngfilter").val(stdgng_id);
	    stdgng_id = "";
    }
    
    // ##################### delete stdgng-course dialog

    // show delete dialog
    $('#stdgng-list').on('click', 'button.delete-stdgng-btn', function(){
		var courseId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createConfirmationDialog('Kurs löschen', 'Soll dieser Kurs gelöscht werden?');
		$('#confirmation-dialog-container').html(dialog);

		// function of dialog
		$('#confirmation-dialog').modal({
			keyboard: false
		// !! important part: on 'show' set data-id= courseId (the one to delete)
		}).on('show', function(){
			$('#conf-dialog-confirm').data('id', courseId);
			$('#conf-dialog-confirm').data('delete', 1);
		// on hide hide ^^
		}).on('hide', function(){
			console.log('hidden');
		}).modal('show');

		return false;
	});
	
	
    // show save dialog
    $('#stdgng-list').on('click', '#degree-program-course-create', function(){
		var degreeProgramId = $(this).attr('name');

		// open dialog and set text to show
		var dialog = createConfirmationDialog('Kurs erstellen', 'Soll der Kurs erstellt werden?');
		$('#confirmation-dialog-container').html(dialog);

		// function of dialog
		$('#confirmation-dialog').modal({
			keyboard: false
		// !! important part: on 'show' set data-id= degreeProgramId (the one to delete)
		}).on('show', function(){
			$('#conf-dialog-confirm').data('id', degreeProgramId);
			$('#conf-dialog-confirm').data('delete', 0);
		// on hide hide ^^
		}).on('hide', function(){
			console.log('hidden');
		}).modal('show');

		return false;
	});
	

	// create dialog element
	function createConfirmationDialog(title, text) {
		var myDeleteDialog = 
			$('<div class="modal hide" id="confirmation-dialog"></div>')
			.html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
			.append('<div class="modal-body"><p>'+text+'</p></div>')
			.append('<div class="modal-footer"><a href="#" class="btn" id="conf-dialog-cancel" data-dismiss="modal">Abbrechen</a>\n\
			<a href="" class="btn btn-primary" data-id="0" data-delete="0" id="conf-dialog-confirm" data-accept="modal">OK</a></div>');

		return myDeleteDialog;
    };

    // behaviour of modal-buttons
    $('#confirmation-dialog-container').on('click', '#conf-dialog-confirm', function(){
		var deleteId = ($(this).data('delete'));
		var id = ($(this).data('id')); // delete delivers course-id; create delivers po
		var callMethod = '';
		var submitData = '';
		console.log(deleteId);

		if(deleteId != 0){
			$('#confirmation-dialog-container .modal-body').html('Kurs wird gelöscht.');
			callMethod = "<?php echo site_url();?>admin/ajax_delete_single_course_from_degree_program/";
			submitData = {course_data : id};
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
			url: callMethod,
			dataType: 'html',
			data: submitData,
			success: function (data){
				$('#stdgng-list').html(data);
				$('#confirmation-dialog').modal().hide();
				$('.modal-backdrop').hide();
			}
		});


		return false;

    });
	
	// make table-header fixed
	function bindFixedHeader(){
		var stdplanTable = $('.table-fixed-header');
		
		console.log('test');
		
		// add fixed header to table
		stdplanTable.fixedHeader();
	}
    
        
    
<?php endblock(); ?>

<?php end_extend(); ?>