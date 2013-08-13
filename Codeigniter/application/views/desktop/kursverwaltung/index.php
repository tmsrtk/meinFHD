<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Kursverwaltung<?php endblock(); ?>

<?php startblock('preCodeContent') ?>
    <?php
        // prepare array with all course ids for json
        $course_ids_jq = array();
        // associative arrays can be handled easier in jquery
        $course_ids = array_keys($course_details);
        // contains both kursids and spkursids
        foreach ($course_ids as $id) {
            $course_ids_jq['KursID'.$id] = $id;
        }

        # definition of general form elements
        $data_labelattrs = array(
            'class' => 'control-label'
        );

        $email_form_open_attrs = array(
            'id' => 'send-email-form'
        );

        $email_topic_input = array(
            'class' => 'input-xlarge',
            'name' => 'email_topic',
            'id' => 'email_topic',
            'placeholder' => 'Betreff',
        );

        $email_message_textarea = array(
            'class' => 'input-xlarge',
            'name' => 'email_body',
            'id' => 'email_message',
            'placeholder' => 'Nachricht',
            'rows'  => '5',
        );

    ?>
<?php endblock(); ?>

<?php startblock('content'); ?>
    <div class="span12 well well-small">
        <div class="row-fluid">
            <h2>Meine Kurse</h2>
        </div>
        <hr/>
        <div class="row-fluid">
            <!-- tabs for courses -->
            <ul class="nav nav-tabs nav-tab-bigger-font" id="course-details-navi">
                <?php
                    // print out an tab for each course the user is assigned to
                    foreach ($course_names_ids as $key => $value) {
                        echo '<li id="course-tab-'.$key.'">';
                        echo '<a href="#tab-panel-'.$key.'" data-toggle="tab">'.$value->kurs_kurz.'</a>';
                        echo '</li>';
                    }

                ?>
            </ul>

            <!-- main content of each tab -->
            <div class="tab-content">

                <?php
                    // print one tab-pane for each course
                    // therefore run through $course_names_ids
                    // array with $key = $course_id and
                    // value as object containing ->kurs_kurz and ->Beschreibung
                    foreach($course_names_ids as $c_id => $value):
                        echo '<div class="tab-pane" id="tab-panel-'.$c_id.'"> ';

                        // prepare data for checkboxes, buttons, labels, ...
                        // has to be generate each time because of uniqueness of ids!
                        // >> appending course_id

                        // checkbox data -
                        $cb_data = array(
                            'name' => '',
                            'class' => 'email-checkbox',
                            'id' => 'email-checkbox-all-id-'.$c_id,
                            'value' => '',
                            'checked' => 'checked',
                        );

                        $submit_data_send_email = array(
                            'name' => 'send-email-button',
                            'content' => 'Email senden',
                            'id' => 'send-email-button-'.$c_id,
                            'class' => 'btn btn-warning'
                        );

                        $submit_data_save_all = array(
                            'name' => $c_id,
                            'value' => 'Kursinformationen speichern',
                            'id' => 'save-all'.$c_id,
                            'class' => 'btn btn-warning'
                        );

                        $overall_label_attrs = array(
                            'id' => 'course-mgt-label-overall-'.$c_id,
                            'class' => 'label label-info',
                        );
                ?>


                <div class="span1"></div>
                <div class="span9"><h3>Emailversand:</h3></div>
                <div class="span2"></div>

                <div class="clearfix">
                    <div class="span1 bold" style="text-align: center;">Email an</div>
                    <div class="span10"></div>
                    <div class="span1"></div>
                </div>

                <!-- print email-row -->
                <?php
                    echo '<div id="staff-send-email-'. $c_id . '" class="clearfix">';
                ?>
                    <?php echo form_open(''); ?>
                    <div class="span1" style="text-align: center">
                        <?php echo form_checkbox($cb_data); ?>
                    </div>
                    <div class="span5">
                        <?php echo form_label('Email senden an alle Personen und Kursteilnehmer', '', $overall_label_attrs); ?>
                    </div>
                    <div class="span2">
                        <?php
                            echo form_button($submit_data_send_email);
                        ?>
                    </div>
                    <?php
                        echo form_close();
                    ?>
                </div>

                <div class="clearfix">
                   <div class="span1" style="text-align: center;"><i class="icon-arrow-down icon-arrow-down-zoom"></i></div>
                   <div class="span10"></div>
                   <div class="span1"></div>
                </div>
                <hr/>

                <!-- staff table -->
                <?php
                    echo '<div>';
                    echo $staff[$c_id];
                    echo '</div>';
                    echo '<hr/>';
                ?>



                <!-- course details -->
                <?php
                    // open form
                    echo form_open('kursverwaltung/save_course_details_all_at_once');
                ?>
                    <div class="row-fluid">
                        <?php

                            // $course_details contains mapped details on course_ids
                            // run through $course_details for each $course_id
                            // $course_details contains arrays with details for each course_id
                            // $key = $course_id
                            // value = simple array containing all dom elements to print
                            // -- lectures ('vorlesungen') and tuts are stored directly
                            // -- labs (pr, Uebung, sem) within nested array >> if else below
                            // (nested array is generated when adding theads to rows
                            foreach ($course_details[$c_id] as $c_details) {
                                // check if element is array
                                if(!is_array($c_details)){
                                    // if not:print
                                    print($c_details);
                                }
                                else {
                                    // otherwise runt through nested array
                                    foreach($c_details as $v){
                                        print($v);
                                    }
                                }
                                echo '<hr/>';
                            }
                        ?>
                    </div>
                    <!-- group application -->
                    <?php
                    // if there are groups, where the application can be activated for
                    // then print one row containing the activation-button
                    if(isset($activate_application[$c_id])){
                        if ($activate_application[$c_id]){
                            echo '<div>';
                            print $activate_application[$c_id];
                            echo '</div>';
                            echo '<hr/>';
                        }
                    }
                    ?>
                    <div class="row-fluid">

                        <?php
                            echo $description[$c_id];
                            echo '<hr/>';
                            echo $topics[$c_id]; # modification by CK to show topics
                            echo '<hr/>';

                            if($show_save_button[$c_id]){
                                echo form_submit($submit_data_save_all);
                            }
                        ?>
                    </div>
                <?php
                    echo form_close();
                ?>
        </div><!-- end of tab -->

        <!--	container for the dialog to add an new tutor. Need to be unique for each panel,
                otherwise there would have been lot of workarounds in jq-part -->
        <div id="add-tutor-dialog-container-<?php echo $c_id; ?>"></div>
        <div id="send-email-dialog-container-<?php echo $c_id; ?>"></div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode'); ?>

    // getting the active course from the controller to activate / show the right tab
    var activeTabId = <?php echo $active_course; ?>;

    // initialize active tab
    if(activeTabId == 0){
        $('.tab-content div:first-child').addClass("active");
        $('#course-details-navi li:first-child').addClass("active");
    }
    else {
        $('#tab-panel-'+activeTabId).addClass("active");
        $('#course-tab-'+activeTabId).addClass("active");
    }

    // create variable that contains all course_ids in that view
    var courseIdsInView = <?php echo json_encode($course_ids_jq); ?>;

    /**
    * run through all ids and assign functions
    * > email-functionality
    * - un/check all boxes if overall cb changes
    * - uncheck overall cb if ONE or more of the single cb is NOT checked
    * - check overall cb if all single cb are checked
    * - click on email-button
    * > panels
    * > activate-application-buttons
    */
    $.each(courseIdsInView, function(indexAll, courseId){

        /*
         * E-MAIL-CHECKBOXES
         */

        // save checkboxes for that course to array
        var checkboxesOnSite = $('.email-checkbox-'+courseId);
        var overallCbId = '#email-checkbox-all-id-'+courseId;
        // save id for email-button
        var sendEmailButtonId = '#send-email-button-'+courseId;
        // base of label-id
        var labelIdBase = '#course-mgt-label-';
        var labelIdOverall = $('#course-mgt-label-overall-'+courseId);

        // find out how many checkboxes there are on the course-site
        var numberCbs = 0;
        $.each(checkboxesOnSite, function(index, value){
            numberCbs++;
        });


        // change of overall cb:
        // - uncheck >> uncheck all | check >> check all
        $(overallCbId).change(function(){
            var cbAll = $(this);
            // run through all elements and set un/checked
            $.each(checkboxesOnSite, function(i, v){
                var cbSelf = $(this);
                var cbId = cbSelf.attr('id');
                var cbName = cbSelf.attr('name');
                var labelId = labelIdBase+cbName;

                // toggle checked/unchecked + color
                if(cbAll.is(':checked')){
                    $('#'+cbId).attr('checked', true);
                    labelIdOverall.text('Email senden an alle Personen und Kursteilnehmer');
                    $(labelIdOverall).addClass('label-info');
                    $(labelIdOverall).removeClass('label-default');
                    $(labelId).addClass('label-info');
                    $(labelId).removeClass('label-default');
                }
                else {
                    $('#'+cbId).attr('checked', false);
                    labelIdOverall.html('keine Auswahl f&uuml;r Email-Versand');
                    $(labelIdOverall).addClass('label-default');
                    $(labelIdOverall).removeClass('label-info');
                    $(labelId).addClass('label-default');
                    $(labelId).removeClass('label-info');
                }
            });
        });

        // change of any of the single checkboxes:
        // - uncheck one >> uncheck overall | check all >> check overall
        $.each(checkboxesOnSite, function(index, value){
            // init counter to detect if there are un/checked checkboxes
            var self = $(this);
            var cbId = self.attr('id');
            var cbName = self.attr('name');

            // build correct label-id
            var labelId = labelIdBase+cbName;

            // if checkbox changes
            $('#'+cbId).change(function(){
                var counter = 0;

                // affect label-color
                if($(this).is(':checked')){
                    $(labelId).addClass('label-info');
                    $(labelId).removeClass('label-default');
                }
                else {
                    $(labelId).addClass('label-default');
                    $(labelId).removeClass('label-info');
                }

                // count unchecked checkboxes
                $.each(checkboxesOnSite, function(i, v){
                    if($(this).is(':checked')){
                        counter++;
                    }
                });


                // if all checkboxes are checked >> check overall checkbox
                if(counter >= 1){
                    $(overallCbId).attr('checked', true);
                    labelIdOverall.addClass('label-info');
                    labelIdOverall.removeClass('label-default');
                    labelIdOverall.text('Email senden an Auswahl');
                    // otherwise uncheck overall checkbox
                }
                else if(counter == 0) {
                    $(overallCbId).attr('checked', false);
                    labelIdOverall.html('keine Auswahl f&uuml;r Email-Versand');
                    labelIdOverall.addClass('label-default');
                    labelIdOverall.removeClass('label-info');
                }
                else if(counter == numberCbs){
                    $(overallCbId).attr('checked', true);
                    labelIdOverall.addClass('label-info');
                    labelIdOverall.removeClass('label-default');
                    labelIdOverall.text('Email senden an Auswahl');
                }
            }); // end checkbox-change
        }); // end run through checkboxes

        // get staff and course checkboxes separatly and put it into an array
        var staffCbElements = $('.email-checkbox-staff-'+courseId);
        var courseCbElements = $('.email-checkbox-courses-'+courseId);
        var bothCbElements = [staffCbElements, courseCbElements];



        // behaviour for sending email to course participants
        $(sendEmailButtonId).click(function(){

            // init arrays to save recipients
            var staffRecipients = new Array();
            var courseRecipients = new Array();

            // detect chosen checkboxes -
            $.each(bothCbElements, function(index, checkboxes){
                $.each(checkboxes, function(i, v){
                    var self = $(this);
                    var cbName = self.attr('name');

                    if(self.is(':checked')){
                        // differ between staff and courses
                        if(index == 0){
                            staffRecipients.push(cbName);
                        }
                        else if(index == 1) {
                            courseRecipients.push(cbName);
                        }
                    }
                });
            });

            var dialog = createSendEmailDialog();
            $('#send-email-dialog-container-'+courseId).html(dialog);

            // function of dialog
            $('#send-email-dialog').modal({
                keyboard: false,
                backdrop: 'static'
                // !! important part: on 'show' set data-id=courseId (the id of the current course)
            }).on('show', function(){
                $('#send-email-dialog-submit').data('id', courseId);
            }).on('hide', function(){
                // on hide
            }).modal('show');

            // behavior if the send mail button is pressed
            $('#send-email-dialog-submit').click(function(){

                // save message and topic and check for correctness
                var email_topic = $('input[id=email_topic]').val();
                var email_message = $('textarea[id=email_message]').val();

                if ( email_topic > '' && email_message > ''){

                    // if there was an error message -> remove it
                    if($('.alert-error').length > 0){
                        $('.alert-error').remove();
                    }

                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url();?>kursverwaltung/ajax_send_email_to_course/",
                        async: false,
                        dataType: 'html',
                        data : {staff_recipients: staffRecipients, course_recipients: courseRecipients,
                                email_subject: email_topic, email_message: email_message},
                        success: function (data){
                            if (data = "true"){
                                $('#send-email-dialog-submit').hide();
                                $('#send-email-dialog-cancel').html('Schlie&szlig;en');
                                $('#modal-body').html('Ihre Email wurde erfolgreich versendet. Ein Kopie der Email wurde\n\
                                                       an Ihre bei meinFHD hinterlegte Email-Adresse gesendet.');
                                $('.modal-header').children().next().html('Email erfolgreich versendet');
                            }
                        }
                    });
                }
                else {

                    var error_message = '<div class="alert alert-error">F&uuml;r die Email muss ein Betreff und eine \n\
                                        Nachricht angegeben werden.</div>';

                    // if the dialog does not contain the error message display it
                    if($('.alert-error').length === 0){
                        $('#send-email-dialog').children('#modal-body').prepend(error_message);
                    }

                }

            });

        });

        /**
         * STAFF-PANLES
         */

        // ids of sliders
        var buttonId = ['#labings-slider-'+courseId, '#tuts-slider-'+courseId];
        var panelId = ['#labings-panel-'+courseId, '#tuts-panel-'+courseId];

        // PANLES - activate buttons for both - labings and tuts
        $.each(buttonId, function(index, value){
            // slide-toggle
            $(value).click(function() {
                // !!usage of index: first buttonId >> first Panel || second buttonId >> second Panel
                $(panelId[index]).slideToggle('slow', function () {
                });
            });
        });

        var spanText = ['#labing-label-', '#tut-label-'];
        var spanIdText = ['added-labings-', 'added-tuts-'];
        var spanId = ['#added-labings-', '#added-tuts-'];
        var cellId = ['#current-labings-', '#current-tuts-'];

        // saving checkboxes into var
        var cb = $('#labings-panel-'+courseId).children('input');

        // ################ activate each panel
        $.each(panelId, function(index, value){
            // show labings in table when clicked - NOT saved yet!
            $(value + ' input').change(function () {
            var self = $(this);
            var id = self.attr("id");

            if(self.is(":checked")) {
                $('<span></span>', {
                    text: $(spanText[index] + id).text()+', ',
                    id: spanIdText[index] + id
                }).appendTo(cellId[index] + courseId);
                };
                if(!self.is(":checked")){
                    $(spanId[index] + id).remove();
                };
            });
        });

        // ################ handle download-tn-buttons
        var downloadTnButtonsLab = $('.download-tn-button-'+courseId);
        var downloadTnButtonCourse = $('.download-tn-button-course-'+courseId);

        // each button in that course-view has to be handled separately
        $.each(downloadTnButtonsLab, function(index, value){
            $(value).click(function(){
                var spCourseId = $(this).data('id');
                var isSPCourse = true;
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url();?>kursverwaltung/ajax_download_course_participants_list/",
                    async: false,
                    dataType: 'html',
                    data : {sp_course_id : spCourseId, is_spcourse: isSPCourse},
                    success: function (data){
                        window.open(data); // open the file for downloading
                    }
                });
            });
        });

        $(downloadTnButtonCourse).click(function(){
            var spCourseId = $(this).data('id');
            var isSPCourse = false;
            $.ajax({
                type: "POST",
                url: "<?php echo site_url();?>kursverwaltung/ajax_download_course_participants_list/",
                async: false,
                dataType: 'html',
                data : {sp_course_id : spCourseId, is_spcourse: isSPCourse},
                success: function (data){
                    window.open(data)
                }
            });
        });

        // ################ assign-new-tutor button
        var assignNewTutorButton = $('#add-tutor-button-'+courseId);

        $('#tuts-panel-button-'+courseId).on('click', assignNewTutorButton, function(){
            var dialog = createAddTutorDialog('Einen Studenten zum Tutor machen',
                            'Suchen Sie einen Studenten &uuml;ber die Matrikelnummer und weisen Sie ihm die Tutorenrolle zu. \n\
                            Er wird danach automatisch Tutor des Kurses und kann Tutorientermine verwalten.',
                            courseId);
            $('#add-tutor-dialog-container-'+courseId).html(dialog);

            // function of dialog
            $('#add-tutor-dialog').modal({
                keyboard: false,
                backdrop: 'static'
                // !! important part: on 'show' set data-id= courseId (the one to delete)
            }).on('show', function(){
                $('#add-tutor-dialog-confirm').data('id', courseId);
                // on hide hide ^^
            }).on('hide', function(){
            }).modal('show');

            return false;
        });

        // behaviour when search started
        $('#add-tutor-dialog-container-'+courseId).on('click', '#add-tutor-dialog-search', function(){
            // creating array to pass courseId and matrikelno to search fot
            // courseId is needed, to generate dom-elements with unique id >> assign tutor-status
            var serverData = new Array(
                $('#matrnr-input').attr('value'),
                courseId
            );

            $('.modal-body').html('Student wird gesucht.');

            $.ajax({
                type: "POST",
                url: "<?php echo site_url();?>kursverwaltung/ajax_search_student_by_matrno/",
                dataType: 'html',
                data : {server_data : serverData},
                success: function (data){
                    $('.modal-body').html(data);
                }
            });

            return false;
        });


        // behaviour when tutor was added
        $('#add-tutor-dialog-container-'+courseId).on('click', '#add-tutor-dialog-assign-'+courseId, function(){

            var studentData = new Array(
                $('#add-tutor-dialog-assign-'+courseId).data('matrno'),
                courseId
            );

            $.ajax({
                type: "POST",
                url: "<?php echo site_url();?>kursverwaltung/ajax_add_student_as_tutor/",
                dataType: 'html',
                data : {student_data: studentData},
                success: function (data){
                    $('.modal-body').html(data);
                    $('.modal-footer').html('<a href="" class="btn btn-primary" id="add-tutor-dialog-confirm" data-accept="modal">OK</a>');
                    $('.modal-header button').hide();
                }
            });

            return true;
        });


        /**
         * APPLICATION BUTTON
         * color, status, text
         */

        var switchActivationButtons = $('.activation-buttons-'+courseId);
        // run through all buttons on site
        $.each(switchActivationButtons, function(index, value) {
            var buttonId = '#'+$(value).attr('id');
            var spCourseId = $(this).data('id');
            var buttonStatus = $(this).data('status');

            // click behaviour
            $(buttonId).click(function(){
                var buttonText = '';
                var rClass = '';
                var aClass = '';
                var buttonStatus = $(this).data('status');
                var courseIdStatus = [spCourseId, buttonStatus];

                switchButtonColorStatus(buttonId, buttonStatus, courseId, courseIdStatus, true);
            });
        });
    }); // end tab-views - all elements has to be prepared for all ids

    /**
     * Function that handles activate group-application buttons.
     * Color, data-status (html), text
     */
    function switchButtonColorStatus(buttonId, buttonStatus, courseId, courseIdStatus, ajax){
        // alter text and status depending on former status
        if(buttonStatus == 'disabled'){
            $('#activation-status-'+courseId+' p').text('Anmeldung ist aktiviert');
            buttonText = 'Anmeldung deaktivieren';
            buttonStatus = 'enabled';
            rClass = 'btn-warning';
            aClass = 'btn-success';
        }
        else {
            $('#activation-status-'+courseId+' p').text('Anmeldung ist deaktivert');
            buttonText = 'Anmeldung aktivieren';
            buttonStatus = 'disabled';
            rClass = 'btn-success';
            aClass = 'btn-warning';
        }

        // de/acitvate sp_course
        $.ajax({
            type: "POST",
            url: "<?php echo site_url();?>kursverwaltung/ajax_toggle_activation_of_sp_course/",
            dataType: 'html',
            data: {course_id_status : courseIdStatus},
                success: function (echo){
                    $(buttonId).data('status', buttonStatus);
                    $(buttonId).text(buttonText);
                    $(buttonId).addClass(rClass);
                    $(buttonId).removeClass(aClass);
            }
        });
    };

    /**
     * Function to create modals that lets a user search for an student
     * to add him as a tutor (in general) and for the course
     */
    function createAddTutorDialog(title, text, courseId) {
        var myDialog =
        $('<div class="modal hide" id="add-tutor-dialog"></div>')
        .html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">x</button><h3>'+title+'</h3></div>')
        .append('<div class="modal-body" id="modal-body"><p>'+text+'</p>\n\
        <p>Matrikelnummer eingeben:&nbsp;&nbsp;<input type="text" id="matrnr-input" name="matrnr" placeholder="Matrikelnummer">\n\
        &nbsp;&nbsp;<input type="submit" class="btn-info" id="add-tutor-dialog-search" value="Suchen"></div>')
        .append('<div class="modal-footer"><a href="#" class="btn" id="add-tutor-dialog-cancel" data-dismiss="modal">Abbrechen</a>\n\
        </div>');

        return myDialog;
    };

    /**
     * Function to create the send email modal dialog
     */
    function createSendEmailDialog(){
        var myDialog =
            $('<div class="modal hide" id="send-email-dialog"></div>')
            .html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">x</button><h3>Email versenden</h3></div>')
            .append('<div class="modal-body" id="modal-body">\n\
                    <p>Geben Sie hier Ihre Nachricht ein, die an die zuvor ausgew&auml;hlten Personen und Teilnehmergruppen versendet werden soll.</p>\n\
                    <hr/>\n\
                    <?php echo form_open('', $email_form_open_attrs); ?>\n\
                    <?php echo form_input($email_topic_input); ?>\n\
                    <br/><p>Hier k&ouml;nnen Sie Ihre Nachricht eingeben. Der Text kann mit <strong>HTML-Tags</strong> formatiert werden.\n\
                    </strong></p>\n\
                    <?php echo form_textarea($email_message_textarea); ?>\n\
                    <?php echo form_close(); ?></div>\n\
                    <div class="modal-footer"><a href="#" id="send-email-dialog-submit" class="btn btn-warning">Email versenden</a>\n\
                    <a href="#" class="btn" id="send-email-dialog-cancel" data-dismiss="modal">Abbrechen</a>');

        return myDialog;
    }

<?php endblock(); ?>
<?php end_extend(); ?>

