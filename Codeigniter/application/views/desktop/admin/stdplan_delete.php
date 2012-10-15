<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan löschen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>
	
<?php
# general form setup
?>

<?php startblock('content'); # additional markup before content ?>

		<div class="row-fluid">
			<h2>Stundenplan löschen</h2>
		</div>
		<hr>

		<div class="row-fluid">
			<table class="table table-striped"> 
				<!-- tablehead -->
				<thead>
					<tr>
						<th>Studiengang:</th>
						<th>Kurzbezeichnung:</th>
						<th>Löschen</th>
					</tr>
				</thead>

				<tbody>
				<?php 
					$btn_attributes = 'class = "btn-danger"';

					foreach($delete_view_data as $sp) : ?>

					<tr>
						<td><?php echo $sp->StudiengangName; ?></td>
						<td><?php echo $sp->StudiengangAbkuerzung.'_'.$sp->Semester.'_'.$sp->Pruefungsordnung; ?></td>
						<td>
							<?php
								echo form_open('admin/delete_stdplan');
								echo form_submit('delete_sdtplan', 'Stundenplan loeschen', $btn_attributes);
								// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
								$hiddenData = array(
			//						'stdgng_id' => $sp->StudiengangID,
									'stdplan_abk' => $sp->StudiengangAbkuerzung,
									'stdplan_semester' => $sp->Semester,
									'stdplan_po' => $sp->Pruefungsordnung
								);
								echo form_hidden($hiddenData);
								echo form_close();
							?>
						</td>
					</tr>

					<?php endforeach; ?>
				</tbody>
			</table>	
		</div>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
<?php endblock(); ?>

<?php end_extend(); ?>