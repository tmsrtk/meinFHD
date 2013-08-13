<?php
    // general form setup before content

    $data_formopen = array(
        'id' => 'degree-program-save-edits'
    );

    $data_submit = 'id = degree-program-course-details-save-button class = "btn-warning"';

?>
<div id="degree-program-courses" class="span12">
    <?php
	    echo form_open('admin/validate_degree_program_course_changes', $data_formopen);
    ?>
	<table class="table table-striped table-fixed-header" id="degree-program-table">
		<thead id="dp-table-head" class="header">
			<tr>
				<th>ID:</th>
				<th>Kursname:</th>
				<th>Abk.:</th>
				<th>CP:</th>
				<th>SWS:</th>
				<th>Sem.:</th>
				<th>
					Pr&uuml;fungstypen:<br />
					Kl, mdl.Pr, ProPr&auml;, HA, FG, Kol, BA, MA
				</th>
				<th>Beschreibung:</th>
				<th>Aktion:</th>
			</tr>
	    </thead>

	    <tbody id="dp-table-body-new-course">
			<!-- first row as own table to insert new course -->
			<?php echo $new_course; ?>
	    </tbody>
		
		<!-- main-table with all course-data -->
		<tbody id="dp-table-body-courses">
		<?php
            // if there are any courses for the selected degree program show them
			if(isset($dp_course_rows)){
                foreach($dp_course_rows as $row){
					echo $row;
				}
			}
		?>
		</tbody>
	</table>
    <hr/>
    <?php
		// hidden field to transmit the degree-program-id
		echo form_hidden('degree_program_id', $dp_id);
		// save-button
		echo form_submit('validate_degree_program_course_changes', html_entity_decode('&Auml;nderungen an den Kursinformationen speichern', ENT_COMPAT, 'UTF-8'), $data_submit);
		// close form
		echo form_close();
    ?>
</div>
