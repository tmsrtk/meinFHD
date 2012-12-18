<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Persönliche Einstellungen<?php endblock(); ?>

<?php
    # general form setup
    $userroles = $this->user_model->get_all_roles();

    $data_formopen = array(
        'class' => 'form-horizontal',
        'id' => 'change_personal_preferences'
    );

    $data_labelattrs = array(
        'class' => 'control-label'
    );

    $data_loginname = array(
        'class' => 'span3',
        'name' => 'loginname',
        'id' => 'loginname',
        'placeholder' => 'Loginname',
        'value' => $formdata['LoginName'],
    );

    $data_passwort = array(
        'class' => 'span3',
        'name' => 'password',
        'id' => 'password',
        'placeholder' => 'Passwort',
    );

    $data_passwort_repeat = array(
        'class' => 'span3',
        'name' => 'password2',
        'id' => 'password2',
        'placeholder' => 'Passwort bestätigen',
    );

    $data_forename = array(
        'class' => 'span3',
        'name' => 'forename',
        'id' => 'forename',
        'placeholder' => 'Vorname',
        'value' => $formdata['Vorname'],
    );

    $data_name = array(
        'class' => 'span3',
        'name' => 'lastname',
        'id' => 'lastname',
        'placeholder' => 'Nachname',
        'value' => $formdata['Nachname'],
    );

    $data_title = array(
        'class' => 'span3',
        'name' => 'title',
        'id' => 'title',
        'placeholder' => 'Titel',
        'value' => $formdata['Titel'],
    );

    $data_email = array(
        'class' => 'span3',
        'name' => 'email',
        'id' => 'email',
        'placeholder' => 'Emailadresse',
        'value' => $formdata['Email'],
    );

    $data_raum = array(
        'class' => 'span1',
        'name' => 'room',
        'id' => 'room',
        'placeholder' => 'Raum',
        'value' => $formdata['Raum'],
    );

    $data_semesteranfang = array(
        'name' => 'semesteranfang',
        'id' => 'semesteranfang',
    );

    $data_startjahr = array(
        'class' => 'span1',
        'name' => 'startjahr',
        'id' => 'startjahr',
        'placeholder' => 'Startjahr',
        'value' => $formdata['StudienbeginnJahr'],
    );

    $submit_data = array(
        'name'			=> 'speichern',
        'class'			=> 'btn btn-danger'
    );

    # define variables for studienbeginn semester type radio buttons, that should be selected by default (only for students)
    if (in_array(Roles::STUDENT, $userroles)){
        if($userdata['studienbeginn_semestertyp'] == 'WS'){
            $check_ws = TRUE;
            $check_ss = FALSE;
        }
        else{
            $check_ws = FALSE;
            $check_ss = TRUE;
        }
    }
?>

<?php startblock('content'); # content for this view ?>
    <div class="well well-small">
        <div class="row-fluid"><!-- preferences header -->
            <div class="span12">
                <h2>Pers&ouml;nliche Einstellungen</h2>
                <h4><?php if ( in_array(Roles::DOZENT, $userroles)) print $formdata['Titel'].' ' ?><?php print $formdata['Vorname'].' '.$formdata['Nachname'] ?></h4>
                <?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
                <h4><?php print 'Aktuelles Semester: '. $userdata['act_semester']?></h4>
                <?php endif ?>
                <hr/>
            </div>
        </div><!-- preferences header end -->
        <div class="row-fluid"><!-- preferences content -->
            <?php
                echo form_open('einstellungen/validate/', $data_formopen);
                echo validation_errors();
            ?>
            <div class="control-group">
                <?php echo form_label('Loginname', 'loginname', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_loginname); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Passwort', 'password', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_password($data_passwort); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Passwort bestätigen', 'password2', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_password($data_passwort_repeat); ?>
                </div>
            </div>
            <hr/>
            <?php if ( in_array(Roles::DOZENT, $userroles)) : ?>
                <div class="control-group">
                    <?php echo form_label('Titel', 'title', $data_labelattrs); ?>
                    <div class="controls">
                        <?php echo form_input($data_title); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="control-group">
                <?php echo form_label('Vorname', 'forename', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_forename); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Nachname', 'lastname', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_name); ?>
                </div>
            </div>
            <hr/>
            <div class="control-group">
                <?php echo form_label('Emailadresse', 'email', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_email); ?>
                </div>
            </div>
            <?php if ( in_array(Roles::DOZENT, $userroles)) : ?>
            <div class="control-group">
                <?php echo form_label('Raum', 'raum', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_raum); ?>
                </div>
            </div>
            <?php endif ?>
            <hr/>

            <?php if ( in_array(Roles::DOZENT, $userroles)) : ?>
            <div class="control-group">
                <?php echo form_label('Raum', 'raum', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_raum); ?>
                </div>
            </div>
            <?php endif ?>

            <?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
                <div class="control-group">
                    <label class="control-label" for="studiengang">Studiengang</label>
                    <div class="controls">
                        <input type="text" name="studiengang" placeholder="Studiengang" value="<?php print $formdata['StudiengangName'] . ' ' . $formdata['Pruefungsordnung'] ?>" disabled>
                        <a href="<?php echo base_url('einstellungen/studiengang_wechseln') ?>" class="btn btn-warning">Studiengang wechseln</a>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo form_label('Semesteranfang', 'semesteranfang', $data_labelattrs); ?>
                    <div class="controls">
                        <label class="radio">
                            <?php echo form_radio($data_semesteranfang, 'WS', $check_ws); ?>
                            WS
                        </label>
                    </div>
                    <div class="controls">
                        <label class="radio">
                            <?php echo form_radio('semesteranfang', 'SS', $check_ss); ?>
                            SS
                        </label>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo form_label('Starjahr', 'startjahr', $data_labelattrs); ?>
                    <div class="controls">
                        <?php echo form_input($data_startjahr); ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-actions">
                <?php echo form_submit($submit_data, 'Änderungen speichern'); ?>
            </div>
            <?php echo form_close(); ?>
        </div><!-- preferences content end-->

    </div>
<?php endblock(); ?>
<?php end_extend(); ?>