<?php
	// open form
	print form_open('admin/validate_stdplan_changes');
?>
<input type="submit" name="savestdplanchanges" value="&Auml;nderungen speichern" class="btn-warning">
<hr/>
<table class="table table-striped table-fixed-header">
	<thead class="header">
		<tr>
			<th>Veranstaltungsname:<br />
			verantw. Lehrk&ouml;rper:</th>
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
<hr/>
<?php 
	// hidden field to transmit the stdgng-id
	print form_hidden('stdplan_id_abk', $kurs_ids_split[0]);
	print form_hidden('stdplan_id_sem', $kurs_ids_split[1]);
	print form_hidden('stdplan_id_po', $kurs_ids_split[2]);
?>
<input type="submit" name="savestdplanchanges" value="&Auml;nderungen speichern" class="btn-warning">

<?php
    // close the form
    echo form_close();
?>