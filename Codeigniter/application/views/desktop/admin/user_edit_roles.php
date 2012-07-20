<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzerrollen bearbeiten<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php
	// needet vars
	$data_formopen = array('id' => 'changeroles_user_row');
	$data_submit = array(
		'name'			=> 'delete_user_btn',
		'class'			=> 'btn btn-mini btn-danger save_userroles_btn'
	);
	//--------------------------------------------------------------------------
?>
<div class="row-fluid">
	<h2>Benutzerrollen bearbeiten</h2>
</div>
<hr>
<div class="row-fluid">
	<table id="user_overview" class="table table-striped">
		<thead>
			<tr>
				<th>
					<div class="span2">Loginname</div>
					<div class="span2">Nachname</div>
					<div class="span2">Vorname</div>
					<div class="span2">Rollen</div>
					<div class="span2">Los</div>
				</th>
			</tr>
					<?php # FB::log($all_user); ?>
		</thead>
		<tbody>
			<?php foreach ($all_user as $user) : ?>
			<tr>
				<td id="content_userrow">
					<?php echo form_open('admin/changeroles_user/', $data_formopen); ?>
					<?php echo form_hidden('user_id', $user['BenutzerID']); ?>

					<div class="span2"><?php echo $user['LoginName']; ?></div>
					<div class="span2"><?php echo $user['Nachname']; ?></div>
					<div class="span2"><?php echo $user['Vorname']; ?></div>
					<div class="span2">
					<?php
						// gib fuenf checkboxen aus
						// pruefe fuer jede checkbox ob ihr index in dem array des aktuellen users uebereinstimmt
						// wenn ja, true ansonsten false

						$sum_roles = count($all_roles);

						// for each possible role
						for ($i=1; $i <= $sum_roles; $i++)
						{
							$tmp = FALSE;
							// look if its in the actual user
							foreach ($user['roles'] as $role)
							{
								// if so, set temp var to true, else leave false
								($i == $role['RolleID']) ? $tmp = TRUE : $tmp = FALSE;
								// if tmp var was set to true, stop looking for this possible role, cause the user has it
								if ($tmp) {
									break;
								}
							}
							// print the role checkbox
							echo form_checkbox('cb_userroles[]', $i, $tmp);
							echo $all_roles[$i];
							echo br();
						}
					?>
					</div> <?php // ROLLEN ?>


					<?php echo "<div class=\"span2\">".form_submit($data_submit, 'Speichern')."</div>"; ?>
					<?php echo form_close(); ?>
					<div class="clearfix"></div>
				</td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

<?php endblock(); ?>
<?php end_extend(); # end extend main template ?>