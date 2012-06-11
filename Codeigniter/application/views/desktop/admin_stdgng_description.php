<div>

<!-- 	<pre> -->
	<?php //print_r($allStdgnge); ?>
<!-- 	</pre> -->
	
	<?php echo form_open('admin/saveStdgngDescriptionChanges'); 
	
// 	TODO get active StudiengangID !!!!!!!!!!!!!!
	$stdgngIndexImArray = 0;
	?>

	
	<div id="stdgng-details">
		<div id="stdgng-details-1" style='float:left;'>
			<table>
			<?php 
				foreach ($allStdgnge[$stdgngIndexImArray] as $key => $value){
					if($key == 'StudiengangName' || $key == 'StudiengangAbkuerzung' || $key == 'Pruefungsordnung'
							|| $key == 'Regelsemester' || $key == 'Creditpoints'){

						echo '<tr><td>';
						echo $key;
						echo '</td><td>';
						
						
						// get data to display in input-field
						$inputFieldData = array(
								'name' => ($allStdgnge[$stdgngIndexImArray]->StudiengangID).$key,
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
				$stdgngDetailTextareaData = array(
						'name' => ($allStdgnge[$stdgngIndexImArray]->StudiengangID).'Beschreibung',
						'id' => 'input-stdgng-beschreibung',
						'value' => $allStdgnge[$stdgngIndexImArray]->Beschreibung,
						'rows' => 7,
						'cols' => 40
				);
				echo form_textarea($stdgngDetailTextareaData);
			?>
	</div>
	
	<div id="stdgng-details-3" style='clear:both;'>
	<?php
		$btn_attributes = 'class = "btn-warning"';
		echo form_submit('save_stdgng_detail_changes', 'Änderungen an den Details speichern', $btn_attributes);
		echo form_close();
	?>
	</div>
	



</div>