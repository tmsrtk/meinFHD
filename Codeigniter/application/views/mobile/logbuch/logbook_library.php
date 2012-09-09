<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbuchbibliothek<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/index'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;zur&uuml;ck</a>
        </div>
    </div>
    <hr><!-- END Logbuch Bibliothek Header -->

    <!-- FOREACH LOGBUCH IN ARRAY SHOW THE FOLLOWING // DYNAMIC GENERATION -->
    <div class="row-fluid">
        <div class="span12">
            <img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_notes_2.png" alt="Logbuecher" width="20" height="27"/>
            <a href="#" class="btn pull-right"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_thin_right_arrow.png" alt="delete" width="11" height="15"/></a>
             <span class="pull-right" style="margin-right: 15%;">
                <a href="#"  id="deleteLogbook" class="btn"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_bin.png" alt="delete" width="11" height="15"/></a>
            </span>
            <a href="<?php print base_url('logbuch/show_logbooks'); ?>" style="margin-left: 4%;"><strong>Dummy Fach</strong></a>
        </div>
    </div>
    <div class="row-fluid"> <!-- second row for success bar -->
        <div class="span12">
            <div class="progress progress-danger progress-striped active" style="width: 30%; margin-left: 13%; margin-bottom: 0%; " >
                <div class="bar" style="width: 20%;"></div
            </div>
        </div>
    </div>
    <hr>
    <!-- END FOREACH -->

    <div class="row-fluid"><!-- start last row to add logbooks -->
        <div class="span12 pagination-centered">
            <a href="<?php print base_url('logbuch/add_logbook'); ?>"><img src="<?php print base_url(''); ?>resources/img/logbuch-icons/glyphicons_circle_plus.png" alt="delete" width="26" height="26"/></a>
        </div>
    </div><!-- end last row -->
</div>

<div id="modalcontent"></div>

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

    // function to create a modal dialog
    function createModalDialog(title, text) {
        var $myModalDialog = $('<div class="modal hide" id="myModal"></div>')
            .html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
            .append('<div class="modal-body"><p>'+text+'</p></div>')
            .append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Abbrechen</a><a href="" class="btn btn-primary" data-accept="modal">OK</a></div>');
        return $myModalDialog;
    }

    // prompt modal - delte logbook
    $("#deleteLogbook").click(function() {
        $(this).attr("data-clicked", "true");

        var myModal = createModalDialog('Logbuch löschen', 'Soll das Logbuch wirkklich gelöscht werden?');
        $("#modalcontent").html(myModal);

        $('#myModal').modal({
            keyboard: false
        }).on('hide', function () {
            $("input[type=submit][data-clicked=true]").removeAttr("data-clicked");
        }).modal('show');

        return false;
    });

    // start delete process, if user has clicked OK - events when elements in modal are clicked
    $("#modalcontent").on('click', 'button, a', function(event) {
        if ($(this).attr("data-accept") === 'modal'){ // delete the selected logbook
            console.log('delete');

        }

        return false;
    });

<?php endblock(); ?>


<?php end_extend(); # end extend main template ?>