<?php
# general form setup

# textarea
$degree_program_details_textarea_data = array(
	'name' => ($stdgng_details->StudiengangID).'Beschreibung',
	'id' => 'input-stdgng-beschreibung',
	'class' => 'input-xlarge span',
	'value' => $stdgng_details->Beschreibung
);

# submit button
$btn_attributes = 'class = "btn-warning"';
?>

<div id="stdgng-details" class="well well-small clearfix">
    <?php echo form_open('admin/validate_degree_program_details_changes'); ?>
    <?php echo form_hidden('stdgng_id', $stdgng_id); // hidden field to transmit the stdgng-id ?>
    <div id="stdgng-details-1" class="span5">
	<table id="dp-details-table"><tbody>
	<?php 
	    foreach ($stdgng_details as $key => $value){
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
			    'name' => ($stdgng_details->StudiengangID).$key,
			    'id' => 'input-stdgng-details',
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
    <div id="stdgng-details-2" class="span6">
	<?php echo form_textarea($degree_program_details_textarea_data); ?>
    </div>
    <div class="span12">
	<?php echo form_submit('save_stdgng_detail_changes', 'Änderungen an den Details speichern', $btn_attributes); ?>
	<?php echo form_close(); ?>
    </div>
</div>
<hr>