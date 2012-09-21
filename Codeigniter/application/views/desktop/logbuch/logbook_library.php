<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbuchbibliothek<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/index'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Logbuchmen&uuml;</a>
        </div>
    </div><!-- END Logbuch Bibliothek Header -->
    <br/>
    <!-- dynamic content foreach logbuch -->
    <table class="table">
        <tbody>
            <?php foreach($logbooks as $book => $book_attr) :?>
            <tr data-id="<?php echo $book_attr['LogbuchID']; ?>">
                <td>
                    <span><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_notes_2.png" alt="Logbuecher" style="max-width: 20px; height: auto;"/></span>
                   &nbsp;&nbsp;&nbsp;<a href="<?php print base_url('logbuch/show_logbook_content'); ?>/<?php echo $book_attr['LogbuchID']; ?>"><strong><?php echo $book_attr['kurs_kurz']; ?></strong></a>
                    <div class="row-fluid" style="margin-left: 35px;">
                          <div class="progress <?php
                                  if($book_attr['Bewertung'] < 50){
                                      echo 'progress-info';
                                  }
                                  else if ($book_attr['Bewertung'] > 50 && $book_attr['Bewertung'] < 70) {
                                      echo 'progress-warning';
                                  }
                                  else if ($book_attr['Bewertung'] > 70) {
                                      echo 'progress-success';
                                  }
                                  ?>  progress-striped active">
                              <div class="bar" style="width: <?php echo $book_attr['Bewertung']; ?>%;"></div>
                          </div>
                    </div>
                </td>
                <td>
                </td>
                <td>
                    <a href="#" class="btn pull-right deleteLogbook"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_bin.png" alt="delete" style="max-width:18px; height: 18px;"/></a>
                </td>
                <td>
                    <a href="<?php print base_url('logbuch/show_logbook_content'); ?>/<?php echo $book_attr['LogbuchID']; ?>" class="btn pull-right"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_thin_right_arrow.png" alt="delete" style="max-width: 18px; height: auto;"/></a>
                </td>
            </tr>
            <?php endforeach ?>
            <!-- end foreach content -->
        </tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr><!-- spacer row for border underneath the last line -->
    </table>
    <!-- start last row to add logbooks -->
    <div class="pagination-centered">
        <a href="<?php print base_url('logbuch/add_logbook'); ?>"><img src="<?php print base_url(''); ?>resources/img/logbuch-icons/glyphicons_circle_plus.png" alt="delete" style="max-width: 26px; height: 26px;"/></a>
    </div>
    <!-- end last row -->
</div><!-- /div well well -->

<div id="modalcontent"></div>

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

    // function to create a modal dialog
    function createDeleteLogbookModalDialog(title, text, logbook_id) {
        var $myModalDialog = $('<div class="modal hide" id="myModal"></div>')
            .html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
            .append('<div class="modal-body"><p>'+text+'</p></div>')
            .append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Nein</a><a href="<?php print base_url('logbuch/delete_logbook');?>/'+logbook_id+'" class="btn btn-primary" data-accept="modal">Ja</a></div>');
        return $myModalDialog;
    }

    // prompt modal - delete logbook
    $(".deleteLogbook").click(function() {
        $(this).attr("data-clicked", "true");
        // get the id of the logbook to delete
        var logbook_to_delete = $(this).parent().parent().data("id");

        // before prompting the modal scroll view to the top -> modal is presented on top of the page
        $(document).scrollTop(1);

        var myModal = createDeleteLogbookModalDialog('Logbuch löschen', 'Möchtest du das ausgewählte Logbuch wirklich löschen? Alle im Logbuch hinterlegten Einträge werden ebenfalls gelöscht und können nicht wiederhergestellt werden.', logbook_to_delete);
        $("#modalcontent").html(myModal);
        $('#myModal').modal({
            keyboard: false
        }).on('hide', function () {
            $("input[type=submit][data-clicked=true]").removeAttr("data-clicked");
        }).modal('show');


        return false;
    });

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>