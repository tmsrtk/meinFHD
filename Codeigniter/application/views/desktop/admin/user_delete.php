<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer löschen<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php
	// needet vars
	$data_formopen = array('id' => 'delete_user_row');
	$data_submit = array(
		'id'			=> 'delete_user_btn',
		'name'			=> 'delete_user_btn',
		'class'			=> 'btn btn-mini btn-danger delete_user_btn'
	);
	//--------------------------------------------------------------------------
?>
<div class="row-fluid">
	<h2>Benutzer löschen</h2>
</div>
<hr>
<!-- <div class="row-fluid">
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
-->
<div class="row-fluid">
	<table id="user_overview" class="table table-striped">
		<thead>
			<tr>
				<th>
					<div class="span2">Loginname</div>
					<div class="span2">Nachname</div>
					<div class="span2">Vorname</div>
					<div class="span2">E-Mail</div>
					<div class="span2">Los</div>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($user as $zeile) : ?>
			<tr>
				<td id="content_userrow">
					<?php echo form_open('admin/delete_user/', $data_formopen); ?>
					<?php echo form_hidden('user_id', $zeile['BenutzerID']); ?>

					<div class="span2"><?php echo $zeile['LoginName']; ?></div>
					<div class="span2"><?php echo $zeile['Nachname']; ?></div>
					<div class="span2"><?php echo $zeile['Vorname']; ?></div>
					<div class="span2"><?php echo $zeile['Email']; ?></div>

					<?php echo "<div class=\"span2\">".form_submit($data_submit, 'Loeschen')."</div>"; ?>
					<div class="clearfix"></div>
					<?php echo form_close(); ?>
				<td>
			<tr>
			<?php endforeach ?>
		</tbody>
	</table>

<!--
	<div class="row">
		<div class="span2"><strong>Loginname</strong></div>
		<div class="span2"><strong>Nachname</strong></div>
		<div class="span2"><strong>Vorname</strong></div>
		<div class="span2"><strong>E-Mail</strong></div>
		<div class="span2"><strong>Los</strong></div>
	</div>

	<div class="row" id="content_userrow">
	<?php foreach ($user as $zeile) : ?>
		<?php echo form_open('admin/delete_user/', $data_formopen); ?>
		<?php echo form_hidden('user_id', $zeile['BenutzerID']); ?>

		<div class="span2"><?php echo $zeile['LoginName']; ?></div>
		<div class="span2"><?php echo $zeile['Nachname']; ?></div>
		<div class="span2"><?php echo $zeile['Vorname']; ?></div>
		<div class="span2"><?php echo $zeile['Email']; ?></div>

		<?php echo "<div class=\"span2\">".form_submit($data_submit, 'Loeschen')."</div>"; ?>
		<div class="clearfix"></div>
		<?php echo form_close(); ?>
	<?php endforeach ?>
	</div>
-->
</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
		// prompt dialogs
		/**
		 * 
		 */
		function createDialog(title, text) {
			var $mydialog = $('<div id="dialog-confirm" title="'+title+'"></div>')
						.html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+text+'</p>')
						.dialog({
							autoOpen: false,
							resizable: false,
							height: 200,
							modal: true,
							buttons: {
								OK: function() {
									$("input[type=submit][clicked=true]").parents("form#delete_user_row").submit();
									$("#content_userrow input#delete_user_btn").removeAttr("clicked");
									$( this ).dialog( "close" );
								},
								Abbrechen: function() {
									$("#content_userrow input#delete_user_btn").removeAttr("clicked");
									$( this ).dialog( "close" );
								}
							}
						});
			return $mydialog;
		}

		$("td#content_userrow").on("click", "input#delete_user_btn", function() {
			// console.log(user_function);
			$(this).attr("clicked", "true");
			createDialog('User löschen', 'Soll der User wirklich gelöscht werden?').dialog("open");

			// prevent default submit behaviour
			return false;
		});




		/* helper functions */
		function hide_all_submit_buttons() {
			$("input#delete_user_btn").hide();
		}

		function show_delete_button(c) {
				c.find("#delete_user_btn").show();
		}
			function hide_delete_button(c) {
				c.find("#delete_user_btn").hide();
		}
<?php endblock(); ?>
<?php end_extend(); # end extend main template ?>