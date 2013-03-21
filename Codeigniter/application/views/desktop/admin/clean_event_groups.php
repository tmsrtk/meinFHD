<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Veranstaltungsgruppen bereinigen<?php endblock(); ?>

<?php startblock('content'); # main content ?>
    <div class="row-fluid">
        <div class="span12">
            <h3>Veranstaltungsgruppen bereinigen</h3>
            <hr/>
            <p>
                Nach Ablauf eines Semesters k&ouml;nnen &uuml;ber den unten stehenden Button alle Studenten aus Gruppen austragen werden, in denen diese
                nicht l&auml;nger Mitglied sind. Dies ist n&ouml;tig, da der Stundenplan des letzten Semesters f&uuml;r die Studenten automatisch
                verschwindet und diese sich nicht mehr aus ihren Veranstaltungsgruppen austragen k&ouml;nnen.<br/><strong>Wichtig:</strong> Die entsprechenden
                Stundenpl&auml;ne m&uuml;ssen im Vorfeld bereits gel&ouml;scht sein. &nbsp;Andernfalls zeigt das Bereinigen der Veranstaltungsgruppen
                keine wirkung<br/><br/>
            </p>

            <?php
                # general form setup
                $form_attributes = 'id="clean_event_groups"';
                // prepare attributes for submit button
                $submit_button_attributes = array(
                    'name' => 'clean_event_groups_button',
                    'type' => 'submit',
                    'id' => 'submit_clean_event_groups',
                    'class' => 'btn btn-primary btn-danger',
                    'value' => 'Alle Veranstaltungsgruppen bereinigen'
                );

                # form itself
                echo form_open('admin/clean_event_groups', $form_attributes);
                echo form_submit($submit_button_attributes);
                echo form_close();
            ?>
        </div>
    </div>

    <div id="modalcontainer">
        <div class="modal hide" id="clean-groups-confirmation-dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">X</button>
                <h3>Veranstaltungsgruppen bereinigen</h3>
            </div>
            <div class="modal-body">
                <p>Sollen wirklich alle Veranstaltungsgruppen bereinigt werden?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" id="dismiss-clean-event-groups-dialog" data-dismiss="modal">Nein</a>
                <a href="#" class="btn btn-primary" id="confirm-clean-event-groups-dialog">Ja</a>
            </div>
        </div>
    </div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

    // if the clean groups buttons is clicked open up the confirmation dialog / modal
    $('#submit_clean_event_groups').click(function(){
        // open up the confirmation dialog / modal
        $('#clean-groups-confirmation-dialog').modal({
            keyboard: false,
            backdrop: 'static'
        }).modal('show');

        return false;
    });

    // if clicked on yes submit start cleaning the event groups
    $('#modalcontainer').on('click', '#confirm-clean-event-groups-dialog', function(){

        // submit the form to clean all event groups
        $('form#clean_event_groups').submit();

        // close the confirmation dialog
        $('#clean-groups-confirmation-dialog').modal('hide');
    });
<?php endblock(); ?>

<?php end_extend(); ?>
