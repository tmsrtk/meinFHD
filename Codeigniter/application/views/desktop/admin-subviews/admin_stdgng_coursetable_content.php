<?php 
    $form_attributes = array('id' => 'stdgng-save-button');
    print form_open('admin/validate_stdgng_course_changes', $form_attributes); //save_stdgng_course_changes
?>


<table class="table table-striped table-bordered table-condensed">
    <thead>
	<tr>
	    <th>Kursname:</hd>
	    <th>Abk.:</th>
	    <th>CP:</th>
	    <th>SWS:</th>
	    <th>Sem.:</th>
	    <th>Beschreibung:</th>
	    <th>Aktion:</th>
	</tr>
    </thead>
    <tbody>
	<!-- TODO first row as insert-row -->

	<?php foreach($stdgng_course_rows as $row) : ?>
	    <tr><?php print $row; ?></tr>
	<?php endforeach; ?>

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