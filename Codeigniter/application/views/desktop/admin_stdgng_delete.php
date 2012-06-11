<div>
	<table> 
		<!-- tablehead -->
		<thead><tr>
			<td>StudiengangName:</td>
			<td>Prüfungsordnung:</td>
			<td>Löschen</td>
		</tr></thead>
	
		<?php 
			$btn_attributes = 'class = "btn-danger"';
			foreach($allStdgnge as $sg) :  
			echo form_open('admin/deleteStdgng'); ?>
			<tr><td>
			<?php echo $sg->StudiengangName; ?>
			</td><td>
			<?php echo $sg->Pruefungsordnung; ?>
			</td><td>
			<?php $attributes = array('class' => 'listform', 'id' => 'stdgngform');
				echo form_submit('delete_sdtgng', 'Studiengang löschen', $btn_attributes); ?>
			</td></tr>
			<?php 
				// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
				$hiddenData = array(
					'deleteStdgngId' => $sg->StudiengangID
				);
				echo form_hidden($hiddenData);
				echo form_close();
				
			endforeach; ?>
	</table>	
</div>