<h2>Benutzer loeschen</h2>

<table id="user_overview" class="table table-striped table-bordered table-condensed">
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
			$attrs = array('id' => 'delete_user_row');
			echo form_open('admin/delete_user/', $attrs);

			// hidden field, with user_id, needed to save changes
			echo form_hidden('user_id', $zeile['BenutzerID']);
			?>
			<td><?php echo $zeile['LoginName'] ?></td>
			<td><?php echo $zeile['Nachname'] ?></td>
			<td><?php echo $zeile['Vorname'] ?></td>
			<td><?php echo $zeile['Email'] ?></td>
			<?php
			$submit_data = array(
					'id' 			=> 'delete_user_btn',
					'name'			=> 'submit',
					'class'			=> 'btn btn-mini btn-danger'
				);
			?>
			<td><?php echo form_submit($submit_data, 'Loeschen'); ?></td>

			<?php echo form_close(); ?>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>



<script>

	(function() {
		hide_all_submit_buttons();
	})();


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