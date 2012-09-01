<?php extend('sso/index.php'); ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - Account verkn&uuml;pfen oder erstellen<?php endblock();?>

<?php
# general form setup: login-form
$linkAccountFormAttributes = array(
    'class' 		=> 'login-form'
);


// prepare attributes for username input field
$usernameInputAttributes = array(
    'name'			=> 'username',
    'id'			=> 'username',
    'placeholder'	=> 'Benutzername',
    'class'			=> 'input-xxlarge'
);

// prepare attributes for username input field
$passwordInputAttributes = array(
    'name'			=> 'password',
    'id'			=> 'password',
    'placeholder'	=> 'Passwort',
    'class'			=> 'input-xxlarge'
);

// prepare attributes for link account button
$submitLinkAccountButtonAttributes = array(
    'name'			=> 'submit',
    'type'			=> 'submit',
    'id'			=> 'submitLoginForm',
    'content'			=> 'Verkn&uuml;pfen',
    'class'			=> 'btn btn-primary btn-medium pull-right'
);

# general form setup: create account form
$createAccountFormAttributes = array('class' => 'form-horizontal', 'id' => 'create_account');

$createAccountFormDepartmentDropdown[0] = 'Bitte ausw&auml;hlen';

# construct the elements for the department dropdown
foreach ($all_departments as $dep) {
    $createAccountFormDepartmentDropdown[$dep['FachbereichID']] = 'FB ' . $dep['FachbereichID']. ' ' . $dep['FachbereichName'];
}

# setup js for dropdown
$departmentDropdownParams = 'class="input-small" id="department"';

$createAccountFormStdgDropdown[0] = 'Bitte ausw&auml;hlen';

$stdgDropdownParams = 'class="input-small" id="studiengang"';

# construct the elements for the stdgaenge dropdown
foreach ($all_stdgnge as $stdg) {
    $createAccountFormStdgDropdown[$stdg->StudiengangID] = $stdg->StudiengangName . ' (PO ' . $stdg->Pruefungsordnung . ')';
}

$createAccountFormForename = array(
    'class' => 'span4',
    'name' => 'forename',
    'id' => 'forename',
    'placeholder' => 'Vorname',
    'value' => set_value('forename')
);
$createAccountFormName = array(
    'class' => 'span4',
    'name' => 'lastname',
    'id' => 'lastname',
    'placeholder' => 'Nachname',
    'value' => set_value('lastname')
);

$createAccountFormEmail = array(
    'class' => 'span4',
    'name' => 'email',
    'id' => 'email',
    'placeholder' => 'E-Mail',
    'value' => set_value('email')
);

$createAccountFormYear = array(
    'class' => 'span2',
    'name' => 'startjahr',
    'placeholder' => 'Startjahr',
    'value' => set_value('startjahr')
);

$class_dd = 'class="studiengang_dd"';

$creatAccountFormMatrikelnummer = array(
    'class' => 'span2',
    'name' => 'matrikelnummer',
    'placeholder' => 'Matrikelnummer',
    'value' => set_value('matrikelnummer')
);
$createAccountFormSubmit = array(
    'name'			=> 'los',
    'class'			=> 'btn btn-danger'
);

$createAccountRoleRadio = array(
    'name' => 'role',
    'id' => 'role',
);

$createAccountErstsemestlerCheck = array(
    'name' => 'erstsemestler_cb',
    'id' => 'erstsemestler_cb'
);

$createAccountLabelAttributes = array(
    'class' => 'control-label'
);

$createAccountStartYear = array(
    'class' => 'span2',
    'name' => 'startjahr',
    'id' => 'startjahr',
    'placeholder' => 'Startjahr',
    'value' => set_value('startjahr')
);

$createAccountStartSemesterRadio = array(
    'name' => 'semesteranfang',
    'id' => 'semesteranfang'
);

