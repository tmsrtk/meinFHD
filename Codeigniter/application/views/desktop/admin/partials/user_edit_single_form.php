<?php

$data_formopen = array(
	'id' => 'edit_user_row_'.$BenutzerID, // custom id because of validation
);

// prepare the array for the functions that could be selected based on the user role
$data_dropdown = array('Speichern', 'Passwort resetten', 'Als ... anmelden'); // base functions for every user
// if the actual user is a student role id -> 5 provide the function to reset a study plan
if ($role_id == 5){
    $data_dropdown[] = 'Studienplan resetten';
}
$data_dropdown_attrs = 'id="user_function" class="input-xxlarge"';

$submit_data = array(
		'id' 			=> 'save',
		'name'			=> 'los',
		'class'			=> 'btn btn-mini btn-danger'
	);

?>

<div class="row-fluid zebra-striped-div">
<?php
    echo form_open('admin/validate_edit_user_form/', $data_formopen);

    $data['name'] = 'loginname';
	$data['value'] = $LoginName;
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

	$data['name'] = 'lastname';
	$data['value'] = $Nachname;
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

	$data['name'] = 'forename';
	$data['value'] = $Vorname;
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

	$data['name'] = 'email';
	$data['value'] = $Email;
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

    echo '<div class="span2">'.form_dropdown('user_function', $data_dropdown, '0', $data_dropdown_attrs).'</div>';
    echo '<div class="span2">'.form_submit($submit_data, 'Los').'</div>';
    echo form_hidden('user_id', $BenutzerID); ?>

<div class="clearfix"></div>
<?php echo form_close(); ?>
</div>