<?php 
	// open form
	print form_open('admin/validate_stdplan_changes');
?>

<table class="table table-striped">
	<thead>
		<tr>
		<th>Veranstaltungsname:</th>
		<th>Veranstaltungsform:</th>
		<th>Alternative:</th>
		<th>Raum:</th>
		<th>verantw. Lehrkörper:</th>
		<th>Beginn:</th>
		<th>Ende:</th>
		<th>Tag:</th>
		<th>WPF?:</th>
		<th>WPF-Name:</th>
		<th>Farbe:</th>
		<th>Aktion:</th>
		</tr>
	</thead>

	<!-- first row of table - add data -->
	<tbody id="stdplan-table-first-row">
		<tr><?php print $stdplan_first_row; ?></tr>
	</tbody>

	<!-- main-table - editable data -->
	<tbody id="stdplan-table-main">
		<?php foreach($stdplan_course_rows as $row) : ?>
			<tr><?php print $row; ?></tr>
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