<div>

	<!-- build array for Studiengang-Filter -->
	<?php 
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

</script>