$createAccountFormMatrikelnummer = array(
    'class' => 'span3',
    'name' => 'matrikelnummer',
    'id' => 'matrikelnummer',
    'placeholder' => 'Matrikelnummer',
    'value' => set_value('matrikelnummer')
);

// prepare attributes for link account button
$submitCreateAccountButton = array(
    'name'			=> 'submit',
    'type'			=> 'submit',
    'id'			=> 'submitCreateAccount',
    'content'			=> 'Account erstellen',
    'class'			=> 'btn btn-primary btn-danger'
);
?>

<?php startblock('content'); # content for this view ?>
                <div class="well well-small clearfix">
                    <h2>Bitte Account verkn&uuml;pfen</h2>
                    <hr>
                    <p>
                        Du hast dich bei meinFHD &uuml;ber den zentralen Shibboleth-Server der Fachhochschule D&uuml;sseldorf angemeldet.
                    </p>
                    <p>
                        F&uuml;r den Zugang zu meinFHD ben&ouml;tigst Du auch einen Benutzeraccount lokal in meinFHD. Diese beiden Accounts m&uuml;ssen miteinander
                        verkn&uuml;pft werden, um Deine Daten konsistent zu halten.
                    </p>
                    <p>
                        Wenn Du bereits einen lokalen Account besitzt, kannst du diesen jetzt dauerhaft mit deinem zentralen Account verkn&uuml;pfen, um jederzeit Zugriff
                        auf meinFHD zu erhalten. Gib daf&uuml;r <a id="openLinkingForm" href="#">hier</a> deine lokale Zugangsdaten an und best&auml;stige diese mit dem Button verkn&uuml;pfen.
                    </p>
                    <p>
                        Wenn Du noch keinen lokalen meinFHD-Account hast, kannst Du ihn <a id="openCreateForm" href="#">hier</a> erstellen und sofort mit dem zentralen Account verkn&uuml;pfen.
                    </p>
                    <p>
                        Solltest Du R&uuml;ckfragen oder Probleme haben kontaktiere bitte das Support-Team unter <a href="mailto:meinfhd.medien@fh-duesseldorf.de">meinfhd.medien@fh-duesseldorf.de</a>.
                    </p>
                </div>

                <!-- account linking accordion -->
                <div id="accordion-app" class="accordion">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#request-accountlinking">Account verkn&uuml;pfen<i class="icon-plus pull-right"></i></h4>
                        </div>
                        <div id="request-accountlinking" class="accordion-body collapse">
                            <div class="accordion-inner">
                                <div class="row-fluid">
                                    <p>Bitte trage deinen Loginnamen und dein Passwort ein. Anschließend wird der angegebene lokale Zugang mit deiner globalen Identit&auml;t verkn&uuml;pft und du wirst eingeloggt.</p>
                                    <?php echo form_open('sso/link_account', $linkAccountFormAttributes); // create opening tag of login form ?>
                                        <?php echo form_fieldset(); // wrap elements ina a fieldset due to semantics ?>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="icon-user"></i></span><?php echo form_input($usernameInputAttributes); // render the username field ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="icon-lock"></i></span><?php echo form_password($passwordInputAttributes); // render the password field ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <?php echo form_button($submitLinkAccountButtonAttributes); // render the submit button ?>
                                        <?php echo form_fieldset_close(); // close the fieldset ?>
                                    <?php echo form_close(); // close the whole login form ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end account linking accordion -->

                    <!-- create account accordion -->
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#request-createaccount">Account erstellen<i class="icon-plus pull-right"></i></h4>
                        </div>
                        <div id="request-createaccount" class="accordion-body collapse">
                           <div class="accordion-inner">
                                <div class="row-fluid">
                                    <p>
                                        Du hast noch keinen Account? Dann kannst du hier einen anlegen:
                                    </p>
                                    <?php echo form_open('sso/validate_create_account_form/', $createAccountFormAttributes);?>
                                    <div id="additional-info" class="alert">
                                        Bitte gib den Fachbereich an, indem du studierst, damit wir feststellen können, ob ein Zugang zum meinFHD für dich sinnvoll ist.
                                    </div>

                                    <div class="control-group">
                                        <?php echo form_label('Fachbereich', 'department', $createAccountLabelAttributes); ?>
                                        <div class="controls docs-input-sizes">
                                            <?php echo form_dropdown('department', $createAccountFormDepartmentDropdown, '', $departmentDropdownParams); ?>
                                        </div>
                                    </div>
                                    <?php echo validation_errors() ?>
                                    <!-- ggf. ab hier erst anzeigen, wenn der richtige Fachbereich ausgewaehlt wurde -->
                                    <div id="formData">
                                        <div id="additional-info2" class="alert">
                                            <!-- input infos for user -->
                                        </div>
                                        <div class="control-group">
                                            <?php echo form_label('Ich bin ein', 'role', $createAccountLabelAttributes); ?>
                                            <div class="controls docs-input-sizes">
                                                <label class="radio">
                                                    <?php echo form_radio($createAccountRoleRadio, '5', TRUE) ?>
                                                    Student
                                                </label>
                                                <label class="radio">
                                                    <?php echo form_radio('role', '2', FALSE) ?>
                                                    Dozent
                                                </label>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <?php echo form_label('Vorname', 'forename', $createAccountLabelAttributes); ?>
                                            <div class="controls docs-input-sizes">
                                                <?php echo form_input($createAccountFormForename); ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <?php echo form_label('Nachname', 'lastname', $createAccountLabelAttributes); ?>
                                            <div class="controls docs-input-sizes">
                                                <?php echo form_input($createAccountFormName); ?>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <?php echo form_label('E-Mail', 'email', $createAccountLabelAttributes); ?>
                                            <div class="controls docs-input-sizes">
                                                <?php echo form_input($createAccountFormEmail); ?>
                                            </div>
                                        </div>
                                        <hr/>

                                        <!-- only needed input for students -->
                                        <div id="studentdata">
                                            <div class="control-group">
                                                <?php echo form_label('Ich bin Erstsemestler!', 'erstsemestler_cb', $createAccountLabelAttributes); ?>
                                                <div class="controls docs-input-sizes">
                                                    <?php echo form_checkbox($createAccountErstsemestlerCheck, 'accept', FALSE) ?>
                                                </div>
                                            </div>
                                            <!-- only needed inputs for erstsemestler-->
                                            <div id="erstsemestler">
                                                <div class="control-group">
                                                    <?php echo form_label('Jahr', 'startjahr', $createAccountLabelAttributes); ?>
                                                    <div class="controls docs-input-sizes">
                                                        <?php echo form_input($createAccountStartYear); ?>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <?php echo form_label('Semesteranfang', 'semesteranfang', $createAccountLabelAttributes); ?>
                                                    <div class="controls docs-input-sizes">
                                                        <label class="radio">
                                                            <?php echo form_radio($createAccountStartSemesterRadio, 'WiSe', TRUE); ?>
                                                            WiSe
                                                        </label>
                                                        <label class="radio">
                                                            <?php echo form_radio('semesteranfang', 'SoSe', FALSE); ?>
                                                            SoSe
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <?php echo form_label('Studiengang', 'studiengang', $createAccountLabelAttributes); ?>
                                                <div class="controls docs-input-sizes">
                                                    <?php echo form_dropdown('studiengang', $createAccountFormStdgDropdown, '', $stdgDropdownParams); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <?php echo form_label('Matrikelnummer', 'matrikelnummer', $createAccountLabelAttributes); ?>
                                                <div class="controls docs-input-sizes">
                                                    <?php echo form_input($createAccountFormMatrikelnummer) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php echo form_button($submitCreateAccountButton); // render the submit button ?>
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end create account accordion -->
                </div>

                <!-- content area for modals -->
                <div id="modelarea"></div>

