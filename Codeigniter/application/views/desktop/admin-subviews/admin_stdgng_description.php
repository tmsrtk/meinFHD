<div id="stdgng-details">

	<?php echo form_open('admin/save_stdgng_details_changes'); ?>
	
	<div id="stdgng-details-1" style='float:left;'>
	    <table>
	    <?php 
		foreach ($stdgng_details as $key => $value){
		    if($key == 'StudiengangName' || $key == 'StudiengangAbkuerzung' || $key == 'Pruefungsordnung'
				|| $key == 'Regelsemester' || $key == 'Creditpoints'){

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

	<div id="stdgng-details-2">
	    <?php 
		$stdgng_details_textarea_data = array(
			'name' => ($stdgng_details->StudiengangID).'Beschreibung',
			'id' => 'input-stdgng-beschreibung',
			'value' => $stdgng_details->Beschreibung,
			'rows' => 7,
			'cols' => 40
		);
		echo form_textarea($stdgng_details_textarea_data);
	    ?>
	</div>
	
	<div id="stdgng-details-3" style='clear:both;'>
	    <?php
		$btn_attributes = 'class = "btn-warning"';
		
		// hidden field to transmit the stdgng-id
		echo form_hidden('stdgng_id', $stdgng_id);
		
		echo form_submit('save_stdgng_detail_changes', 'Änderungen an den Details speichern', $btn_attributes);
		echo form_close();
	    ?>
	</div>
	
</div>