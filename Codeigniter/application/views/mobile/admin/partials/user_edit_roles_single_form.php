<?php
    // general form setup
    $data_formopen = array(
        'id' => 'edit_user_role_row_'.$user_info['BenutzerID'], // custom id because of validation
    );

    // prepare data for the role filter dropdown
    $data_role = array();
    $data_role = $all_roles;

    // add "placeholder" as first element
    array_unshift($data_role, 'Bitte w&auml;hlen');
    $data_role_ext = 'class="role_filter_dd" id="role_filter"';

    $submit_data = array(
        'id' 			=> 'save_user_roles_' . $user_info['BenutzerID'] ,
        'name'			=> 'save_user_roles_btn',
        'class'			=> 'btn btn-mini btn-danger'
    );
?>
<div class="row-fluid zebra-striped-div">
    <?php echo form_open('admin/changeroles_user', $data_formopen); ?>
    <div class="span2"><?php echo $user_info['LoginName']; ?></div>
    <div class="span2"><?php echo $user_info['Nachname']; ?></div>
    <div class="span2"><?php echo $user_info['Vorname']; ?></div>
    <div class="span3"><?php echo $user_info['Email']; ?></div>
    <div class="span2">
        <?php
            // create checkboxes for all user roles
            // and check the box which corresponds to the role_id of the user
            for ($i = 1; $i <= count($all_roles); $i++){

                // save the role of the viewed user in an temp variable to be able to check or uncheck the checkbox the user has got
                $tmp_check = FALSE;
                foreach($user_roles as $single_user_role => $value){
                    if ($value['RolleID'] == $i){
                        $tmp_check = TRUE;
                    }
                }
                // render the checkbox for the viewed role
                echo form_checkbox('cb_userroles[]', $i, $tmp_check);
                echo ' ' . $all_roles[$i];
                echo br();
            }
        ?>
    </div>
    <?php echo form_hidden('user_id', $user_info['BenutzerID']); ?>
    <div class="span1"><?php echo form_submit($submit_data, 'Speichern') ?></div>
    <div class="clearfix"></div>
    <?php echo form_close(); ?>
</div>