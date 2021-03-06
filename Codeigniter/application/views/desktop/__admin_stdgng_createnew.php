<div class="well">

<!-- 	<pre> -->
	<?php //print_r($degree_programs); ?>
<!-- 	</pre> -->
	
	<?php 
	    echo validation_errors();
	    echo form_open('admin/validate_new_created_stdgng');
	?>

	<div id="stdgng-details">
		<div id="stdgng-details-1" style='float:left;'>
		    <table>
		    <?php 
			foreach ($all_degree_programs[0] as $key => $value){
			    if($key == 'StudiengangName' || $key == 'StudiengangAbkuerzung' || $key == 'Pruefungsordnung'
					    || $key == 'Regelsemester' || $key == 'Creditpoints'){

				echo '<tr><td>';
				echo $key;
				echo '</td><td>';


				// create empty fields - new course will be created
				$inputFieldData = array(
						'name' => $key,
						'id' => 'input-stdgng-details',
						'value' => set_value($key, ''),
				);
				echo form_input($inputFieldData);
				echo '</td>';
			    }
			}

			// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
			$staticData = array(
					'CreditpointsMin' => '0',
					'FachbereichID' => '5'
			);
			echo form_hidden($staticData);

		    ?>
		    </table>
		</div>
		<div id="stdgng-details-2">
			<?php 
				$degree_program_details_textarea_data = array(
						'name' => 'Beschreibung',
						'id' => 'input-stdgng-beschreibung',
						'value' => '',
						'rows' => 7,
						'cols' => 40
				);
				echo form_textarea($degree_program_details_textarea_data);
				
			?>
	</div>
	
	<div id="stdgng-details-3" style='clear:both;'>
	<?php
		$btn_attributes = 'class = "btn-warning"';
		echo form_submit('save_stdgng_detail_changes', 'Neuen Studiengang speichern', $btn_attributes);
		echo form_close();
	?>
	</div>
	



</div>