<?php
	// needet vars
	$data_formopen = array('id' => 'delete_user_row');
	$data_submit = array(
		'id' 			=> 'delete_user_btn',
		'name'			=> 'delete_user_btn',
		'class'			=> 'btn btn-mini btn-danger'
	);
	//--------------------------------------------------------------------------
?>

<h2>Benutzer loeschen</h2>

<!-- <table id="user_overview" class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>Benutzername</th>
			<th>Nachname</th>
			<th>Vorname</th>
			<th>Email</th>
			<th>Loeschen</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($user as $zeile): ?>
		<tr onmouseover="show_delete_button($(this));" onmouseout="hide_delete_button($(this));">
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
</table> -->




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


<script>

	(function() {
		// hide_all_submit_buttons();




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

		$("#content_userrow").on("click", "input#delete_user_btn", function() {
			// console.log(user_function);
			$(this).attr("clicked", "true");
			createDialog('User löschen', 'Soll der User wirklich gelöscht werden?').dialog("open");

			// prevent default submit behaviour
			return false;
		});









	})();






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


</script>