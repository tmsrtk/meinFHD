<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengangverwaltung<?php endblock(); ?>

<?php
# general form setup
$stdgnge_filter[0] = 'Bitte auswählen';

# prepare dropdown options
foreach($all_stdgnge as $sg ){ 
	$stdgnge_filter[$sg->StudiengangID] = $sg->StudiengangAbkuerzung . ' ' . $sg->Pruefungsordnung . ' ' . $sg->StudiengangName; 
}
# setup js for dropdown
$params = 'class="input-xxxlarge" id="admin-stdgngfilter"';
?>


<?php startblock('content'); # additional markup before content ?>
	<div class="row-fluid">
	    <div class="span8"><h2>Studiengangverwaltung</h2></div>
	    <div class="span4">
		    <h5>Filter</h5>
		    <?php echo form_dropdown('stdgnge_dropdown', $stdgnge_filter, '', $params); ?>
	    </div>
	</div>
	<hr>
	<?php  echo validation_errors(); ?>
	<div class="row-fluid">
	    <div id="stdgng-list">
		    <!-- this div is dynamically filled after user chose an option from dropdown -->
	    </div>
	<div id="delete-dialog-container"></div>

	</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
				
    // update stdgnge-view according to chosen field in dropdown
    $('#admin-stdgngfilter').change(function() {
	    $("#stdgng-list").html('suche...');
	    // ajax
	    if($(this).val() != 0) {
		    $.get(
			    "<?php echo site_url();?>admin/ajax_show_courses_of_stdgng/",
			    'stdgng_id='+$(this).val(),
			    function(response) {
				// returns view into div
				$('#stdgng-list').html(response);
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
    console.log("nix?: "+ stdgng_id);
    // TODO antoher flag that asks for save-button-click
    if(stdgng_id != "0"){
	console.log(" != 0");
	// auto_load_data_for_id($(this));
	$("#stdgng-list").html('suche...');
	    // reload view
	    $.get(
		"<?php echo site_url();?>admin/ajax_show_courses_of_stdgng/",
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

    // show dialogs
    $('#stdgng-list').on('click', 'button#delete-stdgng-btn', function(){
	var courseId = $(this).attr('name');

	// open dialog and set text to show
	var dialog = createDeleteDialog('Kurs löschen', 'Soll dieser Kurs gelöscht werden?', courseId);
	$('#delete-dialog-container').html(dialog);

	// function of dialog
	$('#delete-dialog').modal({
	    keyboard: false
	// !! important part: on 'show' set data-id= courseId (the one to delete)
	}).on('show', function(){
	    $('#delete-dialog-delete').data('id', courseId);
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
	    .html('<div class="modal-header"><button type="button" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
	    .append('<div class="modal-body"><p>'+text+'</p></div>')
	    .append('<div class="modal-footer"><a href="#" class="btn" id="delete-dialog-cancel" data-dismiss="modal">Abbrechen</a>\n\
		<a href="" class="btn btn-primary" data-id="0" id="delete-dialog-delete">OK</a></div>');

	return myDeleteDialog;
    };

    // behaviour of modal-buttons
    $('#delete-dialog-container').on('click', '#delete-dialog-delete', function(){
	var deleteId = ($(this).data('id'));
	console.log(deleteId);

	// hide action-buttons on dialog
	$('#delete-dialog-container .modal-body').html('Kurs wird gelöscht.');
	$('#delete-dialog-container .modal-footer').hide();

	// pass data to admin-controller to delete course - AJAX
	// AND reload view with updated data
	$.ajax({
	    type: 'POST',
	    url: "<?php echo site_url();?>admin/ajax_delete_single_course_from_stdgng/",
	    dataType: 'html',
	    data: {delete_course_id : deleteId},
	    success: function (data){
		$('#stdgng-list').html(data);
		$('#delete-dialog').modal().hide();
		$('.modal-backdrop').hide();
	    }
	});
	

	

    });
<?php endblock(); ?>

<?php end_extend(); ?>