<?php 
	// open form
	print form_open('admin/validate_stdplan_changes');
?>

<table class="table table-striped">
	<thead>
		<tr>
		<th>Veranstaltungsname:<br />
		verantw. Lehrkörper:</th>
		<th>Veranstaltungsform:<br />
		Alternative:<br />
		Raum:</th>
		<th>Beginn:<br />
		Ende:<br />
		Tag:</th>
		<th>WPF?:<br />
		WPF-Name:</th>
		<th>Farbe:</th>
		<th>Aktion:</th>
		</tr>
	</thead>

	<!-- first row of table - add data -->
	<tbody id="stdplan-table-first-row">
		<?php print $stdplan_first_row; ?>
	</tbody>

	<!-- main-table - editable data -->
	<tbody id="stdplan-table-main">
		<?php foreach($stdplan_course_rows as $row) : ?>
			<?php print $row; ?>
		<?php endforeach; ?>
	</tbody>
</table>

<?php 
	// hidden field to transmit the stdgng-id
	print form_hidden('stdplan_id_abk', $kurs_ids_split[0]);
	print form_hidden('stdplan_id_sem', $kurs_ids_split[1]);
	print form_hidden('stdplan_id_po', $kurs_ids_split[2]);

	// submitbutton and close form
	$btn_attributes = 'class = "btn-warning"';
	print form_submit('savestdplanchanges', 'Änderungen speichern', $btn_attributes);
	print form_close();
?>