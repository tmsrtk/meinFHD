<table> 
    <!-- tablehead -->
    <thead><tr>
	    <th>StudiengangName:</th>
	    <th>Prüfungsordnung:</th>
	    <th>Löschen</th>
    </tr></thead>

    <tbody>
    <?php 

	foreach($all_degree_programs as $sg) :  
	    echo form_open('admin/delete_stdgng'); ?>
	    <tr>
		<td><?php echo $sg->StudiengangName; ?></td>
		<td><?php echo $sg->Pruefungsordnung; ?></td>
		<td>
		    <?php
			$btn_attributes = 'class = "btn-danger"';
			echo form_submit('delete_sdtgng', 'Studiengang löschen', $btn_attributes);
		    ?>
		</td>
	    </tr>
	    <?php 
		// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
		$hiddenData = array(
			'deleteStdgngId' => $sg->StudiengangID
		);
		echo form_hidden($hiddenData);
		echo form_close();
	    ?>

	<?php endforeach; ?>
    </tbody>
</table>	