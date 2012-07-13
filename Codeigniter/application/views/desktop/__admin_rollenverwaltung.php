<div >
	<table>
	
		<!-- Ausgabe des Tabellenkopfs -->
		<thead><tr><td>Berechtigungen</td>
		<?php foreach($roles as $r) : ?>
			<td><?php echo $r->bezeichnung; ?></td>
		<?php endforeach; ?>
		</tr></thead>

		<!-- Bezeichnungen der Permissions zwischenspeichern -->
		<?php foreach($permissions as $p) {
			$permission_names[$p->BerechtigungID] = $p->bezeichnung;
		}?>
	
		<!-- Aufbau der Tabelle -->
		<?php
			echo form_open('admin/savePermissions');
			$counter = 0; // 
			$permission_id = '';
			foreach ($tableviewData as $v) :?>
				<?php
<<<<<<< Temporary merge branch 1
				if($counter % 6 === 0){
=======
				if($counter % ($roleCounter+1) === 0){
					$permission_id = $v;
					echo '<tr><td style="width:250px";>'.$permission_names[$v];
				} else {
					if ($v !== 'x'){
						// Checkbox als aktiv setzen - name wird eindeutig aus Permission und Rolle zusammengesetzt
						echo '<td style="width:100px">'. form_checkbox($permission_id.$counter, 'accept', TRUE) .'</td>';
					} else {
						echo '<td style="width:100px">'. form_checkbox($permission_id.$counter, 'accept', FALSE) .'</td>';
					}
					
				}
				$counter++;
				
				// wenn alle Rollen zu dieser Berechtigung durchlaufen wurden - zurücksetzen
				if($counter == $roleCounter+1) {
					echo '</tr>';
					$counter = 0;
				} ?>
		<?php endforeach; ?>
		
		<?php
			 			 
			echo form_submit('save_role_changes', 'Änderungen speichern');
			echo form_close();
		?>
		
		
	
					
	</table>
</div>