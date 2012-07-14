<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Rechteverwaltung<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php

// prepare attributes for submit button 
$submitButtonAttributes = array(
	'name'			=> 'save_role_changes',
	'type'			=> 'submit',
	'id'			=> 'save_role_changes',
	'content'			=> 'Änderungen speichern',
	'class'			=> 'btn btn-primary span12'
);

?>
				<?php echo form_open('admin/savePermissions'); ?>
					<div class="row-fluid">
						<div class="span8"><h1>Rechteverwaltung</h1></div>
						<div class="span4"><?php echo form_button($submitButtonAttributes); ?></div>
					</div>
					<hr class="clearfix">
					<table class="table table-striped">
						<!-- Ausgabe des Tabellenkopfs -->
						<thead>
							<tr>
								<td></td>
								<?php foreach($roles as $r) : ?>
								<td><?php echo $r->bezeichnung; ?></td>
								<?php endforeach; ?>
							</tr>
						</thead>
						<!-- Bezeichnungen der Permissions zwischenspeichern -->
						<?php
							foreach($permissions as $p) {
								$permission_names[$p->BerechtigungID] = $p->bezeichnung;
							}
						?>
						<!-- Aufbau der Tabelle -->
						<?php
							$counter = 0; // 
							$permission_id = '';
							foreach ($tableviewData as $v) {
//									if($counter % 6 === 0){ // not sure if konstantin's (this line) or frank's version (line 24) is correct
								if($counter % ($roleCounter+1) === 0) {
									$permission_id = $v;
									echo '<tr><td style="width:250px";>'.$permission_names[$v];
								} else {
									if ($v !== 'x') {
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
								}
							};
						?>
					</table>
					<hr>
					<div class="row-fluid">
						<div class="span8"></div>
						<div class="span4"><?php echo form_button($submitButtonAttributes); ?></div>
					</div>
				<?php echo form_close(); ?>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>
