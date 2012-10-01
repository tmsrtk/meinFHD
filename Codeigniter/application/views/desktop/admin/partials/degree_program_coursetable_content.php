<div id="stdgng-courses" class="span12">
    
	
    <?php 
		$change_data_form_attributes = array('id' => 'stdgng-save-button');
		print form_open('admin/validate_degree_program_course_changes', $change_data_form_attributes); //save_stdgng_course_changes
    ?>
	<table class="table table-fixed-header" id="degree-program-table">
		<thead id="dp-table-head" class="header">
			<tr>
				<th>ID:</hd>
				<th>Kursname:</hd>
				<th>Abk.:</th>
				<th>CP:</th>
				<th>SWS:</th>
				<th>Sem.:</th>
				<th>Prüfungstypen:</th>
				<th>Beschreibung:</th>
				<th>Aktion:</th>
			</tr>
	    </thead>
	    
	    <tbody id="dp-table-body-first">
			<!-- first row as own table to insert new course -->
			<?php echo $new_course; ?>
	    </tbody>
		
		<!-- main-table with all course-data -->
		<tbody id="dp-table-body-main">
		<?php 
			if($stdgng_course_rows){
				foreach($stdgng_course_rows as $row){
					echo $row;
				}
			}
		?>

		</tbody>
	</table>

    <?php
		// hidden field to transmit the stdgng-id
		print form_hidden('stdgng_id', $stdgng_id);

		// save-button
		$btn_attributes = 'id = #stdgng-course-details-save-button class = "btn-warning"';
		print form_submit('save_stdgng_course_changes', 'Änderungen speichern', $btn_attributes);
		// close form
		print form_close();

    ?>

</div>