<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

    function createModalDialog(title, text) {
        var $myModalDialog = $('<div class="modal" id="myModal"></div>')
                    .html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
                    .append('<div class="modal-body"><p>'+text+'</p></div>')
                    .append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">OK</a></div>');

        return $myModalDialog;
    }

    /* document ready function used for init stuff */
    $(document).ready(function() {
        var department = $("department option:selected").val();
        toggle_formData(department);

        // validation errors are available -> select department 5 and show the account creation accordion
        if($("div#createAccountErrors").length > 0 ) {
            $("#department").val(5);
            toggle_formData(5); // show the rest of the form
            $('#request-createaccount').collapse(); // open the create account accordion
        }
    });


    // get the selected department of the student and check if he is in the department 5 (media)
    $('#department').change( function() {
        var department = $("#department option:selected").val();
        toggle_formData(department);

        if (department != '5' && department != 0) { // user is not member of fb5 -> show him a message
            var myDialog = createModalDialog('Ungültiger Fachbereich', 'Sorry, aber das meinFHD ist aktuell nur für Angehörige des Fachbereichs Medien verfügbar. Angehörige anderer Fachbereiche haben keinen Nutzen von diesem Dienst');
            $("#modelarea").html(myDialog);
            $('#myModal').modal({
                keyboard: false
            }).on('hide', function () {

            }).modal('show');
        }
    })

    // onchange for radiobuttons

    // onchange for radiobutton role
    $("input[name='role']").change(function() {
        toggle_studentdata($(this));
    });

    // onchange for radiobutton erstsemestler
    $("input[name='erstsemestler_cb']").change(function() {
        toggle_erstsemestlerdata($(this));
    });

    /* toggles additional student data when user selected 'student' from the dropdown */
    function toggle_studentdata(c) {
        var additional_student_data = $("div#studentdata");

        // c jQuery object of toggle button
        if (c.val() === '5') {
            // show additional student da
            additional_student_data.slideDown('slow');
            AdditionalInfo.changeToStudent();
        }
        else {
            additional_student_data.slideUp('slow');
            AdditionalInfo.changeToDozent();
        }
    }

    /* toggles additional semester data when user checked 'Erstsemester' */
    function toggle_erstsemestlerdata(c) {
        var erstsemestler_data = $("div#erstsemestler");

        if (c.attr('checked')) {
            erstsemestler_data.slideUp('slow');
        }
        else {
            erstsemestler_data.slideDown('slow');
        }
    }

    /* toggles and shows the whole form to fill, if the user choosed the right department */
    function toggle_formData(dep) {
        var form_data = $("div#formData");

        if(dep == '5') { // department media is checked
            form_data.slideDown('slow');
        }
        else {
            form_data.slideUp('slow');
        }
    }

    /* preparing and showing input info for the user */
    var AdditionalInfo = {
        init : function(config) {
            this.config = config;
            this.changeToStudent();
        },
        changeToStudent : function() {
            // this.config.additionalInfoContent = this.studentenInfo;
            this.config.additionalInfoContent.html(this.studentenInfo);
        },
        changeToDozent : function() {
            this.config.additionalInfoContent.html(this.dozentenInfo);
        },
        studentenInfo : "Gib bitte die folgenden Daten an, damit wir feststellen können, dass Du ein Student an diesem Fachbereich bist. Die Emailadresse wird für die Kommunikation mit dem meinFHD, den Dozenten und Studierenden verwendet.",
        dozentenInfo : "Geben Sie bitte hier Ihren vollen Namen an, da dieser für die Kommunikation mit den Studierenden gebraucht wird. Die Emailadresse wird für die Kommunikation mit dem meinFHD und den Studierenden verwendet. "
    };

    AdditionalInfo.init({
        additionalInfoContent : $("div#additional-info2")
    });

    // open up account linking accordion
    $("#openLinkingForm").click(function() {
        $("#request-accountlinking").collapse('show');
    });

    // open up create account accordion
    $("#openCreateForm").click(function() {
        $("#request-createaccount").collapse('show');
    });
<?php endblock(); ?>

<?php end_extend(); ?>