<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
<div class="span3"></div>
<div class="span6 well well-small">
<?php endblock(); ?>

<?php
	// general form setup before content
	$data_formopen = array(
        'class' => 'form-horizontal',
        'id' => 'create_user'
    );

    $data_roles = 'class="input-xxlarge" id="role"';

    $data_loginname = array(
			'class' => 'input-xxlarge',
			'name' => 'loginname',
            'id' => 'loginname',
			'placeholder' => 'Loginname',
			'value' => set_value('loginname')
    );

	$data_email = array(
			'class' => 'input-xxlarge',
			'name' => 'email',
            'id' => 'email',
			'placeholder' => 'E-Mail',
			'value' => set_value('email')
    );

	$data_forename = array(
            'class' => 'input-xxlarge',
            'name' => 'forename',
            'id' => 'forename',
            'placeholder' => 'Vorname',
            'value' => set_value('forename')
    );

	$data_lastname = array(
			'class' => 'input-xxlarge',
			'name' => 'lastname',
            'id' => 'lastname',
			'placeholder' => 'Nachname',
			'value' => set_value('lastname')
    );

    $submit_data = array(
			'name'			=> 'submit',
			'class'			=> 'btn btn-danger'
    );

    $data_labelattrs = array(
        'class' => 'control-label'
    );
?>

<?php startblock('content'); # additional markup before content ?>
    <div class="row-fluid">
        <h2>Benutzer anlegen</h2>
        <p>
            Zum Anlegen eines neuen Benutzers bitte alle mit einem Stern (*) gekennzeichneten Felder sowie die
            Benutzerrolle ausw&auml;hlen.<br/>
            Die weiteren, rollenspezifischen Informationen m&uuml;ssen vom erstellten Benutzer in seinen
            pers&ouml;nlichen Einstellungen sp&auml;ter erg&auml;nzt werden.
        </p>
        <?php echo validation_errors(); // validation errors or empty string otherwise ?>
    </div>
    <hr/>

    <?php echo form_open('admin/validate_create_user_mask/', $data_formopen); ?>
        <div class="control-group">
            <?php echo form_label('Rolle*', 'role', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_dropdown('role', $all_roles, set_value('role'), $data_roles); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Loginname*', 'loginname', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_loginname); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('E-Mail*', 'email', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_email); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Vorname', 'forename', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_forename); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Nachname', 'lastname', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_lastname); ?>
            </div>
        </div>
        <hr/>
        <div class="control-group">
            <div class="controls docs-input-sizes">
                <?php echo form_submit($submit_data, 'Neuen Benutzer anlegen'); ?>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
<div class="span3"></div>
<?php endblock(); ?>

<?php end_extend(); ?>