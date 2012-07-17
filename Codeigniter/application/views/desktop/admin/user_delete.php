<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer löschen<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php
	// needet vars
	$data_formopen = array('id' => 'delete_user_row');
	$data_submit = array(
		'name'			=> 'submit',
		'class'			=> 'btn btn-mini btn-danger delete'
	);
	//--------------------------------------------------------------------------
?>
<div class="row-fluid">
	<h2>Benutzer löschen</h2>
</div>
<hr>
<div class="row-fluid">
	<table id="user_overview" class="table table-striped">
		<thead>
			<tr>
				<th>Benutzername</th>
				<th>Nachname</th>
				<th>Vorname</th>
				<th>Email</th>
				<th>Löschen</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($user as $zeile): ?>
			<tr>
				<?php
				echo form_open('admin/delete_user/', $data_formopen);
				// hidden field, with user_id, needed to save changes
				echo form_hidden('user_id', $zeile['BenutzerID']);
				?>
				<td><?php echo $zeile['LoginName'] ?></td>
				<td><?php echo $zeile['Nachname'] ?></td>
				<td><?php echo $zeile['Vorname'] ?></td>
				<td><?php echo $zeile['Email'] ?></td>
				<td><?php echo form_submit($data_submit, 'Loeschen'); ?></td>
				<?php echo form_close(); ?>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<?php endblock(); ?>

<?php # startblock('customFooterJQueryCode');?>
<?php # endblock(); ?>
<?php end_extend(); # end extend main template ?>