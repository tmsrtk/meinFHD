<?php
# general form setup

# textarea
$stdgng_details_textarea_data = array(
	'name' => ($stdgng_details->StudiengangID).'Beschreibung',
	'id' => 'input-stdgng-beschreibung',
	'class' => 'input-xlarge',
	'value' => $stdgng_details->Beschreibung,
	'rows' => 7,
	'cols' => 40
);

# submit button
$btn_attributes = 'class = "btn-warning"';
?>

<div id="stdgng-details">
	<div class="span1"></div>
	<div class="span8">
		<?php echo form_open('admin/validate_stdgng_details_changes'); ?>
		<?php echo form_hidden('stdgng_id', $stdgng_id); // hidden field to transmit the stdgng-id ?>
		<div id="stdgng-details-1" class="span6">
			<table>
			<?php 
				foreach ($stdgng_details as $key => $value){
					if( $key == 'StudiengangName' ||
						$key == 'StudiengangAbkuerzung' ||
						$key == 'Pruefungsordnung' ||
						$key == 'Regelsemester' ||
						$key == 'Creditpoints') {
							
						echo '<tr><td>';
						echo $key;
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
			</table>
		</div>
		<div id="stdgng-details-2" class="span6">
			<?php echo form_textarea($stdgng_details_textarea_data); ?>
			<?php echo form_submit('save_stdgng_detail_changes', 'Änderungen an den Details speichern', $btn_attributes); ?>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>