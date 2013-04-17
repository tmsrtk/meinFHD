<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Rechteverwaltung<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php

// prepare attributes for submit button 
$submitButtonAttributes = array(
	'name'			=> 'save_role_changes',
	'type'			=> 'submit',
    'content'       => '&Auml;nderungen speichern',
	'class'			=> 'btn btn-primary span12'
);

?>
<h1>Rechteverwaltung</h1>
<p>
    Hier k&ouml;nnen den verschiedenen Rollen die einzelnen Rechte durch an- bzw.
    abw&auml;hlen der Checkboxen zugeteilt werden. <strong>Wichtig:</strong> Die
    vorgenommenen &Auml;nderungen m&uuml;ssen anschlie&szlig;end gespeichert werden. Ansonsten gehen diese verloren.
</p>
<?php echo form_open('admin/save_permissions'); ?>
<div class="row-fluid">
    <div class="span8"></div>
    <div class="span4"><?php echo form_button($submitButtonAttributes); ?></div>
</div>
<hr/>
<div class="row-fluid">
        <div class="span4"><strong>Berechtigung</strong></div>
        <?php foreach($roles as $single_role): # print out the different, existing user roles ?>
        <div class="span1"><strong><?php echo $single_role->bezeichnung; ?></strong></div>
        <?php endforeach; ?>
    </div>
    <?php
        // shift the permission names to another array
        foreach($permissions as $s_perm){
            $permission_names[$s_perm->BerechtigungID] = $s_perm->bezeichnung;
        }

        $counter = 0; //
        $permission_id = '';
        // iterate through all permissions display them and their assignment to the particular role
        foreach ($tableviewData as $v) {

            if($counter % ($roleCounter+1) === 0) {
                echo '<div class="row-fluid zebra-striped-div">';
                $permission_id = $v;
                echo '<div class="span4">';
                echo $permission_names[$v];
                echo '</div>';
            }

            else {
                if ($v !== 'x') { // if the viewed permissions is assigned to the viewed role -> activate the checkbox
                    echo '<div class="span1">';
                    echo form_checkbox($permission_id.$counter, 'accept', TRUE);
                    echo '</div>';
                }
                else { // the permission is not assigned to the viewed role -> deactivate the checkbox
                    echo '<div class="span1">';
                    echo form_checkbox($permission_id.$counter, 'accept', FALSE);
                    echo '</div>';
                }
            }
            $counter++;

            // if all roles have been viewed for the actual permission -> reset the counter
            if($counter == $roleCounter+1) {
                echo '</div>';
                $counter = 0;
            }
        }
    ?>
<hr/>
<div class="row-fluid">
    <div class="span8"></div>
    <div class="span4"><?php echo form_button($submitButtonAttributes); ?></div>
</div>
<?php echo form_close();?>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>
