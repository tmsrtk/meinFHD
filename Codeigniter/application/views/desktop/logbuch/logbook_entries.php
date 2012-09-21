<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbucheintr&auml;ge zu (Kurs/Logbuchname)<?php endblock(); ?>

<?php startblock('content'); # content ?>
<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/show_logbooks'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Logbuch&uuml;bersicht</a><br/><br/>
            <span><strong>Du warst schon <?php echo $attendance_count; ?> mal da</strong></span>
        </div>
    </div>
    <br/>
    <!-- END Logbuch Bibliothek Header -->

    <!-- begin dynamic logbook entries -->
    <table class="table">
        <tbody>
            <!-- single row if no entry is available -->
            <?php if(!$logbook_entries) : # if no entry is available display the user an info?>
            <tr>
                <td>
                    <h3>Kein Eintrag gefunden</h3>
                    <p class="alert alert-info">F&uuml;r dieses Logbuch wurde noch kein Eintrag hinterlegt. Hinterlege zun&auml;chst ein Thema, damit Du den vollen Funktionsumfang des Logbuchs nutzen kannst.
                        Zum Anlegen eines Themas, steht dir der Plus-Button am unteren Ende zur Verf&uuml;gung
                    </p>
                </td>
            </tr>
            <?php endif ?>
            <!-- end single row if no entry is available -->
            <!-- begin dynamic logbook entries -->
            <?php foreach($logbook_entries as $single_entry => $entry_value) : ?>
            <tr data-id="<?php echo $entry_value['LogbucheintragID']; ?>">
                <td>
                    <span><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_list.png" alt="Logbuecher" style="max-width: 18px; height: auto;"/></span>
                </td>
                <td>
                    <a href="<?php print base_url('logbuch/edit_entry_mask/'.$entry_value['LogbucheintragID']); ?>"><strong><?php echo $entry_value['Thema']; ?></strong></a>
                    <div class="row-fluid"><!-- second row for successbar -->
                        <div class="progress <?php
                                                if($entry_value['Bewertung'] < 50){
                                                    echo 'progress-info';
                                                }
                                                else if ($entry_value['Bewertung'] > 50 && $entry_value['Bewertung'] < 70) {
                                                    echo 'progress-warning';
                                                }
                                                else if ($entry_value['Bewertung'] > 70) {
                                                    echo 'progress-success';
                                                }
                                                ?>  progress-striped active">
                            <div class="bar" style="width: <?php echo $entry_value['Bewertung']; ?>%;"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="#" class="btn pull-right deleteEntry"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_bin.png" alt="edit" style="max-width: 18px; height: 18px;"/></a>
                </td>
                <td>
                    <a href="<?php print base_url('logbuch/edit_entry_mask/'.$entry_value['LogbucheintragID']); ?>" class="btn pull-right"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_pencil.png" alt="edit" style="max-width: 18px; max-height: auto;"/></a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr><!-- spacer row for border underneath the last line -->
    </table>
    <!-- start last row to add logbooks -->
    <div class="pagination-centered" >
        <a href="<?php print base_url('logbuch/create_entry_mask'); ?>/<?php echo $logbook_id; ?>"><img src="<?php print base_url(''); ?>resources/img/logbuch-icons/glyphicons_circle_plus.png" alt="delete" style="max-width: 26px; height: auto;"/></a>
    </div>

    <!-- end last row -->
</div><!-- /div well well -->

<div id="modalcontent"></div>

<?php endblock(); # end content?>

<?php startblock('customFooterJQueryCode');?>

    // function to create a modal dialog
    function createDeleteEntryModalDialog(title, text, entry_id,logbook_id) {
        var $myModalDialog = $('<div class="modal hide" id="myModal"></div>')
            .html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
            .append('<div class="modal-body"><p>'+text+'</p></div>')
            .append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Nein</a><a href="<?php print base_url('logbuch/delete_single_logbook_entry');?>/'+entry_id+'/'+logbook_id+'" class="btn btn-primary" data-accept="modal">Ja</a></div>');
        return $myModalDialog;
    }

    // prompt modal - delete logbook
    $(".deleteEntry").click(function() {
        // get the id of the entry to delete
        var entry_to_delete = $(this).parent().parent().data("id");
        var logbook_to_delete = <?php echo $logbook_id; ?>;

        // before prompting the modal scroll view to the top -> modal is presented on top of the page
        $(document).scrollTop(1);

        var myModal = createDeleteEntryModalDialog('Eintrag löschen', 'Möchtest du den ausgewählten Logbucheintrag wirklich löschen? Das Löschen kann nicht rückgängig gemacht werden.', entry_to_delete, logbook_to_delete);
        $("#modalcontent").html(myModal);
        $('#myModal').modal({
            keyboard: false
        }).on('hide', function () {
            $("input[type=submit][data-clicked=true]").removeAttr("data-clicked");
        }).modal('show');

        return false;
    });

<?php endblock(); # end custom jQuery Code ?>

<?php end_extend(); # end extend main template ?>