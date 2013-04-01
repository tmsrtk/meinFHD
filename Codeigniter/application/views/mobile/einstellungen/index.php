<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Pers&ouml;nliche Einstellungen<?php endblock(); ?>

<?php
# general form setup
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
    'placeholder' => 'Passwort best&auml;tigen',
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
    'class' => 'span2',
    'name' => 'room',
    'id' => 'room',
    'placeholder' => 'Raum',
    'value' => $formdata['Raum'],
);



// declare student specific data only when the authenticated user is a student
if(in_array(Roles::STUDENT, $userroles)){

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

    $data_private_correspondence = array(
        'name' => 'EmailDarfGezeigtWerden',
        'id' => 'EmailDarfGezeigtWerden',
    );

    $data_studiengang = array(
        'name' => 'studiengang',
        'id' => 'studiengang',
        'disabled' => 'disabled',
        'value' => $formdata['StudiengangName'] . ' ' . $formdata['Pruefungsordnung'],
    );

    $change_degree_program_data = array(
        'name' => 'change_degree_program',
        'id' => 'btn_change_degree_program',
        'class' => 'btn btn-warning',
        'content' => 'Studiengang wechseln',
    );

    // define variables for studienbeginn semester type radio buttons, that should be selected by default (only for students)
    if($userdata['studienbeginn_semestertyp'] == 'WS'){
        $check_ws = TRUE;
        $check_ss = FALSE;
    }
    else{
        $check_ws = FALSE;
        $check_ss = TRUE;
    }

    // define variable to present the saved status for private mail correspondence
    if($formdata['EmailDarfGezeigtWerden'] == 1){
        $show_email_cb = TRUE;
    }
    else {
        $show_email_cb = FALSE;
    }
}
?>

<?php startblock('content'); # content for this view ?>
<div class="well well-small">
    <div class="row-fluid"><!-- preferences header -->
        <div class="span12">
            <h2>Pers&ouml;nliche Einstellungen</h2>
            <h4><?php if ( in_array(Roles::DOZENT, $userroles) || in_array(Roles::BETREUER, $userroles)) print $formdata['Titel'].' ' ?><?php print $formdata['Vorname'].' '.$formdata['Nachname'] ?></h4>
            <?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
            <h4><?php print 'Matrikelnummer: '. $formdata['Matrikelnummer']; ?></h4>
            <h4><?php print 'Fachsemester: '. $userdata['act_semester']; ?></h4>
            <h4><?php print 'Studiengang: '. $formdata['StudiengangAbkuerzung'].' '.$formdata['Pruefungsordnung'] ?></h4>
            <?php endif ?>
            <hr/>
        </div>
    </div><!-- preferences header end -->
    <div class="row-fluid"><!-- preferences content -->
        <?php
        echo form_open('einstellungen/validate_edits/', $data_formopen);
        echo validation_errors();
        ?>
        <h4>Login-Details</h4>
        <br/>
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
        <h4>Kontaktinformationen</h4>
        <br/>
        <?php if ( in_array(Roles::DOZENT, $userroles) || in_array(Roles::BETREUER, $userroles)) : ?>
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
        <div class="control-group">
            <?php echo form_label('Emailadresse', 'email', $data_labelattrs); ?>
            <div class="controls">
                <?php echo form_input($data_email); ?>
            </div>
        </div>

        <?php if (in_array(Roles::STUDENT, $userroles)) : ?>
            <div class="control-group">
                <?php echo form_label('Dozenten d&uuml;rfen mich unter dieser Adresse auch pers&ouml;nlich erreichen', 'EmailDarfGezeigtWerden', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_checkbox($data_private_correspondence, 1, $show_email_cb); ?>
                </div>
            </div>
            <?php endif ?>
        <?php if ( in_array(Roles::DOZENT, $userroles) || in_array(Roles::BETREUER, $userroles)) : ?>
            <div class="control-group">
                <?php echo form_label('Raum', 'raum', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_raum); ?>
                </div>
            </div>
            <?php endif ?>
        <hr/>
        <?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
            <h4>Studiengangsinformationen</h4>
            <br/>
            <div class="control-group">
                <?php echo form_label('Studiengang', 'studiengang', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_studiengang); ?>&nbsp;&nbsp;
                    <?php echo form_button($change_degree_program_data); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Semesteranfang', 'semesteranfang', $data_labelattrs); ?>
                <div class="controls">
                    <label class="radio">
                        <?php echo form_radio($data_semesteranfang, 'WS', $check_ws); ?>
                        Wintersemester
                    </label>
                </div>
                <div class="controls">
                    <label class="radio">
                        <?php echo form_radio('semesteranfang', 'SS', $check_ss); ?>
                        Sommersemester
                    </label>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Startjahr', 'startjahr', $data_labelattrs); ?>
                <div class="controls">
                    <?php echo form_input($data_startjahr); ?>
                </div>
            </div>
            <?php endif ?>

        <div class="form-actions">
            <input type="submit" name="speichern" class="btn btn-danger" value="&Auml;nderungen speichern" />
        </div>
        <?php echo form_close(); ?>
    </div><!-- preferences content end-->
</div>

<?php endblock(); ?>

<?php startblock('postCodeContent'); ?>
<div id="modalcontent"></div><!-- place for modals after content -->
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
    // register a click on the change degree programm - button
    $("#btn_change_degree_program").click(function(){
        // load the change degree program view via ajax and open up the modal
        var request_url = "<?php print base_url('einstellungen/ajax_load_change_degree_program_view/'); ?>"

        $.ajax({
            url: request_url,
            type: 'POST',
            success: function(success_data){
                $('#modalcontent').html(success_data);
                $('#change_degree_program_modal').modal('show');
            }
        });
    });
<?php endblock(); ?>

<?php end_extend(); ?>