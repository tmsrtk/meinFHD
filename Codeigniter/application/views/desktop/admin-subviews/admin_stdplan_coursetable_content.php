<div id="stdplan-change-view">
    
    <?php 
	// open form
	print form_open('admin/save_stdplan_changes');
    ?>
    
    <table class="table table-striped table-bordered table-condensed">
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
	
	<tbody>
	    
	    <!-- build first row static - empty values TODO-->
	
	    <?php foreach($stdplan_course_rows as $row) : ?>
		    <tr><?php print $row; ?></tr>
	    <?php endforeach; ?>
	
	</tbody>
    </table>
	    
    <?php 
	// submitbutton and close form
	$btn_attributes = 'class = "btn-warning"';
	print form_submit('savestdplanchanges', 'Änderungen speichern', $btn_attributes);
	print form_close();
    ?>
    
</div>