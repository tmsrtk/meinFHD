<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
				<div class="span3"></div>
				<div class="span6">
					<div class="well well-small">
<?php endblock(); ?>

<?php
// general form setup

#textarea
$stdgng_details_textarea_data = array(
	'name' => 'Beschreibung',
	'id' => 'input-stdgng-beschreibung',
	'class' => 'input-xxxlarge',
	'value' => '',
	'rows' => 7,
	'cols' => 40
);
# submit button
$submit_data = array(
	'name'		=> 'submit',
	'class'		=> 'btn btn-mini btn-danger delete'
);

?>

<?php startblock('content'); # additional markup before content ?>
				<div class="row-fluid">
					<h2>Studiengang löschen</h2>
				</div>
				<hr>
				<table id="studiengang_delete" class="table table-striped table-bordered">
					<!-- tablehead -->
					<thead>
						<tr>
							<th>Studiengang</th>
							<th>PO</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($allStdgnge as $sg) : ?>
						<?php echo form_open('admin/delete_stdgng'); ?>
						<tr>
							<td><?php echo $sg->StudiengangName; ?></td>
							<td><?php echo $sg->Pruefungsordnung; ?></td>
							<td><?php echo form_submit($data_submit, 'löschen'); ?></td>
						</tr>
						<?php
							// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
							$hiddenData = array( 'deleteStdgngId' => $sg->StudiengangID );
						?>
						<?php echo form_hidden($hiddenData); ?>
						<?php echo form_close(); ?>
					<?php endforeach; ?>
					</tbody>
				</table>	

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
					</div>
				</div>
				<div class="span3"></div>
<?php endblock(); ?>
<?php end_extend(); ?>