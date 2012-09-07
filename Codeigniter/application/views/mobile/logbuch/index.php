<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbuch<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span2">
            <a href="<?php print base_url('dashboard/index'); ?>" class="btn btn-mediuma btn-danger" style="font-weight: bold;" ><i class="icon-arrow-left icon-white"></i>&nbsp;Dashboard</a>
            <hr>
        </div>
    </div><!-- END Logbuch Header -->

    <div class="row-fluid">
        <div class="span12">
            <img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_log_book.png" alt="Logbuecher" width="20" height="20"/>&nbsp;
            <a href="#" class="btn btn-link pull-right" style="font-weight: bold;">&gt;</a>
            <a href="<?php print base_url('logbuch/index'); ?>"><strong>Meine Logb&uuml;cher</strong></a><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>&Uuml;bersicht &uuml;ber alle<br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;anglegten Logbücher</em>
        </div>
    </div>
    <hr><!-- end element logbuecher -->

    <div class="row-fluid">
        <div class="span12">
            <img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_circle_plus.png" alt="Logbuch hinzufuegen" width="20" height="20"/>&nbsp;
            <a href="#" class="btn btn-link pull-right" style="font-weight: bold;">&gt;</a>
            <a href="<?php print base_url('logbuch/index'); ?>"><strong>Logbuch anlegen</strong></a><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Lege dir jetzt ein Logbuch an</em>
        </div>
    </div>
    <hr><!-- end element logbuch anlegen -->

    <div class="row-fluid">
        <div class="span12">
            <img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_stats.png" alt="Auswertungen" width="20" height="20"/>&nbsp;
            <a href="#" class="btn btn-link pull-right" style="font-weight: bold;">&gt;</a>
            <a href="<?php print base_url('logbuch/index'); ?>"><strong>Auswertungen</strong></a><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>f&uuml;r jedes Logbuch</em>
        </div>
    </div>
    <hr><!-- end element Auswertungen -->

    <div class="row-fluid">
        <div class="span12">
            <img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_cup.png" alt="Pokal" width="20" height="20"/>&nbsp;
            <a href="#" class="btn btn-link pull-right" style="font-weight: bold;">&gt;</a>
            <a href="<?php print base_url('logbuch/index'); ?>"><strong>Meine Auszeichnungen</strong></a><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Achievement&uuml;bersicht</em>
        </div>
    </div>
    <hr><!-- end element Auszeichnungen -->

    <div class="row-fluid">
        <div class="span12">
            <img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_question_mark.png" alt="Fragezeichen" width="20" height="20"/>&nbsp;
            <a href="#" class="btn btn-link pull-right" style="font-weight: bold;">&gt;</a>
            <a href="<?php print base_url('logbuch/index'); ?>"><strong>FAQ</strong></a><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Hilfe</em>
        </div>
    </div>
</div>

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>


<?php end_extend(); # end extend main template ?>