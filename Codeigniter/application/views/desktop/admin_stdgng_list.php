<div>

	<!-- build array for Studiengang-Filter -->
	<?php 
		$stdgngeFilter[0] = 'Bitte auswählen';
		foreach($allStdgnge as $sg){ 
			$stdgngeFilter[$sg->StudiengangID] =
				$sg->StudiengangAbkuerzung.' '
				.$sg->Pruefungsordnung.' '
				.$sg->StudiengangName; 
	}
	
	// generates Dropdown with stdgngeFilter as Options
	$js = 'id="admin_stdgngfilter"';
	echo form_dropdown('stdgngeFilter', $stdgngeFilter, '', $js);
	?>

	<div id="stdgng-change-view">
		<!-- load data dynamically via AJAX -->
	</div>
	
	<div class="alert" id="stdgng-course-delete-confirmation-dialog" style="display:none">
	    Soll der gewählte Kurs <br />
	    --Kurs eintragen--<br />
	    wirklich gelöscht werden?	    
	</div>

</div>


<script>
	(function() {

		// update stdgnge-view according to chosen field in dropdown

		$('#admin_stdgngfilter').change(function() {
			$("#stdgng-change-view").html('suche...');
			// ajax
			if($(this).val() != 0) {
				$.get(
					"<?php echo site_url();?>admin/ajax_show_courses_of_stdgng/",
					'stdgng_id='+$(this).val(),
					function(response) {
						// 
						$('#stdgng-change-view').html(response);
					});
			} else {
				$("#stdgng-change-view").html('');
			}

		});
		
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
		})
		
	})(); // self envoked anonymous function

</script>