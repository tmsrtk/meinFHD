<?php
# general form setup

# textarea
$degree_program_details_textarea_data = array(
	'name' => ($dp_details->StudiengangID).'Beschreibung',
	'id' => 'input-degree-program-beschreibung',
	'class' => 'input-xlarge span',
	'value' => $dp_details->Beschreibung
);

# submit button
$btn_attributes = 'class = "btn-warning"';
?>

<div id="degree-program-details" class="well well-small clearfix">
    <?php echo form_open('admin/validate_degree_program_details_changes'); ?>
    <?php echo form_hidden('degree_program_id', $dp_id); // hidden field to transmit the degree-program-id ?>
    <div id="degree-program-details-1" class="span5">
	<table id="dp-details-table"><tbody>
	<?php 
	    foreach ($dp_details as $key => $value){
		if( $key == 'StudiengangName' ||
		    $key == 'StudiengangAbkuerzung' ||
		    $key == 'Pruefungsordnung' ||
		    $key == 'Regelsemester' ||
		    $key == 'Creditpoints') {

				echo '<tr><td>';
				switch($key){
					case 'StudiengangName' : echo 'Studiengang'; break;
					case 'StudiengangAbkuerzung' : echo 'Abkürzung'; break;
					case 'Regelsemester' : echo 'Semester'; break;
					default : echo $key; break;
				}
				echo '</td><td>';

				// get data to display in input-field
				$inputFieldData = array(
					'name' => ($dp_details->StudiengangID).$key,
					'id' => 'input-degree-program-details',
					'value' => $value,
					'rows' => 7,
					'cols' => 40
				);
				echo form_input($inputFieldData);
				echo '</td>';
			}
	    }
	?>
	</tbody></table>
    </div>
    <div id="degree-program-details-2" class="span6">
	<?php echo form_textarea($degree_program_details_textarea_data); ?>
    </div>
    <div class="span12">
	<?php echo form_submit('save_degree_program_detail_changes', 'Änderungen an den Details speichern', $btn_attributes); ?>
	<?php echo form_close(); ?>
    </div>
</div>
<hr>