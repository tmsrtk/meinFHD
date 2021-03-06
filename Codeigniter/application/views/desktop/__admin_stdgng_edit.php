<div class="well">
	<!-- build array for Studiengang-Filter -->
	<?php 
	    echo validation_errors();
	    
	    $stdgnge_filter[0] = 'Bitte auswählen';
	    foreach($all_stdgnge as $sg){ 
		$stdgnge_filter[$sg->StudiengangID] =
			$sg->StudiengangAbkuerzung.' '
			.$sg->Pruefungsordnung.' '
			.$sg->StudiengangName; 
	}
	
	// generates Dropdown with stdgngeFilter as Options
	$js = 'id="admin-stdgngfilter"';
	echo form_dropdown('stdgnge_dropdown', $stdgnge_filter, '', $js);
	?>

	<div id="stdgng-list">
	    <!-- this div is dynamically filled after user chose an option from dropdown -->
	</div>
	
	<!--	confirmation-dialog after pressing delete-button -->
	<div class="alert" id="stdgng-course-delete-confirmation-dialog" style="display:none">
	    Soll der gewählte Kurs <br />
	    --Kurs eintragen--<br />
	    wirklich gelöscht werden?	    
	</div>

</div>


<script>    
    
    
	(function() {
	    
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
		
		// dialog for delete-button
		$('#stdgng-change-view').on('click', '#delete_btn_stdgng', function(){
		    var id = $(this).attr('data-id');
		    $('#stdgng-course-delete-confirmation-dialog').
			data('course-id', id).dialog('open');
		    return false;
		});
		
		$('#stdgng-course-delete-confirmation-dialog').dialog({
		    resizable: false,
		    height: 250,
		    modal: true,
		    autoOpen: false,
		    buttons: {
			'Kurs löschen': function() {
			    var id = $(this).data('course-id');
			    alert('Kurs wurde gelöscht' + id);
			    $(this).dialog('close');
			},
			'Abbrechen': function() {
			    $(this).dialog('close');
			}
		    }
		});
		
	})(); // self envoked anonymous function

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


</script>