<?php

$data_formopen = array(
	'id' => 'edit_user_row_'.$user_info['BenutzerID'], // custom id because of validation
);

// prepare the array for the functions that could be selected based on the user role
//$data_dropdown = array('Speichern', 'Passwort resetten', 'Als ... anmelden'); // base functions for every user
$data_dropdown = array();
$data_dropdown[0] = 'Speichern';
$data_dropdown[1] = 'Passwort resetten';
$data_dropdown[3] = 'Als ... anmelden';
$data_dropdown[4] = 'Benutzer l&ouml;schen'; // at least add the delete function

// iterate through all user roles and check if the user is a student (role id 5) -> provide him the function to reset a study plan
foreach($user_roles as $single_user_role){
    if (in_array(5, $single_user_role)){
        $data_dropdown[2] = 'Studienplan resetten';
    }
}

$data_dropdown_attrs = 'id="user_function_' .$user_info['BenutzerID'] .'" class="input-xxlarge"';

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
	$data['value'] = $user_info['LoginName'];
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

	$data['name'] = 'lastname';
	$data['value'] = $user_info['Nachname'];
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

	$data['name'] = 'forename';
	$data['value'] = $user_info['Vorname'];
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

	$data['name'] = 'email';
	$data['value'] = $user_info['Email'];
	echo $this->load->view('admin/partials/user_edit_single_div', $data, TRUE);

    echo '<div class="span2">'.form_dropdown('user_function', $data_dropdown, '0', $data_dropdown_attrs).'</div>';
    echo '<div class="span2">'.form_submit($submit_data, 'OK').'</div>';
    echo form_hidden('user_id', $user_info['BenutzerID']); ?>

<div class="clearfix"></div>
<?php echo form_close(); ?>
</div>