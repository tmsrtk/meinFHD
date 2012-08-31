<?php

$data_formopen = array(
	'id' => 'edit_user_row'
	);

$data_dropdown = array('Speichern', 'Passwort resetten', 'Studienplan resetten', 'Als ... anmelden');
$data_dropdown_attrs = 'id="user_function" class="input-xxlarge"';

$submit_data = array(
		'id' 			=> 'save',
		'name'			=> 'los',
		'class'			=> 'btn btn-mini btn-danger'
	);

?>
<tr>
	<td class="content_user_row">

<?php echo form_open('admin/validate_edit_user_form/', $data_formopen); ?>
<?php echo form_hidden('user_id', $BenutzerID); ?>

<?php
	$data['name'] = 'loginname';
	$data['value'] = $LoginName;
	echo $this->load->view('admin/partials/user_single_div', $data, TRUE);

	$data['name'] = 'lastname';
	$data['value'] = $Nachname;
	echo $this->load->view('admin/partials/user_single_div', $data, TRUE);

	$data['name'] = 'forename';
	$data['value'] = $Vorname;
	echo $this->load->view('admin/partials/user_single_div', $data, TRUE);

	$data['name'] = 'email';
	$data['value'] = $Email;
	echo $this->load->view('admin/partials/user_single_div', $data, TRUE);
?>

<?php echo '<div class="span2">'.form_dropdown('user_function', $data_dropdown, '0', $data_dropdown_attrs).'</div>'; ?>
<?php echo '<div class="span1">'.form_submit($submit_data, 'LOS!').'</div>'; ?>
<div class="clearfix"></div>
<?php echo form_close(); ?>

	</td>
</tr>