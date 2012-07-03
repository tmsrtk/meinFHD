<?php 

$attrs = array('id' => 'edit_user_row');
$class_dd = 'id="user_function" class="input-xxlarge"';
$dropdown_data = array('Speichern', 'Passwort resetten', 'Stundenplan resetten', 'Als ... anmelden');



$submit_data = array(
		'id' 			=> 'save',
		'name'			=> 'submit',
		'class'			=> 'btn btn-mini btn-danger'
	);

?>



<tr>

	<?php echo form_open('admin/validate_edit_user_form/', $attrs); ?>
	<?php echo form_hidden('user_id', $BenutzerID); ?>

	<?php 

	$data['name'] = 'loginname';
	$data['value'] = $LoginName;
	echo $this->load->view('admin/partials/user_td', $data, TRUE);

	$data['name'] = 'lastname';
	$data['value'] = $Nachname;
	echo $this->load->view('admin/partials/user_td', $data, TRUE);

	$data['name'] = 'forename';
	$data['value'] = $Vorname;
	echo $this->load->view('admin/partials/user_td', $data, TRUE);

	$data['name'] = 'email';
	$data['value'] = $Email;
	echo $this->load->view('admin/partials/user_td', $data, TRUE);

	?>

	<?php echo "<td>".form_dropdown('user_function', $dropdown_data, '0', $class_dd)."</td>"; ?>

	<?php echo "<td>".form_submit($submit_data, 'LOS!')."</td>"; ?>

	<?php echo form_close(); ?>

</tr>